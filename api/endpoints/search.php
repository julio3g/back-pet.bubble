<?php

function api_search_get($request) {
  global $wpdb;

  $search = $request->['_search'];
  $city = sanitize_text_field($request->['_city']) ?: 0;
  $gender = sanitize_text_field($request->['_gender']) ?: 0;
  $_total = sanitize_text_field($request->['_total']) ?: 6;
  $_page = sanitize_text_field($request->['_page']) ?: 1;

  $args = [
    'post_type' => 'post',
    's' => $search,
    'posts_per_page' => $_total,
    'paged' => $_page,
  ];

}


function register_api_search_get() {
  register_rest_route('api', '/search/(?P<id>[0-9]+)', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'api_search_get',
  ]);
}

add_action('rest_api_init', 'register_api_search_get');

?>
