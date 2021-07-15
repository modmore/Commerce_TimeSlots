<?php

namespace modmore\Commerce_TimeSlots\Admin\Schedule;

use comOrderAddress;
use modmore\Commerce\Admin\Widgets\Form\TextField;
use modmore\Commerce\Admin\Widgets\FormWidget;

/**
 * Class Form
 * @package modmore\Commerce\Admin\Configuration\PaymentMethods
 *
 * @property comOrderAddress $record
 */
class Form extends FormWidget
{
    protected $classKey = 'ctsSchedule';
    public $key = 'timeslots-schedule-form';
    public $title = '';

    public function getFields(array $options = array())
    {
        $fields = [];

        $fields[] = new TextField($this->commerce, [
            'name' => 'name',
            'label' => $this->adapter->lexicon('commerce.name'),
        ]);

        return $fields;
    }

    public function getFormAction(array $options = array())
    {
        if (!$this->record->isNew()) {
            return $this->adapter->makeAdminUrl('timeslots/schedule/edit', ['id' => $this->record->get('id')]);
        }

        $exists = $this->adapter->getCount($this->classKey);
        if (!$exists) {
            return $this->adapter->makeAdminUrl('timeslots/schedule/add', [
                'is_first' => 1
            ]);
        }

        return $this->adapter->makeAdminUrl('timeslots/schedule/add');
    }

    public function afterSave()
    {
        if ($this->getOption('is_first')) {
            $this->record->set('default', 1);
            $this->record->save();
        }

        return true;
    }
}