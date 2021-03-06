<!-- Used to display the widget options -->

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
    <?php _e('Update your social media accounts in your user profile "Users" - "Your Profile"', "spiw"); ?>
</p>

<p>
    <label for="<?php echo $this->get_field_id('users'); ?>">
        <?php _e('Chose an user to display:', "spiw"); ?>
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
           type="text" size="4" value="<?php echo $icon_size; ?>" />
    
    <label for="<?php echo $this->get_field_id('icon-size'); ?>">
        <?php _e('px', "spiw"); ?>
    </label>
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
    
    <label for="<?php echo $this->get_field_id('icon-size'); ?>">
        <?php _e('px', "spiw"); ?>
    </label>
</p>

<!-- Rounded -->
<?php $rounded = isset($instance['rounded']) ?
    esc_attr($instance['rounded']) : self::$defaults["rounded"]; ?>
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
    esc_attr($instance['monocron']) : self::$defaults["monocron"]; ?>
<p>
   <input id="<?php echo $this->get_field_id('monocron'); ?>" 
           name="<?php echo $this->get_field_name('monocron'); ?>"
           type="checkbox" <?php checked($monocron); ?> />

    <label for="<?php echo $this->get_field_id('monocron'); ?>">
        <?php _e('Monocron style', "spiw"); ?>
    </label><br />
    <p><?php _e('If checked icons will be displayed in a configurable color
    and fade into their branded color on mouse hover.', "spiw"); ?></p>
</p>

<!-- Monocron color -->
<p>
    <!--Background color -->
    <?php $monocron_color = isset($instance['monocron-color']) ?
        esc_attr($instance['monocron-color']) : self::$defaults["monocron-color"]; ?>
    <p>
    <input id="<?php echo $this->get_field_id('monocron-color'); ?>"
           name="<?php echo $this->get_field_name('monocron-color'); ?>"
           type="text" value="<?php echo $monocron_color; ?>" class="spiw-color-field" 
           data-default-color="<?php echo self::$defaults["monocron-color"]; ?>" />
    <br />
    <label for="<?php echo $this->get_field_id('monocron-color'); ?>">
        <?php _e('Monocron background icon color', "spiw"); ?>
    </label>
    </p>
    
    <!--Font/Icon color -->
    <?php $icon_color = isset($instance['icon-color']) ?
        esc_attr($instance['icon-color']) : self::$defaults["icon-color"]; ?>
    <p>
    <input id="<?php echo $this->get_field_id('icon-color'); ?>"
           name="<?php echo $this->get_field_name('icon-color'); ?>"
           type="text" value="<?php echo $icon_color; ?>" class="spiw-color-field" 
           data-default-color="<?php echo self::$defaults["icon-color"]; ?>" />
    <br />
    <label for="<?php echo $this->get_field_id('icon-color'); ?>">
        <?php _e('Monocron icon color', "spiw"); ?>
    </label>
    </p>
</p>