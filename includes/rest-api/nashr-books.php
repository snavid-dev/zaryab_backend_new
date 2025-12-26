<?php

// Register REST API route for Nashr Books list (paginated)
add_action('rest_api_init', function () {
  register_rest_route('v1', '/nashr-books', array(
    'methods' => 'GET',
    'callback' => 'zaryab_get_nashr_books',
  ));
});

/**
 * Get paginated list of Nashr Books
 *
 * Query params:
 * - page (default: 1)
 * - per_page (default: 10)
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response|WP_Error
 */
function zaryab_get_nashr_books(WP_REST_Request $request)
{

  $page = max(1, (int)$request->get_param('page'));
  $per_page = (int)$request->get_param('per_page') ?: 10;

  $args = array(
    'post_type' => 'nashr-book',
    'post_status' => 'publish',
    'posts_per_page' => $per_page,
    'paged' => $page,
    'orderby' => 'date',
    'order' => 'DESC',
  );

  $query = new WP_Query($args);

  if (!$query->have_posts()) {
    return new WP_Error(
      'no_books',
      'No books found',
      array('status' => 404)
    );
  }

  $items = array();

  while ($query->have_posts()) {
    $query->the_post();
    $book_id = get_the_ID();

    $items[] = array(
      'title' => get_the_title($book_id),
      'featured_image' => get_the_post_thumbnail_url($book_id, 'full'),
      'content' => apply_filters('the_content', get_the_content()),
      'author_name' => get_field('author_name', $book_id),
      'print_date' => get_field('print_date', $book_id),
      'buy_link' => get_field('buy_link', $book_id),
    );
  }

  wp_reset_postdata();

  // Pagination meta
  $response = array(
    'data' => $items,
    'meta' => array(
      'current_page' => $page,
      'per_page' => $per_page,
      'total_posts' => (int)$query->found_posts,
      'total_pages' => (int)$query->max_num_pages,
    ),
  );

  return new WP_REST_Response($response, 200);
}
