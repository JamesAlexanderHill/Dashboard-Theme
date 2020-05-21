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

//custom columns for CLIENT
add_filter('manage_client_posts_columns' , array($this,'client_columns'));
public function client_columns($columns){
	$date_val = $columns['date'];
  $name_val = $columns['title'];
	unset($columns['date']);
  unset($columns['title']);

  //reset values
  // $columns['client_id'] = __( 'ID' );
	// $columns['name'] = $name_val;
	// $columns['role'] = __( 'Role' );
  // $columns['grade'] = __( 'Grade' );
	// $columns['date'] = $date_val;

  return $columns;
}

// add_action( 'manage_client_posts_custom_column' , array($this,'fill_client_columns'), 10, 2 );
// public function fill_client_columns( $column, $post_id ) {
//   // Fill in the columns with meta box info associated with each post
//   switch ( $column ) {
//     case 'client_id' :
//       echo $post_id;
//       break;
//     case 'role' :
//       echo get_post_meta($post_id, 'role', true );
//   		break;
//     case 'grade' :
//       echo get_post_meta($post_id, 'grade', true );
//       break;
//     case 'date' :
//       echo get_the_date('',$post_id);
// 			break;
//   }
// }
