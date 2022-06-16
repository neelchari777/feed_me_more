<?php
if (!defined('WPINC')) {
    die;
}

if (!class_exists('FmGutenBurg')):

    class FmGutenBurg {
        function __construct() {
            add_action('enqueue_block_assets', array($this, 'block_assets'));
            add_action('enqueue_block_editor_assets', array($this, 'block_editor_assets'));
            if(function_exists('register_block_type')) {
                register_block_type('rttpg/food-menu-pro', array(
                    'render_callback' => array($this,'render_shortcode'),
                ));
            }
        }

        static function render_shortcode( $atts ) {
            if(!empty($atts['gridId']) && $id = absint($atts['gridId'])){
                return do_shortcode( '[foodmenu id="' . $id . '"]' );
            }
        }

        function block_assets() {
            wp_enqueue_style('wp-blocks');
        }

        function block_editor_assets() {
            // Scripts.
            wp_enqueue_script(
                'rt-food-menu-cgb-block-js',
                TLPFoodMenu()->assetsUrl . "js/tlp-food-menu-blocks.min.js",
                array('wp-blocks', 'wp-i18n', 'wp-element'),
                (defined('WP_DEBUG') && WP_DEBUG) ? time() : TLP_FOOD_MENU_VERSION,
                true
            );
            wp_localize_script('rt-food-menu-cgb-block-js', 'rtFoodMenu', array(
                'short_codes' => TLPFoodMenu()->get_shortCode_list(),
                'icon' => TLPFoodMenu()->assetsUrl . 'images/icon-20x20.png',
            ));
            wp_enqueue_style('fm-admin');
        }
    }

endif;