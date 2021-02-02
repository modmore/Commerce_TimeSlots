<?php
namespace modmore\Commerce_ClickCollect\Modules;

use modmore\Commerce\Events\Admin\GeneratorEvent;
use modmore\Commerce\Events\Admin\TopNavMenu;
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
//        $root = dirname(__DIR__, 2);
//        $this->commerce->view()->addTemplatesPath($root . '/templates/');

        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_INIT_GENERATOR, [$this, 'initGenerator']);
        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_GET_MENU, [$this, 'getMenu']);
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
//                    [
//                        'name' => $this->adapter->lexicon('commerce_clickcollect.planning'),
//                        'key' => 'clickcollect/planning',
//                        'icon' => 'icon icon-calendar',
//                        'link' => $this->adapter->makeAdminUrl('clickcollect/planning'),
//                    ],
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
