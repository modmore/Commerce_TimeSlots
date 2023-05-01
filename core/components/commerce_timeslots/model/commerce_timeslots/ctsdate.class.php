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
                            // Check for existing date slots that match this base slot
                            $dateSlot = $adapter->getObject(\ctsDateSlot::Class, [
                                'for_date' => $record->get('id'),
                                'base_slot' => $baseSlot->get('id'),
                                'shipping_method' => $methodId,
                            ]);
                            if ($dateSlot) {
                                // Don't create a duplicate
                                continue;
                            }

                            $timeFrom = self::convertToTimestamp($record->get('for_date'), $baseSlot->get('time_from'));
                            $timeUntil = self::convertToTimestamp($record->get('for_date'), $baseSlot->get('time_until'));
                            $closesAfter = $timeFrom - ($baseSlot->get('lead_time') * 60);

                            $newSlot = $adapter->newObject(\ctsDateSlot::class);
                            $newSlot->fromArray([
                                'for_date' => $record->get('id'),
                                'base_slot' => $baseSlot->get('id'),
                                'shipping_method' => $methodId,
                                'schedule' => $assignedSchedule->get('id'),
                                'max_reservations' => $baseSlot->get('max_reservations'),
                                'available_reservations' => $baseSlot->get('max_reservations'),
                                'price' => $baseSlot->get('price'),
                                'time_from' => $timeFrom,
                                'time_until' => $timeUntil,
                                'closes_after' => $closesAfter
                            ]);
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

    /**
     * Converts a date and a time to a single timestamp
     * Date format: Y-m-d (e.g. 2022-11-20)
     * Time format: H:i (e.g. 09:00)
     * @param string $date
     * @param string $time
     * @return string
     * @throws Exception
     */
    protected static function convertToTimestamp(string $date, string $time): string
    {
        // Calculate the timeFrom and timeUntil using DateTime. This makes sure it uses the servers' timezone
        // and that the appropriate offset is handled when converting to UTC.
        $timeParts = explode(':', $time);
        $dateTime = (new DateTime($date))->setTime($timeParts[0], $timeParts[1]);
        return $dateTime->format('U');
    }
}
