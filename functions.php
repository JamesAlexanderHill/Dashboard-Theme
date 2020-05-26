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
      $date_time = get_field('timestamp', $post_id);
      $unix = strtotime($date_time);
      $day = date_i18n("D", $unix);
      $time = date_i18n("g:i A", $unix);

      $type = get_post_meta($post_id, 'type', true );
      $val = get_post_meta($post_id, 'is_lesson_batch', true );

      echo "<a href='/wp-admin/post.php?post=" . $post_id . "&action=edit'>" . $type . " ~" . $val . " | " . $day . " - " . $time . "</a>";
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
      echo $court;
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

//custom columns for NOTIFICATION
add_filter('manage_notification_posts_columns' , 'notification_columns');
function notification_columns($columns){
  $date_var = $columns['date'];
  unset($columns['title']);
	unset($columns['date']);

  //reset values
	$columns['type'] = __( 'Type' );
	$columns['message'] = __( 'Message' );
  $columns['date'] = $date_var;

  return $columns;
}
function notification_custom_column_values( $column, $post_id ) {
  switch ( $column ) {
    case 'type':
      echo get_post_meta($post_id, 'type', true );
      break;
    case 'message':
      echo get_post_meta($post_id, 'message', true );
      break;
  }
}
add_action( 'manage_notification_posts_custom_column' , 'notification_custom_column_values', 10, 2 );

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
		'coach_id' => get_current_user_id(),
    'offset' => '0',
    'term' => '-1'
	), $params));

  //get the list of lessons from database for the current user
  $now = strtotime(date_i18n('Y-m-d H:i:s'));
  $today = strtotime("today",$now);
  $start = strtotime($offset . " days", $today);
  $end = strtotime("+23 hours 59 minutes 59 seconds", $start);

  // Query events.
  $posts = get_posts(array(
    'posts_per_page' => -1,
    'post_type'      => 'lesson',
    'meta_query'     => array(
      array(
        'key'         => 'timestamp',
        'compare'     => 'BETWEEN',
        'value'       => array( date_i18n('Y-m-d H:i:s', $start), date_i18n('Y-m-d H:i:s', $end) ),
        'type'        => 'DATETIME'
      ),
      array(
        'key'           => 'coach',
        'compare'       => '==',
        'value'         => $coach_id,
        'type'          => 'NUMERIC',
      )
    ),
    'order'          => 'ASC',
    'orderby'        => 'meta_value',
    'meta_key'       => 'timestamp',
    'meta_type'      => 'DATETIME'
  ));



  //generate the list
  if( $posts ) {
    $lessonList = date_i18n("d-m-Y g:i A", $start)." -> ".date_i18n("d-m-Y g:i A", $now)." -> ".date_i18n("d-m-Y g:i A", $end);
    $lessonList = "<br><table><tr><th>Time</th><th>Location</th><th>Length</th><th>Attendance</th></tr>";

    foreach( $posts as $post ) {
      $post_id = $post->ID;
      // $lessonList .= "<li>Coach ID: ".get_post_meta($post_id, 'coach', true )." term: ".$term."</li>";
      $date_time = get_field('timestamp', $post_id);
      $unix = strtotime($date_time);
      $time = date_i18n("g:i A", $unix);
      $court_id = get_post_meta($post_id, 'location', true );
      $court = get_the_title($court_id);
      $centre = get_post_meta($court_id, 'location_centre', true );

      $lessonList .= "<tr><td>".$time."</td><td>".$court ." - ". $centre."</td><td>".get_post_meta($post_id, 'length', true )."</td><td><button>Attendance</button></td></tr>";
    }

    $lessonList .= "</table>";
  }

	return $lessonList;
}
add_shortcode('get_lessons', 'generate_lesson_list');

function notification($type, $msg){
  $post_arr = array(
    'post_type' => 'notification',
    'meta_input'   => array(
      'type' => $type,
      'message' => $msg,
    ),
  );
  wp_insert_post( $post_arr );
}

// add_action('transition_post_status', 'my_post_new');
// function create_group( $ID, $post ) {
//   $coach = get_field( 'coach', $ID );
//   notification("Log", $ID);
//   notification("Log", $coach);
// }
// add_action('publish_group', 'create_group', 20, 2 );

// function create_group( $id, $post ) {
//   if($post->post_status == "publish"){
//     echo '<pre>'; print_r( $post );
//     echo '<br />';
//     $meta = get_post_meta( $post->ID );
//     $values = get_fields( $post->ID );
//     print_r( $meta );
//     echo '<br />';
//     print_r( $values );
//     echo '</pre>';
//     die();
//   }
//     // your custom code goes here...
// }
// add_action('publish_group', 'create_group', 10, 2 );


function create_group( $post_id ) {
  $post = get_post($post_id);
  if($post->post_type == "group"){
    if($post->post_status == "publish"){
      $values = get_fields( $post->ID );
      $lessonArr = array();
      //create lesson
      $args = array(
        'post_type' => 'lesson',
        'post_status' => 'publish'
      );
      $lesson_id = wp_insert_post($args);
      //get values
      $time = $values['time'];

      $day = $values['day'];
      $term = $values['term'];

      //get the first Monday of term
      $term_start = get_field('starting_date', $term);

      if(date('N', $term_start) <= 1){
      	$start_of_term = strtotime("This Monday", $term_start);
      }else{
      	$start_of_term = strtotime("Last Monday", $term_start);
      }

      //set week
      $week = strtotime("+0 week", $start_of_term);
      //set the timestamp
      $timestamp = strtotime($time, $week);

      $msg = "init(".$time.", ".$day.", ".$term.") -> ". $term_start ."(".date_i18n("d/m/Y g:i A", $term_start).") -> ".$start_of_term."(".date_i18n("d/m/Y g:i A", $start_of_term).") -> ".$timestamp . "(".date_i18n("d/m/Y g:i A", $timestamp).")";
      notification("Log", $msg);
      //set metadata
      update_field( 'coach', $values['coach'], $lesson_id );
      update_field( 'clients', $values['clients'], $lesson_id );
      update_field( 'location', $values['location'], $lesson_id );
      update_field( 'timestamp', $timestamp, $lesson_id );
      update_field( 'length', $values['length'], $lesson_id );
      update_field( 'type', $values['type'], $lesson_id );
      update_field( 'term', $term, $lesson_id );
      update_field( 'group', $post_id, $lesson_id );

      //add id to lesson array
      array_push($lessonArr, $lesson_id);

      //add array to lessons metadata of the group
      update_field( 'lessons', $lessonArr, $post_id);
    }
  }
}
add_action('acf/save_post', 'create_group', 20);
