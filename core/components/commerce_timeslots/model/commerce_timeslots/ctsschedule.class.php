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

    /**
     * @param int $shippingMethodId
     * @return array
     */
    public function getRepeatDays(int $shippingMethodId = 0): array
    {
        $repeatDays = $this->get('repeat_days');

        if (!empty($repeatDays)) {

            // Return everything if no shipping method was specified
            if ($shippingMethodId === 0) {
                return $repeatDays;
            }

            // Filter by shipping method (the array key)
            foreach ($repeatDays as $k => $v) {
                if ((int)$k === $shippingMethodId) {

                    // Make sure there are no keys without values.
                    $methodDays = array_values(array_filter($v));

                    if (!is_array($methodDays)) {
                        $methodDays = [];
                    }

                    return $methodDays;
                }
            }
        }

        return [];
    }

    /**
     * @param int $shippingMethodId
     * @param array $repeatDays
     */
    public function setRepeatDays(int $shippingMethodId, array $repeatDays)
    {
        $currentDays = $this->getRepeatDays();
        $currentDays[$shippingMethodId] = $repeatDays;
        $this->set('repeat_days', $currentDays);
    }
}
