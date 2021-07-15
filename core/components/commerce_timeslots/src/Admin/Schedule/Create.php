<?php

namespace modmore\Commerce_TimeSlots\Admin\Schedule;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

class Create extends Page {
    public $key = 'timeslots/schedule/create';
    public $title = 'commerce_timeslots.add_schedule';
    public static $permissions = ['commerce'];

    public function setUp()
    {
        $first = false;
        if ((int)$this->getOption('is_first') === 1) {
            $first = true;
        }
        $section = new SimpleSection($this->commerce, [
            'title' => $this->title
        ]);
        $section->addWidget((new Form($this->commerce, [
            'is_first' => $first
        ]))->setUp());
        $this->addSection($section);
        return $this;
    }
}