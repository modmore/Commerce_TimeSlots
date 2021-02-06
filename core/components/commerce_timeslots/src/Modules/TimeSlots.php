<?php
namespace modmore\Commerce_TimeSlots\Modules;

use modmore\Commerce\Events\Admin\GeneratorEvent;
use modmore\Commerce\Events\Admin\TopNavMenu;
use modmore\Commerce\Events\Checkout;
use modmore\Commerce\Modules\BaseModule;
use modmore\Commerce_TimeSlots\Admin\Schedule\Create;
use modmore\Commerce_TimeSlots\Admin\Schedule\Delete;
use modmore\Commerce_TimeSlots\Admin\Schedule\Duplicate;
use modmore\Commerce_TimeSlots\Admin\Schedule\Overview;
use modmore\Commerce_TimeSlots\Admin\Schedule\Update;
use Symfony\Component\EventDispatcher\EventDispatcher;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

class TimeSlots extends BaseModule {

    public function getName()
    {
        $this->adapter->loadLexicon('commerce_timeslots:default');
        return $this->adapter->lexicon('commerce_timeslots');
    }

    public function getAuthor()
    {
        return 'modmore';
    }

    public function getDescription()
    {
        return $this->adapter->lexicon('commerce_timeslots.description');
    }

    public function initialize(EventDispatcher $dispatcher)
    {
        // Load our lexicon
        $this->adapter->loadLexicon('commerce_timeslots:default');

        // Add the xPDO package, so Commerce can detect the derivative classes
        $root = dirname(__DIR__, 2);
        $path = $root . '/model/';
        $this->adapter->loadPackage('commerce_timeslots', $path);

        // Add template path to twig
        $root = dirname(__DIR__, 2);
        $this->commerce->view()->addTemplatesPath($root . '/templates/');

        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_INIT_GENERATOR, [$this, 'initGenerator']);
        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_GET_MENU, [$this, 'getMenu']);
        $dispatcher->addListener(\Commerce::EVENT_CHECKOUT_BEFORE_STEP, [$this, 'beforeCheckoutStep']);
    }

    public function initGenerator(GeneratorEvent $event)
    {
        $generator = $event->getGenerator();

        $generator->addPage('timeslots', Overview::class);
        $generator->addPage('timeslots/schedule', Overview::class);
        $generator->addPage('timeslots/schedule/add', Create::class);
        $generator->addPage('timeslots/schedule/edit', Update::class);
        $generator->addPage('timeslots/schedule/delete', Delete::class);
        $generator->addPage('timeslots/schedule/duplicate', Duplicate::class);
        $generator->addPage('timeslots/schedule/slot/add', \modmore\Commerce_TimeSlots\Admin\Schedule\Slot\Create::class);
        $generator->addPage('timeslots/schedule/slot/edit', \modmore\Commerce_TimeSlots\Admin\Schedule\Slot\Update::class);
        $generator->addPage('timeslots/schedule/slot/delete', \modmore\Commerce_TimeSlots\Admin\Schedule\Slot\Delete::class);
        $generator->addPage('timeslots/schedule/slot/duplicate', \modmore\Commerce_TimeSlots\Admin\Schedule\Slot\Duplicate::class);

        $generator->addPage('timeslots/planning', \modmore\Commerce_TimeSlots\Admin\Planning\Overview::class);
        $generator->addPage('timeslots/planning/edit', \modmore\Commerce_TimeSlots\Admin\Planning\Update::class);
        $generator->addPage('timeslots/planning/slot/add', \modmore\Commerce_TimeSlots\Admin\Planning\Slot\Create::class);
        $generator->addPage('timeslots/planning/slot/edit', \modmore\Commerce_TimeSlots\Admin\Planning\Slot\Update::class);
        $generator->addPage('timeslots/planning/slot/delete', \modmore\Commerce_TimeSlots\Admin\Planning\Slot\Delete::class);
        $generator->addPage('timeslots/planning/slot/duplicate', \modmore\Commerce_TimeSlots\Admin\Planning\Slot\Duplicate::class);
    }

    public function getMenu(TopNavMenu $event)
    {
        $items = $event->getItems();

        $submenu = [];

        $methods = $this->adapter->getCollection('comShippingMethod', [
            'class_key' => \TimeSlotsShippingMethod::class,
            'removed' => false,
        ]);
        $first = null;
        foreach ($methods as $method) {
            if (!$first) {
                $first = $this->adapter->makeAdminUrl('timeslots/planning', ['method' => $method->get('id')]);
            }
            $submenu[] = [
                'name' => $method->get('name') . ' ' . $this->adapter->lexicon('commerce_timeslots.planning'),
                'key' => 'timeslots/planning-' . $method->get('id'),
                'icon' => 'icon icon-calendar',
                'link' => $this->adapter->makeAdminUrl('timeslots/planning', ['method' => $method->get('id')]),
            ];
        }
        if (!$first) {
            $first = $this->adapter->makeAdminUrl('timeslots/schedule');
        }
        $submenu[] = [
            'name' => $this->adapter->lexicon('commerce_timeslots.schedule'),
            'key' => 'timeslots/schedule',
            'icon' => 'icon icon-bars',
            'link' => $this->adapter->makeAdminUrl('timeslots/schedule'),
        ];

        $items = $this->insertInArray($items, [
            'timeslots' => [
                'name' => $this->adapter->lexicon('commerce_timeslots'),
                'key' => 'timeslots',
//                'icon' => 'icon icon-clock',
                'link' => $first,
                'submenu' => $submenu,
            ]
        ], 4);

        $event->setItems($items);
    }

    public function beforeCheckoutStep(Checkout $event)
    {
        $order = $event->getOrder();
        $step = $event->getStepKey();
        $response = $event->getResponse();

        $canChange =
            $order->getState() === \comOrder::STATE_CART
            && !$order->getCurrentTransaction()
            && $step !== 'thank-you';

        $shipments = $order->getShipments();
        foreach ($shipments as $shipment) {
            $method = $shipment->getShippingMethod();
            if (!$method) {
                continue;
            }

            if ($method->get('class_key') !== \TimeSlotsShippingMethod::class) {
                continue;
            }

            $slotId = $shipment->getProperty('timeslots_slot');
            if (!$slotId) {
                if ($step === 'payment') {
                    $response->addError($this->adapter->lexicon('commerce_timeslots.select_a_slot'));
                    $response->setRedirect('checkout', ['step' => 'shipping']);
                }
                continue;
            }

            /** @var \ctsDateSlot $slot */
            $slot = $this->adapter->getObject(\ctsDateSlot::class, ['id' => (int)$slotId]);
            if (!$slot) {
                $shipment->unsetProperty('timeslots_slot');
                $shipment->unsetProperty('timeslots_slot_info');
                $shipment->unsetProperty('timeslots_slot_autoselected');
                $shipment->save();

                if ($step === 'payment') {
                    $response->addError($this->adapter->lexicon('commerce_timeslots.select_a_slot'));
                    $response->setRedirect('checkout', ['step' => 'shipping']);
                }
                continue;
            }

            if ($canChange && !$slot->isAvailable()) {
                $shipment->unsetProperty('timeslots_slot');
                $shipment->unsetProperty('timeslots_slot_info');
                $shipment->unsetProperty('timeslots_slot_autoselected');
                $shipment->save();

                $response->addError($this->adapter->lexicon('commerce_timeslots.selected_slot_no_longer_available'));
                $response->setRedirect('checkout', ['step' => 'shipping']);
            }
        }
    }

    public function getModuleConfiguration(\comModule $module)
    {
        $fields = [];

        // A more detailed description to be shown in the module configuration. Note that the module description
        // ({@see self:getDescription}) is automatically shown as well.
//        $fields[] = new DescriptionField($this->commerce, [
//            'description' => $this->adapter->lexicon('commerce_timeslots.module_description'),
//        ]);

        return $fields;
    }

    private function insertInArray($array,$values,$offset)
    {
        return array_slice($array, 0, $offset, true) + $values + array_slice($array, $offset, NULL, true);
    }
}
