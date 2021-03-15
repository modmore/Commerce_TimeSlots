<?php
$_lang['commerce_timeslots'] = 'Espaces de Temps';
$_lang['commerce_timeslots.description'] = 'Va ajouter une methode d\'expedition interrompue de Commerce, ainsi que quelques nouvelles sections sur la Table des Contenu, pourque le marchand puisse regler ses ordres de livraison planifies.. Il sert au \'\'Click& Collect\'\' ainsi que aux cas de livraison a temps. ';

$_lang['commerce_timeslots.orders'] = 'Des Ordres';
$_lang['commerce_timeslots.planning'] = 'Planifier';
$_lang['commerce_timeslots.planning_description'] = 'Utilisez le plan d\'horloge pour fixer l\'heure exacte des dates specifiques, jusqu\'a 31 jours dans le future. Afin de pouvoir fixer l\'heure desiree, fixez l\'heure dans un Programme pre-installe. Si vous voulez, vous pouvez fixer l\'heure  exactement aux dates specifiques. Important: S\'il n\'y a pas un systeme d\'heure exacte , (en minutes, et secondes) pre-installee, le Client ne pourra pas selecctionner. ';
$_lang['commerce_timeslots.schedule'] = 'Mettre en Programme ';
$_lang['commerce_timeslots.schedule_description'] = 'Create Schedules to manage different configurations of time slots for pickup. For example, create a "Weekday" schedule that you can apply to Monday-Friday to cover standard opening hours, and a "Saturday" schedule for your opening hours in the weekend. You\'ll apply schedules to specific dates in the "Planning" section and will be able of changing individual slots on a specific date there as well.';
$_lang['commerce_timeslots.slot'] = 'Time slot';
$_lang['commerce_timeslots.slots'] = 'Heure Fixee';
$_lang['commerce_timeslots.time_from'] = 'Heure de commencement';
$_lang['commerce_timeslots.time_until'] = 'Fin d\'heuere';
$_lang['commerce_timeslots.lead_time'] = 'Heure de duree';
$_lang['commerce_timeslots.minutes'] = 'min';
$_lang['commerce_timeslots.price'] = 'Prix';
$_lang['commerce_timeslots.price.desc'] = 'Le prix pour cette heure specifique. Ce prix sera ajoute  (ou si vous donnez une monte negative, eliminee de) au prix principal  selon la methode d\'expedition. ';
$_lang['commerce_timeslots.lead_time.desc'] = 'The number of minutes before the start time that orders may be placed for start time. Some common periods in minutes: 1 hour = 60 minutes, 4 hours = 240 minutes, 8 hours = 480, 24 hours = 1440.';
$_lang['commerce_timeslots.max_reservations'] = 'Reservations maximales';
$_lang['commerce_timeslots.max_reservations.desc'] = 'Le nombre d\'ordres maximum qui peut etre reserve dans cette place. Mettre a -1, pour permettre un nombre de reservations infini dans chaque place.. S\'il y a un grand nombre d\'ordres , il peut etre excede car les reservations n\'ont pas ete faites jusqu\'a que l\'ordre soit verifie, et les divers clients, vont mettre leur ordre chacun en meme temps. ';
$_lang['commerce_timeslots.add_slot'] = 'Ajouter place';
$_lang['commerce_timeslots.edit_slot'] = 'Editer fente  ';
$_lang['commerce_timeslots.delete_slot'] = 'Supprimer fente, place, ';
$_lang['commerce_timeslots.delete_slot_named'] = 'Supprimer caisse[[+time_from]][[+time_until]]';
$_lang['commerce_timeslots.delete_planning_slot_named'] = 'Supprimer caisse [[+time_from_formatted]][[+time_until_formatted]]';
$_lang['commerce_timeslots.duplicate_slot'] = 'Dupliquer place (du produit)';
$_lang['commerce_timeslots.add_schedule'] = 'Ajouter plan ';
$_lang['commerce_timeslots.edit_schedule'] = 'Editer plan ';
$_lang['commerce_timeslots.duplicate_schedule'] = 'Dupliquer plan';
$_lang['commerce_timeslots.delete_schedule'] = 'Supprimer plan et fentes';
$_lang['commerce_timeslots.delete_schedule_named'] = 'Supprimer le plan [[+name]] et les fentes respectives';

$_lang['commerce_timeslots.edit_planning'] = 'Editer les plans';
$_lang['commerce_timeslots.date'] = 'Date';
$_lang['commerce_timeslots.day'] = 'Jour';
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

