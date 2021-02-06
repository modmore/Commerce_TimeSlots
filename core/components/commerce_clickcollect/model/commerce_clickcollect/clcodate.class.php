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
}
