<?php
/*
Plugin Name: WTI Like Post (forked for PHPSP)
Plugin URI: https://github.com/jpjoao/wti-like-post
Description: It is a fork for PHP of WTI Like Post to enable a PHP RFC like vote option
Version: 0.0.3.1
Author: Joao Paulo
Author URI: https://github.com/jpjoao/
Original Author: webtechideas
Original Author URI: http://www.webtechideas.com/
License: GPLv2 or later

Copyright 2014  Webtechideas  (email : support@webtechideas.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/

#### INSTALLATION PROCESS ####
/*
1. Download the plugin and extract it
2. Upload the directory '/wti-like-post/' to the '/wp-content/plugins/' directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Click on 'WTI Like Post' link under Settings menu to access the admin section
*/

global $wti_like_post_db_version;
$wti_like_post_db_version = "1.4.2";

add_action('init', 'WtiLoadPluginTextdomain');

/**
 * Load the language files for this plugin
 * @param void
 * @return void
 */
function WtiLoadPluginTextdomain() {
     load_plugin_textdomain('wti-like-post', false, 'wti-like-post/lang');
}

/**
 * Create the settings link for this plugin
 * @param $links array
 * @param $file string
 * @return $links array
 */
function WtiLikePostPluginLinks($links, $file) {
     static $this_plugin;

     if (!$this_plugin) {
		$this_plugin = plugin_basename(__FILE__);
     }

     if ($file == $this_plugin) {
		$settings_link = '<a href="' . admin_url('options-general.php?page=WtiLikePostAdminMenu') . '">' . __('Settings', 'wti-like-post') . '</a>';
		array_unshift($links, $settings_link);
     }

     return $links;
}

register_activation_hook(__FILE__, 'SetOptionsWtiLikePost');

/**
 * Basic options function for the plugin settings
 * @param no-param
 * @return void
 */
function SetOptionsWtiLikePost() {
     global $wpdb, $wti_like_post_db_version;

     // Creating the like post table on activating the plugin
     $wti_like_post_table_name = $wpdb->prefix . "wti_like_post";
	
     if ($wpdb->get_var("show tables like '$wti_like_post_table_name'") != $wti_like_post_table_name) {
		$sql = "CREATE TABLE " . $wti_like_post_table_name . " (
			`id` bigint(11) NOT NULL AUTO_INCREMENT,
			`post_id` int(11) NOT NULL,
			`value` int(2) NOT NULL,
			`date_time` datetime NOT NULL,
			`ip` varchar(40) NOT NULL,
			`user_id` int(11) NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`)
		)";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
     }
	
     // Adding options for the like post plugin
     add_option('wti_like_post_drop_settings_table', '0', '', 'yes');
     add_option('wti_like_post_voting_period', '0', '', 'yes');
     add_option('wti_like_post_voting_style', 'style1', '', 'yes');
     add_option('wti_like_post_alignment', 'left', '', 'yes');
     add_option('wti_like_post_position', 'bottom', '', 'yes');
     add_option('wti_like_post_show_votes', '1', '', 'yes');
     add_option('wti_like_post_enforce_date_limit', '1', '', 'yes');
     add_option('wti_like_post_login_required', '0', '', 'yes');
     add_option('wti_like_post_no_karma_message', __('Not enough Karma to vote.', 'wti-like-post'), '', 'yes');
     add_option('wti_like_post_login_message', __('Please login to vote.', 'wti-like-post'), '', 'yes');
     add_option('wti_like_post_thank_message', __('Thanks for your vote.', 'wti-like-post'), '', 'yes');
     add_option('wti_like_post_voted_message', __('You have already voted.', 'wti-like-post'), '', 'yes');
     add_option('wti_like_post_allowed_posts', '', '', 'yes');
     add_option('wti_like_post_excluded_posts', '', '', 'yes');
     add_option('wti_like_post_allowed_categories', '', '', 'yes');
     add_option('wti_like_post_excluded_sections', '', '', 'yes');
     add_option('wti_like_post_show_on_pages', '0', '', 'yes');
     add_option('wti_like_post_show_on_widget', '1', '', 'yes');
     add_option('wti_like_post_show_symbols', '1', '', 'yes');
     add_option('wti_like_post_show_dislike', '1', '', 'yes');
     add_option('wti_like_post_title_text', 'Like/Unlike', '', 'yes');
     add_option('wti_like_post_db_version', $wti_like_post_db_version, '', 'yes');
}

/**
 * For dropping the table and removing options
 * @param no-param
 * @return no-return
 */
function UnsetOptionsWtiLikePost() {
     global $wpdb;

	// Check the option whether to drop the table on plugin uninstall or not
	$drop_settings_table = get_option('wti_like_post_drop_settings_table');
	
	if ($drop_settings_table == 1) {
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wti_like_post");
	
		// Deleting the added options on plugin uninstall
		delete_option('wti_like_post_drop_settings_table');
		delete_option('wti_like_post_voting_period');
		delete_option('wti_like_post_voting_style');
		delete_option('wti_like_post_alignment');
		delete_option('wti_like_post_position');
        delete_option('wti_like_post_show_votes');
        delete_option('wti_like_post_enforce_date_limit');
         delete_option('wti_like_post_login_required');
         delete_option('wti_like_post_no_karma_message');
		delete_option('wti_like_post_login_message');
		delete_option('wti_like_post_thank_message');
		delete_option('wti_like_post_voted_message');
		delete_option('wti_like_post_db_version');
		delete_option('wti_like_post_allowed_posts');
		delete_option('wti_like_post_excluded_posts');
		delete_option('wti_like_post_allowed_categories');
		delete_option('wti_like_post_excluded_sections');
		delete_option('wti_like_post_show_on_pages');
		delete_option('wti_like_post_show_on_widget');
		delete_option('wti_like_post_show_symbols');
		delete_option('wti_like_post_show_dislike');
		delete_option('wti_like_post_title_text');
	}
}

register_uninstall_hook(__FILE__, 'UnsetOptionsWtiLikePost');

function WtiLikePostAdminRegisterSettings() {
     // Registering the settings
     register_setting('wti_like_post_options', 'wti_like_post_drop_settings_table');
     register_setting('wti_like_post_options', 'wti_like_post_voting_period');
     register_setting('wti_like_post_options', 'wti_like_post_voting_style');
     register_setting('wti_like_post_options', 'wti_like_post_alignment');
     register_setting('wti_like_post_options', 'wti_like_post_position');
     register_setting('wti_like_post_options', 'wti_like_post_show_votes');
     register_setting('wti_like_post_options', 'wti_like_post_enforce_date_limit');
     register_setting('wti_like_post_options', 'wti_like_post_login_required');
     register_setting('wti_like_post_options', 'wti_like_post_no_karma_message');
     register_setting('wti_like_post_options', 'wti_like_post_login_message');
     register_setting('wti_like_post_options', 'wti_like_post_thank_message');
     register_setting('wti_like_post_options', 'wti_like_post_voted_message');
     register_setting('wti_like_post_options', 'wti_like_post_allowed_posts');
     register_setting('wti_like_post_options', 'wti_like_post_excluded_posts');
     register_setting('wti_like_post_options', 'wti_like_post_allowed_categories');
     register_setting('wti_like_post_options', 'wti_like_post_excluded_sections');
     register_setting('wti_like_post_options', 'wti_like_post_show_on_pages');
     register_setting('wti_like_post_options', 'wti_like_post_show_on_widget');
     register_setting('wti_like_post_options', 'wti_like_post_db_version');	
     register_setting('wti_like_post_options', 'wti_like_post_show_symbols');
     register_setting('wti_like_post_options', 'wti_like_post_show_dislike');
     register_setting('wti_like_post_options', 'wti_like_post_title_text');	
}

add_action('admin_init', 'WtiLikePostAdminRegisterSettings');

/**
 * Create the update function for this plugin
 * @param no-param
 * @return no-return
 */
function UpdateOptionsWtiLikePost() {
     global $wpdb, $wti_like_post_db_version;

	// Get current database version for this plugin
	$current_db_version = get_option('wti_like_post_db_version');
	
	if ($current_db_version != $wti_like_post_db_version) {
		// Increase column size to support IPv6
		$wpdb->query("ALTER TABLE `{$wpdb->prefix}wti_like_post` CHANGE `ip` `ip` VARCHAR( 40 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL");
		
		$user_col = $wpdb->get_row("SHOW COLUMNS FROM {$wpdb->prefix}wti_like_post LIKE 'user_id'");
	
		if (count($user_col) == 0) {
			$wpdb->query("ALTER TABLE `{$wpdb->prefix}wti_like_post` ADD `user_id` INT NOT NULL DEFAULT '0'");
		}

		// Update the database version
		update_option('wti_like_post_db_version', $wti_like_post_db_version);
	}
}

add_action('plugins_loaded', 'UpdateOptionsWtiLikePost');

if (is_admin()) {
	// Include the file for loading plugin settings
	require_once('wti_like_post_admin.php');
} else {
	// Include the file for loading plugin settings for
	require_once('wti_like_post_site.php');

	// Load the js and css files
	add_action('init', 'WtiLikePostEnqueueScripts');
	add_action('wp_head', 'WtiLikePostAddHeaderLinks');
}

/**
 * Get the user id
 * @param no-param
 * @return string
 */
function WtiGetUserId() {
    $current_user = wp_get_current_user();
    return (int)$current_user->ID;
}

/**
 * Check whether user has already voted or not
 * @param $post_id integer
 * @param $user_id integer
 * @return integer
 */
function HasWtiAlreadyVoted($post_id, $user_id = null) {
	global $wpdb;
	
	if (null == $user_id) {
        $user_id = WtiGetUserId();
	}
	
	$wti_has_voted = $wpdb->get_var("SELECT COUNT(id) AS has_voted FROM {$wpdb->prefix}wti_like_post WHERE post_id = '$post_id' AND user_id = '$user_id'");
	
	return $wti_has_voted;
}

/**
 * Get last voted date for a given post by ip
 * @param $post_id integer
 * @param $user_id integer
 * @return string
 */
function GetWtiLastVotedDate($post_id, $user_id = null) {
     global $wpdb;
     
     if (null == $user_id) {
         $user_id = WtiGetUserId();
     }
     
     $wti_has_voted = $wpdb->get_var("SELECT date_time FROM {$wpdb->prefix}wti_like_post WHERE post_id = '$post_id' AND user_id = '$user_id'");

     return $wti_has_voted;
}

/**
 * Get next vote date for a given user
 * @param $last_voted_date string
 * @param $voting_period integer
 * @return string
 */
function GetWtiNextVoteDate($last_voted_date, $voting_period) {
     switch($voting_period) {
          case "1":
               $day = 1;
               break;
          case "2":
               $day = 2;
               break;
          case "3":
               $day = 3;
               break;
          case "7":
               $day = 7;
               break;
          case "14":
               $day = 14;
               break;
          case "21":
               $day = 21;
               break;
          case "1m":
               $month = 1;
               break;
          case "2m":
               $month = 2;
               break;
          case "3m":
               $month = 3;
               break;
          case "6m":
               $month = 6;
               break;
          case "1y":
               $year = 1;
            break;
     }
     
     $last_strtotime = strtotime($last_voted_date);
     $next_strtotime = mktime(date('H', $last_strtotime), date('i', $last_strtotime), date('s', $last_strtotime),
                    date('m', $last_strtotime) + $month, date('d', $last_strtotime) + $day, date('Y', $last_strtotime) + $year);
     
     $next_voting_date = date('Y-m-d H:i:s', $next_strtotime);
     
     return $next_voting_date;
}

/**
 * Get last voted date as per voting period
 * @param $post_id integer
 * @return string
 */
function GetWtiLastDate($voting_period) {
     switch($voting_period) {
          case "1":
               $day = 1;
               break;
          case "2":
               $day = 2;
               break;
          case "3":
               $day = 3;
               break;
          case "7":
               $day = 7;
               break;
          case "14":
               $day = 14;
               break;
          case "21":
               $day = 21;
               break;
          case "1m":
               $month = 1;
               break;
          case "2m":
               $month = 2;
               break;
          case "3m":
               $month = 3;
               break;
          case "6m":
               $month = 6;
               break;
          case "1y":
               $year = 1;
            break;
     }
     
     $last_strtotime = strtotime(date('Y-m-d H:i:s'));
     $last_strtotime = mktime(date('H', $last_strtotime), date('i', $last_strtotime), date('s', $last_strtotime),
                    date('m', $last_strtotime) - $month, date('d', $last_strtotime) - $day, date('Y', $last_strtotime) - $year);
     
     $last_voting_date = date('Y-m-d H:i:s', $last_strtotime);
     
     return $last_voting_date;
}

/**
 * Get like count for a post
 * @param $post_id integer
 * @return string
 */
function GetWtiLikeCount($post_id) {
	global $wpdb;
	$show_symbols = get_option('wti_like_post_show_symbols');
	$wti_like_count = $wpdb->get_var("SELECT SUM(value) FROM {$wpdb->prefix}wti_like_post WHERE post_id = '$post_id' AND value >= 0");
	
	if (!$wti_like_count) {
		$wti_like_count = 0;
	} else {
		if ($show_symbols) {
			$wti_like_count = "+" . $wti_like_count;
		} else {
			$wti_like_count = $wti_like_count;
		}
	}
	
	return $wti_like_count;
}

/**
 * Get unlike count for a post
 * @param $post_id integer
 * @return string
 */
function GetWtiUnlikeCount($post_id) {
	global $wpdb;
	$show_symbols = get_option('wti_like_post_show_symbols');
	$wti_unlike_count = $wpdb->get_var("SELECT SUM(value) FROM {$wpdb->prefix}wti_like_post WHERE post_id = '$post_id' AND value <= 0");
	
	if (!$wti_unlike_count) {
		$wti_unlike_count = 0;
	} else {
		if ($show_symbols) {
		} else {
			$wti_unlike_count = str_replace('-', '', $wti_unlike_count);
		}
	}
	
	return $wti_unlike_count;
}

/**
 * Get votes for a post
 * @param $post_id integer
 * @return string
 */
function GetWtiVotes($post_id) {
    global $wpdb;
    $wti_votes = $wpdb->get_results("SELECT user_id, value FROM {$wpdb->prefix}wti_like_post WHERE post_id = '$post_id'");

    if (!$wti_votes) {
        $wti_votes = array();
    }

    return $wti_votes;
}

/**
 * Adds a date-limit meta to posts
 */
function WtiAddVoteExpirationDate() {

    add_meta_box(
        'wti_vote_expiration_date',
        __( 'Vote Expiration Date', 'wti-like-post' ),
        'WtiVoteExpirationDateCallback',
        'post'
    );
}
add_action( 'add_meta_boxes', 'WtiAddVoteExpirationDate' );

/**
 * Prints the expiration form.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function WtiVoteExpirationDateCallback( $post ) {

	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'wti_like_post_expiration_date', 'wti_like_post_expiration_date_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$value = get_post_meta( $post->ID, '_wti_like_post_expiration_date', true );

    if (!empty($value))
    {
        $value = date_create_from_format('Y-m-d', $value);
        $value = $value->format('d/m/Y');
    }

	echo '<label for="wti_like_post_expiration_date">';
	_e( 'Expiration date for voting', 'wti-like-post' );
	echo '</label> ';
	echo '<input type="text" id="wti_like_post_expiration_date" name="wti_like_post_expiration_date" value="' . esc_attr( $value ) . '" size="10" />';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function WtiVoteExpirationDateSave( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['wti_like_post_expiration_date_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['wti_like_post_expiration_date_nonce'], 'wti_like_post_expiration_date' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */

	// Make sure that it is set.
	if ( ! isset( $_POST['wti_like_post_expiration_date'] ) ) {
		return;
	}

	// Sanitize user input.
	$my_data = sanitize_text_field( $_POST['wti_like_post_expiration_date'] );

    if (!empty($my_data)) {
        $my_data = date_create_from_format('d/m/Y', $my_data)->format('Y-m-d');
    }
	// Update the meta field in the database.
	update_post_meta( $post_id, '_wti_like_post_expiration_date', $my_data );
}
add_action( 'save_post', 'WtiVoteExpirationDateSave' );

function WtiVoteRegisterAdminScripts() {
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_style( 'jquery-ui-datepicker', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css' );
    wp_enqueue_script( 'wti-like-post-admin', plugins_url( 'wti-like-post/js/admin.js' ) );
}

/**
 * Checks if the voting is open for the post
 *
 * @param $post_id integer
 * @param $enforce_limit bool
 *
 * @return bool
 */
function WtiIsVoteOpen($post_id, $enforce_limit) {
    $is_vote_open = true;
    if ($enforce_limit) {
        $post_vote_expire_date = get_post_meta($post_id, '_wti_like_post_expiration_date', true);
        if (empty($post_vote_expire_date)) {
            $is_vote_open = true;
        } else {
            $post_vote_expire_date = date_create_from_format('Y-m-d', $post_vote_expire_date);
            $post_vote_expire_date->setTime(23,59,59);
            $today = date_create();
            if($today <= $post_vote_expire_date) {
                $is_vote_open = true;
            } else {
                $is_vote_open = false;
            }
        }
    }

    return $is_vote_open;
}

add_action( 'admin_enqueue_scripts', 'WtiVoteRegisterAdminScripts' );

// Load the widgets
require_once('wti_like_post_widgets.php');

// Include the file for ajax calls
require_once('wti_like_post_ajax.php');

// Associate the respective functions with the ajax call
add_action('wp_ajax_wti_like_post_process_vote', 'WtiLikePostProcessVote');
add_action('wp_ajax_nopriv_wti_like_post_process_vote', 'WtiLikePostProcessVote');

//Add extra "Has Karma to vote in RFCs" option on user account
add_action( 'show_user_profile', 'PHPSP_has_karma_field' );
add_action( 'edit_user_profile', 'PHPSP_has_karma_field' );
function PHPSP_has_karma_field( $user ) {
     if ( current_user_can( 'edit_users' ) ) {
          ?>
          <h3><?php _e( "RFC Vote", "blank" ); ?></h3>
          <table class="form-table">
               <tr>
                    <th><label for="phone"><?php _e( "Has Karma" ); ?></label></th>
                    <td>
                         <input type="checkbox" name="has_karma" id="has_karma"
                                value="1" <?php checked( get_the_author_meta( 'has_karma', $user->ID ), 1 ); ?> /><br/>
                    </td>
               </tr>
          </table>
          <?php
     }
}

add_action( 'personal_options_update', 'PHPSP_save_has_karma_field' );
add_action( 'edit_user_profile_update', 'PHPSP_save_has_karma_field' );
function PHPSP_save_has_karma_field( $user_id ) {
  if ( current_user_can( 'edit_users' ) ) {
    update_user_meta( $user_id, 'has_karma', $_POST['has_karma'] );
  }
  return true;
}