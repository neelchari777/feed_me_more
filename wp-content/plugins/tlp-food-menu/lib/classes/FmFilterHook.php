<?php

if ( ! class_exists( 'FmFilterHook' ) ):

	class FmFilterHook {
		public function __construct() {
            add_filter('plugin_row_meta', array(__CLASS__, 'plugin_row_meta'), 10, 2);
            add_filter( 'plugin_action_links_' . TLP_FOOD_MENU_PLUGIN_ACTIVE_FILE_NAME, array($this, 'tlp_fm_marketing') );
		}

        function tlp_fm_marketing($links) {
		    if (!TLPFoodMenu()->hasPro()) {
                $links[] = '<a target="_blank" href="'. esc_url( 'https://www.radiustheme.com/demo/plugins/food-menu/' ) .'">Demo</a>';
                $links[] = '<a target="_blank" href="'. esc_url( 'https://www.radiustheme.com/docs/food-menu/getting-started/installations/' ) .'">Documentation</a>';
                $links[] = '<a target="_blank" style="color: #39b54a;font-weight: 700;"  href="'. esc_url( 'https://www.radiustheme.com/downloads/food-menu-pro-wordpress/' ) .'">Get Pro</a>';
            }

            return $links;
        }

        static public function plugin_row_meta($links, $file) {
            if ($file == TLP_FOOD_MENU_PLUGIN_ACTIVE_FILE_NAME) {
                $report_url = 'https://www.radiustheme.com/contact/';
                $row_meta['issues'] = sprintf('%2$s <a target="_blank" href="%1$s">%3$s</a>', esc_url($report_url), esc_html__('Facing issue?', 'tlp-food-menu'), '<span style="color: red">' . esc_html__('Please open a support ticket.', 'tlp-food-menu') . '</span>');
                return array_merge($links, $row_meta);
            }
            return (array)$links;
        }
	}

endif;