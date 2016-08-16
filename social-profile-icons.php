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

add_action('plugins_loaded', 'spiw_load_textdomain');
/**
 * Load Textdomain
 */
function spiw_load_textdomain() {
	load_plugin_textdomain('spiw', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

class WP_Widget_social_profile_icons extends WP_Widget {
    
    /**
     * Defines supported profiles and their order
     * 'css' => css icon class
     * 'color' => hex color
     * 'label' => label name
     * find branding colors on: http://brandcolors.net/
     */
    static $profiles = array(
        array(
            'css' => 'facebook',
            'color' => '#3b5998',
            'label' => 'Facebook',
        ),
        array(
            'css' => 'twitter',
            'color' => '#00aced',
            'label' => 'Twitter',
        ),
        array(
            'css' => 'gplus',
            'color' => '#dd4b39',
            'label' => 'Google Plus',
        ),
        array(
            'css' => 'pinterest',
            'color' => '#cb2027',
            'label' => 'Pinterest',
        ),
        array(
            'css' => 'instagram',
            'color' => '#517fa4',
            'label' => 'Instagram',
        ),
        array(
            'css' => 'youtube',
            'color' => '#bb0000',
            'label' => 'Youtube',
        ),
        array(
            'css' => 'github-circled',
            'color' => '#333333',
            'label' => 'Github',
        ),
        array(
            'css' => 'linkedin',
            'color' => '#0077b5',
            'label' => 'LinkedIn',
        ),
        array(
            'css' => 'xing',
            'color' => '#026466',
            'label' => 'Xing',
        ),
        array(
            'css' => 'twitch',
            'color' => '#6441a5',
            'label' => 'Twitch',
        ),
        array(
            'css' => 'vine',
            'color' => '#00b488',
            'label' => 'Vine',
        ),
    );

    /**
     * Default widget settings
     */  
    private static $defaults = array(
        'icon-size' => "50px",
        'border-radius' => "0",
        'icon-color' => "#fffff",
        'monocron-color' => "#eeeee",
    );

    function __construct() {
        $widget_ops = array('classname' => 'widget_social-profile-icons',
                            'description' => __('A widget to display your social profile icons.', "spiw"));

        parent::__construct('social-profile-icons', __('Social Links', "spiw"), $widget_ops);
        $this->alt_option_name = 'widget_social-profile-icons';

        // Add new user fields
        add_filter('user_contactmethods', array($this, 'spiw_user_fields'));
    }

    function widget($args, $instance) {
        extract($args);  
        extract($instance);

        $title = (!empty($instance['title'])) ? $instance['title'] : __('Follow me', "spiw");   

        // See: wp-includes/default-widgets.php
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);              

        $output = '';
        $output .= $before_widget;

        if ($title) {
            $output .= $before_title . $title . $after_title;
        }

        // Print Widget Output
        $user = $this->get_current_user($instance);
        $output .= '<ul class="spiw">';

        foreach (self::$profiles as $key) {
            if (get_the_author_meta($key['css'], $user)) {
                // Set branded background color if monocron setting is deactivated
                $output .= '<li class="spiw-' . $key['css'] . '"';
                if(!isset($instance['monocron'])) {
                    $output .= ' style="background-color:' . $key['color'] . ';"';  
                }
                $output .= '>';
                $output .= '<a href="' . get_the_author_meta($key['css'], $user) .
                '"><i class="spiw-icon-' . $key['css'] . '"></i></a></li>';
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
        $instance = $new_instance;
        $instance['icon-size'] = $this->check_size(strip_tags($new_instance['icon-size']));
        $instance['border-radius'] = $this->check_size(strip_tags($new_instance['border-radius']));
        $instance['monocron-color'] = $this->check_color($new_instance['monocron-color']);
        return $instance;
    }

    function form($instance) {
        $title  = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $user = $this->get_current_user($instance);

        /**
         * Settings:
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
     * Validates size input
     * @param $css String
     */ 
    private function check_size($css) {
        $css = preg_replace("/[^0-9]/", "", $css);
        if(intval($css) < 100 and intval($css) > 0) {            
            return $css;
        }
    }

    /**
     * Returns the actual font size needed to scale the icon to desired size
     * icon_size = 1.5 * font_size;
     * @param $css String
     */ 
    private function get_actual_size($font_size) {
        return $font_size / 1.5;
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
        if(!$rounded = isset($instance['rounded'])) {
            $border_radius = isset($instance['border-radius']) ? esc_attr($instance['border-radius']) :
                self::$defaults["border-radius"];
            $output .= 
                $selector . ' > ul > li {                
                    border-radius:' . $border_radius . ';                
                }';
        } else {
            $output .= 
                $selector . ' > ul > li {                
                    border-radius: 100%;                
                }';
        }

        // Monocron setting
        if (isset($instance['monocron'])) {
            $output .=
                $selector . ' > ul > li > a {
                    color: ' . $instance['icon-color'] . ';
                }' .
                $selector . ' > ul > li {                
                    background-color: ' . $instance['monocron-color'] . ';
                }';
        }

        // Icon size
        if (isset($instance['icon-size']) && $instance['icon-size'] != "50px") {
           $output .= 
                $selector . ' > ul {
                    font-size: ' . $this->get_actual_size($instance['icon-size']) . 'px;
                }';
        }
        $output .= '</style>';
        return $this->minify($output);
    }

    /**
     * Minify CSS
     */
    private function minify($string) {
        return preg_replace('/\s*([{}|:;,])\s+/', '$1', $string);  
    }

    /**
     * Add User Profile fields
     */
    function spiw_user_fields($profile_fields) {
        foreach (self::$profiles as $key) {
            $profile_fields[$key['css']] = __($key['label'] . ' Profile Link', 'spiw');
        }
        return $profile_fields;
    }

}

/**
 * Register the widget
 */
function spiw_register_widgets() {
    register_widget('WP_Widget_social_profile_icons');
}
add_action('widgets_init', 'spiw_register_widgets');

/**
 * Enqueue scripts and styles.
 * css
 */
function spiw_scripts() { 
	wp_enqueue_style('spiw-css', plugins_url('/css/spiw.min.css', __FILE__ ));
}
add_action('wp_enqueue_scripts', 'spiw_scripts');

/**
 * Enqueue scripts and styles.
 * Color picker
 */
function spiw_admin_scripts($hook) {
    if ('widgets.php' != $hook) {
        return;
    }
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('spiw-colorpicker', plugins_url('/js/spiw-colorpicker.js', __FILE__),
        array('wp-color-picker'), false, true);
}
add_action('admin_enqueue_scripts', 'spiw_admin_scripts');