<?php

use modmore\Commerce\Adapter\AdapterInterface;

/**
 * ClickCollect for Commerce.
 *
 * Copyright 2021 by Mark Hamstra <support@modmore.com>
 *
 * This file is meant to be used with Commerce by modmore. A valid Commerce license is required.
 *
 * @package commerce_clickcollect
 * @license See core/components/commerce_clickcollect/docs/license.txt
 */
class clcoDate extends comSimpleObject
{

    public static function createFutureDates(AdapterInterface $adapter)
    {
        $prefill = (int)$adapter->getOption('commerce_clickcollect.prefill_future_days', null, 31);
        $date = new DateTime(date('Y-m-d') . ' 12:00:00');
        while ($prefill > 0) {
            $record = $adapter->getObject('clcoDate', ['for_date' => $date->format('Y-m-d')]);
            if (!$record) {
                $record = $adapter->newObject('clcoDate');
                $record->set('for_date', $date->format('Y-m-d'));

                // @todo automatically fill with a schedule too?
                $record->save();
            }

            $date->modify('+1 day');

            $prefill--;
        }
    }

    public function setFieldValueSchedule($scheduleId)
    {
        $scheduleId = (int)$scheduleId;
        $schedule = $this->adapter->getObject(clcoSchedule::class, ['id' => $scheduleId]);
        if (!$schedule) {
            return;
        }
        $this->set('schedule', $scheduleId);
        $this->save();

        // Clean up all scheduled slots currently assigned
        $c = $this->adapter->newQuery(clcoDateSlot::class);
        $c->where([
            'for_date' => $this->get('id'),
            'base_slot:>' => 0,
        ]);
        foreach ($this->adapter->getIterator(clcoDateSlot::class, $c) as $oldSlot) {
            if ($oldSlot->get('max_reservations') === $oldSlot->get('available_reservations')) {
                $oldSlot->remove();
            }
            // @todo rather than base it of a cached count, try a count() on actual orders for improved accuracy
        }

        // Copy slots from the schedule
        $c = $this->adapter->newQuery(clcoScheduleSlot::class);
        $c->where([
            'schedule' => $scheduleId,
        ]);
        $c->sortby('time_from');
        $c->sortby('time_until');
        foreach ($this->adapter->getIterator(clcoScheduleSlot::class, $c) as $baseSlot) {
            $newSlot = $this->adapter->newObject(clcoDateSlot::class);
            $newSlot->fromArray([
                'for_date' => $this->get('id'),
                'base_slot' => $baseSlot->get('id'),
                'schedule' => $scheduleId,
                'max_reservations' => $baseSlot->get('max_reservations'),
                'available_reservations' => $baseSlot->get('max_reservations'),
            ]);

            // Calculate the timeFrom and timeUntil using DateTime. This makes sure it uses the servers' timezone
            // and that the appropriate offset is handled when converting to UTC.
            $timeFrom = explode(':', $baseSlot->get('time_from'));
            $timeFromDate = (new DateTime($this->get('for_date')))->setTime($timeFrom[0], $timeFrom[1]);
            $newSlot->set('time_from', $timeFromDate->format('U'));

            $timeUntil = explode(':', $baseSlot->get('time_until'));
            $timeUntilDate = (new DateTime($this->get('for_date')))->setTime($timeUntil[0], $timeUntil[1]);
            $newSlot->set('time_until', $timeUntilDate->format('U'));

            // For the "closes after", simply remove the lead time (defined in minutes) from the calculated unix timestamp
            // @todo This may cause oddities during DST switches, but for now I'm okay with that.
            $newSlot->set('closes_after', $newSlot->get('time_from') - ($baseSlot->get('lead_time') * 60));
            $newSlot->save();
        }
    }
}
