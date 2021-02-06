<?php

namespace modmore\Commerce_TimeSlots\Admin\Schedule;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Widgets\HtmlWidget;

class Overview extends Page {
    public $key = 'timeslots/schedule';
    public $title = 'commerce_timeslots.schedule';
    public static $permissions = ['commerce'];

    public function setUp()
    {
        $section = new SimpleSection($this->commerce, [
            'title' => $this->getTitle()
        ]);
        $section->addWidget(new HtmlWidget($this->commerce, [
            'html' => '<p style="margin-bottom: 1em;">' . $this->adapter->lexicon('commerce_timeslots.schedule_description') . '</p>'
        ]));
        $section->addWidget(new Grid($this->commerce));
        $this->addSection($section);
        return $this;
    }
}