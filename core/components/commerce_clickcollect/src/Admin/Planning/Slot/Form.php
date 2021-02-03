<?php

namespace modmore\Commerce_ClickCollect\Admin\Planning\Slot;

use comOrderAddress;
use modmore\Commerce\Admin\Widgets\Form\DateTimeField;
use modmore\Commerce\Admin\Widgets\Form\HiddenField;
use modmore\Commerce\Admin\Widgets\Form\NumberField;
use modmore\Commerce\Admin\Widgets\Form\SelectField;
use modmore\Commerce\Admin\Widgets\Form\TextField;
use modmore\Commerce\Admin\Widgets\Form\Validation\Number;
use modmore\Commerce\Admin\Widgets\Form\Validation\Required;
use modmore\Commerce\Admin\Widgets\FormWidget;

class Form extends FormWidget
{
    protected $classKey = 'clcoDateSlot';
    public $key = 'clickcollect-dateslot-form';
    public $title = '';

    public function getFields(array $options = array())
    {
        $fields = [];

        $fields[] = new HiddenField($this->commerce, [
            'name' => 'for_date',
        ]);

        $fields[] = new DateTimeField($this->commerce, [
            'name' => 'time_from',
            'label' => $this->adapter->lexicon('commerce_clickcollect.time_from'),
            'validation' => [
                new Required(),
            ],
        ]);

        $fields[] = new DateTimeField($this->commerce, [
            'name' => 'time_until',
            'label' => $this->adapter->lexicon('commerce_clickcollect.time_until'),
            'validation' => [
                new Required(),
            ],
        ]);
        $fields[] = new DateTimeField($this->commerce, [
            'name' => 'closes_after',
            'label' => $this->adapter->lexicon('commerce_clickcollect.closes_after'),
            'validation' => [
                new Required(),
            ],
        ]);

        $fields[] = new NumberField($this->commerce, [
            'name' => 'max_reservations',
            'label' => $this->adapter->lexicon('commerce_clickcollect.max_reservations'),
            'description' => $this->adapter->lexicon('commerce_clickcollect.max_reservations.desc'),
            'validation' => [
                new Required(),
                new Number(-1.0),
            ],
            'min' => '-1'
        ]);

        return $fields;
    }

    public function getFormAction(array $options = array())
    {
        if (!$this->record->isNew()) {
            return $this->adapter->makeAdminUrl('clickcollect/planning/slot/edit', ['id' => $this->record->get('id')]);
        }
        return $this->adapter->makeAdminUrl('clickcollect/planning/slot/add');
    }
}
