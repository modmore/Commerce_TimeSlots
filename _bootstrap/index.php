<?php
/* Get the core config */
$componentPath = dirname(__DIR__);
if (!file_exists($componentPath.'/config.core.php')) {
    die('ERROR: missing '.$componentPath.'/config.core.php file defining the MODX core path.');
}

echo "<pre>";
/* Boot up MODX */
echo "Loading modX...\n";
require_once $componentPath . '/config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
echo "Initializing manager...\n";
$modx->initialize('mgr');
$modx->getService('error','error.modError', '', '');
$modx->setLogTarget('HTML');



/* Namespace */
if (!createObject('modNamespace',array(
    'name' => 'commerce_timeslots',
    'path' => $componentPath.'/core/components/commerce_timeslots/',
    'assets_path' => $componentPath.'/assets/components/commerce_timeslots/',
),'name', false)) {
    echo "Error creating namespace commerce_timeslots.\n";
}

/* Path settings */
if (!createObject('modSystemSetting', array(
    'key' => 'commerce_timeslots.core_path',
    'value' => $componentPath.'/core/components/commerce_timeslots/',
    'xtype' => 'textfield',
    'namespace' => 'commerce_timeslots',
    'area' => 'Paths',
    'editedon' => time(),
), 'key', false)) {
    echo "Error creating commerce_timeslots.core_path setting.\n";
}

if (!createObject('modSystemSetting', array(
    'key' => 'commerce_timeslots.assets_path',
    'value' => $componentPath.'/assets/components/commerce_timeslots/',
    'xtype' => 'textfield',
    'namespace' => 'commerce_timeslots',
    'area' => 'Paths',
    'editedon' => time(),
), 'key', false)) {
    echo "Error creating commerce_timeslots.assets_path setting.\n";
}

/* Fetch assets url */
$requestUri = $_SERVER['REQUEST_URI'] ?: __DIR__ . '/_bootstrap/index.php';
$bootstrapPos = strpos($requestUri, '_bootstrap/');
$requestUri = rtrim(substr($requestUri, 0, $bootstrapPos), '/').'/';
$assetsUrl = "{$requestUri}assets/components/commerce_timeslots/";

if (!createObject('modSystemSetting', array(
    'key' => 'commerce_timeslots.assets_url',
    'value' => $assetsUrl,
    'xtype' => 'textfield',
    'namespace' => 'commerce_timeslots',
    'area' => 'Paths',
    'editedon' => time(),
), 'key', false)) {
    echo "Error creating commerce_timeslots.assets_url setting.\n";
}


//$settings = include dirname(__DIR__) . '/_build/data/settings.php';
//foreach ($settings as $key => $opts) {
//    $val = $opts['value'];
//
//    if (isset($opts['xtype'])) $xtype = $opts['xtype'];
//    elseif (is_int($val)) $xtype = 'numberfield';
//    elseif (is_bool($val)) $xtype = 'modx-combo-boolean';
//    else $xtype = 'textfield';
//
//    if (!createObject('modSystemSetting', array(
//        'key' => 'commerce_timeslots.' . $key,
//        'value' => $opts['value'],
//        'xtype' => $xtype,
//        'namespace' => 'commerce_timeslots',
//        'area' => $opts['area'],
//        'editedon' => time(),
//    ), 'key', false)) {
//        echo "Error creating commerce_timeslots.".$key." setting.\n";
//    }
//}


$path = $modx->getOption('commerce.core_path', null, MODX_CORE_PATH . 'components/commerce/') . 'model/commerce/';
$params = ['mode' => $modx->getOption('commerce.mode')];
/** @var Commerce|null $commerce */
$commerce = $modx->getService('commerce', 'Commerce', $path, $params);
if (!($commerce instanceof Commerce)) {
    die("Couldn't load Commerce class");
}

// Make sure our module can be loaded. In this case we're using a composer-provided PSR4 autoloader.
include $componentPath . '/core/components/commerce_timeslots/vendor/autoload.php';

// Grab the path to our namespaced files
$modulePath = $componentPath . '/core/components/commerce_timeslots/src/Modules/';

// Instruct Commerce to load modules from our directory, providing the base namespace and module path twice
$commerce->loadModulesFromDirectory($modulePath, 'modmore\\Commerce_TimeSlots\\Modules\\', $modulePath);

// Load our xPDO package
$root = $componentPath.'/core/components/commerce_timeslots/';
$path = $root . '/model/';
if (!$modx->addPackage('commerce_timeslots', $path)) {
    echo "Failed loading xPDO package - is the path correct?\n";
}

// Load the xPDOManager to create our custom tables
$manager = $modx->getManager();

$containers = [
    ctsDate::class,
    ctsDateSlot::class,
    ctsSchedule::class,
    ctsScheduleSlot::class,
];

foreach ($containers as $container) {
    $manager->createObjectContainer($container);
}

$manager->addIndex(ctsDate::class, 'for_date');
$manager->addIndex(ctsDate::class, 'schedule');

$manager->addField(ctsDateSlot::class, 'shipping_method', ['after' => 'base_slot']);
$manager->addIndex(ctsDateSlot::class, 'shipping_method');
$manager->addIndex(ctsDateSlot::class, 'for_date');
$manager->addField(ctsDateSlot::class, 'price', ['after' => 'available_reservations']);
$manager->addField(ctsScheduleSlot::class, 'price', ['after' => 'max_reservations']);

// Clear the cache
$modx->cacheManager->refresh();

echo "Done.";


/**
 * Creates an object.
 *
 * @param string $className
 * @param array $data
 * @param string $primaryField
 * @param bool $update
 * @return bool
 */
function createObject ($className = '', array $data = array(), $primaryField = '', $update = true) {
    global $modx;
    /* @var xPDOObject $object */
    $object = null;

    /* Attempt to get the existing object */
    if (!empty($primaryField)) {
        if (is_array($primaryField)) {
            $condition = array();
            foreach ($primaryField as $key) {
                $condition[$key] = $data[$key];
            }
        }
        else {
            $condition = array($primaryField => $data[$primaryField]);
        }
        $object = $modx->getObject($className, $condition);
        if ($object instanceof $className) {
            if ($update) {
                $object->fromArray($data);
                return $object->save();
            } else {
                $condition = $modx->toJSON($condition);
                echo "Skipping {$className} {$condition}: already exists.\n";
                return true;
            }
        }
    }

    /* Create new object if it doesn't exist */
    if (!$object) {
        $object = $modx->newObject($className);
        $object->fromArray($data, '', true);
        return $object->save();
    }

    return false;
}
