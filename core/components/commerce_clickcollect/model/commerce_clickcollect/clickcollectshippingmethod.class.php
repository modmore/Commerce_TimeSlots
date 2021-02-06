<?php

use modmore\Commerce\Admin\Widgets\Form\NumberField;

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
class ClickCollectShippingMethod extends comShippingMethod
{
    public function getModelFields()
    {
        $fields = parent::getModelFields();

        $fields[] = new NumberField($this->commerce, [
            'name' => 'properties[max_days_visible]',
            'label' => $this->adapter->lexicon('commerce_clickcollect.max_days_visible'),
            'description' => $this->adapter->lexicon('commerce_clickcollect.max_days_visible.desc'),
            'min' => 0,
            'max' => 31,
            'value' => $this->getProperty('max_days_visible', 7),
        ]);

        return $fields;
    }

    public function getShippingForm(comOrder $order, comOrderShipment $shipment): string
    {
        $options = $this->getAvailableSlots();
        $dateKeys = array_keys($options);
        $defaultDate = reset($dateKeys);

        return $this->commerce->view()->render('clickcollect/frontend/shipping_form.twig', [
            'shipment' => $shipment->toArray(),
            'order' => $order->toArray(),
            'options' => $options,
            'selected_date' => $shipment->getProperty('clickcollect_date', $defaultDate),
            'selected_slot' => $shipment->getProperty('clickcollect_slot', []),
        ]);
    }

//    public function getPriceForShipment(comOrderShipment $shipment)
//    {
//        // @todo implement fees per slot
//        return parent::getPriceForShipment($shipment);
//    }

    public function setShippingInformation(comOrder $order, comOrderShipment $shipment, array $data)
    {
        $options = $this->getAvailableSlots();

        $datesWithAvailability = array_filter($options, static function ($v) { return $v['available_slots'] > 0; });
        $selectedDateInfo = $firstAvailableDate = reset($datesWithAvailability) ?? [];
        $selectedDateSlots = is_array($firstAvailableDate) && is_array($firstAvailableDate['slots']) && count($firstAvailableDate['slots']) >= 1
            ? $firstAvailableDate['slots'] : [];
        $autoSelected = false;

        // Check for a submitted date
        if (array_key_exists('date', $data) && array_key_exists($data['date'], $options)) {
            $selectedDate = $data['date'];
            $selectedDateInfo = $options[$selectedDate];
            $selectedDateSlots = $options[$selectedDate]['slots'];
            $shipment->setProperty('clickcollect_date', $selectedDate);
            $shipment->unsetProperty('clickcollect_slot');
        }

        // Check for a submitted slot on said day
        if (array_key_exists('slot', $data)
            && array_key_exists((int)$data['slot'], $selectedDateSlots)
            && $selectedDateSlots[(int)$data['slot']]['available']
        ) {
            $selectedSlot = $data['slot'];
            $selectedSlotInfo = $selectedDateSlots[$selectedSlot];
        }
        // Check for any slots available on the selectedDate (which may be a preselected date with availability)
        elseif (is_array($selectedDateSlots) && count($selectedDateSlots) >= 1) {
            $slotsForDateWithAvailability = array_filter($selectedDateSlots, static function($v) { return $v['available']; } );
            if (count($slotsForDateWithAvailability) > 0) {
                $autoSelected = true;
                $selectedSlotInfo = reset($slotsForDateWithAvailability);
                $selectedSlot = $selectedSlotInfo['id'];
            }
        }

        // If a slot was found and seems valid, save it on the shipment
        if (isset($selectedSlot, $selectedSlotInfo) && $selectedSlot > 0 && is_array($selectedSlotInfo)) {
            $shipment->setProperty('clickcollect_slot', $selectedSlot);
            $shipment->setProperty('clickcollect_slot_info', $selectedSlotInfo);
            $shipment->setProperty('clickcollect_slot_autoselected', $autoSelected);

            if (is_array($selectedDateInfo)) {
                unset($selectedDateInfo['slots']);
                $shipment->setProperty('clickcollect_date_info', $selectedDateInfo);
            }
            $shipment->set('tracking_reference', $selectedSlotInfo['date_for_date']
                . ' ' . date('H:i', $selectedSlotInfo['time_from'])
                . '-' . date('H:i', $selectedSlotInfo['time_until'])
            );
        }

        return parent::setShippingInformation($order, $shipment, $data);
    }

    private function getAvailableSlots(): array
    {
        $options = [];

        $today = new DateTimeImmutable();
        $maxDays = (int)$this->getProperty('max_days_visible', 7);
        $lastDay = $today->add(new DateInterval("P{$maxDays}D"));

        $current = new DateTime();
        $current->setTime(12, 0);
        $days = [];
        while ($current->format('Y-m-d') !== $lastDay->format('Y-m-d')) {
            $days[] = $current->format('Y-m-d');
            $ts = $current->getTimestamp();
            $options[$current->format('Y-m-d')] = [
                'locale_day' => strftime('%A', $ts), // @todo stftime doesn't seem to be returning the locale-based day/dates.. dev env issue?
                'locale_day_short' => strftime('%a', $ts),
                'locale_date_preferred' => strftime('%x', $ts),
                'locale_date_tz' => strftime('%Z', $ts),
                'available_slots' => 0,
                'slots' => [],
            ];
            $current->add(new DateInterval('P1D'));
        }

        $c = $this->adapter->newQuery(clcoDateSlot::class);
        $c->innerJoin(clcoDate::class, 'Date');
        $c->select($this->adapter->getSelectColumns(clcoDateSlot::class, 'clcoDateSlot'));
        $c->select($this->adapter->getSelectColumns(clcoDate::class, 'Date', 'date_'));
        $c->where([
            'Date.for_date:IN' => $days,
        ]);
        $c->sortby('time_from');
        $c->sortby('time_until');
        $c->sortby('Date.for_date');

        /** @var clcoDateSlot $slot */
        foreach ($this->adapter->getIterator(clcoDateSlot::class, $c) as $slot) {
            $ta = $slot->toArray();
            $ta['available'] = $slot->isAvailable();
            if ($ta['available']) {
                $options[$ta['date_for_date']]['available_slots']++;
            }

            try {
                $closesAfter = new DateTimeImmutable('@' . $ta['closes_after']);
                $ta['closes_after_sameday'] = $closesAfter->format('Y-m-d') === $ta['date_for_date'];
            } catch (Exception $e) {
            }

            $options[$ta['date_for_date']]['slots'][$ta['id']] = $ta;
        }

        return $options;
    }
}
