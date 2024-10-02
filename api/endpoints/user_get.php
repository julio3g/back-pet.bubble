<?php


function api_user_get($request) {
  $user = wp_get_current_user();
  $user_id = $user->ID;

  check_and_return_error($user_id === 0, 'Usuário não logado!', 401) ?: null;

  $zipCode = get_user_meta($user_id, 'zipCode', true);
  $street = get_user_meta($user_id, 'street', true);
  $neighborhood = get_user_meta($user_id, 'neighborhood', true);
  $city = get_user_meta($user_id, 'city', true);
  $state = get_user_meta($user_id, 'state', true);
  $numberAddress = get_user_meta($user_id, 'numberAddress', true);
  $complement = get_user_meta($user_id, 'complement', true);

  $response = [
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
  ];

  return rest_ensure_response($response);
}

function register_api_user_get() {
  register_rest_route('api', '/user', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'api_user_get',
  ]);
}
add_action('rest_api_init', 'register_api_user_get');

function api_user_get_by_username($request) {
  $username = $request['username'];
  $user = get_user_by('login', $username);
  $user_id = $user->ID;

  check_and_return_error(!$user === 0, 'Usuário não encontrado!', 404) ?: null;

  $zipCode = get_user_meta($user_id, 'zipCode', true);
  $street = get_user_meta($user_id, 'street', true);
  $neighborhood = get_user_meta($user_id, 'neighborhood', true);
  $city = get_user_meta($user_id, 'city', true);
  $state = get_user_meta($user_id, 'state', true);
  $numberAddress = get_user_meta($user_id, 'numberAddress', true);
  $complement = get_user_meta($user_id, 'complement', true);

  $response = [
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
  ];

  return rest_ensure_response($response);
}

function register_api_user_get_by_username() {
  register_rest_route('api', '/user/(?P<username>[a-zA-Z0-9_-]+)', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'api_user_get_by_username',
  ]);
}
add_action('rest_api_init', 'register_api_user_get_by_username');

?>
