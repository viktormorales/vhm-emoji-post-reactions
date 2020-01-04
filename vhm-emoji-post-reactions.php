<?php
/**
 * Plugin Name: VHM EMOJI Post Reactions
 * Plugin URI: http://viktormorales.com
 * Description: Let users REACT to your post or pages with EMOJIS.
 * Version: 1.4
 * Author: Viktor H. Morales
 * Author URI: http://viktormorales.com
 * Text Domain: vhm-emoji
 * Domain Path: /languages/
 * Network: true
 * License: GPL2
 */
 
 /*  Copyright 2016  Viktor H. Morales  (email : viktorhugomorales@gmail.com)
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

if(!class_exists('VHM_Emojione_Post_Reactions'))
{
    class VHM_Emojione_Post_Reactions
    {
		private $options;
		private $count_reactions;
		private $reaction_box_width;
		private $plugin_url;
		
		private $loading_text;
		private $sending_text;
		
        /**
         * Construct the plugin object
         */
        public function __construct()
        {
			global $wpdb;

			$this->options = get_option( 'vhmEmojionePostReactionsOptions' );
			$this->count_reactions = count($this->options['reactions']);
			$this->reaction_box_width = ($this->count_reactions > 0) ? round(100 / $this->count_reactions, 2) : 0 ;
			$this->plugin_url = admin_url('options-general.php?page=vhm_emoji_post_reactions');
			
			$this->loading_text = __('Loading VHM EMOJI Post Reactions...', TEXTDOMAIN);
			$this->sending_text = __('Sending reaction. Please wait...', TEXTDOMAIN);
			
			load_plugin_textdomain(TEXTDOMAIN, '', dirname( plugin_basename( __FILE__ ) ) . '/languages' );

			register_activation_hook( __FILE__, array( &$this, 'activate' ) );
			register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );

			add_action( 'admin_menu', array(&$this, 'admin_menu') );
            add_action( 'admin_init', array(&$this, 'admin_init') );
			add_action( 'admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts') );
			add_action( 'wp_enqueue_scripts', array(&$this, 'frontend_enqueue_scripts') );
			
			add_filter( 'the_content', array(&$this, 'the_content') );
			
			add_action( 'wp_ajax_nopriv_vote', array(&$this, 'vote') );
			add_action( 'wp_ajax_vote', array(&$this, 'vote') );
			
			add_action( 'wp_ajax_nopriv_load_reactions_box', array(&$this, 'load_reactions_box') );
			add_action( 'wp_ajax_load_reactions_box', array(&$this, 'load_reactions_box') );

        } // END public function __construct

		/**
		 * add a menu
		 */     
		public function admin_menu()
		{
			add_options_page(
				'VHM EMOJI Post Reactions', 
				'VHM EMOJI Post Reactions', 
				'manage_options',
				'vhm_emoji_post_reactions', 
				array(&$this, 'settings_page')
			);
		} // END public function admin_menu()

		/**
		 * hook into WP's admin_init action hook
		 */
		public function admin_init()
		{
			
			if ($_REQUEST['reset'] == 'reactions')
			{
				delete_post_meta_by_key('_vhm_emoji_post_reactions');
				wp_redirect( $this->plugin_url );
				exit;
			}
			elseif ($_REQUEST['reset'] == 'voters')
			{
				delete_post_meta_by_key('_vhm_emoji_post_reactions_voters');
				wp_redirect( $this->plugin_url );
				exit;
			}
			elseif ($_REQUEST['reset'] == 'all')
			{
				
				delete_post_meta_by_key('_vhm_emoji_post_reactions');
				delete_post_meta_by_key('_vhm_emoji_post_reactions_voters');
				update_option('vhmEmojionePostReactionsOptions', false);
				wp_redirect( $this->plugin_url );
				exit;
			}
			
			register_setting('vhmEmojionePostReactionsGroup', 'vhmEmojionePostReactionsOptions');
			
		} // END public static function activate

		/**
		 * Menu Callback
		 */     
		public function settings_page()
		{
			global $wpdb;

			if(!current_user_can('manage_options'))
			{
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}

			// Render the settings template
			include(sprintf("%s/settings.php", dirname(__FILE__)));

		} // END public function settings_page()

		public function output( $atts = false )
		{
			global $wpdb;

			// Get the shortcode/function arguments
			extract( shortcode_atts( array(
				'id' => ($id) ? $id : false,
			), $atts ) );

			return $out;

		}

		public function admin_enqueue_scripts() {
			wp_enqueue_script( 'vhm_emoji_post_reactions_admin_js', plugins_url('/js/admin_scripts.js', __FILE__), false, false, true );
		}
		
		public function frontend_enqueue_scripts() {
			global $post;
			
			wp_enqueue_script('jquery');
			wp_enqueue_style('vhm_emoji_post_reactions_css', '//cdn.jsdelivr.net/emoji/2.1.3/assets/css/emoji.min.css');
			wp_enqueue_script( 'vhm_emoji_post_reactions_js', '//cdn.jsdelivr.net/emoji/2.1.3/lib/js/emoji.min.js', false, false, true );
			wp_enqueue_script( 'vhm_emoji_post_reactions_frontend_js', plugins_url('/js/frontend_scripts.js', __FILE__), false, false, true );
			wp_localize_script( 'vhm_emoji_post_reactions_frontend_js', 'vhm_emoji_var', array(
				'site_url' => site_url(),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'ajax-nonce' ),
				'loading_text' => $this->loading_text,
				'sending_text' => $this->sending_text,
				'post_id' => $post->ID
			));
			
			// $max_width_768 = ($this->reaction_box_width < 50) ? '50' : $this->reaction_box_width ;
			echo "
				<style>
					#vhm-emoji-post-reactions-box { position: relative; white-space: nowrap;}
					#vhm-emoji-post-reactions-box ol { width: 100% !important; }
					#vhm-emoji-post-reactions-box ol li { width: " . $this->reaction_box_width . "%;display:inline-block;vertical-align:middle; }
					#vhm-emoji-post-reactions-box ol.vote li { cursor: pointer; }
					#vhm-emoji-post-reactions-box ol.voted li { cursor: default; }
					#vhm-emoji-post-reactions-box ol li .vhm-emoji-post-reactions-cell { text-align: center!important; margin-right: -35px; }
					#vhm-emoji-post-reactions-box ol li .vhm-emoji-post-reactions-code { font-size: 2em; }
					#vhm-emoji-post-reactions-box ol.vote li:hover { 
						transform: scale(1.2);
						-webkit-transform: scale(1.2);
						-moz-transform: scale(1.2);
						-o-transform: scale(1.2);
						transition: transform .2s;
					}
					@media (max-width: 768px) {
						#vhm-emoji-post-reactions-box ol li .vhm-emoji-post-reactions-code { font-size: 1em; }
					}
				</style>
			";
		}
		
		public function the_content($content)
		{
			if (is_single())
			{
				$return = '<div id="vhm-emoji-post-reactions-box">' . $this->loading_text . '</div>';
			}
			return $content . $return;
		}
		
		public function vote()
		{
			global $wpdb, $current_user;

			if ($_POST)
				extract($_POST);
			
			$output['status'] = 'error';
			
			// Check if IP already react to this POST
			$get_voters = (array) get_post_meta($post_id, '_vhm_emoji_post_reactions_voters', true);
			if (!in_array($this->get_user_ip(), $get_voters))	
			{
				if (isset($this->options['reactions'][$item_voted]))
				{
					$output['status'] = 'OK';				
					$output['message'] = 'Votado!';
					
					// Update the post meta
					$get_post_reactions = get_post_meta($post_id, '_vhm_emoji_post_reactions', true);
					$get_post_reactions[$item_voted] += 1;
					update_post_meta($post_id, '_vhm_emoji_post_reactions', $get_post_reactions);
					
					// Update voters list
					$get_voters[] = $this->get_user_ip();
					update_post_meta($post_id, '_vhm_emoji_post_reactions_voters', $get_voters);
				}
			}
			else
			{
				$output['message'] = 'Ya votaste!';
			}
			echo json_encode($output);
			exit;
		}
		
		public function load_reactions_box()
		{
			$post_id = $_POST['post_id']; // Get POST ID
			$this->count_reactions = count($this->options['reactions']); // Count reactions
			// If the admin has defined reactions, let's show the box
			if ($this->count_reactions > 0) {
				// Get the reactions this POST has
				$get_post_reactions = get_post_meta($post_id, '_vhm_emoji_post_reactions', true);
				// Sum the total
				$total_reactions = ($get_post_reactions) ? array_sum($get_post_reactions) : 0 ;
				// Get voters this POST has
				$get_voters = get_post_meta($post_id, '_vhm_emoji_post_reactions_voters', true);
				
				$class = (!in_array($this->get_user_ip(), (array)$get_voters)) ? 'vote' : 'voted' ;
				
				$return = '';
				// Show title if explicit on configuration plugin
				if ($this->options['title'])
					$return = '<div class="vhm-emoji-post-reactions-title">' . $this->options['title'] . '</div>';
				
				$return .= '<ol class="' . $class . '">';
				// Loop through the defined reactions by the admin
				foreach ($this->options['reactions'] as $k => $reaction) {
					// Get the reactions of this POST
					$get_post_reaction = ($get_post_reactions[$k]) ? $get_post_reactions[$k] : 0 ;
					// Get the percentaje of reactions
					$percent = ($total_reactions) ? round($get_post_reaction * 100 / $total_reactions, 2) : 0 ;
					
					$return .= '<li data-vhm_emoji_vote_id="' . $k . '"><div class="vhm-emoji-post-reactions-cell">';
					$return .= '<span class="vhm-emoji-post-reactions-code">' . $reaction['code'] . '</span><p><strong>' . $reaction['label'] . '</strong><br> ' . $percent . '%</p>';
					$return .= '</div></li>';
				} 
				$return .= '</ol>';
			}
			echo $return;
			exit;
		}
		
		public function get_user_ip()
		{
			if (!empty($_SERVER['HTTP_CLIENT_IP']))
			{
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			}
			elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			{
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			return apply_filters('wpb_get_ip', $ip);
		}
		
        /**
         * Activate the plugin
         */
        public function activate()
        {
			if (empty($this->options))
			{
				$this->options['title'] = '<h2>' . __('I\'ve found this post...', TEXTDOMAIN) . '</h2>';
				$this->options['reactions'] = array(
					array('label' => __('Love it!', TEXTDOMAIN), 'code' => ':heart_eyes:' ),
					array('label' => __('Fine', TEXTDOMAIN), 'code' => ':slight_smile:' ),
					array('label' => __('Mmmmh...', TEXTDOMAIN), 'code' => ':thinking:' ),
					array('label' => __('Boring!', TEXTDOMAIN), 'code' => ':unamused:' ),
					array('label' => __('No comments...', TEXTDOMAIN), 'code' => ':zipper_mouth:' )
				);
				update_option('vhmEmojionePostReactionsOptions', $this->options);
			}
        } // END public static function activate

        /**
         * Deactivate the plugin
         */     
        public function deactivate()
        {

        } // END public static function deactivate
		
		
    } // END class VHM_Emojione_Post_Reactions

} // END if(!class_exists('VHM_Emojione_Post_Reactions'))

// instantiate the plugin class
define('TEXTDOMAIN', 'vhm-emoji-post-reactions');
$VHM_Emojione_Post_Reactions = new VHM_Emojione_Post_Reactions();
function vhm_emoji_post_reactions( $atts = false )
{
	global $VHM_Emojione_Post_Reactions;
	return $VHM_Emojione_Post_Reactions->output( $atts );
}
add_shortcode( 'vhm_emoji_post_reactions', 'vhm_emoji_post_reactions' );
add_filter('widget_text', 'do_shortcode');