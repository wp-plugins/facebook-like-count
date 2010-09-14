<?php
/*
	Plugin Name: Facebook Like Count
	Plugin URI: http://fblico.mafact.de/
	Description: Counts the likes of blog posts and creates 2 charts: authors by likes and posts by likes
	Version: 2.1
	Author: Marco Scheffel
	Author URI: http://www.facebook.com/ms.fb.ger
	License: GPLv2

	Copyright 2010  Marco Scheffel  (email : Marco.Scheffel@gmx.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
*/
	include_once(ABSPATH . WPINC . '/pluggable.php');
	global $current_user;
	get_currentuserinfo();	

	$curr_user = $current_user->ID;
	$first_name = $current_user->user_firstname;
	$last_name = $current_user->user_lastname;
	
	/**
	 * Loading localisation
	 */
	$plugin_dir = basename(dirname(__FILE__));
	load_plugin_textdomain('fblico', 'wp-content/plugins/' . $plugin_dir . '/lang', $plugin_dir . '/lang');
	
	
	include_once('fblico_admin.php');
	/**
	 * Menu functions
	 */
	function fblico_menu() {
	  add_options_page('Facebook Like Count Admin', 'Facebook Like Count', 'manage_options', 'facebook-like-count-admin', 'fblico_admin');
	}

	/**
	 * Content of Dashboard-Widget
	 */
	function fblico_dashboard() {
		include_once('fblico_dashboard.php');
	}
	 
	/**
	 * Add Dashboard Widget via function wp_add_dashboard_widget()
	 */
	
	
	function fblico_setup() {
		
		global $current_user;
		get_currentuserinfo();	

		$curr_user = $current_user->ID;
		$first_name = $current_user->user_firstname;
		$last_name = $current_user->user_lastname;
		$fblico_title = __("Likes for","fblico")." ".$first_name." ".$last_name;
		wp_add_dashboard_widget( 'fblico', $fblico_title, 'fblico_dashboard' );
	}
	 
	
	/**
	 * Add Actions
	 */
		
	$fblico_posts = get_posts('showposts=-1&post_type=any');
	if ($fblico_posts) {
		foreach($fblico_posts as $fblico_post) {
			$fblico_post_ids[] = $fblico_post->ID;
		}
	}
	
	foreach($fblico_post_ids as $fblico_post) {
		
		$fblico_post_data = get_post($fblico_post);
		$fblico_post_author = $fblico_post_data->post_author; //Author of the post
		
		if($fblico_post_author==$fblico_curr_user){
			$fblico_user_has_posts = 1;
		}
	}
	
	add_action('wp_dashboard_setup', 'fblico_setup');
	add_action('admin_menu', 'fblico_menu');
?>
