<?php

namespace modmore\Commerce_TimeSlots\Admin\Orders;

use modmore\Commerce\Admin\Util\Action;
use modmore\Commerce\Admin\Util\Column;
use modmore\Commerce\Events\Admin\OrderActions;

class Grid extends \modmore\Commerce\Admin\Orders\Grid {
    public $defaultSort = 'slot_from';
    public $defaultSortDir = 'ASC';
    /**
     * @var int
     */
    protected $methodId;
    /**
     * @var \comSimpleObject|\xPDOSimpleObject|null
     */
    protected $method;

    public function getItems(array $options = array())
    {
        $this->methodId = (int)$this->getOption('method');
        $this->method = $this->adapter->getObject('comShippingMethod', [
            'class_key' => \TimeSlotsShippingMethod::class,
            'removed' => false,
            'id' => $this->methodId
        ]);
        if (!$this->method) {
            return [];
        }

        $items = [];
        $stateClasses = array_merge(['comProcessingOrder'], $this->adapter->getDescendants('comProcessingOrder'));

        $c = $this->adapter->newQuery('comOrderShipment');
        $c->innerJoin('comOrder', 'Order');
        $c->innerJoin('ctsOrderSlot', 'OrderSlot', 'comOrderShipment.id = OrderSlot.shipment');
        $c->innerJoin('ctsDateSlot', 'Slot', 'OrderSlot.slot = Slot.id');
        $c->select($this->adapter->getSelectColumns('comOrderShipment', 'comOrderShipment' ));
        $c->select($this->adapter->getSelectColumns('comOrder', 'Order', 'order_'));
        $c->where([
            'Order.class_key:IN' => $stateClasses,
            'Order.test' => $this->commerce->isTestMode(),
            'method' => $this->method->get('id'),
        ]);

        // Filter on the slot
        if (array_key_exists('slot', $options) && $options['slot'] > 0) {
            $c->where([
                'OrderSlot.slot' => (int)$options['slot'],
            ]);
        }

        // Find an exact or fuzzy match by the ID
        if (array_key_exists('search_by_id', $options) && $options['search_by_id'] > 0) {
            $c->where([
                'id' => (int)$options['search_by_id'],
                'OR:id:LIKE' => '%' . $options['search_by_id'] . '%',
                'OR:reference:LIKE' => '%' . $options['search_by_id'] . '%',
            ]);
        }

        if (array_key_exists('search_by_address', $options) && strlen($options['search_by_address']) > 0) {
            $this->_filteredAddress = $addressSearch = $options['search_by_address'];

            $c->leftJoin('comOrderAddress', 'Address', 'comOrder.id = Address.order');
            $c->where([
                'Address.fullname:LIKE' => "%{$addressSearch}%",
                'OR:Address.firstname:LIKE' => "%{$addressSearch}%",
                'OR:Address.lastname:LIKE' => "%{$addressSearch}%",
                'OR:Address.company:LIKE' => "%{$addressSearch}%",
                'OR:Address.address1:LIKE' => "%{$addressSearch}%",
                'OR:Address.address2:LIKE' => "%{$addressSearch}%",
                'OR:Address.address3:LIKE' => "%{$addressSearch}%",
                'OR:Address.zip:LIKE' => "%{$addressSearch}%",
                'OR:Address.city:LIKE' => "%{$addressSearch}%",
                'OR:Address.state:LIKE' => "%{$addressSearch}%",
            ]);
        }

        // Filter on the status
        if (array_key_exists('status', $options) && $options['status'] > 0) {
            $c->where([
                'status' => (int)$options['status']
            ]);
        }

        // Filter on the context
        if (array_key_exists('context', $options) && $options['context'] !== '') {
            $c->where([
                'context' => (string)$options['context']
            ]);
        }

        $sortby = array_key_exists('sortby', $options) && !empty($options['sortby'])
            ? $this->adapter->escape($options['sortby'])
            : $this->defaultSort;
        $sortdir = (array_key_exists('sortdir', $options) && !empty($options['sortdir']))
            ? (strtoupper($options['sortdir']) === 'ASC' ? 'ASC' : 'DESC')
            : $this->defaultSortDir;

        if ($sortby === 'slot_from') {
            $c->sortby('`Slot`.`time_from`', $sortdir);
        }
        else {
            $c->sortby($sortby, $sortdir);
        }

        $count = $this->adapter->getCount('comOrderShipment', $c);
        $this->setTotalCount($count);

        $c->limit($options['limit'], $options['start']);
        /** @var \comOrderShipment[] $collection */
        $collection = $this->adapter->getCollection('comOrderShipment', $c);

        foreach ($collection as $order) {
            $items[] = $this->prepareShipment($order);
        }

        return $items;
    }

    public function getColumns(array $options = array())
    {
        return [
            new Column('id', $this->adapter->lexicon('commerce.order_id'), true),
            new Column('slot_time', $this->adapter->lexicon('commerce_timeslots.time_from'), true),
            new Column('reference', $this->adapter->lexicon('commerce.reference'), true),
            new Column('received_on', $this->adapter->lexicon('commerce.received_on'), true),
            new Column('total', $this->adapter->lexicon('commerce.total'), true),
            new Column('total_due', $this->adapter->lexicon('commerce.total_due'), true),
        ];
    }

    public function getTopToolbar(array $options = array())
    {
        return array_merge([
            [
                'name' => 'slot',
                'title' => $this->adapter->lexicon('commerce_timeslots.slot'),
                'type' => 'select',
                'value' => array_key_exists('slot', $options) ? (int)$options['slot'] : '',
                'options' => $this->_getSlotOptions(),
                'position' => 'top',
                'width' => 'three wide',
            ]
        ], parent::getTopToolbar());
    }

    public function prepareShipment(\comOrderShipment $shipmentItem)
    {
        /** @var \comOrder $order */
        $order = $shipmentItem->getOrder();
        $item = $this->prepareItem($order);

        return $item;



        $itemId = $order->get('id');
        $detailUrl = $this->adapter->makeAdminUrl('order', ['order' => $itemId]);
        $item['detail_url'] = $detailUrl;

        $address = $order->getShippingAddress();
        if ($address) {
            $item['shipping_address'] = $address->toArray();
        }

        $status = $order->getStatus();
        $item['status'] = $status->toArray();
        $item['status_light_color'] = $status->isLightColor();

        $item['shipping_method'] = [];
        $item['shipments'] = [];
        /** @var \comOrderShipment[] $shipments */
        $shipments = $this->adapter->getCollection('comOrderShipment', ['order' => $order->get('id')]);

        $item['shipping_method'] = [];
        foreach ($shipments as $shipment) {
            $method = $shipment->getShippingMethod(false);
            $item['shipping_method'][] = $method ? $method->get('name') : '#' . $shipment->get('method');
            $sa = $shipment->toArray();
            $sa['method'] = $method ? $method->toArray() : false;
            $item['shipments'][] = $sa;
        }
        $shippingMethod = $order->getShippingMethod();
        if ($shippingMethod) {
            $item['shipping_method'][] = $shippingMethod->get('name');
        }
        $item['shipping_method'] = implode(', ', $item['shipping_method']);


        $item['payment_method'] = [];
        $transactions = $order->getTransactions();
        if (count($transactions) !== 0) {
            $transaction = reset($transactions);
            $paymentMethod = $transaction->getMethod();
            $item['payment_method'][] = $paymentMethod ? $paymentMethod->get('name') : '#' . $transaction->get('method');
        }
        $item['payment_method'] = implode(', ', $item['payment_method']);


        $item['items'] = [];

        foreach ($order->getItems() as $orderItem) {
            $oia = $orderItem->toArray();
            if ($product = $orderItem->getProduct()) {
                $oia['product'] = $product->toArray();
            }
            $item['items'][] = $oia;
        }

        $actions = [];

        if ($this->adapter->hasPermission('commerce_order')) {
            $actions[] = (new Action())
                ->setUrl($detailUrl)
                ->setIcon('zoom')
                ->setModalTitle($this->adapter->lexicon('commerce.order.quick_view_details'));

            $actions[] = (new Action())
                ->setUrl($detailUrl)
                ->setTitle($this->adapter->lexicon('commerce.order.view_details'))
                ->setModal(false);
        }

        if ($this->adapter->hasPermission('commerce_order_messages_send')) {
            $defaultMsgType = $this->adapter->getOption('commerce.default_message_type', null, 'comOrderEmailMessage', true);
            $actions[] = (new Action())
                ->setUrl($this->adapter->makeAdminUrl('order/messages/create', ['order' => $order->get('id'), 'class_key' => $defaultMsgType]))
                ->setTitle($this->adapter->lexicon('commerce.create_message'));
        }

        $item['status_changes'] = [];
        if ($this->adapter->hasPermission('commerce_order_change_status')) {
            $status = $order->getStatus();
            if ($status) {
                $changes = $status->getAvailableChanges();

                foreach ($changes as $change) {
                    $changeUrl = $this->adapter->makeAdminUrl('order/set_status', ['order' => $order->get('id'), 'id' => $change->get('id')]);
                    $item['status_changes'][] = (new Action())
                        ->setUrl($changeUrl)
                        ->setTitle($change->get('name'));
                }
            }
        }

        /** @var OrderActions $event */
        $event = $this->commerce->dispatcher->dispatch(\Commerce::EVENT_DASHBOARD_ORDER_ACTIONS, new OrderActions($actions, $order));
        $actions = $event->getActions();

        $item['actions'] = $actions;

        return $item;
    }

    protected function _getStatusOptions()
    {
        $return = [];

        // Grab the configured statuses for the current state
        $c = $this->adapter->newQuery('comStatus');
        $c->where(['state' => \comOrder::STATE_PROCESSING]);
        $c->sortby('sortorder', 'ASC');
        $c->sortby('name', 'ASC');
        $c->limit(0);

        /** @var \comStatus[] $statuses */
        $statuses = $this->adapter->getCollection('comStatus', $c);

        foreach ($statuses as $status) {
            $return[] = [
                'value' => $status->get('id'),
                'label' => $status->get('name')
            ];
        }

        return $return;
    }
    
    protected function _getSlotOptions()
    {
        $return = [];

        // Grab the configured statuses for the current state
        $c = $this->adapter->newQuery('ctsDateSlot');
        $c->where([
            'shipping_method' => $this->method->get('id'),
            'AND:time_until:>' => time() - (60 * 60 * 3), // all slots that ended in the last 3 hours 
        ]);
        $c->sortby('time_from', 'ASC');
        $c->sortby('time_until', 'ASC');
        $c->limit(0);

        /** @var \ctsDateSlot[] $slots */
        $slots = $this->adapter->getCollection('ctsDateSlot', $c);

        foreach ($slots as $slot) {
            $return[] = [
                'value' => $slot->get('id'),
                'label' => strftime('%a %x %H:%M', $slot->get('time_from')) . '-' . strftime('%H:%M', $slot->get('time_until'))
            ];
        }

        return $return;
    }

    protected function _getContextOptions()
    {
        $return = [];

        // Grab the configured statuses for the current state
        $c = $this->adapter->newQuery('comOrder');
        $c->select($this->adapter->getSelectColumns('comOrder', 'comOrder', '', ['context']));
        $c->groupby('context');
        $c->limit(0);

        /** @var \comOrder $order */
        foreach ($this->adapter->getIterator('comOrder', $c) as $order) {
            $return[] = [
                'value' => $order->get('context'),
                'label' => $order->get('context')
            ];
        }

        return $return;
    }

    public function getNoResults()
    {
        return $this->adapter->lexicon('commerce.grid.no_results.orders');
    }


    public function render(array $phs)
    {
        return $this->commerce->view()->render('timeslots/admin/orders_grid.twig', $phs);
    }
}
