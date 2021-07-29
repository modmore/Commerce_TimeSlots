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
    public $availableDays = [];

    public function getFields(array $options = array())
    {
        // Create structure of day options
        $this->availableDays = [
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

        $fields = [];

        $fields[] = new TextField($this->commerce, [
            'name' => 'name',
            'label' => $this->adapter->lexicon('commerce.name'),
        ]);


        $sectionLabel = $this->adapter->lexicon('commerce_timeslots.repeat_schedule');

        if (!method_exists($this->commerce, 'scheduler')) {
            // Requires the Scheduler service in Commerce 1.3+
            $sectionLabel = $sectionLabel . ' ' .$this->adapter->lexicon('commerce_timeslots.repeat_schedule_desc.require_new');
        }

        $fields[] = new SectionField($this->commerce, [
            'label' => $sectionLabel,
            'description' => $this->adapter->lexicon('commerce_timeslots.repeat_schedule_desc'),
        ]);

        // Grab all TimeSlots shipping methods
        $methods = $this->adapter->getCollection(\comShippingMethod::class, [
            'class_key' => \TimeSlotsShippingMethod::class,
            'removed' => false
        ]);

        if (!empty($methods)) {
            foreach ($methods as $method) {
                $id = $method->get('id');

                // Generate descriptions to display any days already set by other schedules
                $days = $this->getUnavailableDays($id);

                $desc = '';
                foreach ($days as $k => $v) {
                    $desc .= $this->adapter->lexicon('commerce_timeslots.schedule') .
                        ' ' . $k . ': (' . implode(', ', $v) . ') ';
                }

                $fields[] = new SelectMultipleField($this->commerce, [
                    'name' => 'repeat_days_' . $id,
                    'label' => $this->adapter->lexicon('commerce_timeslots.shipping_method') . ': ' . $method->get('name'),
                    'description' => $desc,
                    'value' => $this->getCurrentDays($id),
                    'options' => $this->getAvailableDays($id)
                ]);
            }
        }

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
        foreach ($values as $k => $v) {
            $kArray = explode('_',$k);
            if ($kArray[0] === 'repeat' && $kArray[1] === 'days') {
                $methodId = end($kArray);

                $repeatDays = array_values(array_filter($v));
                $this->record->setRepeatDays($methodId, $repeatDays);

                // Now check all the methods and days for this schedule.
                $methods = $this->record->getRepeatDays();

                // If any of the methods have days assigned, declare this schedule repeatable. Otherwise, not.
                $repeat = false;
                foreach ($methods as $days) {
                    $days = array_values(array_filter($days));
                    if (!empty($days)) {
                        $repeat = true;
                    }
                }
                $this->record->set('repeat', $repeat);

            }
        }

        return parent::handleSubmit($values);
    }

    /**
     * @param int $methodId
     * @return string
     */
    public function getCurrentDays(int $methodId): string
    {
        $output = '';
        $days = $this->record->getRepeatDays($methodId);
        if(!empty($days)) {
            foreach ($days as $day) {
                $output .= $day . ',';
            }
            return trim($output,',');
        }

        return  '';
    }

    /**
     * @param int $methodId
     * @return array[]
     */
    protected function getAvailableDays(int $methodId): array
    {
        $collection = $this->adapter->getCollection(\ctsSchedule::class, [
            'repeat' => true,
        ]);

        $availableDays = $this->availableDays;

        // Remove days from the structure that are already assigned
        if (!empty($collection)) {
            foreach ($collection as $schedule) {

                /** @var \ctsSchedule $schedule */
                // Don't remove days from current schedule if any
                if ($schedule->get('id') === $this->record->get('id')) {
                    continue;
                }

                $unavailableDays = $schedule->getRepeatDays($methodId);

                foreach ($availableDays as $k => $availableDay) {
                    if (in_array($availableDay['value'], $unavailableDays)) {
                        unset($availableDays[$k]);
                    }
                }
            }
        }
        return $availableDays;
    }

    /**
     * @param int $methodId
     * @return array
     */
    protected function getUnavailableDays(int $methodId): array
    {
        $schedules = $this->adapter->getCollection(\ctsSchedule::class, [
            'repeat' => true,
        ]);
        if (empty($schedules)) {
            return [];
        }

        $days = $this->availableDays;

        $output = [];
        foreach ($schedules as $schedule) {
            /** @var \ctsSchedule $schedule */
            if ($schedule->get('id') === $this->record->get('id')) {
                continue;
            }

            $unavailableDays = $schedule->getRepeatDays($methodId);
            foreach ($days as $day) {
                if (in_array($day['value'], $unavailableDays)) {
                    $output[$schedule->get('name')][$day['value']] = $day['label'];
                }
            }
        }

        return $output;
    }
}