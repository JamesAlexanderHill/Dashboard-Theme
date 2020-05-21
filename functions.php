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
add_filter('manage_client_posts_columns' , 'client_columns');
function client_columns($columns){
	$date_val = $columns['date'];
	unset($columns['date']);

  //reset values
	$columns['role'] = __( 'Role' );
  $columns['grade'] = __( 'Grade' );
	$columns['date'] = $date_val;

  return $columns;
}

function client_custom_column_values( $column, $post_id ) {
  switch ( $column ) {
    case 'role':
      echo get_post_meta($post_id, 'role', true );
      break;
    case 'grade':
      $grade_var = get_post_meta($post_id, 'grade', true );
      if($grade_var){
        echo $grade_var;
      }else{
        echo '<div class="dashicons dashicons-minus"></div>';
      }
      break;
  }
}
add_action( 'manage_client_posts_custom_column' , 'client_custom_column_values', 10, 2 );

//custom columns for LOCATIONS
add_filter('manage_location_posts_columns' , 'location_columns');
function location_columns($columns){
  $title_val = $columns['title'];
  unset($columns['title']);
	$date_val = $columns['date'];
	unset($columns['date']);

  //reset values
	$columns['title'] = $title_val;
	$columns['centre'] = __( 'Centre' );
  $columns['type'] = __( 'Type' );

  return $columns;
}

function location_custom_column_values( $column, $post_id ) {
  switch ( $column ) {
    case 'centre':
      echo get_post_meta($post_id, 'location_centre', true );
      break;
    case 'type':
      echo get_post_meta($post_id, 'location_type', true );
      break;
  }
}
add_action( 'manage_location_posts_custom_column' , 'location_custom_column_values', 10, 2 );

//custom columns for LESSONS
add_filter('manage_lesson_posts_columns' , 'lesson_columns');
function lesson_columns($columns){
  unset($columns['title']);
	unset($columns['date']);
  unset($columns['categories']);
	unset($columns['tags']);

  //reset values
	$columns['lesson'] = __( 'Lesson' );
	$columns['coach'] = __( 'Coach' );
  $columns['location'] = __( 'Location' );
  $columns['hours'] = __( 'Hours' );
  $columns['term'] = __( 'Term' );

  return $columns;
}

function lesson_custom_column_values( $column, $post_id ) {
  switch ( $column ) {
    case 'lesson':
      $type = get_post_meta($post_id, 'type', true );
      $day = get_post_meta($post_id, 'day', true );
      $time = get_post_meta($post_id, 'time', true );
      echo "<a href='/wp-admin/post.php?post=" . $post_id . "&action=edit'><bold>" . $type . " | " . $day . " - " . $time . "</bold></a>";
      break;
    case 'coach':
      echo get_post_meta($post_id, 'coach', true );
      break;
    case 'location':
      echo get_post_meta($post_id, 'location', true );
      break;
    case 'hours':
      echo get_post_meta($post_id, 'length', true );
      break;
    case 'term':
      echo get_post_meta($post_id, 'term', true );
      break;
  }
}
add_action( 'manage_lesson_posts_custom_column' , 'lesson_custom_column_values', 10, 2 );
