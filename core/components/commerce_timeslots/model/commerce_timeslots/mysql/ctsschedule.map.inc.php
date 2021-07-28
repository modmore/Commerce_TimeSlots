<?php
/**
 * TimeSlots for Commerce.
 *
 * Copyright 2021 by Mark Hamstra <support@modmore.com>
 *
 * This file is meant to be used with Commerce by modmore. A valid Commerce license is required.
 *
 * @package commerce_timeslots
 * @license See core/components/commerce_timeslots/docs/license.txt
 */

$xpdo_meta_map['ctsSchedule']= array (
  'package' => 'commerce_timeslots',
  'version' => '1.1',
  'table' => 'commext_timeslots_schedule',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'name' => '',
    'repeat' => 0,
    'repeat_days' => NULL,
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
    'repeat' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'repeat_days' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'array',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'repeat' => 
    array (
      'alias' => 'repeat',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'repeat' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Slots' => 
    array (
      'class' => 'ctsScheduleSlot',
      'local' => 'id',
      'foreign' => 'schedule',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
