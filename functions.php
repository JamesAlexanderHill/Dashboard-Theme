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
 function wps_add_role() {
   add_role( 'coach', 'Coach', array('read', 'edit_posts'));
 }
 add_action( 'init', 'wps_add_role' );
 function wps_remove_role() {
     remove_role( 'editor' );
     remove_role( 'author' );
     remove_role( 'contributor' );
     remove_role( 'subscriber' );
 }
 add_action( 'init', 'wps_remove_role' );
