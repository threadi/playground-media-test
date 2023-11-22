<?php
/**
 * Plugin Name:       Playground media test
 * Description:       Example of missing /wordpress/wp-includes/images/media directory.
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Version:           1.0
 * Author:            Thomas Zwirner
 * Author URI:		  https://www.thomaszwirner.de
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       playground-media-test
 */

/**
 * Import example file on plugin activation.
 *
 * @return void
 */
function playground_media_test_activation(): void {
    $file = trailingslashit(dirname(__FILE__ )).'example.png';

    // Prepare an array of post data for the attachment.
    $attachment = array(
        'name'     => basename( $file ),
        'tmp_name' => $file,
    );

    // Insert the attachment by prevent removing the original file and get its attachment ID.
    $attachment_id = media_handle_sideload( $attachment, 0, null, array( 'post_author' => get_current_user_id() ) );

    // get attachment as object.
    if ( absint( $attachment_id ) > 0 ) {
        update_post_meta( $attachment_id, 'playground-media-test-example', 1 );
    }
}
register_activation_hook( __FILE__, 'playground_media_test_activation' );

/**
 *
 */
add_action( 'init', function() {
    // get example file
    $query = array(
        'post_type' => 'attachment',
        'post_status' => 'any',
        'meta_query' => array(
            array(
                'key' => 'playground-media-test-example',
                'compare' => 'EXIST'
            )
        ),
        'fields' => 'ids'
    );
    $results = new WP_Query( $query );

    if( 1 === $results->post_count ) {
        wp_prepare_attachment_for_js($results->posts[0]);
    }
} );