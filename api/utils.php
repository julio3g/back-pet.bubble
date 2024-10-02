<?php
function check_and_return_error($condition, $error_message, $status_code = 400) {
  if ($condition) {
    $response = new WP_Error('error', $error_message, ['status' => $status_code]);
    return rest_ensure_response($response);
  }
  return false;
}
