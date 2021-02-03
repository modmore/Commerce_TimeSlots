<?php

namespace modmore\Commerce_ClickCollect\Admin\Schedule\Slot;

use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Widgets\DeleteFormWidget;

class Delete extends Page {
    public $key = 'clickcollect/schedule/slot/delete';
    public $title = 'commerce_clickcollect.delete_slot';
    public static $permissions = ['commerce'];

    public function setUp()
    {
        $objectId = (int)$this->getOption('id', 0);
        $object = $this->adapter->getObject(\clcoScheduleSlot::class, ['id' => $objectId]);

        $section = new SimpleSection($this->commerce, [
            'title' => $this->title
        ]);
        if ($object) {
            $widget = new DeleteFormWidget($this->commerce, [
                'title' => 'commerce_clickcollect.delete_slot_named'
            ]);
            $widget->setRecord($object);
            $widget->setClassKey('clcoSchedule');
            $widget->setFormAction($this->adapter->makeAdminUrl('clickcollect/schedule/slot/delete', ['id' => $object->get('id')]));
            $widget->setUp();
            $section->addWidget($widget);
            $this->addSection($section);
            return $this;
        }

        return $this->returnError('Schedule not found');
    }
}