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
   //check if role exist before removing it
   if( get_role('subscriber') ){
     remove_role( 'subscriber' );
   }
   //check if role exist before removing it
   if( get_role('contributor') ){
     remove_role( 'contributor' );
   }
   //check if role exist before removing it
   if( get_role('editor') ){
     remove_role( 'editor' );
   }
   //check if role exist before removing it
   if( get_role('author') ){
     remove_role( 'author' );
   }
 }
 add_action( 'init', 'wps_remove_role' );
