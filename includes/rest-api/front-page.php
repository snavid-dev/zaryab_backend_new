<?php

// Register REST API route for Front Page
add_action('rest_api_init', function () {
  register_rest_route('v1', '/front-page', array(
    'methods' => 'GET',
    'callback' => 'zaryab_get_front_page',
  ));
});

/**
 * Get Front Page data including ACF ribbon field
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response|WP_Error
 */
function zaryab_get_front_page(WP_REST_Request $request)
{

  // Get Front Page ID from WordPress settings
  $page_id = get_option('page_on_front');

  if (!$page_id) {
    return new WP_Error(
      'no_front_page',
      'Front page is not set',
      array('status' => 404)
    );
  }

  $page = get_post($page_id);

  // Build response data
  $data = array(
    'title' => get_the_title($page_id),
    'ribbon' => get_field('ribbon', $page_id), // ACF textarea
    'general_title' => get_field('general_title', $page_id), // ACF textarea
    'persian_image' => get_field('persian_image', $page_id), // ACF textarea
    'persian_title' => get_field('persian_title', $page_id), // ACF textarea
    'persian_content' => get_field('persian_content', $page_id), // ACF textarea
    'pashto_image' => get_field('pashto_image', $page_id), // ACF textarea
    'pashto_title' => get_field('pashto_title', $page_id), // ACF textarea
    'pashto_content' => get_field('pashto_content', $page_id), // ACF textarea
  );

  return new WP_REST_Response($data, 200);
}
