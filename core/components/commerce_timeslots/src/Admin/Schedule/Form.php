<?php

namespace modmore\Commerce_TimeSlots\Admin\Schedule;

use modmore\Commerce\Admin\Widgets\Form\SectionField;
use modmore\Commerce\Admin\Widgets\Form\SelectMultipleField;
use modmore\Commerce\Admin\Widgets\Form\TextField;
use modmore\Commerce\Admin\Widgets\FormWidget;
use modmore\Commerce_TimeSlots\Modules\TimeSlots;

/**

 * @property \ctsSchedule $record
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

        $fields[] = new SectionField($this->commerce, [
            'label' => 'Repeat This Schedule',
            'description' => ' (Requires Commerce 1.3+) Only one schedule can be assigned to a day.'
        ]);

        $days = new SelectMultipleField($this->commerce, [
            'name' => 'repeat_days',
            'label' => $this->adapter->lexicon('commerce_timeslots.schedule_days'),
            'description' => $this->adapter->lexicon('commerce_timeslots.schedule_days_desc'),
            'value' => $this->getCurrentDays(),
            'options' => $this->getAvailableDays()

        ]);
        $fields[] = $days;

        return $fields;
    }

    public function getFormAction(array $options = array())
    {
        if (!$this->record->isNew()) {
            return $this->adapter->makeAdminUrl('timeslots/schedule/edit', ['id' => $this->record->get('id')]);
        }
        return $this->adapter->makeAdminUrl('timeslots/schedule/add');
    }

    public function handleSubmit($values)
    {
        if (!empty($values['repeat_days'])) {
            $repeatDays = array_values(array_filter($values['repeat_days']));
            $this->record->setRepeatDays($repeatDays, false);

            if (count($this->record->getRepeatDays()) > 0) {
                $this->record->set('repeat', true);
            }
            else {
                $this->record->set('repeat', false);
            }

        }
        return parent::handleSubmit($values);
    }

    public function getCurrentDays(): string
    {
        $output = '';
        $days = $this->record->getRepeatDays();
        if(!empty($days)) {
            foreach ($days as $day) {
                $output .= $day . ',';
            }
            return trim($output,',');
        }

        return  '';
    }

    public function getAvailableDays(): array
    {
        $collection = $this->adapter->getCollection(\ctsSchedule::class, [
            'repeat' => 1,
        ]);

        // Create structure of day options
        $availableDays = [
            [
                'label' => $this->adapter->lexicon('sunday'),
                'value' => TimeSlots::SUNDAY
            ],
            [
                'label' => $this->adapter->lexicon('monday'),
                'value' => TimeSlots::MONDAY
            ],
            [
                'label' => $this->adapter->lexicon('tuesday'),
                'value' => TimeSlots::TUESDAY
            ],
            [
                'label' => $this->adapter->lexicon('wednesday'),
                'value' => TimeSlots::WEDNESDAY
            ],
            [
                'label' => $this->adapter->lexicon('thursday'),
                'value' => TimeSlots::THURSDAY
            ],
            [
                'label' => $this->adapter->lexicon('friday'),
                'value' => TimeSlots::FRIDAY
            ],
            [
                'label' => $this->adapter->lexicon('saturday'),
                'value' => TimeSlots::SATURDAY
            ],
        ];

        // Remove days from the structure that are already assigned
        if (!empty($collection)) {
            foreach ($collection as $schedule) {

                /** @var \ctsSchedule $schedule */
                // Don't remove days from current schedule if any
                if ($schedule->get('id') === $this->record->get('id')) {
                    continue;
                }

                $unavailableDays = $schedule->getRepeatDays();
                foreach ($availableDays as $k => $availableDay) {
                    if (in_array($availableDay['value'], $unavailableDays)) {
                        unset($availableDays[$k]);
                    }
                }
            }
        }
        return $availableDays;
    }
}