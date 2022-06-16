<?php

if ( ! class_exists( 'FmInitWidget' ) ):

	/**
	 *
	 */
	class FmInitWidget {

		function __construct() {
			add_action( 'widgets_init', array( $this, 'initWidget' ) );
		}


		function initWidget() {
            TLPFoodMenu()->fmpLoadWidget( TLPFoodMenu()->widgetsPath );
		}
	}

endif;