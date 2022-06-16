<?php

if ( ! class_exists( 'FmFrontEnd' ) ):

	class FmFrontEnd {
		function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_front_end' ) );
		}

		function enqueue_scripts_front_end() {
			wp_enqueue_style( 'fm-base' );
			wp_enqueue_style( 'fm-frontend' );
		}

	}

endif;