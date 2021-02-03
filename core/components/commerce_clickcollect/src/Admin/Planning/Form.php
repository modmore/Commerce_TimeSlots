<?php

namespace modmore\Commerce_ClickCollect\Admin\Planning;

use comOrderAddress;
use modmore\Commerce\Admin\Widgets\Form\DescriptionField;
use modmore\Commerce\Admin\Widgets\Form\SelectField;
use modmore\Commerce\Admin\Widgets\Form\TextField;
use modmore\Commerce\Admin\Widgets\Form\Validation\Required;
use modmore\Commerce\Admin\Widgets\FormWidget;

class Form extends FormWidget
{
    protected $classKey = 'clcoDate';
    public $key = 'clickcollect-planning-form';
    public $title = '';

    public function getFields(array $options = array())
    {
        $fields = [];

        if ($this->record->get('schedule') > 0) {
            $fields[] = new DescriptionField($this->commerce, [
                'description' => $this->adapter->lexicon('commerce_clickcollect.changing_schedule'),
                'raw' => true,
            ]);
        }

        $fields[] = new SelectField($this->commerce, [
            'name' => 'schedule',
            'label' => $this->adapter->lexicon('commerce_clickcollect.schedule'),
            'optionsClass' => \clcoSchedule::class,
            'validation' => [
                new Required(),
            ]
        ]);

        return $fields;
    }

    public function getFormAction(array $options = array())
    {
        return $this->adapter->makeAdminUrl('clickcollect/planning/edit', ['id' => $this->record->get('id')]);
    }
}