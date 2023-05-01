<?php

use modmore\Commerce\Admin\Widgets\Form\NumberField;
use modmore\Commerce\Admin\Widgets\Form\SelectField;
use modmore\Commerce\Admin\Widgets\Form\Tab;

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
class TimeSlotsShippingMethod extends comShippingMethod
{
    /**
     * @var array
     */
    private $_available_options;

    public function getModelFields()
    {
        $fields = parent::getModelFields();

        $fields[] = new Tab($this->commerce, [
            'label' => $this->adapter->lexicon('commerce_timeslots'),
        ]);

        $fields[] = new NumberField($this->commerce, [
            'name' => 'properties[max_days_visible]',
            'label' => $this->adapter->lexicon('commerce_timeslots.max_days_visible'),
            'description' => $this->adapter->lexicon('commerce_timeslots.max_days_visible.desc'),
            'min' => 0,
            'max' => 31,
            'value' => $this->getProperty('max_days_visible', 7),
        ]);

        $descendants = $this->adapter->getDescendants('comShippingMethod');
        $options = [
            [
                'value' => '',
                'label' => '(none)'
            ]
        ];
        foreach ($descendants as $descendant) {
            if ($descendant !== 'TimeSlotsShippingMethod') {
                $options[] = [
                    'value' => $descendant,
                    'label' => $this->adapter->lexicon('commerce.' . $descendant),
                ];
            }
        }

        $fields[] = new SelectField($this->commerce, [
            'name' => 'properties[composite_method]',
            'label' => $this->adapter->lexicon('commerce_timeslots.composite_method'),
            'description' => $this->adapter->lexicon('commerce_timeslots.composite_method.desc'),
            'value' => $this->getProperty('composite_method'),
            'options' => $options,
        ]);

        if ($comp = $this->getCompositeMethod()) {
            $fields = array_merge($fields, $comp->getModelFields());
        }

        return $fields;
    }

    public function getShippingForm(comOrder $order, comOrderShipment $shipment): string
    {
        $options = $this->getAvailableSlots();
        $dateKeys = array_keys($options);
        $defaultDate = reset($dateKeys);

        return $this->commerce->view()->render('timeslots/frontend/shipping_form.twig', [
            'shipment' => $shipment->toArray(),
            'order' => $order->toArray(),
            'method' => $this->toArray(),
            'options' => $options,
            'selected_date' => $shipment->getProperty('timeslots_date', $defaultDate),
            'selected_slot' => $shipment->getProperty('timeslots_slot', 0),
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
            $shipment->setProperty('timeslots_date', $selectedDate);
            $shipment->unsetProperty('timeslots_slot');
        }
        // Set the date via submitted slot id if no date was provided by the template
        else if (array_key_exists('slot', $data)) {
            foreach ($datesWithAvailability as $k => $date) {
                if (isset($date['slots'][$data['slot']])) {
                    $selectedDate = $k; // Date string is the array key
                    $selectedDateInfo = $options[$selectedDate];
                    $selectedDateSlots = $options[$selectedDate]['slots'];
                    $shipment->setProperty('timeslots_date', $selectedDate);
                    $shipment->unsetProperty('timeslots_slot');
                    break;
                }
            }
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
            $shipment->setProperty('timeslots_slot', $selectedSlot);
            $shipment->setProperty('timeslots_slot_info', $selectedSlotInfo);
            $shipment->setProperty('timeslots_slot_autoselected', $autoSelected);

            if (is_array($selectedDateInfo)) {
                unset($selectedDateInfo['slots']);
                $shipment->setProperty('timeslots_date_info', $selectedDateInfo);
            }
            $shipment->set('tracking_reference', $selectedSlotInfo['date_for_date']
                . ' ' . date('H:i', $selectedSlotInfo['time_from'])
                . '-' . date('H:i', $selectedSlotInfo['time_until'])
            );
        }

        return parent::setShippingInformation($order, $shipment, $data);
    }

    public function getAvailableSlots(): array
    {
        if (!empty($this->_available_options)) {
            return $this->_available_options;
        }
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

        $c = $this->adapter->newQuery(ctsDateSlot::class);
        $c->innerJoin(ctsDate::class, 'Date');
        $c->select($this->adapter->getSelectColumns(ctsDateSlot::class, 'ctsDateSlot'));
        $c->select($this->adapter->getSelectColumns(ctsDate::class, 'Date', 'date_'));
        $c->where([
            'shipping_method' => $this->get('id'),
            '`Date`.`for_date`:IN' => array_values($days), // escaping column names is needed to avoid xPDO from mapping strings (Date.for_date) into integers (for DateSlot.for_date)
        ]);
        $c->sortby('time_from');
        $c->sortby('time_until');
        $c->sortby('Date.for_date');

        /** @var ctsDateSlot $slot */
        foreach ($this->adapter->getIterator(ctsDateSlot::class, $c) as $slot) {
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

        $this->_available_options = $options;

        return $options;
    }

    public function getPriceForShipment(comOrderShipment $shipment): int
    {
        if ($comp = $this->getCompositeMethod()) {
            $price = $comp->getPriceForShipment($shipment);
        }
        else {
            $price = parent::getPriceForShipment($shipment);
        }

        $slotInfo = $shipment->getProperty('timeslots_slot_info');
        if (is_array($slotInfo) && array_key_exists('price', $slotInfo) && $slotInfo['shipping_method'] === $this->get('id')) {
            $price += (int)$slotInfo['price'];
        }

        return $price;
    }

    public function isAvailableForShipment(comOrderShipment $shipment): bool
    {
        if (!parent::isAvailableForShipment($shipment)) {
            return false;
        }
        if ($comp = $this->getCompositeMethod()) {
            return $comp->isAvailableForShipment($shipment);
        }
        return true;
    }

    private function getCompositeMethod(): ?comShippingMethod
    {
        $class = $this->getProperty('composite_method');
        if (empty($class)) {
            return null;
        }

        $comp = $this->adapter->newObject($class);
        if (!($comp instanceof comShippingMethod)) {
            return null;
        }
        $comp->fromArray($this->toArray());

        return $comp;
    }
}
