<?php

namespace modmore\Commerce_TimeSlots\Admin\Orders;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

class Overview extends Page {
    public $key = 'orders/timeslot';
    public $title = 'commerce_timeslots.orders';
    public static $permissions = ['commerce', 'commerce_orders'];
    protected $state;
    /**
     * @var int
     */
    protected $methodId = 0;
    /**
     * @var \TimeSlotsShippingMethod|null
     */
    protected $method;

    public function setUp()
    {
        $this->methodId = (int)$this->getOption('method');
        $this->method = $this->adapter->getObject('comShippingMethod', [
            'class_key' => \TimeSlotsShippingMethod::class,
            'removed' => false,
            'id' => $this->methodId
        ]);
        if (!$this->method) {
            return $this->returnError('Method not found.');
        }

        $section = new SimpleSection($this->commerce, [
            'title' => $this->getTitle()
        ]);
        $section->addWidget(new Grid($this->commerce, ['method' => $this->method->get('id')]));
        $this->addSection($section);
        return $this;
    }

    public function getSubMenuActiveKey()
    {
        return 'orders/timeslot_' . $this->methodId;
    }

    public function getTitle()
    {
        return $this->adapter->lexicon('commerce.orders') . ' &raquo; ' . $this->adapter->lexicon('commerce_timeslots.orders'). $this->method->get('name');
    }
}