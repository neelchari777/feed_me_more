<?php $settings = get_option(TLPFoodMenu()->options['settings']); ?>
<div class="wrap">
    <h2><?php esc_html_e('Food Menu Settings', 'tlp-food-menu'); ?></h2>
    <div class="rt-settings-container">
        <div class="rt-setting-title">
            <h3><?php esc_html_e('General settings', "tlp-food-menu") ?></h3>
        </div>
        <div class="rt-setting-content">
            <form id="fmp-settings-form">
                <?php
                $tabs = [
                    'general'       => [
                        'id'        => 'general',
                        'title'     => __('General', 'tlp-food-menu'),
                        'icon'      => 'dashicons-admin-settings',
                        'content'    => TLPFoodMenu()->rtFieldGenerator(TLPFoodMenu()->generalSettings()),
                    ],
                    'details'       => [
                        'id'        => 'detail-page-settings',
                        'title'     => __('Detail page settings', 'tlp-food-menu'),
                        'icon'      => 'dashicons-media-default',
                        'content'   => TLPFoodMenu()->rtFieldGenerator(TLPFoodMenu()->detailPageSettings()),
                    ],
                    'promotions'    => [
                        'id'    => 'promotions',
                        'title' => apply_filters('tlp_fm_promotion_tab_title', 'Plugin & Themes (Pro)'),
                        'icon'  => 'dashicons-megaphone',
                        'content'    => TLPFoodMenu()->get_product_list_html(TLPFoodMenu()->promotionsFields()),
                    ],
                ];

                $tabs = apply_filters('tlp_fm_settings_tab', $tabs);

                $tabList = '';
                $tabContent = '';

                foreach ($tabs as $tab) {
                    $tabList .= '<li><a href="#'.$tab['id'].'"><i class="dashicons '.$tab['icon'].'"></i>' . $tab['title'] . '</a></li>';

                    $tabContent .= '<div id="'.$tab['id'].'" class="rt-tab-content"><div class="tab-content">';
                        $tabContent .= $tab['content'];
                    $tabContent .= '</div></div>';
                }

                $html = null;
                $html .= '<div id="settings-tabs" class="rt-tabs rt-tab-container">';
                $html .= '<ul class="tab-nav rt-tab-nav">';
                    $html .= $tabList;
                $html .= '</ul>';

                $html .= $tabContent;

                $html .= '</div>';

                echo $html;

                ?>
                <p class="submit"><input type="submit" name="submit" id="fmp-saveButton" class="rt-admin-btn button button-primary"
                                         value="<?php esc_html_e('Save Changes', 'tlp-food-menu'); ?>"></p>

                <?php wp_nonce_field(TLPFoodMenu()->nonceText(), TLPFoodMenu()->nonceId()); ?>
            </form>
            <div class="rt-response"></div>
        </div>
        <!-- <div class="rt-pro-feature-content"> -->
            <?php //TLPFoodMenu()->rt_plugin_sc_pro_information(); ?>
        <!-- </div> -->
    </div>
</div>
