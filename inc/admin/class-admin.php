<?php

namespace AmwnLogos\Inc\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://allmywebneeds.com
 * @since      1.0.0
 *
 * @author    All My Web Needs
 */
class Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The text domain of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_text_domain    The text domain of this plugin.
     */
    private $plugin_text_domain;

    /**
     * Initialize the class and set its properties.
     *
     * @since       1.0.0
     * @param       string $plugin_name        The name of this plugin.
     * @param       string $version            The version of this plugin.
     * @param       string $plugin_text_domain The text domain of this plugin.
     */
    public function __construct($plugin_name, $version, $plugin_text_domain)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->plugin_text_domain = $plugin_text_domain;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'css/amwnlogos-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
    }

    /**
     * Callback for the admin menu
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu()
    {
        /*
		add_menu_page(
			__( 'AMWN Logos', $this->plugin_text_domain ), //page title
			__( 'AMWN Logos', $this->plugin_text_domain ), //menu title
			'manage_options', //capability
			$this->plugin_name,
			array(&$this, 'main_page')
		);
		*/

        $page_hook = add_submenu_page(
            'options-general.php',
            __('Logo Scheduler', $this->plugin_text_domain), //page title
            __('Logo Scheduler', $this->plugin_text_domain), //menu title
            'manage_options', //capability
            $this->plugin_name,
            array(&$this, 'main_page')
        );
    }

    /*
	 * Callback for the add_submenu_page action hook
	 *
	 * The plugin's HTML form is loaded from here
	 *
	 * @since	1.0.0
	 */
    public function main_page()
    {
        $wp_scripts = wp_scripts();
        wp_enqueue_media();
        wp_enqueue_style('dashicons');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script(
            'jquery-ui-timepicker',
            plugin_dir_url(__FILE__) . 'js/jquery-ui-timepicker-addon.min.js',
            array('jquery-ui-datepicker'),
            $this->version,
            false
        );

        wp_enqueue_style(
            'jquery-ui-theme-smoothness',
            plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css'
        );
        wp_enqueue_style(
            'jquery-ui-timepicker',
            plugin_dir_url(__FILE__) . 'css/jquery-ui-timepicker-addon.min.css'
        );

        $amwnlogos_name = get_option('amwnlogos_name');
        $amwnlogos_start_date = get_option('amwnlogos_start_date');
        $amwnlogos_end_date = get_option('amwnlogos_end_date');
        $amwnlogos_repeat = get_option('amwnlogos_repeat');
        $amwnlogos_logo_holder = get_option('amwnlogos_logo_holder');
        $amwnlogos_replacement_logo = get_option('amwnlogos_replacement_logo');
        $amwnlogos_replacement_logo_retina = get_option('amwnlogos_replacement_logo_retina');

        if (!empty($amwnlogos_name)) {
            $attachments = [];
            foreach ($amwnlogos_replacement_logo as $attachment_id) {
                $attachments[] = wp_get_attachment_url($attachment_id);
            }

            $retina_attachments = [];
            foreach ($amwnlogos_replacement_logo_retina as $attachment_id) {
                $retina_attachments[] = wp_get_attachment_url($attachment_id);
            }
        }

        $params = [
            'amwnlogos_name' => $amwnlogos_name,
            'amwnlogos_start_date' => $amwnlogos_start_date,
            'amwnlogos_end_date' => $amwnlogos_end_date,
            'amwnlogos_repeat' => $amwnlogos_repeat,
            'amwnlogos_logo_holder' => $amwnlogos_logo_holder,
            'amwnlogos_replacement_logo' => $amwnlogos_replacement_logo,
            'amwnlogos_replacement_logo_images' => $attachments,
            'amwnlogos_replacement_logo_retina' => $amwnlogos_replacement_logo_retina,
            'amwnlogos_replacement_logo_images_retina' => $retina_attachments,
            'amwnlogos_theme_logo_holder' => $this->get_theme_logo_holder(),
        ];

        wp_enqueue_script($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'js/amwnlogos-admin.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name . '-admin', 'params', $params);


        $logos_nonce = wp_create_nonce('amwn_logo_nonce');

        require_once 'views/amwnlogos-admin-display.php';
    }

    public function setup_sections()
    {
        add_settings_section(
            'amwn_logo_settings',
            __('Scheduled Logos', $this->plugin_text_domain),
            array($this, 'settings_section_callback'),
            $this->plugin_name
        );

        add_settings_section(
            'default',
            __('Settings', $this->plugin_text_domain),
            array($this, 'settings_section_callback'),
            $this->plugin_name
        );
    }

    public function settings_section_callback($args)
    {
        switch ($args['id']) {
            case 'default':
                break;
            case 'amwn_logo_settings':
                break;
            default:
                break;
        }
    }

    private function get_theme_logo_holder()
    {
        $the_theme = wp_get_theme();

        $theme_name = "";
        if ($the_theme->parent() && $the_theme->parent()->Name != "") {
            $theme_name = $the_theme->parent()->Name;
        } else if ($the_theme->Template != "") {
            $theme_name = $the_theme->Template;
        } else {
            $theme_name = $the_theme->Name;
        }
        $theme_name = strtolower($theme_name);

        $logo_holder = "";
        switch ($theme_name) {
            case "astra":
                $logo_holder = 'site-logo-img';
                break;
            case "avada":
                $logo_holder = 'fusion-logo';
                break;
            case "generatepress":
                $logo_holder = 'navigation-logo';
                break;
            case "hestia":
                $logo_holder = 'title-logo-wrapper';
                break;
            case "oceanwp":
                $logo_holder = 'site-logo-inner';
                break;
            case "shapely":
            case "storefront":
            case "sydney":
            case "twentyeleven":
            case "twentytwelve":
            case "twentythirteen":
            case "twentyfourteen":
            case "twentyfifteen":
            case "twentysixteen":
            case "twentyseventeen":
            case "twentynineteen":
                $logo_holder = 'custom-logo-link';
                break;
            default:
                break;
        }

        return $logo_holder;
    }

    public function setup_settings_fields()
    {
        $the_theme = wp_get_theme();
        $logo_holders = get_option('amwnlogos_logo_holder');

        $logo_holder = "";
        if (isset($logo_holders[0])) {
            $logo_holder = $logo_holders[0];
        } else {
            $logo_holder = $this->get_theme_logo_holder();
        }

        // Get regular image
        $logo_images = get_option('amwnlogos_replacement_logo');
        $logo_image = '';
        if (isset($logo_images[0])) {
            $logo_image = wp_get_attachment_url($logo_images[0]);
        }

        // Get retina image
        $logo_images = get_option('amwnlogos_replacement_logo_retina');
        $logo_image_retina = '';
        if (isset($logo_images[0])) {
            $logo_image_retina = wp_get_attachment_url($logo_images[0]);
        }

        $fields = [
            [
                'uid' => 'amwnlogos_show_credit',
                'name' => 'amwnlogos_show_credit',
                'label' => __('Share the love. Check this box to show a credit link for this plugin at the bottom of this website.', $this->plugin_text_domain),
                'section' => 'default',
                'type' => 'checkbox',
                'options' => false,
                'supplemental' => '',
                'default' => get_option('amwnlogos_show_credit'),
            ],
            [
                'uid' => 'amwnlogos_name',
                'name' => 'amwnlogos_name[]',
                'label' => __('Logo Name', $this->plugin_text_domain),
                'section' => 'amwn_logo_settings',
                'type' => 'text',
                'options' => false,
                'placeholder' => 'Logo Name',
                'helper' => __('Title of this logo', $this->plugin_text_domain),
                'supplemental' => '',
            ],
            [
                'uid' => 'amwnlogos_start_date',
                'name' => 'amwnlogos_start_date[]',
                'label' => __('Start Date & Time', $this->plugin_text_domain),
                'section' => 'amwn_logo_settings',
                'type' => 'text',
                'options' => false,
                'placeholder' => '',
                'helper' => __('Please specify the start date & time for the logo', $this->plugin_text_domain),
                'supplemental' => '',
                'attributes' => 'class="datepicker" readonly="readonly"',
            ],
            [
                'uid' => 'amwnlogos_end_date',
                'name' => 'amwnlogos_end_date[]',
                'label' => __('End Date & Time', $this->plugin_text_domain),
                'section' => 'amwn_logo_settings',
                'type' => 'text',
                'options' => false,
                'placeholder' => '',
                'helper' => __('Please specify the end date & time for the logo', $this->plugin_text_domain),
                'supplemental' => '',
                'default' => get_option('amwnlogos_end_date'),
                'attributes' => 'class="datepicker" readonly="readonly"',
            ],
            [
                'uid' => 'amwnlogos_repeat',
                'name' => 'amwnlogos_repeat[]',
                'label' => __('Repeat Yearly', $this->plugin_text_domain),
                'section' => 'amwn_logo_settings',
                'type' => 'select',
                'options' => ['off' => 'No Repeat', 'on' => 'Repeat'],
                'helper' => __('Repeat logo each year on the same dates', $this->plugin_text_domain),
                'supplemental' => '',
                //'default' => get_option('amwnlogos_repeat'),
            ],
            [
                'uid' => 'logo_upload_button',
                'label' => __('Upload Logo', $this->plugin_text_domain),
                'section' => 'amwn_logo_settings',
                'type' => 'button',
                'options' => false,
                'attributes' => 'class="button logo-upload_button" type="button" data-type="regular"',
            ],
            [
                'uid' => 'amwnlogos_replacement_logo',
                'name' => 'amwnlogos_replacement_logo[]',
                'label' => __('Replacement Logo', $this->plugin_text_domain),
                'section' => 'amwn_logo_settings',
                'type' => 'text',
                'options' => false,
                'supplemental' => '',
                'default' => get_option('amwnlogos_replacement_logo'),
                'attributes' => 'readonly="readonly"',
            ],
            [
                'uid' => 'amwnlogos_replacement_logo_image',
                'label' => __('Replacement Logo', $this->plugin_text_domain),
                'section' => 'amwn_logo_settings',
                'type' => 'image',
                'options' => false,
                'supplemental' => '',
                'container' => 'amwn-logo-image',
                'default' => $logo_image,
            ],
            [
                'uid' => 'logo_upload_button_retina',
                'label' => __('Upload Retina Logo', $this->plugin_text_domain),
                'section' => 'amwn_logo_settings',
                'type' => 'button',
                'options' => false,
                'attributes' => 'class="button logo-upload_button" type="button" data-type="retina"',
            ],
            [
                'uid' => 'amwnlogos_replacement_logo_retina',
                'name' => 'amwnlogos_replacement_logo_retina[]',
                'label' => __('Retina Logo ID', $this->plugin_text_domain),
                'section' => 'amwn_logo_settings',
                'type' => 'text',
                'options' => false,
                'supplemental' => '',
                //'default' => get_option('amwnlogos_replacement_logo'),
                'attributes' => 'readonly="readonly"',
            ],
            [
                'uid' => 'amwnlogos_replacement_logo_image_retina',
                'label' => __('Retina Logo', $this->plugin_text_domain),
                'section' => 'amwn_logo_settings',
                'type' => 'image',
                'options' => false,
                'supplemental' => '',
                'container' => 'amwn-logo-image-retina',
                'default' => $logo_image_retina,
            ],
            [
                'uid' => 'amwnlogos_logo_holder',
                'name' => 'amwnlogos_logo_holder[]',
                'label' => __('Logo Container ID', $this->plugin_text_domain),
                'section' => 'amwn_logo_settings',
                'type' => 'text',
                'options' => false,
                'placeholder' => 'DIV id',
                'helper' => __('Unique ID of the &lt;div&gt; tag containing the logo.', $this->plugin_text_domain),
                'supplemental' => '',
                'default' => $logo_holder,
            ],
        ];
        foreach ($fields as $field) {
            add_settings_field($field['uid'], $field['label'], array($this, 'settings_field_callback'), $this->plugin_text_domain, $field['section'], $field);
            if($field['uid'] == "amwnlogos_name" || $field['uid'] == "amwnlogos_logo_holder"){
                register_setting($this->plugin_text_domain, $field['uid'], array($this, "sanitize_cb"));
            }else{
                register_setting($this->plugin_text_domain, $field['uid']);
            }
            
        }
    }

    public function sanitize_cb($input){

        if(!empty($input)){
            foreach($input as $ipk => $ipv){
                $input[$ipk] = sanitize_text_field($ipv);
            }
        }
        
        
        return $input;

    }

    public function settings_field_callback($arguments)
    {
        $value = get_option($arguments['uid']);
        
        if (!$value) {
            $value = isset($arguments['default']) ? $arguments['default'] : "";
        }

        // Check which type of field we want
        switch ($arguments['type']) {
            case 'hidden':
                printf(
                    '<input name="%1$s" id="%2$s" type="%3$s" value="%4$s" %5$s />',
                    $arguments['name'],
                    $arguments['uid'],
                    $arguments['type'],
                    $value,
                    $arguments['attributes']
                );
                break;
            case 'text':
                printf(
                    '<input name="%1$s" id="%2$s" type="%3$s" placeholder="%4$s" value="%5$s" %6$s />',
                    $arguments['name'],
                    $arguments['uid'],
                    $arguments['type'],
                    $arguments['placeholder'],
                    $value,
                    $arguments['attributes']
                );
                break;
            case 'textarea':
                printf(
                    '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>',
                    $arguments['uid'],
                    $arguments['placeholder'],
                    $value
                );
                break;
            case 'select':
                if (!empty($arguments['options']) && is_array($arguments['options'])) {
                    $options_markup = '';
                    foreach ($arguments['options'] as $key => $label) {
                        $options_markup .= sprintf('<option value="%s" %s>%s</option>', $key, selected($value, $key, false), $label);
                    }
                    printf('<select name="%1$s" id="%2$s">%3$s</select>', $arguments['name'], $arguments['uid'], $options_markup);
                }
                break;
            case 'radio':
                if (!empty($arguments['options']) && is_array($arguments['options'])) {
                    $options_markup = '';
                    foreach ($arguments['options'] as $key => $label) {
                        $options_markup .= sprintf('<label>%1$s</label>', $label);
                        $options_markup .= sprintf(
                            '<input type="radio" class="radio" id="%1$s" name="%2$s" value="%3$s" />',
                            $arguments['id'],
                            $arguments['name'],
                            $key
                        );
                    }
                    print $options_markup;
                }
                break;
            case 'checkbox':
                $options_markup  = sprintf(
                    '<input type="hidden" id="%1$s" name="%2$s" value="off" />',
                    $arguments['id'],
                    $arguments['name']
                );
                $options_markup .= sprintf(
                    '<input type="checkbox" class="checkbox" id="%1$s" name="%2$s" value="on" %3$s />',
                    $arguments['id'],
                    $arguments['name'],
                    checked($value, 'on', false)
                );

                echo $options_markup;
                break;
            case 'button':
                printf(
                    '<button name="%1$s" id="%1$s" %2$s>%3$s</button>',
                    $arguments['uid'],
                    $arguments['attributes'],
                    $arguments['label']
                );
                break;
            case 'file':
                printf(
                    '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" %5$s />',
                    $arguments['uid'],
                    $arguments['type'],
                    $arguments['placeholder'],
                    $value,
                    $arguments['attributes']
                );
                break;
            case 'image':
                printf('<div class="%s" style="%s">
					<div class="close">x</div>
					<img src="%s" alt="Custom Logo" style="max-width:300px;" />
					</div>', $arguments['container'], $value != "" ? "" : "display:none;", $value);
                break;
        }

        // If there is help text
        if ($helper = $arguments['helper']) {
            printf('<span class="helper"> %s</span>', $helper);
        }

        // If there is supplemental text
        if ($supplimental = $arguments['supplemental']) {
            printf('<p class="description">%s</p>', $supplimental);
        }
    }

    public function add_plugin_page_settings_link($links)
    {
        $links[] = '<a href="' .
            admin_url('options-general.php?page=amwnlogos') .
            '">' . __('Settings') . '</a>';

        return $links;
    }

    public function share_admin_notice()
    {
        global $pagenow;

        if ($pagenow == 'options-general.php' && $_GET['page'] == $this->plugin_name) {
            $show_credit = get_option('amwnlogos_show_credit');

            if ($show_credit == 'off') {
                echo '<div class="notice notice-info is-dismissible">
		          	<p>Share the Love. Check this box to show a credit link for this plugin at the bottom of this website.
								<input type="checkbox" class="checkbox" id="share-notice" name="amwnlogos_show_credit" value="on">
								</p>
							</div>';
            }
        }
    }
}
