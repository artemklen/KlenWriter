<?php
/**
 * Admin settings page.
 *
 * Plugin site: https://klenwriter.arklen.ru
 *
 * @package KlenWriter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Settings -> KlenWriter.
 */
class KlenWriter_Settings {

	/**
	 * Settings page hook suffix.
	 *
	 * @var string
	 */
	private $page_hook = '';

	/**
	 * Registers admin hooks.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_post_klenwriter_reset_settings', array( $this, 'reset_settings' ) );
	}

	/**
	 * Adds the settings page under Settings.
	 *
	 * @return void
	 */
	public function add_settings_page() {
		$this->page_hook = add_options_page(
			esc_html__( 'KlenWriter Settings', 'klenwriter' ),
			esc_html__( 'KlenWriter', 'klenwriter' ),
			'manage_options',
			'klenwriter',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Registers the plugin option and settings fields.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'klenwriter_settings',
			KLENWRITER_OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_options' ),
				'default'           => KlenWriter::get_default_options(),
			)
		);

		add_settings_section(
			'klenwriter_main',
			esc_html__( 'Основные настройки', 'klenwriter' ),
			array( $this, 'render_section_intro' ),
			'klenwriter'
		);

		add_settings_field(
			'default_mode',
			esc_html__( 'Режим по умолчанию', 'klenwriter' ),
			array( $this, 'render_default_mode_field' ),
			'klenwriter',
			'klenwriter_main'
		);

		add_settings_field(
			'logo_id',
			esc_html__( 'Логотип автора', 'klenwriter' ),
			array( $this, 'render_logo_field' ),
			'klenwriter',
			'klenwriter_main'
		);

		add_settings_field(
			'hide_selectors',
			esc_html__( 'CSS-классы для скрытия', 'klenwriter' ),
			array( $this, 'render_hide_selectors_field' ),
			'klenwriter',
			'klenwriter_main'
		);

		add_settings_field(
			'dark_background_selectors',
			esc_html__( 'Элементы с белым фоном', 'klenwriter' ),
			array( $this, 'render_dark_background_selectors_field' ),
			'klenwriter',
			'klenwriter_main'
		);

		add_settings_field(
			'show_logo',
			esc_html__( 'Показывать логотип', 'klenwriter' ),
			array( $this, 'render_show_logo_field' ),
			'klenwriter',
			'klenwriter_main'
		);

		add_settings_field(
			'controls_position',
			esc_html__( 'Положение кнопок', 'klenwriter' ),
			array( $this, 'render_controls_position_field' ),
			'klenwriter',
			'klenwriter_main'
		);

		add_settings_field(
			'colors',
			esc_html__( 'Цвета тёмного режима', 'klenwriter' ),
			array( $this, 'render_color_fields' ),
			'klenwriter',
			'klenwriter_main'
		);

		add_settings_field(
			'reading_font_size',
			esc_html__( 'Размер шрифта в режиме чтения', 'klenwriter' ),
			array( $this, 'render_reading_font_size_field' ),
			'klenwriter',
			'klenwriter_main'
		);

		add_settings_field(
			'button_text',
			esc_html__( 'Текст кнопок', 'klenwriter' ),
			array( $this, 'render_button_text_fields' ),
			'klenwriter',
			'klenwriter_main'
		);
	}

	/**
	 * Sanitizes all plugin options before saving.
	 *
	 * @param array<string, mixed> $input Raw settings input.
	 * @return array<string, mixed>
	 */
	public function sanitize_options( $input ) {
		$defaults = KlenWriter::get_default_options();
		$input    = is_array( $input ) ? $input : array();

		$default_mode = isset( $input['default_mode'] ) ? sanitize_key( $input['default_mode'] ) : $defaults['default_mode'];
		if ( ! in_array( $default_mode, array( 'light', 'dark' ), true ) ) {
			$default_mode = $defaults['default_mode'];
		}

		$controls_position = isset( $input['controls_position'] ) ? sanitize_key( $input['controls_position'] ) : $defaults['controls_position'];
		if ( ! in_array( $controls_position, array( 'left', 'right' ), true ) ) {
			$controls_position = $defaults['controls_position'];
		}

		return array(
			'default_mode'              => $default_mode,
			'logo_id'                   => isset( $input['logo_id'] ) ? absint( $input['logo_id'] ) : 0,
			'hide_selectors'            => $this->sanitize_selector_list(
				isset( $input['hide_selectors'] ) ? $input['hide_selectors'] : $defaults['hide_selectors'],
				$defaults['hide_selectors']
			),
			'dark_background_selectors' => $this->sanitize_selector_list(
				isset( $input['dark_background_selectors'] ) ? $input['dark_background_selectors'] : $defaults['dark_background_selectors'],
				$defaults['dark_background_selectors']
			),
			'controls_position'         => $controls_position,
			'dark_color'                => $this->sanitize_hex_color_with_fallback(
				isset( $input['dark_color'] ) ? $input['dark_color'] : $defaults['dark_color'],
				$defaults['dark_color']
			),
			'light_color'               => $this->sanitize_hex_color_with_fallback(
				isset( $input['light_color'] ) ? $input['light_color'] : $defaults['light_color'],
				$defaults['light_color']
			),
			'reading_font_size'         => $this->sanitize_font_size(
				isset( $input['reading_font_size'] ) ? $input['reading_font_size'] : $defaults['reading_font_size'],
				$defaults['reading_font_size']
			),
			'show_logo'                 => ! empty( $input['show_logo'] ) ? 1 : 0,
			'dark_button_text'          => isset( $input['dark_button_text'] ) ? sanitize_text_field( wp_unslash( $input['dark_button_text'] ) ) : $defaults['dark_button_text'],
			'distraction_button_text'   => isset( $input['distraction_button_text'] ) ? sanitize_text_field( wp_unslash( $input['distraction_button_text'] ) ) : $defaults['distraction_button_text'],
		);
	}

	/**
	 * Sanitizes a comma-separated selector list for safe frontend output.
	 *
	 * @param mixed  $value Raw selector list.
	 * @param string $fallback Fallback selector list.
	 * @return string
	 */
	private function sanitize_selector_list( $value, $fallback ) {
		$value     = sanitize_text_field( wp_unslash( (string) $value ) );
		$selectors = array_filter( array_map( 'trim', explode( ',', $value ) ) );
		$allowed   = array();

		foreach ( $selectors as $selector ) {
			if ( preg_match( '/^[.#]?[a-zA-Z0-9_-]+(?:[.#][a-zA-Z0-9_-]+)*(?:\s+[.#]?[a-zA-Z0-9_-]+(?:[.#][a-zA-Z0-9_-]+)*)*$/', $selector ) ) {
				$allowed[] = $selector;
			}
		}

		if ( empty( $allowed ) ) {
			return $fallback;
		}

		return implode( ', ', $allowed );
	}

	/**
	 * Sanitizes a hex color and falls back when the value is invalid.
	 *
	 * @param mixed  $value Raw color value.
	 * @param string $fallback Fallback hex color.
	 * @return string
	 */
	private function sanitize_hex_color_with_fallback( $value, $fallback ) {
		$color = sanitize_hex_color( wp_unslash( (string) $value ) );

		return $color ? $color : $fallback;
	}

	/**
	 * Sanitizes the reading font size.
	 *
	 * @param mixed $value Raw font size.
	 * @param int   $fallback Fallback font size.
	 * @return int
	 */
	private function sanitize_font_size( $value, $fallback ) {
		$font_size = absint( $value );

		if ( $font_size < 14 || $font_size > 32 ) {
			return absint( $fallback );
		}

		return $font_size;
	}

	/**
	 * Enqueues media uploader scripts and admin styles only on this settings page.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( $hook !== $this->page_hook ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style(
			'klenwriter-admin',
			KLENWRITER_URL . 'assets/css/admin.css',
			array(),
			KLENWRITER_VERSION
		);
		wp_enqueue_script(
			'klenwriter-admin',
			KLENWRITER_URL . 'assets/js/admin.js',
			array( 'wp-color-picker' ),
			KLENWRITER_VERSION,
			true
		);
		wp_localize_script(
			'klenwriter-admin',
			'klenWriterAdmin',
			array(
				'title'       => esc_html__( 'Выбрать логотип', 'klenwriter' ),
				'buttonText'  => esc_html__( 'Использовать этот логотип', 'klenwriter' ),
				'placeholder' => esc_url( KLENWRITER_URL . 'assets/images/logo-placeholder.svg' ),
			)
		);
	}

	/**
	 * Renders the settings page wrapper.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap kw-admin-wrap">
			<h1><?php echo esc_html__( 'KlenWriter', 'klenwriter' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'klenwriter_settings' );
				do_settings_sections( 'klenwriter' );
				submit_button( esc_html__( 'Сохранить настройки', 'klenwriter' ) );
				?>
			</form>

			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="kw-reset-form">
				<?php wp_nonce_field( 'klenwriter_reset_settings', 'klenwriter_reset_nonce' ); ?>
				<input type="hidden" name="action" value="klenwriter_reset_settings" />
				<?php submit_button( esc_html__( 'Сбросить настройки по умолчанию', 'klenwriter' ), 'secondary', 'submit', false ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Renders a short section description.
	 *
	 * @return void
	 */
	public function render_section_intro() {
		echo '<p>' . esc_html__( 'Настрой внешний вид кнопок и элементы, которые скрываются в режиме чтения без отвлечений.', 'klenwriter' ) . '</p>';
	}

	/**
	 * Renders default color mode controls.
	 *
	 * @return void
	 */
	public function render_default_mode_field() {
		$options = KlenWriter::get_options();
		?>
		<select name="<?php echo esc_attr( KLENWRITER_OPTION_NAME ); ?>[default_mode]" id="klenwriter-default-mode">
			<option value="light" <?php selected( $options['default_mode'], 'light' ); ?>><?php echo esc_html__( 'Светлая тема', 'klenwriter' ); ?></option>
			<option value="dark" <?php selected( $options['default_mode'], 'dark' ); ?>><?php echo esc_html__( 'Тёмная тема', 'klenwriter' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Renders the media uploader logo field.
	 *
	 * @return void
	 */
	public function render_logo_field() {
		$options  = KlenWriter::get_options();
		$logo_id  = absint( $options['logo_id'] );
		$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'thumbnail' ) : KLENWRITER_URL . 'assets/images/logo-placeholder.svg';
		?>
		<div class="kw-logo-field">
			<img class="kw-logo-preview" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr__( 'Предпросмотр логотипа', 'klenwriter' ); ?>" />
			<input type="hidden" class="kw-logo-id" name="<?php echo esc_attr( KLENWRITER_OPTION_NAME ); ?>[logo_id]" value="<?php echo esc_attr( $logo_id ); ?>" />
			<button type="button" class="button kw-upload-logo"><?php echo esc_html__( 'Выбрать логотип', 'klenwriter' ); ?></button>
			<button type="button" class="button kw-remove-logo"><?php echo esc_html__( 'Убрать логотип', 'klenwriter' ); ?></button>
		</div>
		<?php
	}

	/**
	 * Renders selector textarea.
	 *
	 * @return void
	 */
	public function render_hide_selectors_field() {
		$options = KlenWriter::get_options();
		?>
		<textarea name="<?php echo esc_attr( KLENWRITER_OPTION_NAME ); ?>[hide_selectors]" rows="4" class="large-text code"><?php echo esc_textarea( $options['hide_selectors'] ); ?></textarea>
		<p class="description"><?php echo esc_html__( 'Укажи CSS-селекторы через запятую.', 'klenwriter' ); ?></p>
		<?php
	}

	/**
	 * Renders selectors that should receive a dark background in dark mode.
	 *
	 * @return void
	 */
	public function render_dark_background_selectors_field() {
		$options = KlenWriter::get_options();
		?>
		<textarea name="<?php echo esc_attr( KLENWRITER_OPTION_NAME ); ?>[dark_background_selectors]" rows="4" class="large-text code"><?php echo esc_textarea( $options['dark_background_selectors'] ); ?></textarea>
		<p class="description">
			<?php echo esc_html__( 'Если в тёмном режиме на сайте остаются белые блоки, добавь сюда их классы или ID через запятую. Например: .site-content, #content.', 'klenwriter' ); ?>
		</p>
		<?php
	}

	/**
	 * Renders show logo checkbox.
	 *
	 * @return void
	 */
	public function render_show_logo_field() {
		$options = KlenWriter::get_options();
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( KLENWRITER_OPTION_NAME ); ?>[show_logo]" value="1" <?php checked( (int) $options['show_logo'], 1 ); ?> />
			<?php echo esc_html__( 'Показывать логотип автора над кнопками', 'klenwriter' ); ?>
		</label>
		<?php
	}

	/**
	 * Renders frontend controls position selector.
	 *
	 * @return void
	 */
	public function render_controls_position_field() {
		$options = KlenWriter::get_options();
		?>
		<select name="<?php echo esc_attr( KLENWRITER_OPTION_NAME ); ?>[controls_position]" id="klenwriter-controls-position">
			<option value="right" <?php selected( $options['controls_position'], 'right' ); ?>><?php echo esc_html__( 'Справа', 'klenwriter' ); ?></option>
			<option value="left" <?php selected( $options['controls_position'], 'left' ); ?>><?php echo esc_html__( 'Слева', 'klenwriter' ); ?></option>
		</select>
		<p class="description"><?php echo esc_html__( 'Выбери, с какой стороны экрана показывать логотип и кнопки.', 'klenwriter' ); ?></p>
		<?php
	}

	/**
	 * Renders color picker fields for dark mode.
	 *
	 * @return void
	 */
	public function render_color_fields() {
		$options = KlenWriter::get_options();
		?>
		<label class="kw-field-line" for="klenwriter-dark-color">
			<span><?php echo esc_html__( 'Тёмный цвет фона', 'klenwriter' ); ?></span>
			<input
				id="klenwriter-dark-color"
				type="text"
				class="kw-color-field"
				name="<?php echo esc_attr( KLENWRITER_OPTION_NAME ); ?>[dark_color]"
				value="<?php echo esc_attr( $options['dark_color'] ); ?>"
				data-default-color="#111827"
			/>
		</label>
		<label class="kw-field-line" for="klenwriter-light-color">
			<span><?php echo esc_html__( 'Светлый цвет текста', 'klenwriter' ); ?></span>
			<input
				id="klenwriter-light-color"
				type="text"
				class="kw-color-field"
				name="<?php echo esc_attr( KLENWRITER_OPTION_NAME ); ?>[light_color]"
				value="<?php echo esc_attr( $options['light_color'] ); ?>"
				data-default-color="#e5e7eb"
			/>
		</label>
		<p class="description"><?php echo esc_html__( 'Эти цвета используются в тёмном режиме для фона и основного текста.', 'klenwriter' ); ?></p>
		<?php
	}

	/**
	 * Renders reading font size input.
	 *
	 * @return void
	 */
	public function render_reading_font_size_field() {
		$options = KlenWriter::get_options();
		?>
		<input
			id="klenwriter-reading-font-size"
			type="number"
			min="14"
			max="32"
			step="1"
			class="small-text"
			name="<?php echo esc_attr( KLENWRITER_OPTION_NAME ); ?>[reading_font_size]"
			value="<?php echo esc_attr( absint( $options['reading_font_size'] ) ); ?>"
		/>
		<span><?php echo esc_html__( 'px', 'klenwriter' ); ?></span>
		<p class="description"><?php echo esc_html__( 'Размер основного текста в режиме чтения без отвлечений. Допустимый диапазон: 14–32 px.', 'klenwriter' ); ?></p>
		<?php
	}

	/**
	 * Renders button text fields.
	 *
	 * @return void
	 */
	public function render_button_text_fields() {
		$options = KlenWriter::get_options();
		?>
		<label class="kw-field-line" for="klenwriter-dark-button-text">
			<span><?php echo esc_html__( 'Кнопка тёмного режима', 'klenwriter' ); ?></span>
			<input id="klenwriter-dark-button-text" type="text" class="regular-text" name="<?php echo esc_attr( KLENWRITER_OPTION_NAME ); ?>[dark_button_text]" value="<?php echo esc_attr( $options['dark_button_text'] ); ?>" />
		</label>
		<label class="kw-field-line" for="klenwriter-distraction-button-text">
			<span><?php echo esc_html__( 'Кнопка дистракшн-режима', 'klenwriter' ); ?></span>
			<input id="klenwriter-distraction-button-text" type="text" class="regular-text" name="<?php echo esc_attr( KLENWRITER_OPTION_NAME ); ?>[distraction_button_text]" value="<?php echo esc_attr( $options['distraction_button_text'] ); ?>" />
		</label>
		<?php
	}

	/**
	 * Resets plugin settings to defaults after nonce and capability checks.
	 *
	 * @return void
	 */
	public function reset_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Недостаточно прав.', 'klenwriter' ) );
		}

		check_admin_referer( 'klenwriter_reset_settings', 'klenwriter_reset_nonce' );
		update_option( KLENWRITER_OPTION_NAME, KlenWriter::get_default_options() );

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'             => 'klenwriter',
					'settings-updated' => 'true',
				),
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}
}
