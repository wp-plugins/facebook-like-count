<?php
/*
	Plugin Name: Facebook Like Count
	Plugin URI: http://fblico.mafact.de/
	Description: Counts the likes of blog posts and creates 2 charts: authors by likes and posts by likes
	Version: 1.1
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
		$curr_user = get_current_user_id();
		$first_name = get_the_author_meta( 'first_name', $curr_user );
		$last_name = get_the_author_meta( 'last_name', $curr_user );
		$fblico_title = __("Likes for","fblico")." ".$first_name." ".$last_name;
		wp_add_dashboard_widget( 'fblico', $fblico_title, 'fblico_dashboard' );
	}
	
	/**
	 * Add Actions
	 */
	add_action('wp_dashboard_setup', 'fblico_setup');
	add_action('admin_menu', 'fblico_menu');
?>