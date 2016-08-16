<?php
/*
Plugin Name: Social Profile Icons Widget
Plugin URI:  http://fabianstiehle.com/spiw
Description: Displays social profile icons based on user profiles. Easy to use and highly customizable.
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
        'icon-size' => "50",
        'border-radius' => "2",
        'icon-color' => "#ffffff",
        'monocron-color' => "#cccccc",
        'rounded' => False,
        'monocron' => False,
        'title' => 'Follow me'
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

        $title = (!isset($instance['title'])) ? $instance['title'] : __(self::$defaults["title"], "spiw");

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
                $output .= '<li class="spiw-' . $key['css'];
                if (!$instance['monocron']) {
                    // add class for non-monocron-icons
                    $output .= ' non-mono"';
                    // add background color
                    $output .= ' style="background-color:' . $key['color'] . ';"';  
                } else {
                    // add class for monocron-icons
                    $output .= ' mono"';
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
        $instance['title'] = sanitize_title($new_instance['title']);
        $instance['users'] = sanitize_user($new_instance['users']);
        $instance['rounded'] = $this->sanitize_checkbox($new_instance['rounded']);
        $instance['monocron'] = $this->sanitize_checkbox($new_instance['monocron']);
        $instance['icon-size'] = $this->sanitize_size($new_instance['icon-size']);
        $instance['border-radius'] = $this->sanitize_size($new_instance['border-radius']);
        $instance['monocron-color'] = $this->sanitize_color($new_instance['monocron-color']);
        $instance['icon-color'] = $this->sanitize_color($new_instance['icon-color']);
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
     * Sanitize Checkbox
     * 
     * Sanitization callback for 'checkbox' type controls.
     * This callback sanitizes $input as a Boolean value, either
     * TRUE or FALSE.
     * Source: https://github.com/WPTRT/code-examples/blob/master/customizer/sanitization-callbacks.php
     */
    private function sanitize_checkbox($input) {

        // Boolean check 
        return ((isset($input) && True == $input) ? True : False);
    }

    /**
     * Validates size input
     * @param $size String
     */ 
    private function sanitize_size($size) {
        $size = preg_replace("/[^0-9]/", "", $size);
        if(intval($size) < 100 and intval($size) > 0) {            
            return $size;
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
    private function sanitize_color($color) {
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
        if(!$rounded = $instance['rounded']) {
            $border_radius = esc_attr($instance['border-radius']);
            $output .= 
                $selector . ' > ul > li {                
                    border-radius:' . $border_radius . 'px;                
                }';
        } else {
            $output .= 
                $selector . ' > ul > li {                
                    border-radius: 100%;                
                }';
        }

        // Monocron setting
        if ($instance['monocron']) {
            $output .=
                $selector . ' > ul > li > a {
                    color: ' . $instance['icon-color'] . ';
                }' .
                $selector . ' > ul > li {                
                    background-color: ' . $instance['monocron-color'] . ';
                }';
        }

        // Icon size
        if ($instance['icon-size'] != "50px") {
           $icon_size = esc_attr($this->get_actual_size($instance['icon-size']));
           $output .= 
                $selector . ' > ul {
                    font-size: ' . $icon_size . 'px;
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