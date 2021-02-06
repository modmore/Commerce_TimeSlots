<?php

namespace modmore\Commerce_ClickCollect\Admin\Planning;

use modmore\Commerce\Admin\Util\Action;
use modmore\Commerce\Admin\Util\Column;
use modmore\Commerce\Admin\Widgets\GridWidget;

class Grid extends GridWidget {
    public $key = 'clickcollect-planning-grid';
    public $title = '';
    public $defaultSort = 'for_date';
    /**
     * @var \comShippingMethod
     */
    protected $method;

    public function generate(array $options = [])
    {
        $this->method = $this->adapter->getObject('comShippingMethod', [
            'id' => (int)$this->getOption('method'),
            'removed' => false,
            'class_key' => \ClickCollectShippingMethod::class,
        ]);
        if (!($this->method instanceof \comShippingMethod)) {
            throw new \RuntimeException('Method not provided.');
        }

        return parent::generate($options);
    }

    public function getItems(array $options = array())
    {
        $items = [];

        $c = $this->adapter->newQuery(\clcoDate::class);
        $c->select($this->adapter->getSelectColumns(\clcoDate::class, \clcoDate::class));
        $c->where([
            'for_date:>=' => date('Y-m-d')
        ]);

        if (31 > $this->adapter->getCount(\clcoDate::class, $c)) {
            \clcoDate::createFutureDates($this->adapter);
        }

        $sortby = array_key_exists('sortby', $options) && !empty($options['sortby']) ? $this->adapter->escape($options['sortby']) : $this->defaultSort;
        $sortdir = array_key_exists('sortdir', $options) && strtoupper($options['sortdir']) === 'DESC' ? 'DESC' : 'ASC';
        $c->sortby($sortby, $sortdir);

        $count = $this->adapter->getCount(\clcoDate::class, $c);
        $this->setTotalCount($count);

        $c->limit($options['limit'], $options['start']);
        /** @var \clcoDate[] $collection */
        $collection = $this->adapter->getCollection(\clcoDate::class, $c);

        foreach ($collection as $date) {
            $items[] = $this->prepareItem($date);
        }

        return $items;
    }

    public function getColumns(array $options = array())
    {
        return [
            new Column('for_date', $this->adapter->lexicon('commerce_clickcollect.date'), true, true),
//            new Column('day', $this->adapter->lexicon('commerce_clickcollect.day'), false),
            new Column('slots', $this->adapter->lexicon('commerce_clickcollect.slots'), false, true),
        ];
    }

    public function prepareItem(\clcoDate $date)
    {
        $item = $date->toArray();
        $editLink = $this->adapter->makeAdminUrl('clickcollect/planning/edit', [
            'id' => $date->get('id'),
            'method' => $this->method->get('id'),
        ]);
        $item['edit_link'] = $editLink;

        $item['for_date'] = strftime('%x', strtotime($date->get('for_date') . ' 12:00:00'));
        $item['for_date'] = '<a href="' . $editLink . '" class="commerce-ajax-modal" style="font-weight: 600;">' . $this->encode($item['for_date']) . '</a>';
//        $item['for_date'] .= ' <nobr style="color: #6a6a6a;">(#' . $item['id'] . ')</nobr>';
        $item['for_date'] .= ', ' . strftime('%A', strtotime($date->get('for_date') . ' 12:00:00'));

        if ($date->get('for_date') === date('Y-m-d')) {
            $item['for_date'] .= '<br><i class="icon icon-calendar"></i>' . $this->adapter->lexicon('commerce_clickcollect.today');
        }

        $addSlotLink = $this->adapter->makeAdminUrl('clickcollect/planning/slot/add', [
            'for_date' => $date->get('id'),
            'time_from' => $date->get('for_date'),
            'time_until' => $date->get('for_date'),
            'closes_after' => $date->get('for_date'),
        ]);
        $item['add_slot_link'] = $addSlotLink;

        $item['slots'] = [];

        $c = $this->adapter->newQuery(\clcoDateSlot::class);
        $c->where([
            'for_date' => $date->get('id'),
            'shipping_method' => $this->method->get('id'),
        ]);
        $c->sortby('time_from', 'ASC');
        $c->sortby('time_until', 'ASC');
        
        /** @var \clcoDateSlot[] $slots */
        $slots = $this->adapter->getCollection(\clcoDateSlot::class, $c);
        foreach ($slots as $slot) {
            $ta = $slot->toArray();
            $ta['available'] = $slot->isAvailable();
            $editSlotLink = $this->adapter->makeAdminUrl('clickcollect/planning/slot/edit', ['id' => $slot->get('id')]);
            $ta['edit'] = (new Action())
                ->setUrl($editSlotLink)
                ->setTitle($this->adapter->lexicon('commerce_clickcollect.edit_slot'))
                ->setIcon('icon-edit');
            $deleteSlotLink = $this->adapter->makeAdminUrl('clickcollect/planning/slot/delete', ['id' => $slot->get('id')]);
            $ta['delete'] = (new Action())
                ->setUrl($deleteSlotLink)
                ->setTitle($this->adapter->lexicon('commerce_clickcollect.delete_slot'))
                ->setIcon('icon-trash');
            $duplicateSlotLink = $this->adapter->makeAdminUrl('clickcollect/planning/slot/duplicate', ['id' => $slot->get('id')]);
            $ta['duplicate'] = (new Action())
                ->setUrl($duplicateSlotLink)
                ->setTitle($this->adapter->lexicon('commerce_clickcollect.duplicate_slot'))
                ->setIcon('icon-copy');

            $item['slots'][] = $ta;
        }
        $item['slots'] = $this->commerce->view()->render('clickcollect/admin/planning_slots.twig', $item);
        return $item;
    }


    public function getTopToolbar(array $options = array())
    {
        $toolbar = [];
        $toolbar[] = [
            'name' => 'limit',
            'title' => $this->adapter->lexicon('commerce.limit'),
            'type' => 'textfield',
            'value' => ((int)$options['limit'] === 10) ? '' : (int)$options['limit'],
            'position' => 'bottom',
        ];
        return $toolbar;
    }
}