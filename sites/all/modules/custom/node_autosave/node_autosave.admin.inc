<?php
// $Id$

/**
 * @file
 * This file contains declaration of admin settings form.
 */

function node_autosave_admin_form() {
  $form = array();
  ctools_include('dependent');

  $form['content_types_config']['node_autosave_content_types'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Content types'),
    '#options' => node_type_get_names(),
    '#default_value' => variable_get('node_autosave_content_types', array()),
    '#description' => t('Select content types you want to be autosaved during the editing or creation.')
  );
  $form['autosave_config'] = array(
    '#type' => 'fieldset',
    '#title' => t('Node autosave settings'),
    '#collapsible' => FALSE,
  );
  $form['autosave_config']['node_autosave_timer'] = array(
    '#type' => 'textfield',
    '#title' => t('Save period'),
    '#description' => t('Specify the period in seconds, for node saving.'),
    '#default_value' => variable_get('node_autosave_timer', 180),
  );
  $form['autosave_config']['node_autosave_storage'] = array(
    '#type' => 'radios',
    '#title' => t('Storage place'),
    '#options' => array(
      'ctools_cache' => t('CTools object cache'),
      'session' => t('Session')
    ),
    '#default_value' => variable_get('node_autosave_storage', 'ctools_cache')
  );
  $form['autosave_config']['node_autosave_autoload'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable autoload of saved node'),
    '#default_value' => variable_get('node_autosave_autoload', 0),
  );
  $form['autosave_controls_config'] = array(
    '#type' => 'fieldset',
    '#title' => t('Controls settings'),
    '#collapsible' => FALSE,
  );

  $form['autosave_controls_config']['node_autosave_presave_button_config'] = array(
    '#type' => 'fieldset',
    '#title' => t('Presave button configuration'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['autosave_controls_config']['node_autosave_presave_button_config']['node_autosave_presave_button_position'] = array(
    '#type' => 'radios',
    '#title' => t('Button position'),
    '#options' => node_autosave_control_positions(TRUE),
    '#default_value' => variable_get('node_autosave_presave_button_position', NODE_AUTOSAVE_CONTROL_POSITION_BOTH),
    '#description' => t('Select the position of the button.')
  );
  $form['autosave_controls_config']['node_autosave_presave_button_config']['node_autosave_presave_button_text'] = array(
    '#type' => 'textfield',
    '#title' => t('Button text'),
    '#default_value' => variable_get('node_autosave_presave_button_text', t('Presave')),
    '#description' => t('If checked shows presave button on the node form, allowing user to manually save node.')
  );
  $form['autosave_controls_config']['node_autosave_presave_button_config']['node_autosave_presave_button_weight'] = array(
    '#type' => 'textfield',
    '#title' => t('Button weight'),
    '#default_value' => variable_get('node_autosave_presave_button_weight', -2),
    '#description' => t('Put the appropriate weight to position the button on the form.')
  );

  $form['autosave_controls_config']['node_autosave_restore_button_config'] = array(
    '#type' => 'fieldset',
    '#title' => t('Restore button configuration'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['autosave_controls_config']['node_autosave_restore_button_config']['node_autosave_restore_button_position'] = array(
    '#type' => 'radios',
    '#title' => t('Button position'),
    '#options' => node_autosave_control_positions(TRUE),
    '#default_value' => variable_get('node_autosave_restore_button_position', NODE_AUTOSAVE_CONTROL_POSITION_BOTH),
    '#description' => t('Select the position of the button.')
  );
  $form['autosave_controls_config']['node_autosave_restore_button_config']['node_autosave_restore_button_text'] = array(
    '#type' => 'textfield',
    '#title' => t('Button text'),
    '#default_value' => variable_get('node_autosave_restore_button_text', t('Restore')),
    '#description' => t('If checked shows restore button on the node form, allowing user to manually save node.')
  );
  $form['autosave_controls_config']['node_autosave_restore_button_config']['node_autosave_restore_button_weight'] = array(
    '#type' => 'textfield',
    '#title' => t('Button weight'),
    '#default_value' => variable_get('node_autosave_restore_button_weight', -1),
    '#description' => t('Put the appropriate weight to position the button on the form.')
  );

  $form['autosave_controls_config']['node_autosave_notice_config'] = array(
    '#type' => 'fieldset',
    '#title' => t('Notice configuration'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#description' => t('This is just a label which shows when last autosave took place.')
  );
  $form['autosave_controls_config']['node_autosave_notice_config']['node_autosave_notice_position'] = array(
    '#type' => 'radios',
    '#title' => t('Notice position'),
    '#options' => node_autosave_control_positions(TRUE),
    '#default_value' => variable_get('node_autosave_notice_position', NODE_AUTOSAVE_CONTROL_POSITION_BOTH),
    '#description' => t('Select the position of the notice.')
  );
  $form['autosave_controls_config']['node_autosave_notice_config']['node_autosave_notice_prefix'] = array(
    '#type' => 'textfield',
    '#title' => t('Notice prefix'),
    '#default_value' => variable_get('node_autosave_notice_prefix', t('Node is autosaved:')),
  );
  $form['autosave_controls_config']['node_autosave_notice_config']['node_autosave_notice_suffix'] = array(
    '#type' => 'textfield',
    '#title' => t('Notice suffix'),
    '#default_value' => variable_get('node_autosave_notice_suffix', ''),
  );

  $form['autosave_controls_config']['node_autosave_notice_config']['node_autosave_notice_date_format'] = array(
    '#type' => 'select',
    '#title' => t('Date format'),
    '#options' => array(
      'short' => format_date(REQUEST_TIME, 'short'),
      'medium' => format_date(REQUEST_TIME, 'medium'),
      'long' => format_date(REQUEST_TIME, 'long'),
      'custom' => t('Custom'),
    ),
    '#default_value' => variable_get('node_autosave_notice_date_format', 'short'),
  );
  $form['autosave_controls_config']['node_autosave_notice_config']['node_autosave_notice_custom_date_format'] = array(
    '#type' => 'textfield',
    '#title' => t('Custom date format'),
    '#description' => t('If "Custom", see <a href="http://us.php.net/manual/en/function.date.php" target="_blank">the PHP docs</a> for date formats. If "Time ago" this is the the number of different units to display, which defaults to two.'),
    '#default_value' => variable_get('node_autosave_notice_custom_date_format', ''),
    '#process' => array('ctools_dependent_process'),
    '#dependency' => array('edit-node-autosave-notice-date-format' => array('custom')),
  );

  $form['autosave_controls_config']['node_autosave_notice_config']['node_autosave_notice_weight'] = array(
    '#type' => 'textfield',
    '#title' => t('Notice weight'),
    '#default_value' => variable_get('node_autosave_notice_weight', -3),
    '#description' => t('Put the appropriate weight to position the autosave notice on the form.')
  );

  $form['autosave_controls_config']['node_autosave_top_container_config'] = array(
    '#type' => 'fieldset',
    '#title' => t('Top container configuration'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#description' => t('This is the container located in the top of the form and its used to hold node autosave module controls.')
  );
  $form['autosave_controls_config']['node_autosave_top_container_config']['node_autosave_top_container_type'] = array(
    '#type' => 'radios',
    '#title' => t('Container type'),
    '#options' => array(
      'fieldset' => t('Fieldset'),
      'markup' => t('Div container')
    ),
    '#default_value' => variable_get('node_autosave_top_container_type', 'fieldset'),
  );
  $form['autosave_controls_config']['node_autosave_top_container_config']['node_autosave_top_container_title'] = array(
    '#type' => 'textfield',
    '#title' => t('Fieldset title'),
    '#default_value' => variable_get('node_autosave_top_container_title', ''),
    '#process' => array('ctools_dependent_process'),
    '#dependency' => array('radio:node_autosave_top_container_type' => array('fieldset')),
  );

  $form['autosave_controls_config']['node_autosave_top_container_config']['node_autosave_top_container_weight'] = array(
    '#type' => 'textfield',
    '#title' => t('Container weight'),
    '#default_value' => variable_get('node_autosave_top_container_weight', -100),
    '#description' => t('This container should be in the top of the form. If its not, please decrease this value.')
  );
  $form['array_filter'] = array('#type' => 'value', '#value' => TRUE);

  return system_settings_form($form);
}

/**
 * Validation function for timer field.
 */
function node_autosave_admin_form_validate($form, &$form_state) {
  if (!is_numeric($form_state['values']['node_autosave_timer']) || $form_state['values']['node_autosave_timer'] <= 0) {
    form_set_error('node_autosave_timer', t('%title should be positive integer', array('%title' => $form['autosave_timer_config']['node_autosave_timer']['#title'])));
  }
}

/**
 * This function builds options list for "position" selects.
 * @param bool $none
 *    Indicate whether "none" element need to be added.
 * @return array
 *    List of options for positions select
 */
function node_autosave_control_positions($none = FALSE) {
  $options = array(
    NODE_AUTOSAVE_CONTROL_POSITION_TOP => t('Container in the top of the form'),
    NODE_AUTOSAVE_CONTROL_POSITION_BOTTOM  => t('Together with node buttons'),
    NODE_AUTOSAVE_CONTROL_POSITION_BOTH => t('Both')
  );
  if ($none) {
    $options[NODE_AUTOSAVE_CONTROL_POSITION_NONE] = t('None');
  }
  return $options;
}