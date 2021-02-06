<?php

use modmore\Commerce\Adapter\AdapterInterface;

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
class ctsDate extends comSimpleObject
{

    public static function createFutureDates(AdapterInterface $adapter)
    {
        $prefill = (int)$adapter->getOption('commerce_timeslots.prefill_future_days', null, 31);
        $date = new DateTime(date('Y-m-d') . ' 12:00:00');
        while ($prefill > 0) {
            $record = $adapter->getObject('ctsDate', ['for_date' => $date->format('Y-m-d')]);
            if (!$record) {
                $record = $adapter->newObject('ctsDate');
                $record->set('for_date', $date->format('Y-m-d'));

                // @todo automatically fill with a schedule too?
                $record->save();
            }

            $date->modify('+1 day');

            $prefill--;
        }
    }
}
