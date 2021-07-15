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

    /**
     * @throws Exception
     */
    public static function createFutureDates(AdapterInterface $adapter, bool $scheduled = false)
    {
        $prefill = (int)$adapter->getOption('commerce_timeslots.prefill_future_days', null, 31);
        $date = new DateTime(date('Y-m-d') . ' 12:00:00');
        while ($prefill > 0) {
            $record = $adapter->getObject('ctsDate', ['for_date' => $date->format('Y-m-d')]);
            if (!$record) {
                $record = $adapter->newObject('ctsDate');
                $record->set('for_date', $date->format('Y-m-d'));
            }
            $record->save();

            // Populate dates with slots from the default schedule (if run via nightly Scheduler)
            if ($scheduled) {

                // Get the default schedule
                $schedule = $adapter->getObject(\ctsSchedule::class, [
                    'default'   =>  1
                ]);
                if (!$schedule instanceof \ctsSchedule) {
                    throw new \RuntimeException('Default schedule missing or invalid.');
                }

                // Only add slots from the default schedule if there's not already a schedule applied
                if ($record->get('schedule') < 1) {

                    // Copy slots from the schedule
                    $c = $adapter->newQuery(\ctsScheduleSlot::class);
                    $c->where([
                        'schedule' => $schedule->get('id'),
                    ]);
                    $c->sortby('time_from');
                    $c->sortby('time_until');
                    foreach ($adapter->getIterator(\ctsScheduleSlot::class, $c) as $baseSlot) {
                        $newSlot = $adapter->newObject(\ctsDateSlot::class);
                        $newSlot->fromArray([
                            'for_date' => $record->get('id'),
                            'base_slot' => $baseSlot->get('id'),
                            //'shipping_method' => $baseSlot->get('method'),
                            'schedule' => $schedule->get('id'),
                            'max_reservations' => $baseSlot->get('max_reservations'),
                            'available_reservations' => $baseSlot->get('max_reservations'),
                            'price' => $baseSlot->get('price'),
                        ]);

                        // Calculate the timeFrom and timeUntil using DateTime. This makes sure it uses the servers' timezone
                        // and that the appropriate offset is handled when converting to UTC.
                        $timeFrom = explode(':', $baseSlot->get('time_from'));
                        $timeFromDate = (new DateTime($record->get('for_date')))->setTime($timeFrom[0], $timeFrom[1]);
                        $newSlot->set('time_from', $timeFromDate->format('U'));

                        $timeUntil = explode(':', $baseSlot->get('time_until'));
                        $timeUntilDate = (new DateTime($record->get('for_date')))->setTime($timeUntil[0], $timeUntil[1]);
                        $newSlot->set('time_until', $timeUntilDate->format('U'));

                        // For the "closes after", simply remove the lead time (defined in minutes) from the calculated unix timestamp
                        // @todo This may cause oddities during DST switches, but for now I'm okay with that.
                        $newSlot->set('closes_after', $newSlot->get('time_from') - ($baseSlot->get('lead_time') * 60));
                        $newSlot->save();
                    }
                }
            }



            $date->modify('+1 day');

            $prefill--;
        }
    }
}
