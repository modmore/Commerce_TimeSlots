<?php

$_lang['commerce_clickcollect'] = 'Click & Collect';
$_lang['commerce_clickcollect.description'] = 'Adds a timeslot based "Click & Collect" style shipping method to Commerce, as well as new sections in the dashbaord for the merchant to manage their incoming collect orders.';

$_lang['commerce_clickcollect.orders'] = 'Orders';
$_lang['commerce_clickcollect.planning'] = 'Planning';
$_lang['commerce_clickcollect.planning_description'] = 'Use the Planning to set the available timeslots on specific dates, up to 31 days into the future. To quickly prepare your planning, assign a pre-made Schedule that fits your opening hours. Or, add and editing individual slots to fine-tune a specific day. If no Schedule or slots are added to a specific date, it will not be possible for a customer to choose that date.';
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
$_lang['commerce_clickcollect.delete_planning_slot_named'] = 'Delete slot [[+time_from_formatted]] - [[+time_until_formatted]]';
$_lang['commerce_clickcollect.duplicate_slot'] = 'Duplicate slot';
$_lang['commerce_clickcollect.add_schedule'] = 'Add schedule';
$_lang['commerce_clickcollect.edit_schedule'] = 'Edit schedule';
$_lang['commerce_clickcollect.duplicate_schedule'] = 'Duplicate schedule';
$_lang['commerce_clickcollect.delete_schedule'] = 'Delete schedule and slots';
$_lang['commerce_clickcollect.delete_schedule_named'] = 'Delete schedule "[[+name]]" and associated slots';

$_lang['commerce_clickcollect.edit_planning'] = 'Edit planning';
$_lang['commerce_clickcollect.date'] = 'Date';
$_lang['commerce_clickcollect.day'] = 'Day';
$_lang['commerce_clickcollect.assigning_schedule'] = 'You\'re <b>assigning a schedule</b> for this day. This means that the date will be filled with slots from the schedule. You can edit individual slots after this, or change the schedule which will remove the previous slots added from a schedule';
$_lang['commerce_clickcollect.changing_schedule'] = 'You\'re <b>replacing the schedule</b> for this day. This means that all slots that were added to this date from a schedule <b>will be removed</b>. Slots that already have orders assigned, or that were manually added, will stay in place. New slots will be added based on the schedule you select.';
$_lang['commerce_clickcollect.closes_after'] = 'Closes at';
$_lang['commerce_clickcollect.unavailable'] = 'Unavailable';
$_lang['commerce_clickcollect.today'] = 'Today';
$_lang['commerce_clickcollect.unlimited_reservations_available'] = 'unlimited reservations';
$_lang['commerce_clickcollect.reservations_available'] = 'reservations available';
$_lang['commerce_clickcollect.no_date_slots'] = 'No slots defined, date not available for customers.';
$_lang['commerce_clickcollect.different_schedule'] = 'This slot is not part of the schedule selected for the current date. It may have been manually added or was left over after replacing the schedule.';
