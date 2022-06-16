<?php
if (!class_exists('FmInit')):
    /**
     *
     */
    class FmInit {

        function __construct() {
            add_action('init', array($this, 'init'), 1);
            add_action('plugins_loaded', array($this, 'plugin_loaded'));
            add_action('wp_ajax_fmpSettingsUpdate', array($this, 'fmpSettingsUpdate'));
            add_action('admin_enqueue_scripts', array($this, 'settings_admin_enqueue_scripts'));
            register_activation_hook(TLP_FOOD_MENU_PLUGIN_ACTIVE_FILE_NAME, array($this, 'activate'));
            register_deactivation_hook(TLP_FOOD_MENU_PLUGIN_ACTIVE_FILE_NAME, array($this, 'deactivate'));
            add_action( 'admin_init', array( $this, 'fm_redirect' ) );
        }

        public function activate() {
            $this->flushFmp();
            add_option( 'rtfm_activation_redirect', true );
        }

        private function flushFmp() {
            flush_rewrite_rules();
        }

        /**
         * Fired for each blog when the plugin is deactivated.
         *
         * @since 0.1.0
         */
        public function deactivate() {
            $this->flushFmp();
        }

        function init() {

            $labels = array(
                'menu_name'          => __('Food Menu', 'tlp-food-menu'),
                'name'               => __('Food Menu', 'tlp-food-menu'),
                'singular_name'      => __('Food Menu', 'tlp-food-menu'),
                'all_items'          => __('All Foods', 'tlp-food-menu'),
                'add_new'            => __('Add Food', 'tlp-food-menu'),
                'add_new_item'       => __('Add Food', 'tlp-food-menu'),
                'edit_item'          => __('Edit Food', 'tlp-food-menu'),
                'new_item'           => __('New Food', 'tlp-food-menu'),
                'view_item'          => __('View Food', 'tlp-food-menu'),
                'search_items'       => __('Search Food', 'tlp-food-menu'),
                'not_found'          => __('No Food found', 'tlp-food-menu'),
                'not_found_in_trash' => __('No Food in the trash', 'tlp-food-menu'),
            );
            $supports = array(
                'title',
                'editor',
                'thumbnail',
                'excerpt',
                'page-attributes'
            );
            $args = array(
                'labels'          => $labels,
                'supports'        => $supports,
                'public'          => true,
                'capability_type' => 'post',
                'rewrite'         => array(
                    'slug'       => TLPFoodMenu()->post_type_slug,
                    'with_front' => false,
                    'feeds'      => true
                ),
                'menu_position'   => 20,
                'menu_icon'       => TLPFoodMenu()->assetsUrl . 'images/icon-16x16.png',
            );
            register_post_type(TLPFoodMenu()->post_type, $args);

            $catLabels = array(
                'name'                       => __('Categories', 'tlp-food-menu'),
                'singular_name'              => __('Category', 'tlp-food-menu'),
                'menu_name'                  => __('Categories', 'tlp-food-menu'),
                'edit_item'                  => __('Edit Category', 'tlp-food-menu'),
                'update_item'                => __('Update Category', 'tlp-food-menu'),
                'add_new_item'               => __('Add New Category', 'tlp-food-menu'),
                'new_item_name'              => __('New Category', 'tlp-food-menu'),
                'parent_item'                => __('Parent Category', 'tlp-food-menu'),
                'parent_item_colon'          => __('Parent Category:', 'tlp-food-menu'),
                'all_items'                  => __('All Categories', 'tlp-food-menu'),
                'search_items'               => __('Search Categories', 'tlp-food-menu'),
                'popular_items'              => __('Popular Categories', 'tlp-food-menu'),
                'separate_items_with_commas' => __('Separate categories with commas', 'tlp-food-menu'),
                'add_or_remove_items'        => __('Add or remove categories', 'tlp-food-menu'),
                'choose_from_most_used'      => __('Choose from the most used  categories', 'tlp-food-menu'),
                'not_found'                  => __('No categories found.', 'tlp-food-menu'),
            );
            $catArgs = array(
                'labels'            => $catLabels,
                'public'            => true,
                'show_in_nav_menus' => true,
                'show_ui'           => true,
                'show_tagcloud'     => true,
                'hierarchical'      => true,
                'rewrite'           => array(
                    'slug'         => TLPFoodMenu()->post_type_slug . "-category",
                    'with_front'   => false,
                    'hierarchical' => true,
                ),
                'show_admin_column' => true,
                'query_var'         => true,
            );
            register_taxonomy(TLPFoodMenu()->taxonomies['category'], TLPFoodMenu()->post_type, $catArgs);

            // Depricated Category

            $categoryLabels = array(
                'name'                       => __('Food Categories', 'tlp-food-menu'),
                'singular_name'              => __('Food Category', 'tlp-food-menu'),
                'menu_name'                  => __('Categories Depricated', 'tlp-food-menu'),
                'edit_item'                  => __('Edit Category', 'tlp-food-menu'),
                'update_item'                => __('Update Category', 'tlp-food-menu'),
                'add_new_item'               => __('Add New Category', 'tlp-food-menu'),
                'new_item_name'              => __('New Category', 'tlp-food-menu'),
                'parent_item'                => __('Parent Category', 'tlp-food-menu'),
                'parent_item_colon'          => __('Parent Category:', 'tlp-food-menu'),
                'all_items'                  => __('All Categories', 'tlp-food-menu'),
                'search_items'               => __('Search Categories', 'tlp-food-menu'),
                'popular_items'              => __('Popular Categories', 'tlp-food-menu'),
                'separate_items_with_commas' => __('Separate categories with commas', 'tlp-food-menu'),
                'add_or_remove_items'        => __('Add or remove categories', 'tlp-food-menu'),
                'choose_from_most_used'      => __('Choose from the most used  categories', 'tlp-food-menu'),
                'not_found'                  => __('No categories found.', 'tlp-food-menu'),
            );
            $categoryArgs = array(
                'labels'            => $categoryLabels,
                'public'            => false,
                'show_in_nav_menus' => false,
                'show_ui'           => false,
                'show_tagcloud'     => false,
                'hierarchical'      => true,
                'rewrite'           => array(
                    'slug'         => TLPFoodMenu()->post_type_slug . "-depricated-category",
                ),
                'show_admin_column' => false,
                'query_var'         => true,
            );
            register_taxonomy('food-menu-category', TLPFoodMenu()->post_type, $categoryArgs);

            // ShortCode
            $sc_args = array(
                'label'               => __('ShortCode', 'tlp-food-menu'),
                'description'         => __('Food menu pro shortcode generator', 'tlp-food-menu'),
                'labels'              => array(
                    'all_items'          => __('ShortCode Generator', 'tlp-food-menu'),
                    'menu_name'          => __('ShortCode', 'tlp-food-menu'),
                    'singular_name'      => __('ShortCode', 'tlp-food-menu'),
                    'edit_item'          => __('Edit ShortCode', 'tlp-food-menu'),
                    'new_item'           => __('New ShortCode', 'tlp-food-menu'),
                    'view_item'          => __('View ShortCode', 'tlp-food-menu'),
                    'search_items'       => __('ShortCode Locations', 'tlp-food-menu'),
                    'not_found'          => __('No ShortCode found.', 'tlp-food-menu'),
                    'not_found_in_trash' => __('No ShortCode found in trash.', 'tlp-food-menu')
                ),
                'supports'            => array('title'),
                'public'              => false,
                'rewrite'             => false,
                'show_ui'             => true,
                'show_in_menu'        => 'edit.php?post_type=' . TLPFoodMenu()->post_type,
                'show_in_admin_bar'   => true,
                'show_in_nav_menus'   => false,
                'can_export'          => true,
                'has_archive'         => false,
                'exclude_from_search' => false,
                'publicly_queryable'  => false,
                'capability_type'     => 'page',
            );
            register_post_type(TLPFoodMenu()->shortCodePT, $sc_args);
            $flush = get_option(TLPFoodMenu()->options['flash']);
            if ($flush) {
                $this->flushFmp();
                update_option(TLPFoodMenu()->options['flash'], false);
            }
            // register scripts
            $scripts = array();
            $styles = array();

            $scripts[] = array(
                'handle' => 'fm-frontend',
                'src'    => TLPFoodMenu()->assetsUrl . 'js/foodmenu.js',
                'deps'   => array('jquery'),
                'footer' => true
            );
            // register acf styles

            $styles['fm-base'] = TLPFoodMenu()->assetsUrl . 'css/fm-base.css';
            $styles['fm-frontend'] = TLPFoodMenu()->assetsUrl . 'css/foodmenu.css';

            if (is_admin()) {

                $scripts[] = array(
                    'handle' => 'fm-select2',
                    'src'    => TLPFoodMenu()->assetsUrl . "vendor/select2/select2.min.js",
                    'deps'   => array('jquery'),
                    'footer' => false
                );
                $scripts[] = array(
                    'handle' => 'fm-admin',
                    'src'    => TLPFoodMenu()->assetsUrl . "js/admin.js",
                    'deps'   => array('jquery'),
                    'footer' => true
                );
                $scripts[] = array(
                    'handle' => 'fm-admin-preview',
                    'src'    => TLPFoodMenu()->assetsUrl . "js/admin-preview.js",
                    'deps'   => array('jquery'),
                    'footer' => true
                );

                $styles['fm-select2'] = TLPFoodMenu()->assetsUrl . 'vendor/select2/select2.min.css';
                $styles['fm-admin'] = TLPFoodMenu()->assetsUrl . 'css/admin.css';
                $styles['fm-admin-preview'] = TLPFoodMenu()->assetsUrl . 'css/admin-preview.css';
            }

            $version = (defined('WP_DEBUG') && WP_DEBUG) ? time() : TLPFoodMenu()->options['version'];
            foreach ($scripts as $script) {
                wp_register_script($script['handle'], $script['src'], $script['deps'], $version, $script['footer']);
            }

            foreach ($styles as $k => $v) {
                wp_register_style($k, $v, false, $version);
            }

            // admin only
            if (is_admin()) {
                add_action('admin_menu', array($this, 'fmp_menu_register'));
                add_filter('post_updated_messages', array($this, 'fmp_post_updated_messages'));
            }
        }

        function fmpSettingsUpdate() {

            $error = true;
            if (TLPFoodMenu()->verifyNonce()) {
                unset($_REQUEST['fmp_nonce']);
                unset($_REQUEST['_wp_http_referer']);
                unset($_REQUEST['action']);
                $data = array();

                $mates = TLPFoodMenu()->fmpAllSettingsFields();

                foreach ($mates as $key => $field) {
                    $rValue = !empty($_REQUEST[$key]) ? $_REQUEST[$key] : null;
                    $value = TLPFoodMenu()->sanitize($field, $rValue);
                    $data[$key] = $value;
                }

                $settings = get_option(TLPFoodMenu()->options['settings']);

                if (!empty($settings['slug']) && $_REQUEST['slug'] && $settings['slug'] !== $_REQUEST['slug']) {
                    update_option(TLPFoodMenu()->options['flash'], true);
                }
                update_option(TLPFoodMenu()->options['settings'], $data);

                $error = false;
                $msg = __('Settings successfully updated', 'tlp-food-menu');
            } else {
                $msg = __('Security Error !!', 'tlp-food-menu');
            }
            $response = array(
                'error' => $error,
                'msg'   => $msg,
            );
            wp_send_json($response);
            die();

        }

        function settings_admin_enqueue_scripts() {
            global $pagenow, $typenow;

            // validate page
            if (!in_array($pagenow, array('edit.php'))) {
                return;
            }
            if ($typenow != TLPFoodMenu()->post_type) {
                return;
            }

            // Scripts
            wp_enqueue_script(array(
                'jquery',
                'fm-select2',
                'fm-admin',
            ));

            // Styles
            wp_enqueue_style(array(
                'fm-select2',
                'fm-admin',
            ));

            $nonce = wp_create_nonce(TLPFoodMenu()->nonceText());
            wp_localize_script('fm-admin', 'fmp_var',
                array(
                    'nonceID' => TLPFoodMenu()->nonceId(),
                    'nonce'   => $nonce,
                    'ajaxurl' => admin_url('admin-ajax.php')
                ));
        }

		function fm_redirect() {
			if ( get_option( 'rtfm_activation_redirect', false ) ) {
				delete_option( 'rtfm_activation_redirect' );
				wp_redirect( admin_url( 'edit.php?post_type=' . TLPFoodMenu()->post_type . '&page=rtfm_get_help' ) );
			}
		}

        function fmp_menu_register() {

            add_submenu_page('edit.php?post_type=' . TLPFoodMenu()->post_type,
                __('Food menu pro settings', 'tlp-food-menu'), __('Settings', 'tlp-food-menu'), 'administrator',
                'food_menu_settings', array($this, 'food_menu_settings'));

            add_submenu_page(
                'edit.php?post_type=' . TLPFoodMenu()->post_type,
                esc_html__( 'Get Help', 'tlp-food-menu' ),
                esc_html__( 'Get Help', 'tlp-food-menu' ),
                'administrator',
                'rtfm_get_help',
                array( $this, 'get_help' )
            );
        }

        function food_menu_settings() {
            TLPFoodMenu()->renderView('settings.settings');
        }

		function get_help() {
			TLPFoodMenu()->renderView( 'help' );
		}

        public function plugin_loaded() {
            load_plugin_textdomain('tlp-food-menu', false, TLP_FOOD_MENU_LANGUAGE_PATH);
            TLPFoodMenu()->migrateData();
            $this->updateVersion();
        }

        private function updateVersion() {
            update_option(TLPFoodMenu()->options['installed_version'], TLPFoodMenu()->options['version']);
        }

        function fmp_post_updated_messages($messages) {

            $messages[TLPFoodMenu()->shortCodePT] = array(
                0  => '', // Unused. Messages start at index 1.
                1  => __('ShortCode options updated.', 'tlp-food-menu'),
                2  => __('ShortCode options updated.', 'tlp-food-menu'),
                3  => __('Custom field deleted.', 'tlp-food-menu'),
                4  => __('ShortCode updated.', 'tlp-food-menu'),
                /* translators: %s: date and time of the revision */
                5  => isset($_GET['revision']) ? sprintf(__('ShortCode restored to revision from %s',
                    'tlp-food-menu'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
                6  => __('ShortCode published.', 'tlp-food-menu'),
                7  => __('ShortCode saved.', 'tlp-food-menu'),
                8  => __('ShortCode submitted.', 'tlp-food-menu'),
                9  => __('ShortCode scheduled for.', 'tlp-food-menu'),
                10 => __('ShortCode draft updated.', 'tlp-food-menu'),
            );

            return $messages;
        }

    }
endif;
