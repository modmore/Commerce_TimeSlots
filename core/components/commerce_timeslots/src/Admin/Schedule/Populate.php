<?php

namespace modmore\Commerce_TimeSlots\Admin\Schedule;

use Matrix\Exception;
use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Widgets\HtmlWidget;
use modmore\Commerce_TimeSlots\Modules\TimeSlots;

class Populate extends Page
{
    public $key = 'timeslots/schedule/populate';
    public $title = '';
    public static $permissions = ['commerce'];

    public function setUp(): Populate
    {
        $section = new SimpleSection($this->commerce, [
            'title' => $this->adapter->lexicon('commerce_timeslots.populate_daily_slots'),
        ]);

        $options = $this->getOptions();
        if (!empty($options['populate'])) {
            try {
                TimeSlots::populateDailySlots($this->commerce, ['manual' => true]);
            }
            catch (\Exception $e) {
                $this->adapter->log(\modX::LOG_LEVEL_ERROR,
                    '[Commerce TimeSlots] Unable to populate daily slots: ' . $e->getMessage());
                $section->addWidget(new HtmlWidget($this->commerce, [
                    'html' => $this->commerce->view()->render('admin/widgets/messages/success.twig', [
                        'message' => 'Unable to populate daily slots: ' . $e->getMessage(),
                    ])
                ]));
                return $this;
            }

            $section->addWidget(new HtmlWidget($this->commerce, [
                'html' => $this->commerce->view()->render('admin/widgets/messages/success.twig', [
                    'message' => 'Daily slots populated.'
                ])
            ]));
        }
        else {
            $section->addWidget(new HtmlWidget($this->commerce, [
                'html' => $this->commerce->view()->render('timeslots/admin/populate.twig', [
                    'confirm' => $this->adapter->lexicon('commerce_timeslots.populate_daily_slots.confirm'),
                    'note' => $this->adapter->lexicon('commerce_timeslots.populate_daily_slots.note'),
                    'message' => $this->adapter->lexicon('commerce_timeslots.populate_daily_slots.description'),
                    'action_url' => $this->adapter->makeAdminUrl('timeslots/schedule/populate', [
                        'populate' => true,
                    ]),
                ]),
            ]));
        }
        $this->addSection($section);
        return $this;
    }

}
