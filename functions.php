<?php

/*show show own post or data*/
//ref:http://wordpress.stackexchange.com/questions/149143/hide-the-post-count-behind-post-views-remove-all-published-and-trashed-in-cus
add_filter('wp_count_posts', 'wpse149143_wp_count_posts', 10, 3);

function wpse149143_wp_count_posts( $counts, $type, $perm ) {
    global $wpdb;
    if ( ! is_admin() || 'readable' !== $perm ) {
        return $counts;
    }
    $post_type_object = get_post_type_object($type);
    if (current_user_can( $post_type_object->cap->edit_others_posts ) ) {
        return $counts;
    }

    $query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s AND (post_author = %d) GROUP BY post_status";
    $results = (array) $wpdb->get_results( $wpdb->prepare( $query, $type, get_current_user_id() ), ARRAY_A );
    $counts = array_fill_keys( get_post_stati(), 0 );

    foreach ( $results as $row ) {
        $counts[ $row['post_status'] ] = $row['num_posts'];
    }

    return (object) $counts;
}
