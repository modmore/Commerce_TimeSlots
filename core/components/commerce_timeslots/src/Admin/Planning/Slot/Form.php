<?php

namespace modmore\Commerce_TimeSlots\Admin\Planning\Slot;

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
    protected $classKey = 'ctsDateSlot';
    public $key = 'timeslots-dateslot-form';
    public $title = '';

    public function getFields(array $options = array())
    {
        $fields = [];

        $fields[] = new HiddenField($this->commerce, [
            'name' => 'for_date',
        ]);
        $fields[] = new HiddenField($this->commerce, [
            'name' => 'shipping_method',
        ]);

        $fields[] = new DateTimeField($this->commerce, [
            'name' => 'time_from',
            'label' => $this->adapter->lexicon('commerce_timeslots.time_from'),
            'validation' => [
                new Required(),
            ],
        ]);

        $fields[] = new DateTimeField($this->commerce, [
            'name' => 'time_until',
            'label' => $this->adapter->lexicon('commerce_timeslots.time_until'),
            'validation' => [
                new Required(),
            ],
        ]);
        $fields[] = new DateTimeField($this->commerce, [
            'name' => 'closes_after',
            'label' => $this->adapter->lexicon('commerce_timeslots.closes_after'),
            'validation' => [
                new Required(),
            ],
        ]);

        $fields[] = new NumberField($this->commerce, [
            'name' => 'max_reservations',
            'label' => $this->adapter->lexicon('commerce_timeslots.max_reservations'),
            'description' => $this->adapter->lexicon('commerce_timeslots.max_reservations.desc'),
            'validation' => [
                new Required(),
                new Number(-1.0),
            ],
            'min' => '-1'
        ]);

        $fields[] = new NumberField($this->commerce, [
            'name' => 'price',
            'label' => $this->adapter->lexicon('commerce_timeslots.price'),
            'description' => $this->adapter->lexicon('commerce_timeslots.price.desc'),
            'input_class' => 'commerce-field-currency',
            'validation' => [],
        ]);

        return $fields;
    }

    public function getFormAction(array $options = array())
    {
        if (!$this->record->isNew()) {
            return $this->adapter->makeAdminUrl('timeslots/planning/slot/edit', ['id' => $this->record->get('id')]);
        }
        return $this->adapter->makeAdminUrl('timeslots/planning/slot/add');
    }
}
