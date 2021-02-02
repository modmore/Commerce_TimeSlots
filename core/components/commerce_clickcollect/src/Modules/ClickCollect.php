<?php
namespace modmore\Commerce_ClickCollect\Modules;

use modmore\Commerce\Admin\Configuration\About\ComposerPackages;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Events\Admin\PageEvent;
use modmore\Commerce\Modules\BaseModule;
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
//        $root = dirname(__DIR__, 2);
//        $path = $root . '/model/';
//        $this->adapter->loadPackage('commerce_clickcollect', $path);

        // Add template path to twig
//        $root = dirname(__DIR__, 2);
//        $this->commerce->view()->addTemplatesPath($root . '/templates/');
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
}
