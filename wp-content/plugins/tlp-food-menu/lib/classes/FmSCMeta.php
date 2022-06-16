<?php

if (!class_exists('FmSCMeta')):
    /**
     *
     */
    class FmSCMeta {

        function __construct() {
            add_action('add_meta_boxes', array($this, 'fmp_sc_meta_boxes'), 10);
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            add_action('save_post', array($this, 'save_post'), 10, 2);
            add_action('edit_form_after_title', array($this, 'fmp_sc_after_title'));
            add_action('admin_init', array($this, 'fm_pro_remove_all_meta_box'));
            add_filter('manage_edit-fmsc_columns', array($this, 'arrange_fmp_sc_columns'));
            add_action('manage_fmsc_posts_custom_column', array($this, 'manage_fmp_sc_columns'), 10, 2);
        }

        public function manage_fmp_sc_columns($column) {
            switch ($column) {
                case 'fmp_short_code':
                    echo '<input type="text" onfocus="this.select();" readonly="readonly" value="[foodmenu id=&quot;' . get_the_ID() . '&quot; title=&quot;' . get_the_title() . '&quot;]" class="large-text code rt-code-sc">';
                    break;
                default:
                    break;
            }
        }

        public function arrange_fmp_sc_columns($columns) {
            $shortcode = array('fmp_short_code' => __('Shortcode', 'tlp-food-menu'));

            return array_slice($columns, 0, 2, true) + $shortcode + array_slice($columns, 1, null, true);
        }

        /**
         * This will add input text field for shortCode
         *
         * @param $post
         */
        function fmp_sc_after_title($post) {
            if (TLPFoodMenu()->shortCodePT !== $post->post_type) {
                return;
            }

            $html = null;
            $html .= '<div class="postbox" style="margin-bottom: 0;"><div class="inside">';
            $html .= '<p><input type="text" onfocus="this.select();" readonly="readonly" value="[foodmenu id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]" class="large-text code rt-code-sc">
            <input type="text" onfocus="this.select();" readonly="readonly" value="&#60;&#63;php echo do_shortcode( &#39;[foodmenu id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]&#39; ); &#63;&#62;" class="large-text code rt-code-sc">
            </p>';
            $html .= '</div></div>';
            echo $html;
        }

        function fm_pro_remove_all_meta_box() {
            if (is_admin()) {
                add_filter("get_user_option_meta-box-order_" . TLPFoodMenu()->shortCodePT,
                    array($this, 'remove_all_meta_boxes_fmp_sc'));
            }
        }

        /**
         * Add only custom meta box
         *
         * @return array
         */
        function remove_all_meta_boxes_fmp_sc() {
            global $wp_meta_boxes;
            $publishBox = $wp_meta_boxes[TLPFoodMenu()->shortCodePT]['side']['core']['submitdiv'];
            $scBox = $wp_meta_boxes[TLPFoodMenu()->shortCodePT]['normal']['high'][TLPFoodMenu()->shortCodePT . '_sc_settings_meta'];
            $scPreviewBox = $wp_meta_boxes[TLPFoodMenu()->shortCodePT]['normal']['high'][TLPFoodMenu()->shortCodePT . '_sc_preview_meta'];
            $docBox = $wp_meta_boxes[TLPFoodMenu()->shortCodePT]['side']['default']['rt_plugin_sc_pro_information'];
            $wp_meta_boxes[TLPFoodMenu()->shortCodePT] = array(
                'side'   => array(
                    'core' => array('submitdiv' => $publishBox),
                    'default' => [
                        'rt_plugin_sc_pro_information' => $docBox
                    ]
                ),
                'normal' => array(
                    'high' => array(
                        TLPFoodMenu()->shortCodePT . '_sc_settings_meta' => $scBox,
                        TLPFoodMenu()->shortCodePT . '_sc_preview_meta'  => $scPreviewBox
                    )
                )
            );

            return array();
        }

        function admin_enqueue_scripts() {

            global $pagenow, $typenow;
            // validate page
            if (!in_array($pagenow, array('post.php', 'post-new.php', 'edit.php'))) {
                return;
            }
            if ($typenow != TLPFoodMenu()->shortCodePT) {
                return;
            }

            wp_enqueue_media();
            // scripts
            $select2Id = 'fm-select2';
            if (class_exists('WPSEO_Admin_Asset_Manager') && class_exists('Avada')) {
                $select2Id = 'yoast-seo-select2';
            } elseif (class_exists('WPSEO_Admin_Asset_Manager')) {
                $select2Id = 'yoast-seo-select2';
            } elseif (class_exists('Avada')) {
                $select2Id = 'select2-avada-js';
            } elseif (class_exists('wp_megamenu_base')) {
                wp_dequeue_script('wpmm-select2');
                wp_dequeue_script('wpmm_scripts_admin');
            }
            wp_enqueue_script(array(
                'jquery',
                'wp-color-picker',
                $select2Id,
                'fm-admin',
                'fm-admin-preview',
            ));

            // styles
            wp_enqueue_style(array(
                'wp-color-picker',
                'fm-select2',
                'fm-frontend',
                'fm-admin',
                'fm-admin-preview',
            ));

            $nonce = wp_create_nonce(TLPFoodMenu()->nonceText());
            wp_localize_script('fm-admin', 'fmp',
                array(
                    'nonceID' => TLPFoodMenu()->nonceID(),
                    'nonce'   => $nonce,
                    'ajaxurl' => admin_url('admin-ajax.php')
                ));

        }

        function fmp_sc_meta_boxes() {

            add_meta_box(
                TLPFoodMenu()->shortCodePT . '_sc_settings_meta',
                __('Short Code Generator', 'tlp-food-menu'),
                array($this, 'fm_sc_settings_selection'),
                TLPFoodMenu()->shortCodePT,
                'normal',
                'high');
            add_meta_box(
                TLPFoodMenu()->shortCodePT . '_sc_preview_meta',
                __('Layout Preview', 'tlp-food-menu'),
                array($this, 'fm_sc_preview_selection'),
                TLPFoodMenu()->shortCodePT,
                'normal',
                'high');

            add_meta_box(
                'rt_plugin_sc_pro_information',
                __('Documentation', 'tlp-food-menu'),
                array($this, 'rt_plugin_sc_pro_information'),
                TLPFoodMenu()->shortCodePT,
                'side',
                'default'
            );
        }

        /**
         * Setting Sections
         *
         * @param $post
         */
        function fm_sc_settings_selection($post) {
            wp_nonce_field(TLPFoodMenu()->nonceText(), TLPFoodMenu()->nonceID());
            $html = null;
            $html .= '<div class="rt-tab-container">';
            $html .= '<ul class="rt-tab-nav">
	                            <li><a href="#sc-fmp-layout"><i class="dashicons dashicons-layout"></i>' . __('Layout', 'tlp-food-menu') . '</a></li>
	                            <li><a href="#sc-fmp-filter"><i class="dashicons dashicons-filter"></i>' . __('Filtering', 'tlp-food-menu') . '</a></li>
	                            <li><a href="#sc-fmp-field-selection"><i class="dashicons dashicons-editor-table"></i>' . __('Field selection', 'tlp-food-menu') . '</a></li>
	                            <li><a href="#sc-fmp-style"><i class="dashicons dashicons-admin-customizer"></i>' . __('Styling', 'tlp-food-menu') . '</a></li>
	                          </ul>';
            $html .= sprintf('<div id="sc-fmp-layout" class="rt-tab-content">%s</div>', TLPFoodMenu()->rtFieldGenerator(TLPFoodMenu()->scLayoutMetaFields()));
            $html .= sprintf('<div id="sc-fmp-filter" class="rt-tab-content">%s</div>', TLPFoodMenu()->rtFieldGenerator(TLPFoodMenu()->scFilterMetaFields()));
            $html .= sprintf('<div id="sc-fmp-field-selection" class="rt-tab-content">%s</div>', TLPFoodMenu()->rtFieldGenerator(TLPFoodMenu()->scItemFields()));
            $html .= sprintf('<div id="sc-fmp-style" class="rt-tab-content">%s</div>', TLPFoodMenu()->rtFieldGenerator(TLPFoodMenu()->scStyleFields()));
            $html .= '</div>';

            echo $html;
        }

        function rt_plugin_sc_pro_information() {
            global $pagenow;

            $html = '';

            if (!TLPFoodMenu()->hasPro()) {
                if ( $pagenow === 'post.php' ) {
                    $html .= sprintf('<div class="rt-document-box"><div class="rt-box-icon"><i class="dashicons dashicons-megaphone"></i></div><div class="rt-box-content"><h3 class="rt-box-title">Pro Features</h3>%s</div></div>', TLPFoodMenu()->get_pro_feature_list());
                } else {
                    $html .= '<div class="rt-document-box rt-update-pro-btn-wrap">
                            <a href="https://www.radiustheme.com/downloads/food-menu-pro-wordpress/" target="_blank" class="rt-update-pro-btn">Update Pro To Get More Features</a>
                        </div>';
                }
            }

            $html .= sprintf('<div class="rt-document-box">
							<div class="rt-box-icon"><i class="dashicons dashicons-media-document"></i></div>
							<div class="rt-box-content">
                    			<h3 class="rt-box-title">%1$s</h3>
                    				<p>%2$s</p>
                        			<a href="https://www.radiustheme.com/docs/food-menu/getting-started/installations/" target="_blank" class="rt-admin-btn">%1$s</a>
                			</div>
						</div>',
                __("Documentation", 'tlp-food-menu'),
                __("Get started by spending some time with the documentation we included step by step process with screenshots with video.", 'food-men-prou')
            );

            $html .= '<div class="rt-document-box">
							<div class="rt-box-icon"><i class="dashicons dashicons-sos"></i></div>
							<div class="rt-box-content">
                    			<h3 class="rt-box-title">Need Help?</h3>
                    				<p>Stuck with something? Please create a 
                        <a href="https://www.radiustheme.com/contact/">ticket here</a> or post on <a href="https://www.facebook.com/groups/234799147426640/">facebook group</a>. For emergency case join our <a href="https://www.radiustheme.com/">live chat</a>.</p>
                        			<a href="https://www.radiustheme.com/contact/" target="_blank" class="rt-admin-btn">Get Support</a>
                			</div>
						</div>';

            echo $html;
        }


        /**
         *  Preview section
         */
        function fm_sc_preview_selection() {
            echo "<div class='fmp-response'><span class='spinner'></span></div><div id='fmp-preview-container'></div>";
        }


        function save_post($post_id, $post) {

            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }
            if (!TLPFoodMenu()->verifyNonce()) {
                return $post_id;
            }
            if (TLPFoodMenu()->shortCodePT != $post->post_type) {
                return $post_id;
            }

            $mates = TLPFoodMenu()->fmpScMetaFields();
            foreach ($mates as $metaKey => $field) {
                $rValue = !empty($_REQUEST[$metaKey]) ? $_REQUEST[$metaKey] : null;
                $value = TLPFoodMenu()->sanitize($field, $rValue);
                if (empty($field['multiple'])) {
                    update_post_meta($post_id, $metaKey, $value);
                } else {
                    if (apply_filters('tlp_fmp_has_multiple_meta_issue', false)) {
                        update_post_meta($post_id, $metaKey, $value);
                    } else {
                        delete_post_meta($post_id, $metaKey);
                        if (is_array($value) && !empty($value)) {
                            foreach ($value as $item) {
                                add_post_meta($post_id, $metaKey, $item);
                            }
                        } else {
                            update_post_meta($post_id, $metaKey, "");
                        }
                    }
                }
            }

        }
    }
endif;