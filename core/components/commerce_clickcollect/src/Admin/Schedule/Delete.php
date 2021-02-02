<?php

namespace modmore\Commerce_ClickCollect\Admin\Schedule;

use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Widgets\DeleteFormWidget;
use modmore\Commerce\Admin\Widgets\TextWidget;

class Delete extends Page {
    public $key = 'clickcollect/schedule/delete';
    public $title = 'commerce_clickcollect.delete_schedule';
    public static $permissions = ['commerce'];

    public function setUp()
    {
        $objectId = (int)$this->getOption('id', 0);
        $object = $this->adapter->getObject('clcoSchedule', ['id' => $objectId]);

        $section = new SimpleSection($this->commerce, [
            'title' => $this->title
        ]);
        if ($object) {
            $widget = new DeleteFormWidget($this->commerce, [
                'title' => 'commerce_clickcollect.delete_schedule_named'
            ]);
            $widget->setRecord($object);
            $widget->setClassKey('clcoSchedule');
            $widget->setFormAction($this->adapter->makeAdminUrl('clickcollect/schedule/delete', ['id' => $object->get('id')]));
            $widget->setUp();
            $section->addWidget($widget);
            $this->addSection($section);
            return $this;
        }

        return $this->returnError('Schedule not found');
    }
}