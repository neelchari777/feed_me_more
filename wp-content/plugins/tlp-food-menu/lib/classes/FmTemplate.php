<?php

if ( ! class_exists( 'FmTemplate' ) ):

	/**
	 *
	 */
	class FmTemplate {

		function __construct() {
			add_filter( 'template_include', array( $this, 'template_loader' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_template_script' ) );
		}

		public static function template_loader( $template ) {

			if ( is_embed() ) {
				return $template;
			}

			$find = array( 'food-menu.php' );
			$file = null;
			if ( is_single() && get_post_type() == TLPFoodMenu()->post_type ) {

				$file   = 'single-food-menu.php';
				$find[] = $file;
				$find[] = TLPFoodMenu()->template_path() . $file;

			} elseif ( is_food_taxonomy() ) {

				$term = get_queried_object();

				if ( is_tax( TLPFoodMenu()->taxonomies['category'] ) ) {
					$file = 'taxonomy-' . $term->taxonomy . '.php';
				} else {
					$file = 'archive-food-menu-cat.php';
				}

				$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = TLPFoodMenu()->template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = TLPFoodMenu()->template_path() . 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = $file;
				$find[] = TLPFoodMenu()->template_path() . $file;

			} elseif ( is_post_type_archive( TLPFoodMenu()->post_type ) ) {

				$file   = 'archive-food-menu-cat.php';
				$find[] = $file;
				$find[] = TLPFoodMenu()->template_path() . $file;

			}

			if ( $file ) {
				$template = locate_template( array_unique( $find ) );
				if ( ! $template ) {
					$template = TLPFoodMenu()->plugin_template_path() . $file;
				}
			}

			return $template;
		}

		public function load_template_script() {
			if ( get_post_type() == TLPFoodMenu()->post_type || is_post_type_archive( TLPFoodMenu()->post_type ) ) {
				wp_enqueue_script( array(
					'fm-frontend'
				) );
				$nonce = wp_create_nonce( TLPFoodMenu()->nonceText() );
				wp_localize_script( 'fm-frontend', 'fmp',
					array(
						'nonceID' => TLPFoodMenu()->nonceId(),
						'nonce'   => $nonce,
						'ajaxurl' => admin_url( 'admin-ajax.php' )
					) );
			}

		}

	}

endif;
