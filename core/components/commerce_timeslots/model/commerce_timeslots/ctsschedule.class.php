<?php
/**
 * TimeSlots for Commerce.
 *
 * Copyright 2021 by Mark Hamstra <support@modmore.com>
 *
 * This file is meant to be used with Commerce by modmore. A valid Commerce license is required.
 *
 * @package commerce_timeslots
 * @license See core/components/commerce_timeslots/docs/license.txt
 */
class ctsSchedule extends comSimpleObject
{
    public function duplicate(array $options = [])
    {
        $new = parent::duplicate($options);
        if (!$new) {
            return false;
        }

        /** @var ctsScheduleSlot[] $slots */
        $slots = $this->getMany('Slots');
        foreach ($slots as $slot) {
            $data = $slot->toArray();
            unset($data['schedule']);

            $newSlot = $this->adapter->newObject(ctsScheduleSlot::class);
            $newSlot->fromArray($data);
            $new->addMany($newSlot, 'Slots');
        }
        $new->save();

        return $new;
    }

    public function getRepeatDays()
    {
        $repeatDays = $this->get('repeat_days');
        $repeatDays = array_values(array_filter($repeatDays));
        if (!is_array($repeatDays)) {
            $repeatDays = array();
        }
        return $repeatDays;
    }

    public function setRepeatDays($repeatDays, $merge = true)
    {
        if ($merge) {
            $repeatDays = array_merge($this->getRepeatDays(), $repeatDays);
        }
        $this->set('repeat_days', $repeatDays);
    }
}
