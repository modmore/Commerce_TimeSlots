<?php

namespace modmore\Commerce_ClickCollect\Admin\Schedule\Slot;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

class Create extends Page {
    public $key = 'clickcollect/schedule/slot/create';
    public $title = 'commerce_clickcollect.add_slot';
    public static $permissions = ['commerce'];

    public function setUp()
    {
        $section = new SimpleSection($this->commerce, [
            'title' => $this->title
        ]);
        $section->addWidget((new Form($this->commerce, [
            'id' => 0,
        ]))->setUp());
        $this->addSection($section);
        return $this;
    }
}