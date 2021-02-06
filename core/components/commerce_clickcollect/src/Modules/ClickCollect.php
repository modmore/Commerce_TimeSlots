<?php
namespace modmore\Commerce_ClickCollect\Modules;

use modmore\Commerce\Events\Admin\GeneratorEvent;
use modmore\Commerce\Events\Admin\TopNavMenu;
use modmore\Commerce\Events\Checkout;
use modmore\Commerce\Modules\BaseModule;
use modmore\Commerce_ClickCollect\Admin\Schedule\Create;
use modmore\Commerce_ClickCollect\Admin\Schedule\Delete;
use modmore\Commerce_ClickCollect\Admin\Schedule\Duplicate;
use modmore\Commerce_ClickCollect\Admin\Schedule\Overview;
use modmore\Commerce_ClickCollect\Admin\Schedule\Update;
use Symfony\Component\EventDispatcher\EventDispatcher;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

class ClickCollect extends BaseModule {

    public function getName()
    {
        $this->adapter->loadLexicon('commerce_clickcollect:default');
        return $this->adapter->lexicon('commerce_clickcollect');
    }

    public function getAuthor()
    {
        return 'modmore';
    }

    public function getDescription()
    {
        return $this->adapter->lexicon('commerce_clickcollect.description');
    }

    public function initialize(EventDispatcher $dispatcher)
    {
        // Load our lexicon
        $this->adapter->loadLexicon('commerce_clickcollect:default');

        // Add the xPDO package, so Commerce can detect the derivative classes
        $root = dirname(__DIR__, 2);
        $path = $root . '/model/';
        $this->adapter->loadPackage('commerce_clickcollect', $path);

        // Add template path to twig
        $root = dirname(__DIR__, 2);
        $this->commerce->view()->addTemplatesPath($root . '/templates/');

        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_INIT_GENERATOR, [$this, 'initGenerator']);
        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_GET_MENU, [$this, 'getMenu']);
        $dispatcher->addListener(\Commerce::EVENT_CHECKOUT_BEFORE_STEP, [$this, 'beforeCheckoutStep']);
    }

    public function initGenerator(GeneratorEvent $event)
    {
        $generator = $event->getGenerator();

        $generator->addPage('clickcollect', Overview::class);
        $generator->addPage('clickcollect/schedule', Overview::class);
        $generator->addPage('clickcollect/schedule/add', Create::class);
        $generator->addPage('clickcollect/schedule/edit', Update::class);
        $generator->addPage('clickcollect/schedule/delete', Delete::class);
        $generator->addPage('clickcollect/schedule/duplicate', Duplicate::class);
        $generator->addPage('clickcollect/schedule/slot/add', \modmore\Commerce_ClickCollect\Admin\Schedule\Slot\Create::class);
        $generator->addPage('clickcollect/schedule/slot/edit', \modmore\Commerce_ClickCollect\Admin\Schedule\Slot\Update::class);
        $generator->addPage('clickcollect/schedule/slot/delete', \modmore\Commerce_ClickCollect\Admin\Schedule\Slot\Delete::class);
        $generator->addPage('clickcollect/schedule/slot/duplicate', \modmore\Commerce_ClickCollect\Admin\Schedule\Slot\Duplicate::class);

        $generator->addPage('clickcollect/planning', \modmore\Commerce_ClickCollect\Admin\Planning\Overview::class);
        $generator->addPage('clickcollect/planning/edit', \modmore\Commerce_ClickCollect\Admin\Planning\Update::class);
        $generator->addPage('clickcollect/planning/slot/add', \modmore\Commerce_ClickCollect\Admin\Planning\Slot\Create::class);
        $generator->addPage('clickcollect/planning/slot/edit', \modmore\Commerce_ClickCollect\Admin\Planning\Slot\Update::class);
        $generator->addPage('clickcollect/planning/slot/delete', \modmore\Commerce_ClickCollect\Admin\Planning\Slot\Delete::class);
        $generator->addPage('clickcollect/planning/slot/duplicate', \modmore\Commerce_ClickCollect\Admin\Planning\Slot\Duplicate::class);
    }

    public function getMenu(TopNavMenu $event)
    {
        $items = $event->getItems();

        $items = $this->insertInArray($items, [
            'clickcollect' => [
                'name' => $this->adapter->lexicon('commerce_clickcollect'),
                'key' => 'clickcollect',
                'icon' => 'icon icon-shopping-cart',
                'link' => $this->adapter->makeAdminUrl('clickcollect/schedule'),
                'submenu' => [
//                    [
//                        'name' => $this->adapter->lexicon('commerce_clickcollect.orders'),
//                        'key' => 'clickcollect/orders',
//                        'icon' => 'icon icon-shopping-cart',
//                        'link' => $this->adapter->makeAdminUrl('clickcollect/orders'),
//                    ],
                    [
                        'name' => $this->adapter->lexicon('commerce_clickcollect.planning'),
                        'key' => 'clickcollect/planning',
                        'icon' => 'icon icon-calendar',
                        'link' => $this->adapter->makeAdminUrl('clickcollect/planning'),
                    ],
                    [
                        'name' => $this->adapter->lexicon('commerce_clickcollect.schedule'),
                        'key' => 'clickcollect/schedule',
                        'icon' => 'icon icon-bars',
                        'link' => $this->adapter->makeAdminUrl('clickcollect/schedule'),
                    ],
                ]
            ]
        ], 4);

        $event->setItems($items);
    }

    public function beforeCheckoutStep(Checkout $event)
    {
        $order = $event->getOrder();
        $step = $event->getStepKey();
        $response = $event->getResponse();

        $canChange =
            $order->getState() === \comOrder::STATE_CART
            && !$order->getCurrentTransaction()
            && $step !== 'thank-you';

        $shipments = $order->getShipments();
        foreach ($shipments as $shipment) {
            $method = $shipment->getShippingMethod();
            if (!$method) {
                continue;
            }

            if ($method->get('class_key') !== \ClickCollectShippingMethod::class) {
                continue;
            }

            $slotId = $shipment->getProperty('clickcollect_slot');
            if (!$slotId) {
                if ($step === 'payment') {
                    $response->addError($this->adapter->lexicon('commerce_clickcollect.select_a_slot'));
                    $response->setRedirect('checkout', ['step' => 'shipping']);
                }
                continue;
            }

            /** @var \clcoDateSlot $slot */
            $slot = $this->adapter->getObject(\clcoDateSlot::class, ['id' => (int)$slotId]);
            if (!$slot) {
                $shipment->unsetProperty('clickcollect_slot');
                $shipment->unsetProperty('clickcollect_slot_info');
                $shipment->unsetProperty('clickcollect_slot_autoselected');
                $shipment->save();

                if ($step === 'payment') {
                    $response->addError($this->adapter->lexicon('commerce_clickcollect.select_a_slot'));
                    $response->setRedirect('checkout', ['step' => 'shipping']);
                }
                continue;
            }

            if ($canChange && !$slot->isAvailable()) {
                $shipment->unsetProperty('clickcollect_slot');
                $shipment->unsetProperty('clickcollect_slot_info');
                $shipment->unsetProperty('clickcollect_slot_autoselected');
                $shipment->save();

                $response->addError($this->adapter->lexicon('commerce_clickcollect.selected_slot_no_longer_available'));
                $response->setRedirect('checkout', ['step' => 'shipping']);
            }
        }
    }

    public function getModuleConfiguration(\comModule $module)
    {
        $fields = [];

        // A more detailed description to be shown in the module configuration. Note that the module description
        // ({@see self:getDescription}) is automatically shown as well.
//        $fields[] = new DescriptionField($this->commerce, [
//            'description' => $this->adapter->lexicon('commerce_clickcollect.module_description'),
//        ]);

        return $fields;
    }

    private function insertInArray($array,$values,$offset)
    {
        return array_slice($array, 0, $offset, true) + $values + array_slice($array, $offset, NULL, true);
    }
}
