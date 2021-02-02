<?php

namespace modmore\Commerce_ClickCollect\Admin\Schedule;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

class Overview extends Page {
    public $key = 'clickcollect/schedule';
    public $title = 'commerce_clickcollect.schedule';
    public static $permissions = ['commerce'];

    public function setUp()
    {
        $section = new SimpleSection($this->commerce, [
            'title' => $this->getTitle()
        ]);
        $section->addWidget(new Grid($this->commerce));
        $this->addSection($section);
        return $this;
    }
}