<?php
// remove_action('rest_api_init', 'create_initial_rest_routes', 99);

add_filter('rest_endpoints', function ($endpoints) {
  unset($endpoints['/wp/v2/users']);
  unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
  return $endpoints;
});

$dirbase = get_template_directory();

require_once $dirbase. '/utils.php';

require_once $dirbase . '/endpoints/user_post.php';
require_once $dirbase . '/endpoints/user_get.php';

require_once $dirbase . '/endpoints/photo_post.php';
require_once $dirbase . '/endpoints/photo_get.php';
require_once $dirbase . '/endpoints/photo_delete.php';

require_once $dirbase . '/endpoints/stats_get.php';

require_once $dirbase . '/endpoints/password.php';

update_option('large_size_w', 1000);
update_option('large_size_h', 1000);
update_option('large_crop', 1);

function change_api() {
  return 'json';
}
add_filter('rest_url_prefix', 'change_api');

function expire_token() {
  return time() + (60 * 60 * 24 * 7);
}
add_action('jwt_auth_expire', 'expire_token');

function register_user_meta_fields() {
  register_meta('user', 'cep', [
      'type'         => 'string',
      'description'  => 'CEP do usuário',
      'single'       => true,
      'show_in_rest' => true,
  ]);

  register_meta('user', 'street', [
      'type'         => 'string',
      'description'  => 'Logradouro do usuário',
      'single'       => true,
      'show_in_rest' => true,
  ]);

  register_meta('user', 'neighborhood', [
      'type'         => 'string',
      'description'  => 'Bairro do usuário',
      'single'       => true,
      'show_in_rest' => true,
  ]);

  register_meta('user', 'city', [
      'type'         => 'string',
      'description'  => 'Cidade do usuário',
      'single'       => true,
      'show_in_rest' => true,
  ]);

  register_meta('user', 'state', [
      'type'         => 'string',
      'description'  => 'Estado do usuário',
      'single'       => true,
      'show_in_rest' => true,
  ]);

  register_meta('user', 'number', [
    'type'         => 'string',
    'description'  => 'Número do usuário',
    'single'       => true,
    'show_in_rest' => true,
  ]);
}
add_action('init', 'register_user_meta_fields');

function add_custom_user_meta_to_rest_api($response, $user, $request) {
  $response->data['cep'] = get_user_meta($user->ID, 'cep', true);
  $response->data['neighborhood'] = get_user_meta($user->ID, 'neighborhood', true);
  $response->data['street'] = get_user_meta($user->ID, 'street', true);
  $response->data['city'] = get_user_meta($user->ID, 'city', true);
  $response->data['state'] = get_user_meta($user->ID, 'state', true);

  return $response;
}
add_filter('rest_prepare_user', 'add_custom_user_meta_to_rest_api', 10, 3);

?>
