<?php
$_lang['commerce_timeslots'] = 'Tijdslots';
$_lang['commerce_timeslots.description'] = 'Adds a timeslot based shipping method to Commerce, as well as new sections in the dashboard for the merchant to manage their planning and incoming orders. Useful for "Click & Collect" as well as timed-delivery use cases.';

$_lang['commerce_timeslots.orders'] = 'Bestellingen';
$_lang['commerce_timeslots.planning'] = 'Planning';
$_lang['commerce_timeslots.planning_description'] = 'Gebruik de planning om beschikbare tijdslots te configereren op specifieke data, tot 31 dagen in de toekomst. Om snel en eenvoudig de planning in te vullen, kies dan vooraf ingestelde tijdschema\'s. Indien nodig kunnen specifieke tijdslots worden bewerkt of toevoegd om afwijkende tijdslots op een specifieke datum mogelijk te maken. Belangrijk: als er geen tijdslots zijn toegevoegd op een bepaalde datum is het voor klanten niet mogelijk die dag te selecteren.';
$_lang['commerce_timeslots.schedule'] = 'Schema\'s';
$_lang['commerce_timeslots.schedule_description'] = 'Maak tijdschema\'s aan om verschillende configuraties van beschikbare tijdslots te beheren. Denk bijvoorbeeld aan een "weekdag" schema die eenvoudig om maandag t/m vrijdag toegepast kan worden, en een "zaterdag" schema voor de weekenden. Na het aanmaken van schema\'s, worden deze in het Planning onderdeel aan specifieke data toegewezen en is het mogelijk de tijdslots eventueel nog aan te passen.';
$_lang['commerce_timeslots.slot'] = 'Tijdslot';
$_lang['commerce_timeslots.slots'] = 'Tijdslots';
$_lang['commerce_timeslots.time_from'] = 'Starttijd';
$_lang['commerce_timeslots.time_until'] = 'Eindtijd';
$_lang['commerce_timeslots.lead_time'] = 'Aanlooptijd';
$_lang['commerce_timeslots.minutes'] = 'min';
$_lang['commerce_timeslots.price'] = 'Prijs';
$_lang['commerce_timeslots.price.desc'] = 'De prijs voor dit specifieke tijdslot. Dit zal aan de basis prijs van de verzendmethode worden toegevoegd (of in het geval van een negative prijs, van de basisprijs worden afgetrokken) om tot de prijs van de verzending te komen.';
$_lang['commerce_timeslots.lead_time.desc'] = 'De tijd (in minuten) voorafgaand aan de starttijd waarop bestellingen geplaatst moeten zijn. Bijvoorbeeld 60 (voor 1 uur), 240 (voor 4 uur), 480 (voor 8 uur) of 1440 (voor 24 uur). ';
$_lang['commerce_timeslots.max_reservations'] = 'Maximaal aantal reserveringen';
$_lang['commerce_timeslots.max_reservations.desc'] = 'Het maximum aantal bestellingen waarvoor deze tijdslot beschikbaar is. Om onbeperkt bestellingen te accepteren, stel -1 in. In situaties waar veel gelijktijdige bestellingen worden geplaatst is het technisch mogelijk dat dit aantal toch wordt overschreden doordat klanten tegelijkertijd bestellen.';
$_lang['commerce_timeslots.add_slot'] = 'Tijdslot toevoegen';
$_lang['commerce_timeslots.edit_slot'] = 'Tijdslot bewerken';
$_lang['commerce_timeslots.delete_slot'] = 'Tijdslot verwijderen';
$_lang['commerce_timeslots.delete_slot_named'] = 'Verwijder tijdslot [[+time_from]] - [[+time_until]]';
$_lang['commerce_timeslots.delete_planning_slot_named'] = 'Verwijder tijdslot [[+time_from_formatted]] - [[+time_until_formatted]]';
$_lang['commerce_timeslots.duplicate_slot'] = 'Dupliceer tijdslot';
$_lang['commerce_timeslots.add_schedule'] = 'Schema toevoegen';
$_lang['commerce_timeslots.edit_schedule'] = 'Schema bewerken';
$_lang['commerce_timeslots.duplicate_schedule'] = 'Schema dupliceren';
$_lang['commerce_timeslots.delete_schedule'] = 'Verwijder schema en tijdslots';
$_lang['commerce_timeslots.delete_schedule_named'] = 'Verwijder schema "[[+name]]" en bijbehorende tijdslots';

$_lang['commerce_timeslots.edit_planning'] = 'Bewerk planning';
$_lang['commerce_timeslots.date'] = 'Datum';
$_lang['commerce_timeslots.day'] = 'Dag';
$_lang['commerce_timeslots.changing_schedule'] = 'Je <b>vervangt het schema</b> voor [[+date]]. Tijdslots die door een ander schema al waren toegevoegd zullen worden verwijderd. Als specifieke tijdslots al zijn gebruikt voor bestellingen, of als een tijdslot handmatig is toegevoegd, zal deze worden bewaard.';
$_lang['commerce_timeslots.closes_after'] = 'Sluit na';
$_lang['commerce_timeslots.unavailable'] = 'Niet beschikbaar';
$_lang['commerce_timeslots.today'] = 'Vandaag';
$_lang['commerce_timeslots.unlimited_reservations_available'] = 'ongelimiteerde bestellingen';
$_lang['commerce_timeslots.reservations_available'] = 'bestellingen beschikbaar';
$_lang['commerce_timeslots.reservations_placed'] = 'tijdslots gereserveerd';
$_lang['commerce_timeslots.no_date_slots'] = 'Geen tijdslots geactiveerd, datum niet beschikbaar voor klanten.';
$_lang['commerce_timeslots.different_schedule'] = 'Dit tijdslot is geen onderdeel van het schema dat voor deze datum is geselecteerd en is mogelijk handmatig toegevoegd, of overgebleven na het vervangen van een schema.';

// Shipping method / frontend
$_lang['commerce_timeslots.max_days_visible'] = 'Maximum aantal dagen zichtbaar';
$_lang['commerce_timeslots.max_days_visible.desc'] = 'Het maximaal aantal dagen in de toekomst dat klanten een tijdslot kunnen kiezen. De minimum tijd tussen het plaatsen van een bestelling en het kunnen kiezen van een tijdslot wordt beheerd via de planning ("sluit na") en schema\'s ("aanlooptijd").';
$_lang['commerce_timeslots.slot_unavailable'] = 'Niet meer beschikbaar';
$_lang['commerce_timeslots.no_options_available'] = 'Helaas zijn er op geen bestelmomenten beschikbaar. Probeer het later nog eens, kies een andere verzendmethode, of neem contact met ons op voor assistentie.';
$_lang['commerce_timeslots.num_available_slots'] = '[[+num]] slots beschikbaar';
$_lang['commerce_timeslots.selected_slot_no_longer_available'] = 'Het tijdslot dat u voor levering of ophalen had gekozen is helaas niet meer beschikbaar. Kies alstublieft een nieuw moment om uw bestelling te voltooien.';
$_lang['commerce_timeslots.select_a_slot'] = 'Kies alstublieft een tijdslot voor u verder gaat met het plaatsen van uw bestelling.';

$_lang['commerce.add_TimeSlotsShippingMethod'] = 'Add Time Slots Shipping Method';
$_lang['commerce.TimeSlotsShippingMethod'] = 'Time Slots Shipping Method';
$_lang['commerce.add_ReserveTimeSlotStatusChangeAction'] = 'Reserve Time Slot';
$_lang['commerce.ReserveTimeSlotStatusChangeAction'] = 'Reserve Time Slot';

$_lang['commerce_timeslots.orders'] = 'Ontvangen bestellingen voor ';

