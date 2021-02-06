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
class ctsDateSlot extends comSimpleObject
{
    public function isAvailable(): bool
    {
        if (time() > $this->get('closes_after')) {
            return false;
        }

        if ($this->get('max_reservations') > -1 && $this->get('available_reservations') < 1) {
            return false;
        }

        return true;
    }

    public function setFieldValueMax_reservations($value)
    {
        $this->set('max_reservations', $value);
        $this->updateCount();
    }

    public function updateCount()
    {
        $this->set('placed_reservations', $this->adapter->getCount(ctsOrderSlot::class, [
            'slot' => $this->get('id'),
        ]));

        $this->set('available_reservations', $this->get('max_reservations') - $this->get('placed_reservations'));
        $this->save();
    }
}
