<?php
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
class clcoSchedule extends comSimpleObject
{
    public function duplicate(array $options = [])
    {
        $new = parent::duplicate($options);
        if (!$new) {
            return false;
        }

        /** @var clcoScheduleSlot[] $slots */
        $slots = $this->getMany('Slots');
        foreach ($slots as $slot) {
            $data = $slot->toArray();
            unset($data['schedule']);

            $newSlot = $this->adapter->newObject(clcoScheduleSlot::class);
            $newSlot->fromArray($data);
            $new->addMany($newSlot, 'Slots');
        }
        $new->save();

        return $new;
    }


}
