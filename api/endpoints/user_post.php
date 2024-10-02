<?php

function api_user_post($request) {
  $email = sanitize_email($request['email']);
  $username = sanitize_text_field($request['username']);
  $password = $request['password'];

  $zipCode = sanitize_text_field($request['zipCode']);
  $zipCode = preg_replace('/[^0-9]/', '', $zipCode);

  $numberAddress = sanitize_text_field($request['numberAddress']);
  $complement = sanitize_text_field($request['complement']);

  check_and_return_error(empty($username) || empty($password) || empty($email), 'Dados incompletos!', 406) ?: null;

  check_and_return_error(email_exists($email), 'Email já cadastrado!', 403) ?: null;

  $zipCode_api_url = "https://brasilapi.com.br/api/cep/v2/" . $zipCode;
  $response = wp_remote_get($zipCode_api_url);

  if(is_wp_error($response)) {
    $error_message = $response->get_error_message();
    return new WP_Error('error', 'Erro ao buscar CEP: ' . $error_message, ['status' => 500]);
  }

  $address_data = json_decode(wp_remote_retrieve_body($response), true);

  if(empty($address_data) || isset($address_data['erro'])) {
    return new WP_Error('error', 'CEP inválido!', ['status' => 404]);
  }

  $street = $address_data['street'];
  $neighborhood = $address_data['neighborhood'];
  $city = $address_data['city'];
  $state = $address_data['state'];

  $response = wp_insert_user([
    'user_login' => $username,
    'user_email' => $email,
    'user_pass' => $password,
    'role' => 'subscriber',
    'meta_input' => [
      'zipCode' => $zipCode,
      'street' => $street,
      'neighborhood' => $neighborhood,
      'city' => $city,
      'state' => $state,
      'numberAddress' => $numberAddress,
      'complement' => $complement
    ]
  ]);

  return rest_ensure_response($response);
}

function register_api_user_post() {
  register_rest_route('api', '/user', [
    'methods' => WP_REST_Server::CREATABLE,
    'callback' => 'api_user_post',
  ]);
}

add_action('rest_api_init', 'register_api_user_post');

?>
