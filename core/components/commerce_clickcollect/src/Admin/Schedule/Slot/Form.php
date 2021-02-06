<?php

namespace modmore\Commerce_ClickCollect\Admin\Schedule\Slot;

use comOrderAddress;
use modmore\Commerce\Admin\Widgets\Form\HiddenField;
use modmore\Commerce\Admin\Widgets\Form\NumberField;
use modmore\Commerce\Admin\Widgets\Form\SelectField;
use modmore\Commerce\Admin\Widgets\Form\TextField;
use modmore\Commerce\Admin\Widgets\Form\Validation\Number;
use modmore\Commerce\Admin\Widgets\Form\Validation\Required;
use modmore\Commerce\Admin\Widgets\FormWidget;

class Form extends FormWidget
{
    protected $classKey = 'clcoScheduleSlot';
    public $key = 'clickcollect-scheduleslot-form';
    public $title = '';

    public function getFields(array $options = array())
    {
        $fields = [];

        $fields[] = new HiddenField($this->commerce, [
            'name' => 'schedule',
        ]);

        $fields[] = new SelectField($this->commerce, [
            'name' => 'time_from',
            'label' => $this->adapter->lexicon('commerce_clickcollect.time_from'),
            'validation' => [
                new Required(),
            ],
            'options' => $this->timeOptions(),
        ]);

        $fields[] = new SelectField($this->commerce, [
            'name' => 'time_until',
            'label' => $this->adapter->lexicon('commerce_clickcollect.time_until'),
            'validation' => [
                new Required(),
            ],
            'options' => $this->timeOptions(),
        ]);

        $fields[] = new NumberField($this->commerce, [
            'name' => 'lead_time',
            'label' => $this->adapter->lexicon('commerce_clickcollect.lead_time'),
            'description' => $this->adapter->lexicon('commerce_clickcollect.lead_time.desc'),
            'validation' => [
                new Required(),
                new Number(0),
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

        $fields[] = new NumberField($this->commerce, [
            'name' => 'price',
            'label' => $this->adapter->lexicon('commerce_clickcollect.price'),
            'description' => $this->adapter->lexicon('commerce_clickcollect.price.desc'),
            'input_class' => 'commerce-field-currency',
            'validation' => [],
        ]);

        return $fields;
    }

    public function getFormAction(array $options = array())
    {
        if (!$this->record->isNew()) {
            return $this->adapter->makeAdminUrl('clickcollect/schedule/slot/edit', ['id' => $this->record->get('id')]);
        }
        return $this->adapter->makeAdminUrl('clickcollect/schedule/slot/add');
    }

    private function timeOptions(): array
    {
        $times = [];

        $hour = 0;
        while ($hour < 24) {
            $minute = 0;
            while ($minute < 60) {
                $hourDisplay = $hour < 10 ? '0' . $hour : $hour;
                $minuteDisplay = $minute < 10 ? '0' . $minute : $minute;
                $times[] = [
                    'value' => "{$hourDisplay}:{$minuteDisplay}",
                    'label' => "{$hourDisplay}:{$minuteDisplay}",
                ];
                $minute += 5;
            }
            $hour++;
        }
        return $times;
    }
}