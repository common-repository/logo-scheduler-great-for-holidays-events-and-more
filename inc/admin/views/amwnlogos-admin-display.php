<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://allmywebneeds.com
 * @since      1.0.0
 *
 * @author    All My Web Needs
 */

?>

<div class="wrap amwn-logo-settings">

  <h1 class="wp-heading-inline"><?php _e('Logo Scheduler Settings', $this->plugin_text_domain) ?></h1>

  <form action="options.php" method="post" id="amwnlogos-settings">

    <?php settings_fields( $this->plugin_name ); ?>
    <?php do_settings_sections( $this->plugin_name ); ?>

    <?php submit_button( __('Add Logo', $this->plugin_text_domain), 'secondary', 'add-logo', true ); ?>
    <?php
      submit_button();
    ?>

  </form>

</div>
