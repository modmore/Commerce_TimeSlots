<?php

namespace modmore\Commerce_ClickCollect\Admin\Schedule;

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
    protected $classKey = 'clcoSchedule';
    public $key = 'clickcollect-schedule-form';
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
            return $this->adapter->makeAdminUrl('clickcollect/schedule/edit', ['id' => $this->record->get('id')]);
        }
        return $this->adapter->makeAdminUrl('clickcollect/schedule/add');
    }
}