<?php

namespace modmore\Commerce_ClickCollect\Admin\Schedule;

use modmore\Commerce\Admin\Util\Action;
use modmore\Commerce\Admin\Util\Column;
use modmore\Commerce\Admin\Widgets\GridWidget;

class Grid extends GridWidget {
    public $key = 'clickcollect-schedule-grid';
    public $title = '';

    public function getItems(array $options = array())
    {
        $items = [];

        $c = $this->adapter->newQuery('clcoSchedule');
        $c->select($this->adapter->getSelectColumns('clcoSchedule', 'clcoSchedule'));

        $sortby = array_key_exists('sortby', $options) && !empty($options['sortby']) ? $this->adapter->escape($options['sortby']) : $this->defaultSort;
        $sortdir = array_key_exists('sortdir', $options) && strtoupper($options['sortdir']) === 'DESC' ? 'DESC' : 'ASC';
        $c->sortby($sortby, $sortdir);

        $count = $this->adapter->getCount('clcoSchedule', $c);
        $this->setTotalCount($count);

        $c->limit($options['limit'], $options['start']);
        /** @var \clcoSchedule[] $collection */
        $collection = $this->adapter->getCollection('clcoSchedule', $c);

        foreach ($collection as $schedule) {
            $items[] = $this->prepareItem($schedule);
        }

        return $items;
    }

    public function getColumns(array $options = array())
    {
        return [
            new Column('name', $this->adapter->lexicon('commerce.name'), true, true),
            new Column('slots', $this->adapter->lexicon('commerce_clickcollect.slots'), false, true),
        ];
    }

    public function prepareItem(\clcoSchedule $schedule)
    {
        $item = $schedule->toArray();

        $item['slots'] = [];

        $c = $this->adapter->newQuery(\clcoScheduleSlot::class);
        $c->where([
            'schedule' => $schedule->get('id')
        ]);
        $c->sortby('time_from', 'ASC');
        $c->sortby('time_until', 'ASC');
        
        /** @var \clcoScheduleSlot[] $slots */
        $slots = $this->adapter->getCollection(\clcoScheduleSlot::class, $c);
        foreach ($slots as $slot) {
            $ta = $slot->toArray();
            $editSlotLink = $this->adapter->makeAdminUrl('clickcollect/schedule/slot/edit', ['id' => $slot->get('id')]);
            $ta['edit'] = (new Action())
                ->setUrl($editSlotLink)
                ->setTitle($this->adapter->lexicon('commerce_clickcollect.edit_slot'))
                ->setIcon('icon-edit');
            $deleteSlotLink = $this->adapter->makeAdminUrl('clickcollect/schedule/slot/delete', ['id' => $slot->get('id')]);
            $ta['delete'] = (new Action())
                ->setUrl($deleteSlotLink)
                ->setTitle($this->adapter->lexicon('commerce_clickcollect.delete_slot'))
                ->setIcon('icon-trash');
            $duplicateSlotLink = $this->adapter->makeAdminUrl('clickcollect/schedule/slot/duplicate', ['id' => $slot->get('id')]);
            $ta['duplicate'] = (new Action())
                ->setUrl($duplicateSlotLink)
                ->setTitle($this->adapter->lexicon('commerce_clickcollect.duplicate_slot'))
                ->setIcon('icon-copy');

            $item['slots'][] = $ta;
        }

        $addSlotLink = $this->adapter->makeAdminUrl('clickcollect/schedule/slot/add', ['schedule' => $schedule->get('id')]);
        $item['add_slot_link'] = $addSlotLink;

        $item['slots'] = $this->commerce->view()->render('clickcollect/admin/schedule_slots.twig', $item);

        $editLink = $this->adapter->makeAdminUrl('clickcollect/schedule/edit', ['id' => $schedule->get('id')]);
        $item['name'] = '<a href="' . $editLink . '" class="commerce-ajax-modal">' . $this->encode($item['name']) . '</a>';
        $item['name'] .= ' <nobr style="color: #6a6a6a;">(#' . $item['id'] . ')</nobr>';

        $item['actions'] = [];

        $item['actions'][] = (new Action())
            ->setUrl($addSlotLink)
            ->setTitle($this->adapter->lexicon('commerce_clickcollect.add_slot'))
            ->setIcon('icon-plus');

        $duplicateLink = $this->adapter->makeAdminUrl('clickcollect/schedule/duplicate', ['id' => $schedule->get('id')]);
        $item['actions'][] = (new Action())
            ->setUrl($duplicateLink)
            ->setTitle($this->adapter->lexicon('commerce_clickcollect.duplicate_schedule'))
            ->setIcon('icon-copy');

        $deleteLink = $this->adapter->makeAdminUrl('clickcollect/schedule/delete', ['id' => $schedule->get('id')]);
        $item['actions'][] = (new Action())
            ->setUrl($deleteLink)
            ->setTitle($this->adapter->lexicon('commerce_clickcollect.delete_schedule'))
            ->setIcon('icon-trash');

        return $item;
    }


    public function getTopToolbar(array $options = array())
    {
        $toolbar = [];

        $toolbar[] = [
            'name' => 'add-product',
            'title' => $this->adapter->lexicon('commerce_clickcollect.add_schedule'),
            'type' => 'button',
            'link' => $this->adapter->makeAdminUrl('clickcollect/schedule/add'),
            'button_class' => 'commerce-ajax-modal',
            'icon_class' => 'plus',
            'modal_title' => $this->adapter->lexicon('commerce_clickcollect.add_schedule'),
            'position' => 'top',
        ];

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