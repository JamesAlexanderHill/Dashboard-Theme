<?php
/**
 * Dashboard Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Dashboard_Theme
 * @since Dashboard Theme 1.0
 */

//Edit Roles for users
function role_changes() {
  add_role( 'coach', 'Coach', array('read', 'edit_posts'));
  remove_role('subscriber');
  remove_role('editor');
  remove_role('author');
  remove_role('contributor');
}
add_action( 'init', 'role_changes' );

// //show columns in custom post types
// add_filter( 'manage_edit-trips_columns', 'my_edit_trips_columns' ) ;
// function my_edit_trips_columns( $columns ) {
//
// 	// // store value temporarily.
// 	// $date_val = $columns['date'];
//   //
// 	// // Unset original index.
// 	// unset( $columns['date'] );
//   //
// 	// $columns['title'] = __( 'Trip name' );
// 	// $columns['region'] = __( 'Region' );
// 	// $columns['start_date'] = __( 'Start date' );
// 	// $columns['date'] = $date_val;
//   $columns['start_date'] = __( 'Start date' );
//
// 	return $columns;
// }
function client_columns($columns) {
    // unset( $columns['title']  );
    // unset( $columns['author'] );
    // unset( $columns['date']   );
    //
    // $columns['product_number'] = 'Product Number';
    // $columns['custom_handler'] = 'Nice name';
    $columns['role'] = __( 'Role' );
    $columns['grade'] = __( 'Grade' );
    return $columns;
}
add_filter( 'client_posts_columns', 'client_columns' );
