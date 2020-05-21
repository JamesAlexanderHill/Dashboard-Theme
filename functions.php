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
      // $time = get_post_meta($post_id, 'time', true );
      $time = get_field( 'time', $post_id );
      echo "<a href='/wp-admin/post.php?post=" . $post_id . "&action=edit'>" . $type . " | " . $day . " - " . $time . "</a>";
      break;
    case 'coach':
      $coach_id = get_post_meta($post_id, 'coach', true );
      $first_name = get_user_meta( $coach_id, 'first_name', true );
      $last_name = get_user_meta( $coach_id, 'last_name', true );
      echo $first_name . " " . $last_name;
      break;
    case 'location':
      $court_id = get_post_meta($post_id, 'location', true );
      $court = get_the_title($court_id);
      $centre = get_post_meta($court_id, 'location_centre', true );
      echo $court . " | " . $centre;
      break;
    case 'hours':
      echo get_post_meta($post_id, 'length', true );
      break;
    case 'term':
      echo get_the_title(get_post_meta($post_id, 'term', true ));
      break;
  }
}
add_action( 'manage_lesson_posts_custom_column' , 'lesson_custom_column_values', 10, 2 );

//redirect users to login page if they are not logged in
add_action( 'template_redirect', 'redirect_users');
function redirect_users(){
  if(!is_page('login') && !is_user_logged_in()) {
    wp_redirect(site_url('/login'));
    exit();
  }
  if(is_page('login') && is_user_logged_in()) {
    wp_redirect(site_url());
    exit();
  }
}

//shortcodes
function generate_lesson_list($params = array()) {

	// default parameters
	extract(shortcode_atts(array(
		'coach_id' => '-1',
    'term' => '-1'
	), $params));

  //get the list of lessons from database for the current user

  //generate the list
  $lessonList = "<ul>";
  // $lessonList += "<li>Coach ID: ".$coach_id." term: ".$term."</li>";
  // $lessonList += "</ul>";

	return $lessonList;
}
add_shortcode('get_lessons', 'generate_lesson_list');
