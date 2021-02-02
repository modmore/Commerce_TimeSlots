<?php

namespace modmore\Commerce_ClickCollect\Admin\Schedule;

use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Widgets\TextWidget;

class Update extends Page {
    public $key = 'clickcollect/schedule/edit';
    public $title = 'commerce_clickcollect.edit_schedule';
    public static $permissions = ['commerce'];

    public function setUp()
    {
        $objectId = (int)$this->getOption('id', 0);
        $exists = $this->adapter->getCount('clcoSchedule', ['id' => $objectId]);

        if ($exists) {
            $section = new SimpleSection($this->commerce, [
                'title' => $this->title
            ]);
            $section->addWidget((new Form($this->commerce, ['isUpdate' => true, 'id' => $objectId]))->setUp());
            $this->addSection($section);
            return $this;
        }

        return $this->returnError($this->adapter->lexicon('commerce.item_not_found'));
    }
}