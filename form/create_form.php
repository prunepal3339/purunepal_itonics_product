<?php

function purunepal_itonics_products_create_form($form, &$form_state)
{
  $form = [
    'title' => [
      '#type' => 'textfield',
      '#title' => t('Product Title'),
      '#size' => 60,
      '#maxlength' => 191,
      '#required' => TRUE,
    ],
    'summary' => [
      '#type' => 'textarea',
      '#title' => 'Product Summary',
    ],
    'description' => [
      '#type' => 'text_format',
      '#title' => 'Product Description',
      '#format' => 'filtered_html',
    ],
    'category' => [
      '#type' => 'select',
      '#title' => t('Category'),
      '#multiple' => TRUE,
      '#required' => TRUE,
      '#options' => [
        'industry' => 'Industry',
        'functionality' => 'Functionality',
        'customer_needs' => 'Customer Needs',
        'customer_preferences' => 'Customer Preferences',
        'demographics' => 'Demographics'
      ]
    ],
    'type' => [
      '#type' => 'radios',
      '#title' => t('Type'),
      '#required' => TRUE,
      '#options' => [
        'consumer_products' => 'Consumer Products',
        'convenience_products' => 'Convenience Products',
        'shopping_products' => 'Shopping Products',
        'specialty_products' => 'Specialty Products'
      ]
    ],
    'owner_email' => [
      '#type' => 'textfield',
      '#title' => t('Owner Email'),
      '#required' => TRUE,
      '#element_validate' => [
        'purunepal_itonics_create_from_email_validate'
      ]
    ],
    'expiry_date' => [
      '#type' => 'date',
      '#title' => 'Expiry Date',
      '#required' => TRUE,
    ],  
    'submit' => [
      '#type' => 'submit',
      '#value' => t('Save')
    ],
    '#method' => 'post',
  ];

  return $form;
}

function purunepal_itonics_create_from_email_validate($element, &$form_state, $form)
{
  $value = $element['#value'];
  if (!valid_email_address($value)) {
    form_error($element, t('Please input validate email address'));
  }
}

function purunepal_itonics_products_create_form_submit($form, &$form_state)
{
  $data = $form_state['input'];
  db_insert(TABLE_NAME)
    ->fields([
      'title' => $data['title'],
      'description' => $data['description']['value'],
      'summary' => $data['summary'],
      'category' => implode(',', $data['category']),
      'type' => $data['type'],
      'owner_email' => $data['owner_email'],
      'expiry_date' => get_timestamp_from_date_array($data['expiry_date'])
    ])
    ->execute();

  drupal_set_message(t('Product Created Successfully'));
}

if (!function_exists('get_timestamp_from_date_array')){
  function get_timestamp_from_date_array($expiry_date_array) {
    if (isset($expiry_date_array) && is_array($expiry_date_array)) {
      $expiry_date = sprintf(
        '%04d-%02d-%02d',
        $expiry_date_array['year'],
        $expiry_date_array['month'],
        $expiry_date_array['day']
      );
      $expiry_timestamp = strtotime($expiry_date);
  } else {
      $expiry_timestamp = 0;
  }
  return $expiry_timestamp;
}
}
if (!function_exists('get_date_array_from_timestamp')) {
  function get_date_array_from_timestamp($timestamp) {
    $date = getdate($timestamp);
    return [
      'year' => $date['year'],
      'month' => $date['mon'],
      'day' => $date['mday']
    ];
  }
}