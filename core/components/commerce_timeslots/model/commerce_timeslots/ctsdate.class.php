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
    public static function createFutureDates(AdapterInterface $adapter, bool $scheduled = false, $commerce = null)
    {
        $prefill = (int)$adapter->getOption('commerce_timeslots.prefill_future_days', null, 31);
        $date = new DateTime(date('Y-m-d') . ' 12:00:00');

        while ($prefill > 0) {
            /** @var \ctsDate $record */
            $record = $adapter->getObject('ctsDate', ['for_date' => $date->format('Y-m-d')]);
            if (!$record) {
                $record = $adapter->newObject('ctsDate');
                $record->set('for_date', $date->format('Y-m-d'));
                // Save for new object so we can use the id
                $record->save();
            }

            // Populate new dates with slots from any daily assigned schedules (if run via nightly Scheduler)
            if ($scheduled) {

                // Determine the day - numbered 0(Sun) -> 6(Sat)
                $dayNum = $date->format('w');

                // We need to offset the day number to match ours
                // (due to a problem saving "0" via serialization in the multi-select field)
                $dayNum++;

                // Get assigned schedules, if any.
                $schedules = $adapter->getCollection(\ctsSchedule::class, [
                    'repeat'   =>  true
                ]);

                if (!empty($schedules)) {

                    $assignedSchedules = [];
                    foreach ($schedules as $schedule) {
                        if (!$schedule instanceof \ctsSchedule) {
                            $adapter->log(modX::LOG_LEVEL_ERROR, 'Assigned schedule missing or invalid for date '
                                . $date->format('Y-m-d'));
                            continue;
                        }

                        // Grab schedule that is assigned to the specified day.
                        $methodDays = $schedule->getRepeatDays();
                        if (!empty($methodDays)) {
                            foreach ($methodDays as $methodId => $assignedDays) {
                                if (in_array($dayNum, $assignedDays)) {
                                    $assignedSchedules[$methodId] = $schedule;
                                }
                            }
                        }
                    }

                    foreach ($assignedSchedules as $methodId => $assignedSchedule) {

                        // Save new schedule id to date
                        $record->set('schedule', $assignedSchedule->get('id'));

                        // Copy slots from the schedule
                        $c = $adapter->newQuery(\ctsScheduleSlot::class);
                        $c->where([
                            'schedule' => $assignedSchedule->get('id'),
                        ]);
                        $c->sortby('time_from');
                        $c->sortby('time_until');
                        foreach ($adapter->getIterator(\ctsScheduleSlot::class, $c) as $baseSlot) {
                            $newSlot = $adapter->newObject(\ctsDateSlot::class);
                            $newSlot->fromArray([
                                'for_date' => $record->get('id'),
                                'base_slot' => $baseSlot->get('id'),
                                'shipping_method' => $methodId,
                                'schedule' => $assignedSchedule->get('id'),
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
            }

            $record->save();

            $date->modify('+1 day');

            $prefill--;
        }
    }
}
