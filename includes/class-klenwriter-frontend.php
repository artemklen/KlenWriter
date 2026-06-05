<?php
/**
 * Frontend output and assets.
 *
 * @package KlenWriter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles frontend controls for posts and pages.
 */
class KlenWriter_Frontend {

	/**
	 * Tracks whether controls were already printed.
	 *
	 * @var bool
	 */
	private $controls_rendered = false;

	/**
	 * Registers frontend hooks.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_body_open', array( $this, 'render_controls' ) );
		add_action( 'wp_footer', array( $this, 'render_controls' ) );
	}

	/**
	 * Checks whether KlenWriter controls should be active on the current request.
	 *
	 * @return bool
	 */
	private function should_render() {
		return ! is_admin() && ( is_single() || is_page() );
	}

	/**
	 * Enqueues frontend CSS and JavaScript only on single posts and pages.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		if ( ! $this->should_render() ) {
			return;
		}

		$options = KlenWriter::get_options();

		wp_enqueue_style(
			'klenwriter-dark-mode',
			KLENWRITER_URL . 'assets/css/dark-mode.css',
			array(),
			KLENWRITER_VERSION
		);

		wp_enqueue_style(
			'klenwriter-distraction-mode',
			KLENWRITER_URL . 'assets/css/distraction-mode.css',
			array( 'klenwriter-dark-mode' ),
			KLENWRITER_VERSION
		);

		wp_add_inline_style( 'klenwriter-dark-mode', $this->get_custom_css( $options ) );

		wp_enqueue_script(
			'klenwriter-frontend',
			KLENWRITER_URL . 'assets/js/klenwriter.js',
			array(),
			KLENWRITER_VERSION,
			true
		);

		wp_localize_script(
			'klenwriter-frontend',
			'klenWriterSettings',
			array(
				'defaultMode'             => esc_attr( $options['default_mode'] ),
				'hideSelectors'           => esc_attr( $options['hide_selectors'] ),
				'darkBackgroundSelectors' => esc_attr( $options['dark_background_selectors'] ),
				'cookieDays'              => 180,
			)
		);
	}

	/**
	 * Builds custom CSS variables from sanitized settings.
	 *
	 * @param array<string, mixed> $options Plugin options.
	 * @return string
	 */
	private function get_custom_css( $options ) {
		$dark_color        = sanitize_hex_color( $options['dark_color'] );
		$light_color       = sanitize_hex_color( $options['light_color'] );
		$reading_font_size = absint( $options['reading_font_size'] );
		$dark_selectors    = $this->prefix_selector_list( $options['dark_background_selectors'], 'body.kw-dark-mode' );

		if ( ! $dark_color ) {
			$dark_color = '#111827';
		}

		if ( ! $light_color ) {
			$light_color = '#e5e7eb';
		}

		if ( $reading_font_size < 14 || $reading_font_size > 32 ) {
			$reading_font_size = 18;
		}

		$css = sprintf(
			':root{--kw-dark-color:%1$s;--kw-light-color:%2$s;--kw-reading-font-size:%3$dpx;}',
			esc_html( $dark_color ),
			esc_html( $light_color ),
			$reading_font_size
		);

		if ( $dark_selectors ) {
			$css .= sprintf(
				'%1$s{background:%2$s!important;color:%3$s!important;}',
				$dark_selectors,
				esc_html( $dark_color ),
				esc_html( $light_color )
			);
		}

		return $css;
	}

	/**
	 * Prefixes a comma-separated selector list with a parent selector.
	 *
	 * @param string $selectors Raw selector list.
	 * @param string $prefix Parent selector.
	 * @return string
	 */
	private function prefix_selector_list( $selectors, $prefix ) {
		$selectors = array_filter( array_map( 'trim', explode( ',', (string) $selectors ) ) );
		$prefixed  = array();

		foreach ( $selectors as $selector ) {
			if ( preg_match( '/^[.#]?[a-zA-Z0-9_-]+(?:[.#][a-zA-Z0-9_-]+)*(?:\s+[.#]?[a-zA-Z0-9_-]+(?:[.#][a-zA-Z0-9_-]+)*)*$/', $selector ) ) {
				$prefixed[] = $prefix . ' ' . $selector;
			}
		}

		return implode( ',', $prefixed );
	}

	/**
	 * Builds an inline style attribute for the selected controls side.
	 *
	 * @param string $position Selected position.
	 * @return string
	 */
	private function get_controls_style( $position ) {
		if ( 'left' === $position ) {
			return 'left:18px;right:auto;';
		}

		return 'right:18px;left:auto;';
	}

	/**
	 * Renders the floating frontend controls.
	 *
	 * @return void
	 */
	public function render_controls() {
		if ( $this->controls_rendered || ! $this->should_render() ) {
			return;
		}

		$this->controls_rendered = true;
		$options           = KlenWriter::get_options();
		$controls_position = 'left' === $options['controls_position'] ? 'left' : 'right';
		$controls_style    = $this->get_controls_style( $controls_position );
		$logo_url          = $this->get_logo_url( absint( $options['logo_id'] ) );
		?>
		<div class="kw-controls kw-controls-position-<?php echo esc_attr( $controls_position ); ?>" style="<?php echo esc_attr( $controls_style ); ?>" data-kw-controls>
			<?php if ( ! empty( $options['show_logo'] ) && $logo_url ) : ?>
				<img class="kw-author-logo" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr__( 'Логотип автора', 'klenwriter' ); ?>" />
			<?php endif; ?>

			<button class="kw-toggle kw-toggle-dark" type="button" data-kw-toggle="dark" aria-pressed="false">
				<span class="kw-toggle-icon" aria-hidden="true">☾</span>
				<span class="kw-toggle-text"><?php echo esc_html( $options['dark_button_text'] ); ?></span>
			</button>

			<button class="kw-toggle kw-toggle-distraction" type="button" data-kw-toggle="distraction" aria-pressed="false">
				<span class="kw-toggle-icon" aria-hidden="true">□</span>
				<span class="kw-toggle-text"><?php echo esc_html( $options['distraction_button_text'] ); ?></span>
			</button>
		</div>

		<button class="kw-exit-distraction" type="button" data-kw-exit-distraction aria-label="<?php echo esc_attr__( 'Выйти из режима без отвлечений', 'klenwriter' ); ?>">
			<?php echo esc_html__( 'Вернуться', 'klenwriter' ); ?>
		</button>
		<?php
	}

	/**
	 * Returns the uploaded logo URL or the bundled placeholder.
	 *
	 * @param int $logo_id Attachment ID.
	 * @return string
	 */
	private function get_logo_url( $logo_id ) {
		if ( $logo_id > 0 ) {
			$logo_url = wp_get_attachment_image_url( $logo_id, 'thumbnail' );

			if ( $logo_url ) {
				return $logo_url;
			}
		}

		return KLENWRITER_URL . 'assets/images/logo-placeholder.svg';
	}
}
