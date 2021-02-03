<?php

namespace modmore\Commerce_ClickCollect\Admin\Planning\Slot;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

class Duplicate extends Page {
    public $key = 'clickcollect/planning/duplicate/slot';
    public $title = 'commerce_clickcollect.duplicate_slot';
    public static $permissions = ['commerce'];

    public function setUp()
    {
        $objectId = (int)$this->getOption('id', 0);
        $duplicate = $this->adapter->getObject(\clcoDateSlot::class, ['id' => $objectId]);

        if ($duplicate instanceof \clcoDateSlot) {
            $new = $duplicate->duplicate();
            if (!$new) {
                return $this->returnError('Could not create duplicate of schedule slot.');
            }
            $section = new SimpleSection($this->commerce, [
                'title' => $this->title
            ]);

            $section->addWidget((new Form($this->commerce, ['isUpdate' => true, 'id' => $new->get('id')]))->setUp());
            $this->addSection($section);
            return $this;
        }
        return $this->returnError($this->adapter->lexicon('commerce.item_not_found'));
    }
}