<?php
/**
 * ClickCollect for Commerce.
 *
 * Copyright 2021 by Mark Hamstra <support@modmore.com>
 *
 * This file is meant to be used with Commerce by modmore. A valid Commerce license is required.
 *
 * @package commerce_clickcollect
 * @license See core/components/commerce_clickcollect/docs/license.txt
 */

$xpdo_meta_map['clcoSchedule']= array (
  'package' => 'commerce_clickcollect',
  'version' => '1.1',
  'table' => 'commext_clickcollect_schedule',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'name' => '',
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '190',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
  ),
  'composites' => 
  array (
    'Slots' => 
    array (
      'class' => 'clcoScheduleSlot',
      'local' => 'id',
      'foreign' => 'schedule',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
