<?php
/**
 * Commerce_Timeslots
 *
 * Copyright 2021 by Mark Hamstra <mark@modmore.com>
 *
 * This file is part of Commerce_Timeslots, developed for modmore.
 *
 * It is built to be used with the MODX Revolution CMS.
 *
 * @category commerce
 * @package commerce_timeslots
 * @author Mark Hamstra <mark@modmore.com>
 * @license See core/components/commerce/docs/license.txt
 * @link https://www.modmore.com/commerce/
 */
class Commerce_Timeslots {

    public $modx = null;
    public $commerce = null;
    public $adapter = null;
    public $namespace = 'commerce_timeslots';
    public $cache = null;
    public $options = [];

    public function __construct(modX &$modx, array $options = []) {
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $options, 'commerce_timeslots');

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/commerce_timeslots/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/commerce_timeslots/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/commerce_timeslots/');

        $this->options = array_merge([
            'namespace' => $this->namespace,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'connectorUrl' => $assetsUrl . 'connector.php'
        ], $options);

        // Load the Commerce service
        $commercePath = $modx->getOption('commerce.core_path', null, MODX_CORE_PATH . 'components/commerce/');
        $this->commerce = $this->modx->getService('commerce','Commerce',$commercePath . 'model/commerce/',$options);
        if (!($this->commerce instanceof \Commerce)) $this->modx->log(modX::LOG_LEVEL_ERROR,'Couldn\'t load Commerce service!');

        $this->adapter = $this->commerce->adapter;
        $this->adapter->loadLexicon('commerce:default');

        $this->adapter->loadPackage('commerce_timeslots', $this->getOption('modelPath'));
        $this->adapter->loadLexicon('commerce_timeslots:default');
    }

    /**
     * @param array $params
     * @return string
     */
    public function getTimeslots(array $params): string
    {
        // Check if a custom template was provided - grid template is used by default
        $tpl = $params['tpl'];
        if(!$tpl) {
            $tpl = 'timeslots/frontend/snippet_grid.twig';
        }

        // Check if a shipping method id was specified ( 0 returns all )
        $shippingMethodId = $params['shippingMethod'] ?? 0;

        $shippingMethods = $this->getShippingMethods($shippingMethodId);
        if(empty($shippingMethods)) return '';

        return $this->render($shippingMethods, $tpl);
    }

    /**
     * Renders a grid with all available timeslots for each shipping method
     * @param $shippingMethods
     * @return string
     */
    public function render($shippingMethods, $tpl): string
    {
        $output = '';
        foreach($shippingMethods as $shippingMethod) {
            if(!$shippingMethod instanceof TimeSlotsShippingMethod) continue;

            $options = $shippingMethod->getAvailableSlots();

            $output .= $this->commerce->view()->render($tpl, [
                'method'    => $shippingMethod->toArray(),
                'options'   => $options,
            ]);
        }
        return $output;
    }

    /**
     * Retrieves an array of TimeslotsShippingMethods
     * @param int $id
     * @return array
     */
    public function getShippingMethods(int $id) : array
    {
        $c = $this->adapter->newQuery(TimeSlotsShippingMethod::class);

        $where = [];
        if ($this->commerce->isTestMode()) {
            $where['enabled_in_test:='] = true;
        } else {
            $where['enabled_in_live:='] = true;
        }
        $where['AND:class_key:='] = TimeSlotsShippingMethod::class;
        if($id > 0) {
            $where['AND:`id`:='] = $id;
        }
        $c->where($where);

        return $this->adapter->getCollection(TimeSlotsShippingMethod::class,$c);
    }


    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = [], $default = null) {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }
        return $option;
    }

}