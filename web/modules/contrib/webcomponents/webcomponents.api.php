<?php
/**
 * @file
 * Documentation for web components to bring new components into scope.
 */

/**
 * Implements hook_register_component_format().
 * @example  webcomponents_polymer_register_component_format
 * @return  array   documented below
 */
function hook_register_component_format() {
  return array(
    // the machine name for the project in question
    'polymer1' => array(
      // processing callback function
      'process' => '_webcomponents_polymer_process_component',
    ),
  );
}

/**
 * Implements hook_register_component_format_alter().
 */
function hook_register_component_format_alter(&$formats) {
  // allow modidying a process hook to implement your own processing methodology
}

/**
 * Implements hook_webcomponents().
 * @example   webcomponents_polymer_webcomponents
 * @return  array   keyed by format with an array of files
 */
function hook_webcomponents() {
  return array(
    // machine name of the format / bundle type for the entity
    'newformat' => array(
      // paths to files that bring in components for this type of bundle
      'filelocation/newformat/component-1.html',
      'filelocation/newformat/component-2.html',
      'filelocation/newformat/component-3.html',
    ),
  );
}

/**
 * Implements hook_webcomponents_alter().
 */
function hook_webcomponents_alter($components) {
  // add / remove files you might not want pulled in as legit components
}
