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

//hook into post creation
// add_action( 'new_to_publish', 'create_lesson_batch', 10, 1 );
// add_action(  'new_post',  'on_post_scheduled', 10, 2 );
// function create_lesson_batch( $post ) {
//   $post_arr = array(
//     'post_title'   => 'Test post',
//     'post_content' => 'Test post content',
//     'post_status'  => 'publish',
//     'post_author'  => get_current_user_id(),
//     'meta_input'   => array(
//       'test_meta_key' => 'value of test_meta_key',
//     ),
//   );
//   wp_insert_post( $post_arr );
// }
// function create_lesson_batch($new_status, $old_status=null, $post=null){
//   if ($new_status == "publish" && $old_status == null){
//   }
// }
// add_action('transition_post_status', 'create_lesson_batch');
// function create_lesson_batch( $post_id, $post, $update ) {
//   // $post_arr = array(
//   //   'post_title'   => 'Test post',
//   //   'post_content' => 'Test post content',
//   //   'post_status'  => 'publish',
//   //   'post_author'  => get_current_user_id(),
//   //   'meta_input'   => array(
//   //     'test_meta_key' => 'value of test_meta_key',
//   //   ),
//   // );
//   // wp_insert_post( $post_arr );
// }
// add_action( 'new_post', 'create_lesson_batch', 10, 3 );
// Add the hook action
// add_action('transition_post_status', 'send_new_post', 10, 3);
//
// // Listen for publishing of a new post
// function send_new_post($new_status, $old_status, $post) {
//   if('publish' === $new_status && 'publish' !== $old_status) {
//   }
// }

// function my_post_new($new_status, $old_status=null, $post=null){
//     if ($new_status == "auto-draft"){
//     }
// }
// add_action('transition_post_status', 'my_post_new');
function create_lesson_batch( $ID, $post ) {
  //check if it is a single lesson
  if(get_post_meta($ID, 'is_lesson_batch', true ) == "1"){
    $to = 'jhill7177@gmail.com';
    $subject = 'create_lesson_batch';
    $body = 'Success';
    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail( $to, $subject, $body, $headers );
  }
}
add_action('publish_lesson', 'create_lesson_batch', 10, 2 );
