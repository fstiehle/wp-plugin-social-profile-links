<?php
/*
Plugin Name: Social Profile Icons Widget
Plugin URI:  http://fabianstiehle.com/spiw
Description: This describes my plugin in a short sentence
Version:     0.1
Author:      Fabian Stiehle
Author URI:  http://fabianstiehle.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: spiw
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/***
/* Social Links Widget
/***/
class WP_Widget_social_profile_icons extends WP_Widget {    
    
    /***
    /* Defines supported profiles and their order
    /***/    
    private static $profiles = array(
    'facebook' => '#3b5998',
    'twitter' => '#00aced',
    'gplus' => '#dd4b39',
    'pinterest' => '#cb2027',
    'instagram' => '#517fa4',
    'youtube' => '#bb0000',
    );
    
    /***
    /* Default widget settings
    /***/  
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

        /** wp-includes/default-widgets.php */
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );              

        $output = '';
        $output .= $before_widget;

        if ($title) {
            $output .= $before_title . $title . $after_title;
        }

        $user = $this->get_current_user($instance);
        $output .= '<ul id="spiw"' . '-sociallinks">';

        foreach (self::$profiles as $key => $color) {
            if (get_the_author_meta($key, $user)) {
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
        
        /** HTML Code **/ ?>   
        <!-- Title -->
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                <?php _e('Title:', "spiw"); ?>
            </label>

            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" 
                   name="<?php echo $this->get_field_name('title'); ?>"
                   type="text" value="<?php echo $title; ?>" />
        </p>

        <!-- User -->
        <p>
            <label for="<?php echo $this->get_field_id('users'); ?>">
                <?php _e('Chose a user to display:', "spiw"); ?>
            </label>

        <?php wp_dropdown_users(array('id' => $this->get_field_id('users'),
                             'name' => $this->get_field_name('users'), 'selected'=> $user)); ?>
        </p>

        <!-- Icon Size -->
        <?php $icon_size = isset($instance['icon-size']) ? esc_attr($instance['icon-size']) :
            self::$defaults["icon-size"]; ?>
        <p>
            <label for="<?php echo $this->get_field_id('icon-size'); ?>">
                <?php _e('Icon Size:', "spiw"); ?>
            </label>

            <input id="<?php echo $this->get_field_id('icon-size'); ?>" 
                   name="<?php echo $this->get_field_name('icon-size'); ?>"
                   type="text" size="4" value="<?php echo $icon_size; ?> size=" />
        </p>

        <!-- Border Radius -->
        <?php $border_radius = isset($instance['border-radius']) ?
            esc_attr($instance['border-radius']) : self::$defaults["border-radius"]; ?>
        <p>
            <label for="<?php echo $this->get_field_id('icon-size'); ?>">
                <?php _e('Border radius:', "spiw"); ?>
            </label>

            <input id="<?php echo $this->get_field_id('border-radius'); ?>" 
                   name="<?php echo $this->get_field_name('border-radius'); ?>"
                   type="text" size="4" value="<?php echo $border_radius; ?>" />
        </p>

        <!-- Rounded -->
        <?php $rounded = isset($instance['rounded']) ?
            True : False; ?>
        <p>
            <input id="<?php echo $this->get_field_id('rounded'); ?>" 
                   name="<?php echo $this->get_field_name('rounded'); ?>"
                   type="checkbox" <?php checked($rounded); ?> />
            
            <label for="<?php echo $this->get_field_id('rounded'); ?>">
                <?php _e('Round icons', "spiw"); ?>
            </label>
        </p>

        <!-- Monocron -->
        <?php $monocron = isset($instance['monocron']) ?
             True : False; ?>
        <p>
           <input id="<?php echo $this->get_field_id('monocron'); ?>" 
                   name="<?php echo $this->get_field_name('monocron'); ?>"
                   type="checkbox" <?php checked($monocron); ?> />
            
             <label for="<?php echo $this->get_field_id('monocron'); ?>">
                <?php _e('Monocron style', "spiw"); ?>
            </label><br />
            <p><?php _e('Icons will be displayed in a configurable color
            and fade into their branded color on mouse hover.', "spiw"); ?></p>
        </p>
        <!-- Monocron color -->
        <p>
            <?php $monocron_color = isset($instance['monocron-color']) ?
                esc_attr($instance['monocron-color']) : self::$defaults["monocron-color"]; ?>
            
            <input id="<?php echo $this->get_field_id('monocron-color'); ?>"
                   name="<?php echo $this->get_field_name('monocron-color'); ?>"
                   type="text" value="<?php echo $monocron_color; ?>" class="spiw-color-field" 
                   data-default-color="<?php echo self::$defaults["monocron-color"]; ?>" />
            <br />
            <label for="<?php echo $this->get_field_id('monocron-color'); ?>">
                <?php _e('Moncron background icon color', "spiw"); ?>
            </label>
        </p>
        <?php /** END HTML Code **/
    }

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
    
    private function check_size($css) {
        $css = preg_replace("/[^0-9]/", "", $css);
        if(intval($css) < 100 and intval($css) > 0) {            
            return $css . 'px';
        }
    }
    
    private function check_color($color) {
        if (preg_match( '/^#[a-f0-9]{6}$/i', $color)) {
            return $color;
        }
    }
    
    private function get_custom_css($instance) {
        $output = '<style media="screen" type="text/css">';
        
        if(!$rounded = isset($instance['rounded']) ? True : False) {
            $border_radius = isset($instance['border-radius']) ? esc_attr($instance['border-radius']) :
                self::$defaults["border-radius"];
        
            $output .= '
                .widget_social-profile-icons li,
                .widget_social-profile-icons .spiw-icon {                
                    border-radius:' . $border_radius . ';                
                }
            ';
        } else {
            $output .= '
                .widget_social-profile-icons li,
                .widget_social-profile-icons .spiw-icon {                
                    border-radius: 100%;                
                }
            ';
        }
        if (!isset($instance['monocron'])) {
            $output .= '
                .widget_social-profile-icons .spiw-icon {                
                    color: #fff;
                }
                .widget_social-profile-icons li:hover .spiw-icon {
	                opacity: 0.6;	
                }
            ';
        } else if ($instance['monocron-color'] != "#F5f5f5") {
           $output .= '
                .widget_social-profile-icons li {                
                    background-color: ' . $instance['monocron-color'] . ';
                }
            '; 
        }
        if (isset($instance['icon-size']) && $instance['icon-size'] != "40px") {
           $output .= '
                .widget_social-profile-icons ul {                
                    font-size: ' . $instance['icon-size'] . ';
                }
            '; 
        }
        $output .= '</style>';
        return $output;
    }
}


function spiw_register_widgets() {
    register_widget('WP_Widget_social_profile_icons');
}
add_action('widgets_init', 'spiw_register_widgets');

/***
/* Add new user fields
/***/
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

/**
 * Enqueue scripts and styles.
 */
function spiw_scripts() { 
	wp_enqueue_style('spiw-css', plugins_url('/css/spiw.css', __FILE__ ));
}
add_action('wp_enqueue_scripts', 'spiw_scripts');

function spiw_admin_scripts($hook) {
    if ('widgets.php' != $hook) {
        return;
    }
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('spiw-colorpicker', plugins_url('/js/spiw-colorpicker.js', __FILE__),
                    array('wp-color-picker'), false, true);
}
add_action('admin_enqueue_scripts', 'spiw_admin_scripts');