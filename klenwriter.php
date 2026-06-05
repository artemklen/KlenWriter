<?php
/**
 * Plugin Name: KlenWriter
 * Description: Adds dark mode and distraction-free reading controls for single posts and pages.
 * Version:     1.0
 * Author:      Артёмка Клён
 * Text Domain: klenwriter
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 *
 * @package KlenWriter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KLENWRITER_VERSION', '1.0' );
define( 'KLENWRITER_FILE', __FILE__ );
define( 'KLENWRITER_PATH', plugin_dir_path( __FILE__ ) );
define( 'KLENWRITER_URL', plugin_dir_url( __FILE__ ) );
define( 'KLENWRITER_OPTION_NAME', 'klenwriter_options' );
define( 'KLENWRITER_ACTIVATION_NOTICE', 'klenwriter_activation_notice' );

if ( ! class_exists( 'KlenWriter' ) ) {
	/**
	 * Main plugin class.
	 */
	final class KlenWriter {

		/**
		 * Single plugin instance.
		 *
		 * @var KlenWriter|null
		 */
		private static $instance = null;

		/**
		 * Settings page controller.
		 *
		 * @var KlenWriter_Settings|null
		 */
		private $settings = null;

		/**
		 * Frontend controller.
		 *
		 * @var KlenWriter_Frontend|null
		 */
		private $frontend = null;

		/**
		 * Returns the single plugin instance.
		 *
		 * @return KlenWriter
		 */
		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Registers core plugin hooks.
		 */
		private function __construct() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'admin_notices', array( $this, 'show_activation_notice' ) );
		}

		/**
		 * Loads translation files.
		 *
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain(
				'klenwriter',
				false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages'
			);
		}

		/**
		 * Starts admin and frontend controllers after WordPress is ready.
		 *
		 * @return void
		 */
		public function init() {
			require_once KLENWRITER_PATH . 'includes/class-klenwriter-settings.php';
			require_once KLENWRITER_PATH . 'includes/class-klenwriter-frontend.php';

			$this->settings = new KlenWriter_Settings();
			$this->frontend = new KlenWriter_Frontend();
		}

		/**
		 * Runs on plugin activation.
		 *
		 * @return void
		 */
		public static function activate() {
			if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
				deactivate_plugins( plugin_basename( __FILE__ ) );
				wp_die(
					esc_html__( 'KlenWriter requires PHP 7.4 or higher.', 'klenwriter' ),
					esc_html__( 'Plugin activation failed', 'klenwriter' ),
					array( 'back_link' => true )
				);
			}

			if ( false === get_option( KLENWRITER_OPTION_NAME ) ) {
				add_option( KLENWRITER_OPTION_NAME, self::get_default_options() );
			}

			set_transient( KLENWRITER_ACTIVATION_NOTICE, true, 60 );
		}

		/**
		 * Runs on plugin deactivation.
		 *
		 * @return void
		 */
		public static function deactivate() {
			delete_transient( KLENWRITER_ACTIVATION_NOTICE );
		}

		/**
		 * Displays the activation notice once.
		 *
		 * @return void
		 */
		public function show_activation_notice() {
			if ( ! current_user_can( 'manage_options' ) || ! get_transient( KLENWRITER_ACTIVATION_NOTICE ) ) {
				return;
			}

			delete_transient( KLENWRITER_ACTIVATION_NOTICE );
			$settings_url = esc_url( admin_url( 'options-general.php?page=klenwriter' ) );
			$message      = sprintf(
				'%s <a href="%s">%s</a>.',
				esc_html__( 'KlenWriter активирован. Настрой внешний вид в разделе', 'klenwriter' ),
				$settings_url,
				esc_html__( 'Настройки → KlenWriter', 'klenwriter' )
			);

			printf(
				'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
				wp_kses(
					$message,
					array(
						'a' => array(
							'href' => array(),
						),
					)
				)
			);
		}

		/**
		 * Returns default plugin settings.
		 *
		 * @return array<string, mixed>
		 */
		public static function get_default_options() {
			return array(
				'default_mode'              => 'light',
				'logo_id'                   => 0,
				'hide_selectors'            => '.site-header, .site-footer, .sidebar, .widget-area, .comments-area, .navigation',
				'dark_background_selectors' => '.site-content, .site, .content-area, .site-main, .entry-content',
				'controls_position'         => 'right',
				'dark_color'                => '#111827',
				'light_color'               => '#e5e7eb',
				'reading_font_size'         => 18,
				'show_logo'                 => 1,
				'dark_button_text'          => __( 'Тёмный режим', 'klenwriter' ),
				'distraction_button_text'   => __( 'Читать без отвлечений', 'klenwriter' ),
			);
		}

		/**
		 * Returns saved options merged with defaults.
		 *
		 * @return array<string, mixed>
		 */
		public static function get_options() {
			$options = get_option( KLENWRITER_OPTION_NAME, array() );

			if ( ! is_array( $options ) ) {
				$options = array();
			}

			return wp_parse_args( $options, self::get_default_options() );
		}
	}
}

register_activation_hook( __FILE__, array( 'KlenWriter', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'KlenWriter', 'deactivate' ) );

KlenWriter::instance();
