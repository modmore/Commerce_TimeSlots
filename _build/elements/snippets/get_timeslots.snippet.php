<?php
/**
 * commerce.get_timeslots
 *
 * Add this snippet to any MODX template to either display a grid of available timeslots, or display a notice:
 * "Order by {date|time} to pick up at {timeslot}".
 *
 * This snippet currently has two parameters that can be used.
 *
 * - &shipId: Add a shipping id here to only display timeslots for that shipping method. If not used, timeslots for all
 *          shipping methods will be displayed.
 *
 * - &orderBy: Set this value to 1 and the output of the snippet will be the "Order by" notice. Don't set it to
 *          output the grid instead.
 *
 * Example usage:
 * [[!commerce.get_timeslots?
 *     &shipId=`4`
 *     &orderBy=`1`
 * ]]
 *
 *
 * @var modX $modx
 * @var array $scriptProperties
 */

// Instantiate the Commerce_Timeslots class
$path = $modx->getOption('commerce_timeslots.core_path', null, MODX_CORE_PATH . 'components/commerce_timeslots/') . 'model/commerce_timeslots/';
$params = ['mode' => $modx->getOption('commerce.mode')];

/** @var Commerce_Timeslots|null $timeslots */
$timeslots = $modx->getService('commerce_timeslots', 'Commerce_Timeslots', $path, $params);
if (!($timeslots instanceof Commerce_Timeslots)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not load Commerce_Timeslots service in commerce_timeslots.get_timeslots snippet.');
    return 'Could not load Commerce_Timeslots. Please try again later.';
}

if ($timeslots->commerce->isDisabled()) {
    return $timeslots->commerce->adapter->lexicon('commerce.mode.disabled.message');
}

return $timeslots->getTimeslots($scriptProperties);