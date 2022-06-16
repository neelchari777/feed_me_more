<?php
/**
 * Elementor Addons Class.
 *
 * @package RT_FM
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'FmElementor' ) ) :

	/**
	 * Elementor Addons Class.
	 */
	class FmElementor {

		/**
		 * Class Constructor.
		 *
		 * @return void
		 */
		public function __construct() {
			if ( did_action( 'elementor/loaded' ) ) {
				add_action( 'elementor/widgets/register', [ $this, 'init' ] );
			}
		}

		/**
		 * Registers Elementor Widgets.
		 *
		 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
		 * @return void
		 */
		public function init( $widgets_manager ) {
			require_once TLPFoodMenu()->plugin_path() . '/vendor/FmElementorWidget.php';

			// Register widget.
			$widgets_manager->register( new FmElementorWidget() );
		}
	}

endif;
