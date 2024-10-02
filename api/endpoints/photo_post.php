<?php

function api_photo_post($request) {
  $user = wp_get_current_user();
  $user_id = $user->ID;

  check_and_return_error(($user_id === 0), 'Usuário não possui permissão!', 401) ?: null;

  $animal_name = sanitize_text_field($request['name']);
  $animal_type = sanitize_text_field($request['animalType']);
  $animal_age = sanitize_text_field($request['age']);
  $animal_weight = sanitize_text_field($request['weight']);
  $animal_gender = sanitize_text_field($request['gender']);
  $animal_breed = sanitize_text_field($request['breed']);
  $responsible_contact = sanitize_text_field($request['responsibleContact']);
  $files = $request->get_file_params();

  $animal_vaccinated = filter_var($request['vaccinated'], FILTER_VALIDATE_BOOLEAN);
  $animal_castrated = filter_var($request['castrated'], FILTER_VALIDATE_BOOLEAN);
  $animal_special_condition = filter_var($request['specialCondition'], FILTER_VALIDATE_BOOLEAN);
  $animal_special_condition_description = sanitize_text_field($request['special_condition_description']);

  if(empty($animal_name) || empty($animal_type) || empty($animal_age) || empty($animal_weight) || empty($animal_breed) || empty($animal_gender) || empty($files) || empty($responsible_contact)) {
    $response = new WP_Error('error', 'Dados incompletos!', ['status' => 422]);
    return rest_ensure_response($response);
  }

  $response = [
    'post_author' => $user_id,
    'post_type' => 'post',
    'post_status' => 'publish',
    'post_title' => $animal_name,
    'post_content' => $animal_name,
    'files' => $files,
    'meta_input' => [
      'animal_type' => $animal_type,
      'animal_age' => $animal_age,
      'animal_weight' => $animal_weight,
      'animal_gender' => $animal_gender,
      'animal_breed' => $animal_breed,
      'animal_vaccinated' => $animal_vaccinated,
      'animal_castrated' => $animal_castrated,
      'animal_special_condition' => $animal_special_condition,
      'animal_special_condition_description' => $animal_special_condition_description,
      'responsible_contact' => $responsible_contact,
      'visualizations' => 0
    ],
  ];

  $post_id = wp_insert_post($response);

  require_once ABSPATH . 'wp-admin/includes/image.php';
  require_once ABSPATH . 'wp-admin/includes/file.php';
  require_once ABSPATH . 'wp-admin/includes/media.php';

  $photo_id = media_handle_upload('img', $post_id);
  update_post_meta($post_id, 'img', $photo_id);

  return rest_ensure_response([
    'post_id' => $post_id,
    'photo_id' => $photo_id,
    'status' => 'success',
  ]);
}

function register_api_photo_post() {
  register_rest_route('api', '/photo', [
    'methods' => WP_REST_Server::CREATABLE,
    'callback' => 'api_photo_post',
  ]);
}
add_action('rest_api_init', 'register_api_photo_post');

?>
