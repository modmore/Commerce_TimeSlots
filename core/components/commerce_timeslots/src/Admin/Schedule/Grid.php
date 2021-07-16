<?php

namespace modmore\Commerce_TimeSlots\Admin\Schedule;

use modmore\Commerce\Admin\Util\Action;
use modmore\Commerce\Admin\Util\Column;
use modmore\Commerce\Admin\Widgets\GridWidget;
use modmore\Commerce_TimeSlots\Modules\TimeSlots;

class Grid extends GridWidget {
    public $key = 'timeslots-schedule-grid';
    public $title = '';

    public function getItems(array $options = array())
    {
        $items = [];

        $c = $this->adapter->newQuery('ctsSchedule');
        $c->select($this->adapter->getSelectColumns('ctsSchedule', 'ctsSchedule'));

        $sortby = array_key_exists('sortby', $options) && !empty($options['sortby']) ? $this->adapter->escape($options['sortby']) : $this->defaultSort;
        $sortdir = array_key_exists('sortdir', $options) && strtoupper($options['sortdir']) === 'DESC' ? 'DESC' : 'ASC';
        $c->sortby($sortby, $sortdir);

        $count = $this->adapter->getCount('ctsSchedule', $c);
        $this->setTotalCount($count);

        $c->limit($options['limit'], $options['start']);
        /** @var \ctsSchedule[] $collection */
        $collection = $this->adapter->getCollection('ctsSchedule', $c);

        foreach ($collection as $schedule) {
            $items[] = $this->prepareItem($schedule);
        }

        return $items;
    }

    public function getColumns(array $options = array())
    {
        return [
            new Column('name', $this->adapter->lexicon('commerce.name'), true, true),
            new Column('slots', $this->adapter->lexicon('commerce_timeslots.slots'), false, true),
        ];
    }

    public function prepareItem(\ctsSchedule $schedule)
    {
        $item = $schedule->toArray();

        $item['slots'] = [];

        $c = $this->adapter->newQuery(\ctsScheduleSlot::class);
        $c->where([
            'schedule' => $schedule->get('id')
        ]);
        $c->sortby('time_from', 'ASC');
        $c->sortby('time_until', 'ASC');
        
        /** @var \ctsScheduleSlot[] $slots */
        $slots = $this->adapter->getCollection(\ctsScheduleSlot::class, $c);
        foreach ($slots as $slot) {
            $ta = $slot->toArray();
            $editSlotLink = $this->adapter->makeAdminUrl('timeslots/schedule/slot/edit', ['id' => $slot->get('id')]);
            $ta['edit'] = (new Action())
                ->setUrl($editSlotLink)
                ->setTitle($this->adapter->lexicon('commerce_timeslots.edit_slot'))
                ->setIcon('icon-edit');
            $deleteSlotLink = $this->adapter->makeAdminUrl('timeslots/schedule/slot/delete', ['id' => $slot->get('id')]);
            $ta['delete'] = (new Action())
                ->setUrl($deleteSlotLink)
                ->setTitle($this->adapter->lexicon('commerce_timeslots.delete_slot'))
                ->setIcon('icon-trash');
            $duplicateSlotLink = $this->adapter->makeAdminUrl('timeslots/schedule/slot/duplicate', ['id' => $slot->get('id')]);
            $ta['duplicate'] = (new Action())
                ->setUrl($duplicateSlotLink)
                ->setTitle($this->adapter->lexicon('commerce_timeslots.duplicate_slot'))
                ->setIcon('icon-copy');

            $item['slots'][] = $ta;
        }

        $addSlotLink = $this->adapter->makeAdminUrl('timeslots/schedule/slot/add', ['schedule' => $schedule->get('id')]);
        $item['add_slot_link'] = $addSlotLink;

        $item['slots'] = $this->commerce->view()->render('timeslots/admin/schedule_slots.twig', $item);

        $editLink = $this->adapter->makeAdminUrl('timeslots/schedule/edit', ['id' => $schedule->get('id')]);
        $item['name'] = '<a href="' . $editLink . '" class="commerce-ajax-modal">' . $this->encode($item['name']) . '</a>';
        $item['name'] .= ' <nobr style="color: #6a6a6a;">(#' . $item['id'] . ')</nobr>';

        // Display the days this schedule is assigned to automatically
        if ($schedule->get('repeat')) {
            $days = $schedule->getRepeatDays();
            foreach ($days as $day) {
                $name = '';
                switch ($day) {
                    case TimeSlots::MONDAY:
                        $name = $this->adapter->lexicon('monday');
                        break;
                    case TimeSlots::TUESDAY:
                        $name = $this->adapter->lexicon('tuesday');
                        break;
                    case TimeSlots::WEDNESDAY:
                        $name = $this->adapter->lexicon('wednesday');
                        break;
                    case TimeSlots::THURSDAY:
                        $name = $this->adapter->lexicon('thursday');
                        break;
                    case TimeSlots::FRIDAY:
                        $name = $this->adapter->lexicon('friday');
                        break;
                    case TimeSlots::SATURDAY:
                        $name = $this->adapter->lexicon('saturday');
                        break;
                    case TimeSlots::SUNDAY:
                        $name = $this->adapter->lexicon('sunday');
                        break;
                }
                // TODO: Make these into nice-looking styled tags
                $item['name'] .= '<br><span style="color: #6a6a6a; font-size: 9px;">*' . $name . '</span>';
            }

        }

        $item['actions'] = [];

        $item['actions'][] = (new Action())
            ->setUrl($addSlotLink)
            ->setTitle($this->adapter->lexicon('commerce_timeslots.add_slot'))
            ->setIcon('icon-plus');

        $setEditLink = $this->adapter->makeAdminUrl('timeslots/schedule/edit', ['id' => $schedule->get('id')]);
        $item['actions'][] = (new Action())
            ->setUrl($setEditLink)
            ->setTitle($this->adapter->lexicon('commerce_timeslots.edit_schedule'))
            ->setIcon('icon-edit');

        $duplicateLink = $this->adapter->makeAdminUrl('timeslots/schedule/duplicate', ['id' => $schedule->get('id')]);
        $item['actions'][] = (new Action())
            ->setUrl($duplicateLink)
            ->setTitle($this->adapter->lexicon('commerce_timeslots.duplicate_schedule'))
            ->setIcon('icon-copy');

        $deleteLink = $this->adapter->makeAdminUrl('timeslots/schedule/delete', ['id' => $schedule->get('id')]);
        $item['actions'][] = (new Action())
            ->setUrl($deleteLink)
            ->setTitle($this->adapter->lexicon('commerce_timeslots.delete_schedule'))
            ->setIcon('icon-trash');

        return $item;
    }


    public function getTopToolbar(array $options = array())
    {
        $toolbar = [];

        $toolbar[] = [
            'name' => 'add-product',
            'title' => $this->adapter->lexicon('commerce_timeslots.add_schedule'),
            'type' => 'button',
            'link' => $this->adapter->makeAdminUrl('timeslots/schedule/add'),
            'button_class' => 'commerce-ajax-modal',
            'icon_class' => 'plus',
            'modal_title' => $this->adapter->lexicon('commerce_timeslots.add_schedule'),
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