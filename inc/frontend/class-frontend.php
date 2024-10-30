<?php

namespace AmwnLogos\Inc\Frontend;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @link       https://allmywebneeds.com
 * @since      1.0.0
 *
 * @author    All My Web Needs
 */
class Frontend {

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
	public function __construct( $plugin_name, $version, $plugin_text_domain ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_text_domain = $plugin_text_domain;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name . '-frontend', plugin_dir_url( __FILE__ ) . 'css/amwnlogos-frontend.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->plugin_name . '-frontend', plugin_dir_url( __FILE__ ) . 'js/amwnlogos-frontend.js', array( 'jquery' ), $this->version.rand(1,10000), false );
		$params = [
			'amwnlogos_name' => get_option('amwnlogos_name'),
			'amwnlogos_start_date' => get_option('amwnlogos_start_date'),
			'amwnlogos_end_date' => get_option('amwnlogos_end_date'),
			'amwnlogos_repeat' => get_option('amwnlogos_repeat'),
			'amwnlogos_replacement_logo' => get_option('amwnlogos_replacement_logo'),
			'amwnlogos_replacement_logo_retina' => get_option('amwnlogos_replacement_logo_retina'),
			'amwnlogos_logo_holder' => get_option('amwnlogos_logo_holder'),
		];

// print_r ($params); die();
		
		if ($params['amwnlogos_name']) {
			$count = intval( count($params['amwnlogos_name']) );
			for ($i=0; $i<$count; $i++) {
				if ($params['amwnlogos_repeat'][$i] == 'on') {
					$start_time = strtotime($params['amwnlogos_start_date'][$i]);
					$end_time = strtotime($params['amwnlogos_end_date'][$i]);
					$start_string = sprintf('%s %s, %s', date('F', $start_time), date('j', $start_time), date('Y'));
					$end_string = sprintf('%s %s, %s', date('F', $end_time), date('j', $end_time), date('Y'));
					if ( strtotime($start_string) < current_time('timestamp') && strtotime($end_string) > current_time('timestamp') ) {
						$attachment = wp_get_attachment_url( $params['amwnlogos_replacement_logo'][$i] );
						$attachment_retina = wp_get_attachment_url( $params['amwnlogos_replacement_logo_retina'][$i] );
						$params = [
							'scheduled_logo' => true,
							'scheduled_logo_url' => $attachment,
							'scheduled_logo_url_retina' => $attachment_retina,
							'scheduled_logo_container' => $params['amwnlogos_logo_holder'][$i],
						];
						wp_localize_script( $this->plugin_name . '-frontend', 'params', $params);
					}
				} else {
					if ( strtotime($params['amwnlogos_start_date'][$i]) < current_time('timestamp')
							 && strtotime($params['amwnlogos_end_date'][$i]) > current_time('timestamp') ) {
						$attachment = wp_get_attachment_url( $params['amwnlogos_replacement_logo'][$i] );
						$attachment_retina = wp_get_attachment_url( $params['amwnlogos_replacement_logo_retina'][$i] );
			 			$params = [
			 				'scheduled_logo' => true,
			 				'scheduled_logo_url' => $attachment,
							'scheduled_logo_url_retina' => $attachment_retina,
			 				'scheduled_logo_container' => $params['amwnlogos_logo_holder'][$i],
			 			];
			 			wp_localize_script( $this->plugin_name . '-frontend', 'params', $params);
					}
				}
			}
		} // if ($params['amwnlogos_name']) {

		/*
		if ( strtotime(get_option('amwnlogos_start_date')) < current_time('timestamp') && strtotime(get_option('amwnlogos_end_date')) > current_time('timestamp') ) {
			$attachment = wp_get_attachment_url(get_option('amwnlogos_replacement_logo'));
			$params = [
				'scheduled_logo' => true,
				'scheduled_logo_url' => $attachment,
				'scheduled_logo_container' => get_option('amwnlogos_logo_holder'),
			];
			wp_localize_script( $this->plugin_name . '-frontend', 'params', $params);
		}
		*/
		wp_enqueue_script( $this->plugin_name . '-frontend' );

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */


	}

	public function amwnlogos_footer() {
		$show_credit = get_option('amwnlogos_show_credit');

		$url = 'http://' . $_SERVER['SERVER_NAME'];
		$url_parts = explode(".", parse_url($url, PHP_URL_HOST));
		$tld = end($url_parts);
		$domain_letter = substr($url_parts[count($url_parts)-2], 0, 1);
		$destination = 'https://allmywebneeds.com/';
		$anchor_text = 'All My Web Needs';
		$footer_text = 'Logo scheduler plugin was brought to you by the website developers at <a href="%s" target="_blank">%s</a>';

		if ( in_array($tld, ['com','org','net']) ) {
			if ($domain_letter >= 'a' && $domain_letter <= 'e') {
				$anchor_text = 'All My Web Needs';
			} else if ($domain_letter >= 'f' && $domain_letter <= 'j') {
				$anchor_text = 'allmywebneeds.com';
			} else if ($domain_letter >= 'k' && $domain_letter <= 'o') {
				$anchor_text = 'allmywebneeds';
			} else if ($domain_letter >= 'p' && $domain_letter <= 't') {
				$anchor_text = 'https://allmywebneeds.com/';
			} else {
				$anchor_text = 'www.allmywebneeds.com';
			}
		} else if ($tld == 'gov') {
			$anchor_text = 'website developer';
		} else if ($tld == 'edu') {
			$anchor_text = 'website designers';
			$footer_text = 'Logo scheduler plugin was brought to you by the website designers at <a href="%s" target="_blank">%s</a>';
		} else {
			if ($domain_letter >= 'a' && $domain_letter <= 'e') {
				$destination = 'https://www.facebook.com/allmywebneeds/';
			} else if ($domain_letter >= 'f' && $domain_letter <= 'j') {
				$destination = 'http://www.citysearch.com/profile/803109240/franklin_tn/all_my_web_needs.html';
			} else if ($domain_letter >= 'k' && $domain_letter <= 'o') {
				$destination = 'https://www.local.com/business/details/nashville-tn/all-my-web-needs-139524542';
			} else if ($domain_letter >= 'p' && $domain_letter <= 't') {
				$destination = 'https://allmywebneeds.optimizelocation.com/partnerpages/whiteandyellowpages/all-my-web-needs-nashville-tennessee-us';
			} else {
				$destination = 'https://local.yahoo.com/info-167704373?guccounter=1';
			}
		}

		$footer_text = sprintf($footer_text, $destination, $anchor_text);

		echo sprintf('<div class="amwnlogos-footer" %s>
			<div>%s</div>
			</div>',
			$show_credit != "on" ? 'style="display:none;"':'', $footer_text);

	}

}
