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
        // @todo Replace this with proper count() of used reservations for accurate accounting
        $this->set('available_reservations', $value - ($this->get('max_reservations') - $this->get('available_reservations')));
        $this->set('max_reservations', $value);
    }

}
