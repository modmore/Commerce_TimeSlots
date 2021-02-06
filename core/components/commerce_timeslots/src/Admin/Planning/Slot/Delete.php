<?php

namespace modmore\Commerce_TimeSlots\Admin\Planning\Slot;

use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Widgets\DeleteFormWidget;

class Delete extends Page {
    public $key = 'timeslots/planning/slot/delete';
    public $title = 'commerce_timeslots.delete_slot';
    public static $permissions = ['commerce'];

    public function setUp()
    {
        $objectId = (int)$this->getOption('id', 0);
        $object = $this->adapter->getObject(\ctsDateSlot::class, ['id' => $objectId]);

        $section = new SimpleSection($this->commerce, [
            'title' => $this->title
        ]);
        if ($object) {
            $widget = new DeleteFormWidget($this->commerce, [
                'title' => 'commerce_timeslots.delete_planning_slot_named'
            ]);
            $widget->setRecord($object);
            $widget->setClassKey('ctsDateSlot');
            $widget->setFormAction($this->adapter->makeAdminUrl('timeslots/planning/slot/delete', ['id' => $object->get('id')]));
            $widget->setUp();
            $section->addWidget($widget);
            $this->addSection($section);
            return $this;
        }

        return $this->returnError('Schedule not found');
    }
}