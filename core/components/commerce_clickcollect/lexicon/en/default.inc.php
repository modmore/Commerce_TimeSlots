<?php

$_lang['commerce_clickcollect'] = 'Click & Collect';
$_lang['commerce_clickcollect.description'] = 'Adds a timeslot based "Click & Collect" style shipping method to Commerce, as well as new sections in the dashbaord for the merchant to manage their incoming collect orders.';

$_lang['commerce_clickcollect.orders'] = 'Orders';
$_lang['commerce_clickcollect.planning'] = 'Planning';
$_lang['commerce_clickcollect.schedule'] = 'Schedule';
$_lang['commerce_clickcollect.schedule_description'] = 'Create Schedules to manage different configurations of time slots for pickup. For example, create a "Weekday" schedule that you can apply to Monday-Friday to cover standard opening hours, and a "Saturday" schedule for your opening hours in the weekend. You\'ll apply schedules to specific dates in the "Planning" section and will be able of changing individual slots on a specific date there as well.';
$_lang['commerce_clickcollect.slots'] = 'Slots';
$_lang['commerce_clickcollect.time_from'] = 'Start time';
$_lang['commerce_clickcollect.time_until'] = 'End time';
$_lang['commerce_clickcollect.lead_time'] = 'Lead time';
$_lang['commerce_clickcollect.minutes'] = 'min';
$_lang['commerce_clickcollect.lead_time.desc'] = 'The number of minutes before the start time that orders may be placed for start time. Some common periods in minutes: 1 hour = 60 minutes, 4 hours = 240 minutes, 8 hours = 480, 24 hours = 1440.';
$_lang['commerce_clickcollect.max_reservations'] = 'Max reservations';
$_lang['commerce_clickcollect.max_reservations.desc'] = 'The maximum number of orders that may be reserved in this slot. Set to -1 to allow an infinite number of reservations per slot. In high-traffic situations it may be possible for the number of orders in a slot to be exceeded as reservations aren\'t made until an order is completed in the checkout, and multiple customers may be ordering simultaneously.';
$_lang['commerce_clickcollect.add_slot'] = 'Add slot';
$_lang['commerce_clickcollect.edit_slot'] = 'Edit slot';
$_lang['commerce_clickcollect.delete_slot'] = 'Delete slot';
$_lang['commerce_clickcollect.delete_slot_named'] = 'Delete slot [[+time_from]] - [[+time_until]]';
$_lang['commerce_clickcollect.duplicate_slot'] = 'Duplicate slot';
$_lang['commerce_clickcollect.add_schedule'] = 'Add schedule';
$_lang['commerce_clickcollect.edit_schedule'] = 'Edit schedule';
$_lang['commerce_clickcollect.duplicate_schedule'] = 'Duplicate schedule';
$_lang['commerce_clickcollect.delete_schedule'] = 'Delete schedule and slots';
$_lang['commerce_clickcollect.delete_schedule_named'] = 'Delete schedule "[[+name]]" and associated slots';
