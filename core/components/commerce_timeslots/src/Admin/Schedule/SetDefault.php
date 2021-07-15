<?php

namespace modmore\Commerce_TimeSlots\Admin\Schedule;

use ctsSchedule;
use modmore\Commerce\Admin\Page;

class SetDefault extends Page {
    public $key = 'timeslots/schedule/set_default';
    public $title = 'commerce_timeslots.set_default_schedule';
    public static $permissions = ['commerce'];

    public function setUp()
    {
        $objectId = (int)$this->getOption('id', 0);
        $schedule = $this->adapter->getObject('ctsSchedule', ['id' => $objectId]);

        if ($schedule) {
            // Find any schedules set to default and unset them.
            $collection = $this->adapter->getCollection(ctsSchedule::class, [
                'default' => 1
            ]);
            if (!empty($collection)) {
                foreach ($collection as $item) {
                    $item->set('default', 0);
                    $item->save();
                }
            }

            // Set new schedule as default.
            $schedule->set('default', 1);
            $schedule->save();

            $name = $schedule->get('name');
            return $this->returnSuccess($this->adapter->lexicon('commerce_timeslots.set_default_success', [
                'name' => $name
            ]));
        }

        return $this->returnError($this->adapter->lexicon('commerce.item_not_found'));
    }
}