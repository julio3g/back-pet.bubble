<?php

function photo_data($post) {
  $post_meta = get_post_meta($post->ID);
  $source_img = wp_get_attachment_image_src($post_meta['img'][0], 'large')[0];
  $user = get_userdata($post->post_author);

  $user_id = $user->ID;

  // Pegando os meta dados do usuário
  $zipCode = get_user_meta($user_id, 'zipCode', true);
  $street = get_user_meta($user_id, 'street', true);
  $neighborhood = get_user_meta($user_id, 'neighborhood', true);
  $city = get_user_meta($user_id, 'city', true);
  $state = get_user_meta($user_id, 'state', true);
  $numberAddress = get_user_meta($user_id, 'numberAddress', true);
  $complement = get_user_meta($user_id, 'complement', true);

  return [
    'id' => $post->ID,
    'author' => $user->user_login,
    'title' => $post->post_title,
    'date' => $post->post_date,
    'src' => $source_img,
    'animal_type' => $post_meta['animal_type'][0],
    'animal_age' => $post_meta['animal_age'][0],
    'animal_breed' => $post_meta['animal_breed'][0],
    'animal_gender' => $post_meta['animal_gender'][0],
    'animal_vaccinated' => $post_meta['animal_vaccinated'][0],
    'animal_castrated' => $post_meta['animal_castrated'][0],
    'animal_special_condition' => $post_meta['animal_special_condition'][0],
    'animal_special_condition_description' => $post_meta['animal_special_condition_description'][0],
    'responsible_contact' => $post_meta['responsible_contact'][0],
    'visualizations' => $post_meta['visualizations'][0],
    'user' => [
      'id' => $user_id,
      'username' => $user->user_login,
      'email' => $user->user_email,
      'roles' => $user->roles,
      'name' => $user->display_name,
      'zipCode' => $zipCode,
      'state' => $state,
      'city' => $city,
      'neighborhood' => $neighborhood,
      'street' => $street,
      'numberAddress' => $numberAddress,
      'complement' => $complement
    ]
  ];
}

function api_photo_get($request) {
  $post_id = $request['id'];
  $post = get_post($post_id);

  check_and_return_error((!isset($post) || empty($post_id)), 'Post não encontrado!', 404) ?: null;

  $photo = photo_data($post);
  $photo['visualizations'] = (int) $photo['visualizations'] + 1;
  update_post_meta($post_id, 'visualizations', $photo['visualizations']);

  $response = $photo;

  return rest_ensure_response($response);
}

function register_api_photo_get() {
  register_rest_route('api', '/photo/(?P<id>[0-9]+)', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'api_photo_get',
  ]);
}

add_action('rest_api_init', 'register_api_photo_get');

function api_photos_get($request) {
  $_total = sanitize_text_field($request['_total']) ?: 6;
  $_page = sanitize_text_field($request['_page']) ?: 1;
  $_user = sanitize_text_field($request['_user']) ?: 0;

  if (!is_numeric($_user)) {
    $user = get_user_by('login', $_user);
    $_user = $user->ID;
  }

  $args = [
    'post_type' => 'post',
    'author' => $_user,
    'posts_per_page' => $_total,
    'paged' => $_page,
  ];

  $query = new WP_Query($args);
  $posts = $query->posts;

  $photos = [];
  if ($posts) {
    foreach ($posts as $post) {
      $photos[] = photo_data($post);
    }
  }

  return rest_ensure_response($photos);
}

function register_api_photos_get() {
  register_rest_route('api', '/photo', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'api_photos_get',
  ]);
}

add_action('rest_api_init', 'register_api_photos_get');


?>
