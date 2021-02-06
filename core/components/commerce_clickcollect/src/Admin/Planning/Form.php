<?php

namespace modmore\Commerce_ClickCollect\Admin\Planning;

use clcoDateSlot;
use clcoScheduleSlot;
use DateTime;
use modmore\Commerce\Admin\Widgets\Form\DescriptionField;
use modmore\Commerce\Admin\Widgets\Form\HiddenField;
use modmore\Commerce\Admin\Widgets\Form\SelectField;
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

        $fields[] = new DescriptionField($this->commerce, [
            'description' => $this->adapter->lexicon('commerce_clickcollect.changing_schedule', ['date' => $this->record->get('for_date')]),
            'raw' => true,
        ]);

        $fields[] = new HiddenField($this->commerce, [
            'name' => 'method',
        ]);

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
        return $this->adapter->makeAdminUrl('clickcollect/planning/edit', [
            'id' => $this->record->get('id'),
        ]);
    }

    public function afterSave()
    {
        $methodId = (int)$_POST['method'];
        $method = $this->adapter->getObject('comShippingMethod', [
            'id' => $methodId,
            'class_key' => \ClickCollectShippingMethod::class,
            'removed' => false,
        ]);
        if (!$method) {
            throw new \RuntimeException('Invalid method provided.');
        }

        $schedule = $this->adapter->getObject(\clcoSchedule::class, [
            'id' => (int)$this->record->get('schedule'),
        ]);
        if (!$schedule) {
            throw new \RuntimeException('Invalid schedule provided.');
        }

        // Clean up all scheduled slots currently assigned
        $c = $this->adapter->newQuery(clcoDateSlot::class);
        $c->where([
            'for_date' => $this->record->get('id'),
            'shipping_method' => $method->get('id'),
            'base_slot:>' => 0,
        ]);
        foreach ($this->adapter->getIterator(clcoDateSlot::class, $c) as $oldSlot) {
            if ($oldSlot->get('max_reservations') === $oldSlot->get('available_reservations')) {
                $oldSlot->remove();
            }
            // @todo rather than base it of a cached count, try a count() on actual orders for improved accuracy
        }

        // Copy slots from the schedule
        $c = $this->adapter->newQuery(clcoScheduleSlot::class);
        $c->where([
            'schedule' => $schedule->get('id'),
        ]);
        $c->sortby('time_from');
        $c->sortby('time_until');
        foreach ($this->adapter->getIterator(clcoScheduleSlot::class, $c) as $baseSlot) {
            $newSlot = $this->adapter->newObject(clcoDateSlot::class);
            $newSlot->fromArray([
                'for_date' => $this->record->get('id'),
                'base_slot' => $baseSlot->get('id'),
                'shipping_method' => $method->get('id'),
                'schedule' => $schedule->get('id'),
                'max_reservations' => $baseSlot->get('max_reservations'),
                'available_reservations' => $baseSlot->get('max_reservations'),
            ]);

            // Calculate the timeFrom and timeUntil using DateTime. This makes sure it uses the servers' timezone
            // and that the appropriate offset is handled when converting to UTC.
            $timeFrom = explode(':', $baseSlot->get('time_from'));
            $timeFromDate = (new DateTime($this->record->get('for_date')))->setTime($timeFrom[0], $timeFrom[1]);
            $newSlot->set('time_from', $timeFromDate->format('U'));

            $timeUntil = explode(':', $baseSlot->get('time_until'));
            $timeUntilDate = (new DateTime($this->record->get('for_date')))->setTime($timeUntil[0], $timeUntil[1]);
            $newSlot->set('time_until', $timeUntilDate->format('U'));

            // For the "closes after", simply remove the lead time (defined in minutes) from the calculated unix timestamp
            // @todo This may cause oddities during DST switches, but for now I'm okay with that.
            $newSlot->set('closes_after', $newSlot->get('time_from') - ($baseSlot->get('lead_time') * 60));
            $newSlot->save();
        }

        return true;
    }
}