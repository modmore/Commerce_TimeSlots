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
class ReserveTimeSlotStatusChangeAction extends comStatusChangeAction
{
    public function process(comOrder $order, comStatus $oldStatus, comStatus $newStatus, comStatusChange $statusChange)
    {
        foreach ($order->getShipments() as $shipment) {
            $this->reserveTimeSlot($order, $shipment);
        }

        return true;
    }

    private function reserveTimeSlot(comOrder $order, comOrderShipment $shipment): void
    {
        $method = $shipment->getShippingMethod();
        if (!($method instanceof TimeSlotsShippingMethod)) {
            return;
        }

        $slotId = (int)$shipment->getProperty('timeslots_slot');
        /** @var ctsDateSlot $slot */
        $slot = $this->adapter->getObject(ctsDateSlot::class, [
            'shipping_method' => $method->get('id'),
            'id' => $slotId,
        ]);
        if (!($slot instanceof ctsDateSlot)) {
            $this->adapter->log(1, '[ReserveTimeSlotStatusChangeAction] Could not find slot ' . $slotId . ' for method ' . $method->get('id'));
            return;
        }

        /** @var ctsOrderSlot $reservation */
        $reservation = $this->adapter->newObject(ctsOrderSlot::class);
        $reservation->fromArray([
            'for_date' => $slot->get('for_date'),
            'slot' => $slot->get('id'),
            'order' => $order->get('id'),
            'shipment' => $shipment->get('id')
        ]);
        if (!$reservation->save()) {
            $this->adapter->log(1, '[ReserveTimeSlotStatusChangeAction] Could not save ctsOrderSlot: ' . print_r($reservation->toArray(), true));
            return;
        }

        $slot->updateCount();
    }
}
