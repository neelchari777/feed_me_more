<?php

if (!class_exists('TLPFoodMenu')) {

    class TLPFoodMenu {
        public $post_type;
        public $taxonomies;
        public $options;

        protected static $_instance;

        function __construct() {
            $this->options = array(
                'settings'          => 'tpl_food_menu_settings',
                'version'           => TLP_FOOD_MENU_VERSION,
                'title'             => __('Food Menu', 'tlp-food-menu'),
                'installed_version' => 'tlp-food-menu-installed-version',
                'slug'              => 'tlp-food-menu',
                'flash'             => 'tlp-fm-flash'
            );

            $settings = get_option($this->options['settings']);
            $this->post_type = "food-menu";
            $this->shortCodePT = "fmsc";
            $this->post_type_slug = isset($settings['slug']) ? ($settings['slug'] ? sanitize_title_with_dashes($settings['slug']) : 'food-menu') : 'food-menu';
            $this->taxonomies = array(
                'category'              => $this->post_type . '-cat',
            );

            $this->functionsPath = $this->plugin_path() . '/functions/';
            $this->classesPath = $this->plugin_path() . '/classes/';
            $this->widgetsPath = $this->plugin_path() . '/widgets/';
            $this->modelsPath = $this->plugin_path() . '/models/';
            $this->viewsPath = $this->plugin_path() . '/views/';
            $this->includePath = $this->plugin_path() . '/includes/';
            $this->templatesPath = $this->plugin_path() . '/templates/';

            $this->assetsUrl = TLP_FOOD_MENU_PLUGIN_URL . '/assets/';
            $this->fmpLoadModel($this->modelsPath);
            $this->fmpLoadFunctions($this->functionsPath);
            $this->fmpLoadClass($this->classesPath);

            add_action('init', function () {
                $activeVersion     = get_option( 'tlp-food-menu-installed-version' );
                $migrateFlag = get_option('tlp_fm_m_3_0');

                if ( ! $migrateFlag && version_compare($activeVersion, '3.0.0', '<') ) {
                    add_action( 'admin_notices', [ $this, 'upgrade_is_not_completed' ] );
                }

            });

            add_action('init', [$this, 'upgrade_data']);

        }

        function upgrade_data() {
            if (isset($_GET['migrate']) && wp_verify_nonce($_GET['_wpnonce'], TLPFoodMenu()->nonceText() )) {
                TLPFoodMenu()->migrateData();
            }
        }

        function upgrade_is_not_completed() {
            ?>
            <div class="notice notice-warning is-dismissible">
                <div class="fm-upgrade-notice">
                    <p><?php esc_html_e('Need to update Food Menu Data', 'tlp-food-menu'); ?></p>
                    <p>
                        <?php $nonceText = TLPFoodMenu()->nonceText(); ?>
                        <a href="<?php echo wp_nonce_url(add_query_arg( 'migrate', 'continue' ), $nonceText); ?>" class="button button-primary fm-upgrade-action">
                            <span><?php esc_html_e('Click here to update', 'tlp-food-menu'); ?></span>
                        </a>
                    </p>
                </div>
            </div>
            <?php
        }

        static function hasPro() {
            return class_exists('FMP') ? true : false;
        }

        function isWcActive() {
            return class_exists('WooCommerce') ? true : false;
        }

        /**
         * Get the plugin path.
         *
         * @return string
         */
        public function plugin_path() {
            return untrailingslashit(plugin_dir_path(__FILE__));
        }

        public function template_path() {
            return apply_filters('fmp_template_path', 'food-menu/');
        }

        public function plugin_template_path() {
            $plugin_template = $this->plugin_path() . '/templates/';

            return apply_filters('tlp_fm_template_path', $plugin_template);
        }

        public static function instance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        function fmpLoadClass($dir) {
            if (!file_exists($dir)) {
                return;
            }

            $classes = array();

            foreach (scandir($dir) as $item) {
                if (preg_match("/.php$/i", $item)) {
                    require_once($dir . $item);
                    $className = str_replace(".php", "", $item);
                    $classes[] = new $className;
                }
            }

            if ($classes) {
                foreach ($classes as $class) {
                    $this->objects[] = $class;
                }
            }
        }

        /**
         * Load Model class
         *
         * @param $dir
         */
        function fmpLoadModel($dir) {
            if (!file_exists($dir)) {
                return;
            }
            foreach (scandir($dir) as $item) {
                if (preg_match("/.php$/i", $item)) {
                    require_once($dir . $item);
                }
            }
        }

        function fmpLoadWidget($dir) {
            if (!file_exists($dir)) {
                return;
            }
            foreach (scandir($dir) as $item) {
                if (preg_match("/.php$/i", $item)) {
                    require_once($dir . $item);
                    $class = str_replace(".php", "", $item);

                    if (method_exists($class, 'register_widget')) {
                        $caller = new $class;
                        $caller->register_widget();
                    } else {
                        register_widget($class);
                    }
                }
            }
        }

        function fmpLoadFunctions($dir) {
            if (!file_exists($dir)) {
                return;
            }

            foreach (scandir($dir) as $item) {
                if (preg_match("/.php$/i", $item)) {
                    require_once($dir . $item);
                }
            }

        }

        /**
         * @return string
         */
        public function getShortCodePT() {
            return $this->shortCodePT;
        }

        function render($template_name, $args = array(), $return = false) {

            $template_name = str_replace(".", "/", $template_name);

            if (!empty($args) && is_array($args)) {
                extract($args);
            }

            $template = array(
                "food-menu-pro/{$template_name}.php",
                "tlp-food-menu/{$template_name}.php",
                $template_name . ".php"
            );

            if (!$template_file = locate_template($template)) {
                $template_file = $this->plugin_template_path() . $template_name . '.php';
            }
            if (!file_exists($template_file)) {
                _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_file), '1.7.0');

                return;
            }

            if ($return) {

                ob_start();
                include $template_file;

                return ob_get_clean();
            } else {

                include $template_file;
            }
        }

        function renderView($viewName, $args = array(), $return = false) {

            $viewName = str_replace(".", "/", $viewName);

            if (!empty($args) && is_array($args)) {
                extract($args);
            }

            $view_file = TLPFoodMenu()->viewsPath . $viewName . '.php';

            if (!file_exists($view_file)) {
                _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $view_file), '1.7.0');

                return;
            }

            if ($return) {

                ob_start();
                include $view_file;

                return ob_get_clean();
            } else {

                include $view_file;
            }
        }

        /**
         * Dynamicaly call any  method from models class
         * by pluginFramework instance
         */
        function __call($name, $args) {
            if (!is_array($this->objects)) {
                return;
            }
            foreach ($this->objects as $object) {
                if (method_exists($object, $name)) {
                    $count = count($args);
                    if ($count == 0) {
                        return $object->$name();
                    } elseif ($count == 1) {
                        return $object->$name($args[0]);
                    } elseif ($count == 2) {
                        return $object->$name($args[0], $args[1]);
                    } elseif ($count == 3) {
                        return $object->$name($args[0], $args[1], $args[2]);
                    } elseif ($count == 4) {
                        return $object->$name($args[0], $args[1], $args[2], $args[3]);
                    } elseif ($count == 5) {
                        return $object->$name($args[0], $args[1], $args[2], $args[3], $args[4]);
                    } elseif ($count == 6) {
                        return $object->$name($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
                    }
                }
            }
        }

    }

    function TLPFoodMenu() {
        return TLPFoodMenu::instance();
    }

    TLPFoodMenu();
}