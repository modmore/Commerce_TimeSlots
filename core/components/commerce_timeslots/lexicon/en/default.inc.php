<?php
$_lang['commerce_timeslots'] = 'Time Slots';
$_lang['commerce_timeslots.description'] = 'Adds a timeslot based shipping method to Commerce, as well as new sections in the dashboard for the merchant to manage their planning and incoming orders. Useful for "Click & Collect" as well as timed-delivery use cases.';

$_lang['commerce_timeslots.orders'] = 'Orders';
$_lang['commerce_timeslots.planning'] = 'Planning';
$_lang['commerce_timeslots.planning_description'] = 'Use the planning to set the available timeslots on specific dates, up to 31 days into the future. To quickly set your planning, set slots from a pre-made Schedule. If needed you can add and change individual slots to fine-tune availability on specific dates. Important: if there are no slots set on a certain date, the customer cannot select it.';
$_lang['commerce_timeslots.schedule'] = 'Schedule';
$_lang['commerce_timeslots.schedule_description'] = 'Create Schedules to manage different configurations of time slots for pickup. For example, create a "Weekday" schedule that you can apply to Monday-Friday to cover standard opening hours, and a "Saturday" schedule for your opening hours in the weekend. You\'ll apply schedules to specific dates in the "Planning" section and will be able of changing individual slots on a specific date there as well.';
$_lang['commerce_timeslots.slot'] = 'Time slot';
$_lang['commerce_timeslots.slots'] = 'Slots';
$_lang['commerce_timeslots.time_from'] = 'Start time';
$_lang['commerce_timeslots.time_until'] = 'End time';
$_lang['commerce_timeslots.lead_time'] = 'Lead time';
$_lang['commerce_timeslots.minutes'] = 'min';
$_lang['commerce_timeslots.price'] = 'Price';
$_lang['commerce_timeslots.price.desc'] = 'The price for this specific slot. This will be added to (or if you provide a negative amount, removed from) the base price configured on the shipping method. ';
$_lang['commerce_timeslots.lead_time.desc'] = 'The number of minutes before the start time that orders may be placed for start time. Some common periods in minutes: 1 hour = 60 minutes, 4 hours = 240 minutes, 8 hours = 480, 24 hours = 1440.';
$_lang['commerce_timeslots.max_reservations'] = 'Max reservations';
$_lang['commerce_timeslots.max_reservations.desc'] = 'The maximum number of orders that may be reserved in this slot. Set to -1 to allow an infinite number of reservations per slot. In high-traffic situations it may be possible for the number of orders in a slot to be exceeded as reservations aren\'t made until an order is completed in the checkout, and multiple customers may be ordering simultaneously.';
$_lang['commerce_timeslots.add_slot'] = 'Add slot';
$_lang['commerce_timeslots.edit_slot'] = 'Edit slot';
$_lang['commerce_timeslots.delete_slot'] = 'Delete slot';
$_lang['commerce_timeslots.delete_slot_named'] = 'Delete slot [[+time_from]] - [[+time_until]]';
$_lang['commerce_timeslots.delete_planning_slot_named'] = 'Delete slot [[+time_from_formatted]] - [[+time_until_formatted]]';
$_lang['commerce_timeslots.duplicate_slot'] = 'Duplicate slot';
$_lang['commerce_timeslots.add_schedule'] = 'Add schedule';
$_lang['commerce_timeslots.edit_schedule'] = 'Edit schedule';
$_lang['commerce_timeslots.duplicate_schedule'] = 'Duplicate schedule';
$_lang['commerce_timeslots.repeat'] = 'Repeat';
$_lang['commerce_timeslots.start_repeating'] = 'Start Repeating';
$_lang['commerce_timeslots.stop_repeating'] = 'Stop Repeating';
$_lang['commerce_timeslots.schedule_days'] = 'Schedule Days';
$_lang['commerce_timeslots.schedule_days_desc'] = 'Select days to repeat this schedule on. If a day is already assigned to another schedule, you must unassign it before the days are displayed here.';
$_lang['commerce_timeslots.delete_schedule'] = 'Delete schedule and slots';
$_lang['commerce_timeslots.delete_schedule_named'] = 'Delete schedule "[[+name]]" and associated slots';

$_lang['commerce_timeslots.edit_planning'] = 'Edit planning';
$_lang['commerce_timeslots.date'] = 'Date';
$_lang['commerce_timeslots.day'] = 'Day';
$_lang['commerce_timeslots.changing_schedule'] = 'You\'re <b>replacing the schedule</b> for [[+date]]. Any slots that were added by another schedule will be removed. If specific slots already have received orders assigned, or if those slots were manually added, they will be kept.';
$_lang['commerce_timeslots.closes_after'] = 'Closes at';
$_lang['commerce_timeslots.unavailable'] = 'Unavailable';
$_lang['commerce_timeslots.today'] = 'Today';
$_lang['commerce_timeslots.unlimited_reservations_available'] = 'unlimited reservations';
$_lang['commerce_timeslots.reservations_available'] = 'reservations available';
$_lang['commerce_timeslots.reservations_placed'] = 'slots booked';
$_lang['commerce_timeslots.no_date_slots'] = 'No slots defined, date not available for customers.';
$_lang['commerce_timeslots.different_schedule'] = 'This slot is not part of the schedule selected for the current date. It may have been manually added or was left over after replacing the schedule.';

// Shipping method / frontend
$_lang['commerce_timeslots.max_days_visible'] = 'Maximum days visible';
$_lang['commerce_timeslots.max_days_visible.desc'] = 'How many days into the future you want customers to be able of selecting their desired date slot. The minimum time between when the customer places their order and the available timeslots is managed through the Time Slots planning ("closes after") and schedules ("lead time").';
$_lang['commerce_timeslots.slot_unavailable'] = 'No longer available';
$_lang['commerce_timeslots.no_options_available'] = 'Unfortunately there are no timeslots available at the moment. Please check again later, choose a different shipping method, or contact us for help.';
$_lang['commerce_timeslots.num_available_slots'] = '[[+num]] available slots';
$_lang['commerce_timeslots.selected_slot_no_longer_available'] = 'The time slot you selected for delivery or pickup is unfortunately no longer available. Please choose a new time slot to complete your order.';
$_lang['commerce_timeslots.select_a_slot'] = 'Please select a time slot before completing your order.';

$_lang['commerce.add_TimeSlotsShippingMethod'] = 'Add Time Slots Shipping Method';
$_lang['commerce.TimeSlotsShippingMethod'] = 'Time Slots Shipping Method';
$_lang['commerce.add_ReserveTimeSlotStatusChangeAction'] = 'Reserve Time Slot';
$_lang['commerce.ReserveTimeSlotStatusChangeAction'] = 'Reserve Time Slot';

$_lang['commerce_timeslots.orders'] = 'Received Orders for ';

// Snippet
$_lang['commerce_timeslots.snippet.order_by_to_pick_up'] = 'Order by [[+order_by]] to pick up at [[+from]] &ndash; [[+until]]';

