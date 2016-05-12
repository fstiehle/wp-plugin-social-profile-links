<?php
/*
Plugin Name: Social Profile Icons Widget
Plugin URI:  http://fabianstiehle.com/spiw
Description: This describes my plugin in a short sentence
Version:     0.8
Author:      Fabian Stiehle
Author URI:  http://fabianstiehle.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: spiw
*/
defined('ABSPATH') or die();

class WP_Widget_social_profile_icons extends WP_Widget {
    
    /**
     * Defines supported profiles and their order
     * 'css-style' => 'hex-color'
     * find branding colors on: http://brandcolors.net/
     */    
    private static $profiles = array(
        'facebook' => '#3b5998',
        'twitter' => '#00aced',
        'gplus' => '#dd4b39',
        'pinterest' => '#cb2027',
        'instagram' => '#517fa4',
        'youtube' => '#bb0000',
    );
    
    /**
     * Default widget settings
     */  
    private static $defaults = array(
        'icon-size' => "40px",
        'border-radius' => "0",
        'monocron-color' => "#F5f5f5",
    );

    function __construct() {
        $widget_ops = array('classname' => 'widget_social-profile-icons',
                            'description' => __('A widget to display your social profile icons.', "spiw"));

        parent::__construct('social-profile-icons', __('Social Links', "spiw"), $widget_ops);
        $this->alt_option_name = 'widget_social-profile-icons';
    }

    function widget($args, $instance) {
        extract($args);  
        extract($instance);

        $title = (!empty($instance['title'])) ? $instance['title'] : __('Follow me', "spiw");   

        // See: wp-includes/default-widgets.php
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );              

        $output = '';
        $output .= $before_widget;

        if ($title) {
            $output .= $before_title . $title . $after_title;
        }
        
        // Print Widget Output
        $user = $this->get_current_user($instance);
        $output .= '<ul id="spiw"' . '-sociallinks">';

        foreach (self::$profiles as $key => $color) {
            if (get_the_author_meta($key, $user)) {

                // Set branded background color if monocron setting is deactivated
                if(!isset($instance['monocron'])) {
                    $output .= '<li style="background-color:' . $color . '">';
                } else {
                    $output .= '<li>';
                }
                $output .= '<a class="spiw-' . $key . '" href="' . get_the_author_meta($key, $user) .
                '"><i class="spiw-icon spiw-icon-' . $key . '"></i></a></li>';
            }
        }
        $output .= '</ul>';
        $output .= $this->get_custom_css($instance);

        $output .= $after_widget;
        echo $output;    
    }
    
    /**
     * Update widget options
     */  
    function update($new_instance, $old_instance) {
        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['users'] = stripslashes($new_instance['users']);
        $instance['icon-size'] = $this->check_size(strip_tags(stripslashes($new_instance['icon-size'])));
        $instance['border-radius'] = $this->check_size(strip_tags(stripslashes($new_instance['border-radius'])));
        $instance['rounded'] = $new_instance['rounded'];
        $instance['monocron'] = $new_instance['monocron'];
        $instance['monocron-color'] = $this->check_color($new_instance['monocron-color']); 
        return $instance;
    }

    function form($instance) {
        $title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $user = $this->get_current_user($instance);
        
        /**
         * widget-view.php
         *
         * Widget Settings:
         * Title : text field
         * User : wp_dropdown_users
         * Icon size : text field
         * Border radius: text field
         * Rounded : checkbox
         * Monocron : checkbox
         * Monocron color: wp_colorpicker
         */ 
        include('widget-view.php');
    }

    /**
     * Gets current selected user in widget options
     */ 
    private function get_current_user($instance) {
        $output = '';
        if (!empty($instance['users'])) {
            $user = $instance['users'];       
            
            $userobj = get_userdata($user);           
            if (username_exists($userobj->user_login)) {
                $output = $user;
            }
        }
        return $output;
    }

    /**
     * Validates size input, makes sure the size is given in "px"
     * -> Is directly used in css!
     * @param $css String
     */ 
    private function check_size($css) {
        $css = preg_replace("/[^0-9]/", "", $css);
        if(intval($css) < 100 and intval($css) > 0) {            
            return $css . 'px';
        }
    }

    /**
     * Validates color input
     * -> Color is directly used in css!
     * @param $color String
     */ 
    private function check_color($color) {
        if (preg_match( '/^#[a-f0-9]{6}$/i', $color)) {
            return $color;
        }
    }

    /**
     * Gets custom inline css
     * as much as possible should be coming from the integrated spiw.css
     */ 
    private function get_custom_css($instance) {
        $output = '<style media="screen" type="text/css">';
        $selector = '#social-profile-icons-' . $this->number;
        
        // Rounded or not
        if(!$rounded = isset($instance['rounded']) ? True : False) {
            $border_radius = isset($instance['border-radius']) ? esc_attr($instance['border-radius']) :
                self::$defaults["border-radius"];
        
            $output .= 
                $selector . ' li,
                #social-profile-icons-' . $this->number . ' .spiw-icon {                
                    border-radius:' . $border_radius . ';                
                }';
        } else {
            $output .= 
                $selector . ' li,' .
                $selector . ' .spiw-icon {                
                    border-radius: 100%;                
                }
            ';
        }

        // Monocron setting
        if (!isset($instance['monocron'])) {
            $output .= 
                $selector . ' .spiw-icon {                
                    color: #fff;
                }' .
                $selector . ' li:hover .spiw-icon {
	                opacity: 0.6;	
                }';
        } else if ($instance['monocron-color'] != "#F5f5f5") {
           $output .= 
                $selector . ' li {                
                    background-color: ' . $instance['monocron-color'] . ';
                }'; 
        }

        // Icon size
        if (isset($instance['icon-size']) && $instance['icon-size'] != "40px") {
           $output .= 
                $selector . ' ul {                
                    font-size: ' . $instance['icon-size'] . ';
                }'; 
        }
        $output .= '</style>';
        return $output;
    }
} /* END Widget class */

// Register the widget
function spiw_register_widgets() {
    register_widget('WP_Widget_social_profile_icons');
}
add_action('widgets_init', 'spiw_register_widgets');

// Add new user fields
function spiw_user_fields($profile_fields) {
	$profile_fields['twitter'] = __('Twitter URL', "spiw");
	$profile_fields['facebook'] = __('Facebook URL', "spiw");
	$profile_fields['gplus'] = __('Google+ URL', "spiw");
	$profile_fields['pinterest'] = __('Pinterest URL', "spiw");
	$profile_fields['instagram'] = __('Instagram URL', "spiw");
	$profile_fields['youtube'] = __('Youtube URL', "spiw");
	return $profile_fields;
}
add_filter('user_contactmethods', 'spiw_user_fields');

// Enqueue scripts and styles.
function spiw_scripts() { 
	wp_enqueue_style('spiw-css', plugins_url('/css/spiw.css', __FILE__ ));
}
add_action('wp_enqueue_scripts', 'spiw_scripts');

// Color picker script
function spiw_admin_scripts($hook) {
    if ('widgets.php' != $hook) {
        return;
    }
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('spiw-colorpicker', plugins_url('/js/spiw-colorpicker.js', __FILE__),
                    array('wp-color-picker'), false, true);
}
add_action('admin_enqueue_scripts', 'spiw_admin_scripts');

// Load textdomain
add_action('plugins_loaded', 'spiw_load_textdomain');
function spiw_load_textdomain() {
	load_plugin_textdomain('spiw', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
