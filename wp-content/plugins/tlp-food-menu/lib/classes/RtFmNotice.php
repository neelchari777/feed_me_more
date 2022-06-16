<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'RtFmNotice' ) ) :

	class RtFmNotice {
		public function __construct() {
			$current      = time();
			$black_friday = mktime( 0, 0, 0, 11, 22, 2021 ) <= $current && $current <= mktime( 0, 0, 0, 12, 6, 2021 );

			if ( $black_friday ) {
				add_action( 'admin_init', array( $this, 'black_friday_notice' ) );
			} else {
				register_activation_hook( TLP_FOOD_MENU_PLUGIN_ACTIVE_FILE_NAME, array( $this, 'rtfm_activation_time' ) );
				add_action( 'admin_init', array( $this, 'rtfm_check_installation_time' ) );
				add_action( 'admin_init', array( __CLASS__, 'rtfm_spare_me' ), 5 );
			}
		}

		/**
		 * Check if Black Friday Notice should be shown or not.
		 *
		 * @return void
		 */
		public static function black_friday_notice() {
			if ( get_option( 'rtfm_bf_2021' ) != '1' ) {
				if ( ! isset( $GLOBALS['rt_bf_2021_notice'] ) ) {
					$GLOBALS['rt_bf_2021_notice'] = 'rtfm_bf_2021';
					self::notice();
				}
			}
		}

		/**
		 * Black Friday Notice.
		 *
		 * @return void
		 */
		public static function notice() {
			add_action(
				'admin_enqueue_scripts',
				function () {
					wp_enqueue_script( 'jquery' );
				}
			);

			add_action(
				'admin_notices',
				function () {
					$plugin_name   = 'Food Menu Pro';
					$download_link = 'https://www.radiustheme.com/downloads/food-menu-pro-wordpress/';
					?>
				<div class="notice notice-info is-dismissible" data-rtfmdismissable="rtfm_bf_2021" style="display:grid;grid-template-columns: 100px auto;padding-top: 25px; padding-bottom: 22px;">
					<img alt="<?php echo esc_attr( $plugin_name ); ?>" src="<?php echo TLPFoodMenu()->assetsUrl . 'images/icon-128x128.png'; ?>" width="74px" height="74px" style="grid-row: 1 / 4; align-self: center;justify-self: center"/>
					<h3 style="margin:0;"><?php echo sprintf( '%s Black Friday Deal!!', $plugin_name ); ?></h3>
					<p style="margin:0 0 2px;">Don't miss out on our biggest sale of the year! Get your
						<b><?php echo $plugin_name; ?> plan</b> with <b>50% OFF</b>! Limited time offer expires on
						December 5.
					</p>
					<p style="margin:0;">
						<a class="button button-primary" href="<?php echo esc_url( $download_link ); ?>" target="_blank">Buy Now</a>
						<a class="button button-dismiss" href="#">Dismiss</a>
					</p>
				</div>
					<?php
				}
			);

			add_action(
				'admin_footer',
				function () {
					?>
				<script type="text/javascript">
					(function ($) {
						$(function () {
							setTimeout(function () {
								$('div[data-rtfmdismissable] .notice-dismiss, div[data-rtfmdismissable] .button-dismiss')
									.on('click', function (e) {
										e.preventDefault();
										$.post(ajaxurl, {
											'action': 'rtfm_dismiss_admin_notice',
											'nonce': <?php echo json_encode( wp_create_nonce( 'rtfm-dismissible-notice' ) ); ?>
										});
										$(e.target).closest('.is-dismissible').remove();
									});
							}, 1000);
						});
					})(jQuery);
				</script>
					<?php
				}
			);

			add_action(
				'wp_ajax_rtfm_dismiss_admin_notice',
				function () {
					check_ajax_referer( 'rtfm-dismissible-notice', 'nonce' );

					update_option( 'rtfm_bf_2021', '1' );
					wp_die();
				}
			);
		}

		/**
		 * Add plugin activation time
		 *
		 * @return void
		 */
		public static function rtfm_activation_time() {
			$get_activation_time = strtotime( 'now' );
			add_option( 'rtfm_plugin_activation_time', $get_activation_time );
		}

		/**
		 * Check if review notice should be shown or not.
		 *
		 * @return void
		 */
		public static function rtfm_check_installation_time() {

			// Added Lines Start.
			$nobug = get_option( 'rtfm_spare_me', '0' );

			if ( $nobug == '1' || $nobug == '3' ) {
				return;
			}

			$install_date = get_option( 'rtfm_plugin_activation_time' );
			$past_date    = strtotime( '-10 days' );

			$remind_time = get_option( 'rtfm_remind_me' );
			$remind_due  = strtotime( '+15 days', $remind_time );
			$now         = strtotime( 'now' );

			if ( $now >= $remind_due ) {
				add_action( 'admin_notices', array( __CLASS__, 'rtfm_display_admin_notice' ) );
			} elseif ( ( $past_date >= $install_date ) && $nobug !== '2' ) {
				add_action( 'admin_notices', array( __CLASS__, 'rtfm_display_admin_notice' ) );
			}
		}

		/**
		 * Display Admin Notice, asking for a review
		 *
		 * @return void
		 */
		public static function rtfm_display_admin_notice() {
			// WordPress global variable.
			global $pagenow;

			$exclude = [ 'themes.php', 'users.php', 'tools.php', 'options-general.php', 'options-writing.php', 'options-reading.php', 'options-discussion.php', 'options-media.php', 'options-permalink.php', 'options-privacy.php', 'edit-comments.php', 'upload.php', 'media-new.php', 'admin.php', 'import.php', 'export.php', 'site-health.php', 'export-personal-data.php', 'erase-personal-data.php' ];

			if ( ! in_array( $pagenow, $exclude ) ) {
				$dont_disturb = esc_url( add_query_arg( 'rtfm_spare_me', '1', self::rtfm_current_admin_url() ) );
				$remind_me    = esc_url( add_query_arg( 'rtfm_remind_me', '1', self::rtfm_current_admin_url() ) );
				$rated        = esc_url( add_query_arg( 'rtfm_rated', '1', self::rtfm_current_admin_url() ) );
				$reviewurl    = esc_url( 'https://wordpress.org/support/plugin/tlp-food-menu/reviews/?filter=5#new-post' );

				printf( __( '<div class="notice rtfm-review-notice rtfm-review-notice--extended">
                <div class="rtfm-review-notice_content">
                    <h3>Enjoying Food Menu - Restaurant Menu & Online Ordering for WooCommerce?</h3>
                    <p>Thank you for choosing Food Menu - Restaurant Menu & Online Ordering for WooCommerce. If you have found our plugin useful and makes you smile, please consider giving us a 5-star rating on WordPress.org. It will help us to grow.</p>
                    <div class="rtfm-review-notice_actions">
                        <a href="%s" class="rtfm-review-button rtfm-review-button--cta" target="_blank"><span>⭐ Yes, You Deserve It!</span></a>
                        <a href="%s" class="rtfm-review-button rtfm-review-button--cta rtfm-review-button--outline"><span>😀 Already Rated!</span></a>
                        <a href="%s" class="rtfm-review-button rtfm-review-button--cta rtfm-review-button--outline"><span>🔔 Remind Me Later</span></a>
                        <a href="%s" class="rtfm-review-button rtfm-review-button--cta rtfm-review-button--error rtfm-review-button--outline"><span>😐 No Thanks</span></a>
                    </div>
                </div>
            </div>' ), $reviewurl, $rated, $remind_me, $dont_disturb );

				echo '<style>
            .rtfm-review-button--cta {
                --e-button-context-color: #5d3dfd;
                --e-button-context-color-dark: #5d3dfd;
                --e-button-context-tint: rgb(75 47 157/4%);
                --e-focus-color: rgb(75 47 157/40%);
            }
            .rtfm-review-notice {
                position: relative;
                margin: 5px 20px 5px 2px;
                border: 1px solid #ccd0d4;
                background: #fff;
                box-shadow: 0 1px 4px rgba(0,0,0,0.15);
                font-family: Roboto, Arial, Helvetica, Verdana, sans-serif;
                border-inline-start-width: 4px;
            }
            .rtfm-review-notice.notice {
                padding: 0;
            }
            .rtfm-review-notice:before {
                position: absolute;
                top: -1px;
                bottom: -1px;
                left: -4px;
                display: block;
                width: 4px;
                background: -webkit-linear-gradient(bottom, #4C6FFF 0%, #6939c6 100%);
                background: linear-gradient(0deg, #4C6FFF 0%, #6939c6 100%);
                content: "";
            }
            .rtfm-review-notice_content {
                padding: 20px;
            }
            .rtfm-review-notice_actions > * + * {
                margin-inline-start: 8px;
                -webkit-margin-start: 8px;
                -moz-margin-start: 8px;
            }
            .rtfm-review-notice p {
                margin: 0;
                padding: 0;
                line-height: 1.5;
            }
            p + .rtfm-review-notice_actions {
                margin-top: 1rem;
            }
            .rtfm-review-notice h3 {
                margin: 0;
                font-size: 1.0625rem;
                line-height: 1.2;
            }
            .rtfm-review-notice h3 + p {
                margin-top: 8px;
            }
            .rtfm-review-button {
                display: inline-block;
                padding: 0.4375rem 0.75rem;
                border: 1px solid var(--e-button-context-color);
                border-radius: 3px;;
                background: var(--e-button-context-color);
                color: #fff;
                vertical-align: middle;
                text-align: center;
                text-decoration: none;
                white-space: nowrap;
            }
            .rtfm-review-button:active {
                background: var(--e-button-context-color-dark);
                color: #fff;
                text-decoration: none;
            }
            .rtfm-review-button:focus {
                outline: 0;
                background: var(--e-button-context-color-dark);
                box-shadow: 0 0 0 2px var(--e-focus-color);
                color: #fff;
                text-decoration: none;
            }
            .rtfm-review-button:hover {
                background: var(--e-button-context-color-dark);
                color: #fff;
                text-decoration: none;
            }
            .rtfm-review-button.focus {
                outline: 0;
                box-shadow: 0 0 0 2px var(--e-focus-color);
            }
            .rtfm-review-button--error {
                --e-button-context-color: #d72b3f;
                --e-button-context-color-dark: #ae2131;
                --e-button-context-tint: rgba(215,43,63,0.04);
                --e-focus-color: rgba(215,43,63,0.4);
            }
            .rtfm-review-button.rtfm-review-button--outline {
                border: 1px solid;
                background: 0 0;
                color: var(--e-button-context-color);
            }
            .rtfm-review-button.rtfm-review-button--outline:focus {
                background: var(--e-button-context-tint);
                color: var(--e-button-context-color-dark);
            }
            .rtfm-review-button.rtfm-review-button--outline:hover {
                background: var(--e-button-context-tint);
                color: var(--e-button-context-color-dark);
            }
            </style>';
			}
		}

		/**
		 * Current Admin URL.
		 *
		 * @return void
		 */
		protected static function rtfm_current_admin_url() {
			$uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
			$uri = preg_replace( '|^.*/wp-admin/|i', '', $uri );

			if ( ! $uri ) {
				return '';
			}
			return remove_query_arg( [ '_wpnonce', '_wc_notice_nonce', 'wc_db_update', 'wc_db_update_nonce', 'wc-hide-notice' ], admin_url( $uri ) );
		}

		// remove the notice for the user if review already done or if the user does not want to
		public static function rtfm_spare_me() {
			if ( isset( $_GET['rtfm_spare_me'] ) && ! empty( $_GET['rtfm_spare_me'] ) ) {
				$spare_me = $_GET['rtfm_spare_me'];
				if ( 1 == $spare_me ) {
					update_option( 'rtfm_spare_me', "1" );
				}
			}

			if ( isset( $_GET['rtfm_remind_me'] ) && ! empty( $_GET['rtfm_remind_me'] ) ) {
				$remind_me = $_GET['rtfm_remind_me'];
				if ( 1 == $remind_me ) {
					$get_activation_time = strtotime( "now" );
					update_option( 'rtfm_remind_me', $get_activation_time );
					update_option( 'rtfm_spare_me', "2" );
				}
			}

			if ( isset( $_GET['rtfm_rated'] ) && ! empty( $_GET['rtfm_rated'] ) ) {
				$rtfm_rated = $_GET['rtfm_rated'];
				if ( 1 == $rtfm_rated ) {
					update_option( 'rtfm_rated', 'yes' );
					update_option( 'rtfm_spare_me', "3" );
				}
			}
		}
	}

endif;
