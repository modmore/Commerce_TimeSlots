<?php

namespace modmore\Commerce_TimeSlots\Admin\Planning;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Widgets\HtmlWidget;

class Overview extends Page {
    public $key = 'timeslots/planning';
    public $title = 'commerce_timeslots.planning';
    public static $permissions = ['commerce'];

    public function setUp()
    {
        $methodId = (int)$this->getOption('method', 0);
        $method = $this->adapter->getObject('comShippingMethod', [
            'id' => $methodId,
            'class_key' => \TimeSlotsShippingMethod::class,
            'removed' => false,
        ]);

        if (!$method) {
            return $this->returnError('Method ' . $methodId . ' not found.');
        }

        $this->key .= '-' . $methodId;

        $section = new SimpleSection($this->commerce, [
            'title' => $method->get('name') . ' ' . $this->getTitle(),
        ]);
        $section->addWidget(new HtmlWidget($this->commerce, [
            'html' => '<p style="margin-bottom: 1em;">' . $this->adapter->lexicon('commerce_timeslots.planning_description') . '</p>'
        ]));
        $section->addWidget(new Grid($this->commerce, ['method' => $method->get('id')]));
        $this->addSection($section);
        return $this;
    }
}