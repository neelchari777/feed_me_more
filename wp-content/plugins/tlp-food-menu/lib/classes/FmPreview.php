<?php

if (!class_exists('FmPreview')):

    class FmPreview {

        function __construct() {
            add_action('wp_ajax_fmpPreviewAjaxCall', array($this, 'fmpPreviewAjaxCall'));
        }

        function fmpPreviewAjaxCall() {

            $msg = $data = null;
            $error = true;
            if (TLPFoodMenu()->verifyNonce()) {
                $error = false;
                $scMeta = $_REQUEST;

                $rand = mt_rand();
                $layoutID = "fmp-container-" . $rand;

                $arg['class'] = '';

                $layout = (!empty($scMeta['fmp_layout']) ? $scMeta['fmp_layout'] : 'layout-free');
                if (!in_array($layout, array_keys(TLPFoodMenu()->scLayout()))) {
                    $layout = 'layout-free';
                }
                $dCol = (isset($scMeta['fmp_desktop_column']) ? absint($scMeta['fmp_desktop_column']) : 3);
                $tCol = (isset($scMeta['fmp_tab_column']) ? absint($scMeta['fmp_tab_column']) : 2);
                $mCol = (isset($scMeta['fmp_mobile_column']) ? absint($scMeta['fmp_mobile_column']) : 1);
                if (!in_array($dCol, array_keys(TLPFoodMenu()->scColumns()))) {
                    $dCol = 3;
                }
                if (!in_array($tCol, array_keys(TLPFoodMenu()->scColumns()))) {
                    $tCol = 2;
                }
                if (!in_array($dCol, array_keys(TLPFoodMenu()->scColumns()))) {
                    $mCol = 1;
                }
                $imgSize = (!empty($scMeta['fmp_image_size']) ? $scMeta['fmp_image_size'] : "medium");
                $excerpt_limit = (!empty($scMeta['fmp_excerpt_limit']) ? absint($scMeta['fmp_excerpt_limit']) : 0);

                $isIsotope = preg_match('/isotope/', $layout);
                $isCat = preg_match('/grid-by-cat/', $layout);
                $isCarousel = preg_match('/carousel/', $layout);

                $isoClass = '';
                if ($isIsotope) {
                    $isoClass = 'fmp-isotope-layout';
                }

                /* Argument create */
                $containerDataAttr = false;
                $args  = array();
                $source = isset($scMeta['fmp_source']) ? $scMeta['fmp_source'] : 'food-menu';
                $args['post_type'] = ($source && in_array($source, array_keys(TLPFoodMenu()->scProductSource()))) ? $source : TLPFoodMenu()->post_type;
                $categoryTaxonomy = ($args['post_type'] == 'product') ? 'product_cat' : TLPFoodMenu()->taxonomies['category'];
                $arg['taxonomy'] = $categoryTaxonomy;
                // Common filter
                /* post__in */
                $post__in = (isset($scMeta['fmp_post__in']) ? $scMeta['fmp_post__in'] : null);
                if ($post__in) {
                    $post__in = explode(',', $post__in);
                    $args['post__in'] = $post__in;
                }
                /* post__not_in */
                $post__not_in = (isset($scMeta['fmp_post__not_in']) ? $scMeta['fmp_post__not_in'] : null);
                if ($post__not_in) {
                    $post__not_in = explode(',', $post__not_in);
                    $args['post__not_in'] = $post__not_in;
                }

                /* LIMIT */
                $limit = ((empty($scMeta['fmp_limit']) || $scMeta['fmp_limit'] === '-1') ? 10000000 : (int)$scMeta['fmp_limit']);
                $args['posts_per_page'] = $limit;

                // Taxonomy
                $cats = (isset($scMeta['fmp_categories']) ? array_filter($scMeta['fmp_categories']) : array());
                $taxQ = array();
                if (is_array($cats) && !empty($cats)) {
                    $taxQ[] = array(
                        'taxonomy' => $categoryTaxonomy,
                        'field'    => 'term_id',
                        'terms'    => $cats,
                    );
                }
                if (!empty($taxQ)) {
                    $args['tax_query'] = $taxQ;
                }

                // Order
                $order_by = (isset($scMeta['fmp_order_by']) ? $scMeta['fmp_order_by'] : null);
                $order = (isset($scMeta['fmp_order']) ? $scMeta['fmp_order'] : null);
                if ($order) {
                    $args['order'] = $order;
                }
                if ($order_by) {
                    if ($order_by == "price") {
                        $args['orderby'] = 'meta_value_num';
                        $args['meta_key'] = '_regular_price';
                    } else {
                        $args['orderby'] = $order_by;
                    }
                }

                // Validation
                $containerDataAttr .= " data-layout='{$layout}' data-desktop-col='{$dCol}'  data-tab-col='{$tCol}'  data-mobile-col='{$mCol}'";
                $dCol = round(12 / $dCol);
                $tCol = round(12 / $tCol);
                $mCol = round(12 / $mCol);

                if ($isCarousel) {
                    $dCol = $tCol = $mCol = 12;
                }

                $arg['grid'] = "fmp-col-lg-{$dCol} fmp-col-md-{$dCol} fmp-col-sm-{$tCol} fmp-col-xs-{$mCol}";

                $arg['class'] .= " fmp-grid-item";

                $rowClass = [
                    'masonryG'  => '',
                    'preLoader' => ''
                ];

                $arg['items'] = !empty($scMeta['fmp_item_fields']) ? $scMeta['fmp_item_fields'] : array();
                $arg['anchorClass'] = null;
                $link = !empty($scMeta['fmp_detail_page_link']) ? true : false;

                if ($link) {
                    $arg['link'] = true;
                } else {
                    $arg['link'] = false;
                    $arg['anchorClass'] .= ' fmp-disable';
                }

                $parentClass = (!empty($scMeta['fmp_parent_class']) ? trim($scMeta['fmp_parent_class']) : null);

                $arg['wc'] = class_exists('WooCommerce') ? true : false;
                $arg['source'] = $args['post_type'];
                $html = null;

                $rowClass = apply_filters('rt_fm_preview_row_class', $rowClass, $scMeta);
                // Start layout
                $html .= $this->layoutStyle($layoutID, $scMeta);
                $html .= "<div class='fmp-container-fluid fmp-wrapper fmp {$parentClass}' id='{$layoutID}' {$containerDataAttr}>";
                $html .= "<div class='fmp-row fmp-{$layout}{$rowClass["masonryG"]} {$rowClass["preLoader"]} {$isoClass}'>";

                $scID = $scMeta['sc_id'];

                $args = apply_filters('rt_fm_preview_query_args', $args, $scMeta);
                $arg = apply_filters('rt_fm_preview_data', $arg, $scMeta);

                if ($isCat) {
                    $terms = array();
                    $catVar = array();
                    $catVar['hide_empty'] = false;
                    if (!empty($cats)) {
                        $catVar['include'] = $cats;
                    }

                    if (function_exists('get_term_meta')) {
                        $catVar['taxonomy'] = $categoryTaxonomy;
                        $catVar['orderby'] = 'meta_value_num';
                        $catVar['order'] = 'ASC';
                        $metaKey = ($categoryTaxonomy === "product") ? 'order' : '_order';
                        $catVar['meta_query'] = [
                            'relation' => 'OR',
                            [
                                'key' => $metaKey,
                                'compare' => 'NOT EXISTS'
                            ],
                            [
                                'key' => $metaKey,
                                'type' => 'NUMERIC'
                            ]
                        ];

                        $terms = get_terms($catVar);

                    } else {
                        $terms = get_terms($categoryTaxonomy, $catVar);
                    }

                    if (is_array($terms) && !empty($terms) && empty($terms['errors'])) {
                        foreach ($terms as $term) {
                            $taxQ = array();
                            $taxQ[] = array(
                                'taxonomy' => $categoryTaxonomy,
                                'field'    => 'term_id',
                                'terms'    => array($term->term_id),
                            );
                            $args['tax_query'] = $taxQ;
                            $data['taxonomy'] = $categoryTaxonomy;
                            $data['args'] = $args;
                            $data['excerpt_limit'] = $excerpt_limit;
                            $data['imgSize'] = $imgSize;
                            $data['catId'] = $term->term_id;
                            $data['catName'] = $term->name;
                            $data['catDescription'] = $term->description;
                            $data['term'] = $term;
                            $data['arg'] = $arg;

                            $html .= TLPFoodMenu()->render('layouts/' . $layout, $data, true);
                        }
                    } else {
                        $html .= "<p>" . __('No category found', 'tlp-food-menu') . "</p>";
                    }
                } else {
                    $args['post_status'] = 'publish';
                    $fmpQuery = new WP_Query($args);
                    if ($fmpQuery->have_posts()) {

                        ob_start();
                        do_action('rt_fm_preview_before_loop', $scMeta, $rand);
                        $html .= ob_get_contents();
                        ob_end_clean();

                        while ($fmpQuery->have_posts()) : $fmpQuery->the_post();

                            $pID = get_the_ID();
                            $arg['pID'] = $pID;
                            $arg['title'] = get_the_title();
                            $arg['pLink'] = get_permalink();
                            $excerpt = get_the_excerpt();
                            $arg['excerpt'] = TLPFoodMenu()->strip_tags_content($excerpt, $excerpt_limit);
                            $arg['imgSize']    = $imgSize;
                            if ($isIsotope) {
                                $termAs = wp_get_post_terms($pID, $categoryTaxonomy,
                                    array("fields" => "all"));
                                $isoFilter = null;
                                if (!empty($termAs)) {
                                    foreach ($termAs as $term) {
                                        $isoFilter .= " " . "iso_" . $term->term_id;
                                        $isoFilter .= " " . $term->slug;
                                    }
                                }
                                $arg['isoFilter'] = $isoFilter;
                            }
                            $html .= TLPFoodMenu()->render('layouts/' . $layout, $arg, true);
                        endwhile;

                        ob_start();
                        do_action('rt_fm_preview_after_loop', $scMeta);
                        $html .= ob_get_contents();
                        ob_end_clean();

                    } else {
                        $html .= "<p>" . __('No post found.', 'tlp-food-menu') . "</p>";
                    }
                }
                $html .= "</div>"; // End row

                $html .= "</div>"; // container fmp-fmp
                $data = $html;
                wp_reset_postdata();

            } else {
                $msg = __('Security Error !!', 'tlp-food-menu');
            }

            wp_send_json(array(
                'error' => $error,
                'msg'   => $msg,
                'data'  => $data
            ));
            die();

        }

        private function layoutStyle($ID, $scMeta) {

            $css = null;
            $css .= "<style type='text/css' media='all'>";

            // Title
            $title = (!empty($scMeta['fmp_title_style']) ? $scMeta['fmp_title_style'] : array());
            if (!empty($title)) {
                $title_color = (!empty($title['color']) ? $title['color'] : null);
                $title_hover_color = (!empty($title['hover_color']) ? $title['hover_color'] : null);
                $title_size = (!empty($title['size']) ? absint($title['size']) : null);
                $title_weight = (!empty($title['weight']) ? $title['weight'] : null);
                $title_alignment = (!empty($title['align']) ? $title['align'] : null);

                $css .= "#{$ID} .fmp-title h3,";
                $css .= "#{$ID} .fmp-content h3,";
                $css .= "#{$ID} .fmp-content h3 a,";
                $css .= "#{$ID} h3.fmp-title,";
                $css .= "#{$ID} h3.fmp-title a,";
                $css .= "#{$ID} .fmp-title h3 a { ";

                if ($title_color) {
                    $css .= "color:" . $title_color . ";";
                }
                if ($title_size) {
                    $css .= "font-size:" . $title_size . "px;";
                }
                if ($title_weight) {
                    $css .= "font-weight:" . $title_weight . ";";
                }
                if ($title_alignment) {
                    $css .= "text-align:" . $title_alignment . ";";
                }
                $css .= "}";

                $css .= "#{$ID} .fmp-content h3 a:hover,";
                $css .= "#{$ID} h3.fmp-title a:hover,";
                $css .= "#{$ID} .fmp-title h3 a:hover { ";
                if ($title_hover_color) {
                    $css .= "color:" . $title_hover_color . ";";
                }
                $css .= "}";
            }

            // Price
            $price = (!empty($scMeta['fmp_price_style']) ? $scMeta['fmp_price_style'] : array());

            if (!empty($price)) {
                $price_color = (!empty($price['color']) ? $price['color'] : null);
                $price_size = (!empty($price['size']) ? absint($price['size']) : null);
                $price_weight = (!empty($price['weight']) ? $price['weight'] : null);
                $price_alignment = (!empty($price['align']) ? $price['align'] : null);

                $css .= "#{$ID} .fmp-box .fmp-price,";
                $css .= "#{$ID} .fmp-content-wrap .price {";

                if ($price_color) {
                    $css .= "color:" . $price_color . ";";
                }
                if ($price_size) {
                    $css .= "font-size:" . $price_size . "px;";
                }
                if ($price_weight) {
                    $css .= "font-weight:" . $price_weight . ";";
                }
                if ($price_alignment) {
                    $css .= "text-align:" . $price_alignment . ";";
                }
                $css .= "}";

            }

            // Button bg color
            $btnBg = (!empty($scMeta['fmp_button_bg_color']) ? TLPFoodMenu()->sanitize_hex_color($scMeta['fmp_button_bg_color']) : null);
            if ($btnBg) {
                $css .= "#{$ID} a.fmp-btn-read-more,
                #{$ID} a.fmp-wc-add-to-cart-btn,
                #{$ID} .owl-theme .owl-dots .owl-dot span,
				#{$ID} .owl-theme .owl-nav [class*=owl-],
				#{$ID} .fmp-isotope-buttons button,
				#{$ID} .fmp-utility .fmp-load-more button,
				#{$ID} .fmp-pagination ul.pagination-list li a,
				#{$ID} .fmp-layout5 .fmp-price,
				#{$ID} .fmp-layout5 .fmp-attr-variation-wrapper .fmp-attr-variation,
                #{$ID} .fmp-food-item .button {";
                $css .= "background-color:" . $btnBg . ";";
                $css .= "}";
            }

            if ($btnBg) {
                $css .= "#{$ID} .fmp-layout5 .fmp-wc-add-to-cart-btn,
                #{$ID} .fmp-layout5 .fmp-price-box .quantity .input-text.qty.text {";
                $css .= "border-color:" . $btnBg . ";";
                $css .= "}";
            }

            // button text color
            $btnText = (!empty($scMeta['fmp_button_text_color']) ? TLPFoodMenu()->sanitize_hex_color($scMeta['fmp_button_text_color']) : null);
            if ($btnText) {
                $css .= "#{$ID} a.fmp-btn-read-more,
                #{$ID} a.fmp-wc-add-to-cart-btn,
                #{$ID} .owl-theme .owl-dots .owl-dot span,
				#{$ID} .owl-theme .owl-nav [class*=owl-],
				#{$ID} .fmp-isotope-buttons button,
				#{$ID} .fmp-utility .fmp-load-more button,
				#{$ID} .fmp-pagination ul.pagination-list li a,
				#{$ID} .fmp-layout5 .fmp-attr-variation-wrapper .fmp-attr-variation,
				#{$ID} .fmp-layout5 .fmp-wc-add-to-cart-btn,
                #{$ID} .fmp-food-item .button {";
                $css .= "color:" . $btnText . ";";
                $css .= "}";
            }

            // Button hover bg color
            $btnHbg = (!empty($scMeta['fmp_button_hover_bg_color']) ? TLPFoodMenu()->sanitize_hex_color($scMeta['fmp_button_hover_bg_color']) : null);
            if ($btnHbg) {
                $css .= "#{$ID} a.fmp-btn-read-more:hover,
                #{$ID} a.fmp-wc-add-to-cart-btn:hover,
				#{$ID} .owl-theme .owl-nav [class*=owl-]:hover, 
				#{$ID} .fmp-utility .fmp-load-more button:hover, 
				#{$ID} .owl-theme .owl-dots .owl-dot:hover span,
				#{$ID} .owl-theme .owl-dots .owl-dot.active span,
				#{$ID} .fmp-isotope-buttons button.selected,
				#{$ID} .fmp-isotope-buttons button:hover,
				#{$ID} .fmp-pagination ul.pagination-list li.active span,
				#{$ID} .fmp-pagination ul.pagination-list li a:hover,
				#{$ID} .fmp-layout5 .fmp-wc-add-to-cart-btn:hover,
                #{$ID} .fmp-food-item .button:hover {";
                $css .= "background-color:" . $btnHbg . ";";
                $css .= "}";
            }

            if ($btnHbg) {
                $css .= "#{$ID} .fmp-layout5 .fmp-wc-add-to-cart-btn:hover {";
                $css .= "border-color:" . $btnHbg . ";";
                $css .= "}";
            }

            // Button hover text color
            $btnHtext = (!empty($scMeta['fmp_button_hover_text_color']) ? TLPFoodMenu()->sanitize_hex_color($scMeta['fmp_button_hover_text_color']) : null);
            if ($btnHtext) {
                $css .= "#{$ID} a.fmp-btn-read-more:hover,
                #{$ID} a.fmp-wc-add-to-cart-btn:hover,
				#{$ID} .owl-theme .owl-nav [class*=owl-]:hover, 
				#{$ID} .fmp-utility .fmp-load-more button:hover, 
				#{$ID} .owl-theme .owl-dots .owl-dot:hover span,
				#{$ID} .owl-theme .owl-dots .owl-dot.active span,
				#{$ID} .fmp-isotope-buttons button.selected,
				#{$ID} .fmp-isotope-buttons button:hover,
				#{$ID} .fmp-pagination ul.pagination-list li.active span,
				#{$ID} .fmp-pagination ul.pagination-list li a:hover,
				#{$ID} .fmp-layout5 .fmp-wc-add-to-cart-btn:hover,
                #{$ID} .fmp-food-item .button:hover {";
                $css .= "color:" . $btnHtext . ";";
                $css .= "}";
            }

            // Button Typography
            $btn_typo = (!empty($scMeta['fmp_button_typo']) ? $scMeta['fmp_button_typo'] : array());

            if (!empty($btn_typo)) {
                $btn_size = (!empty($btn_typo['size']) ? absint($btn_typo['size']) : null);
                $btn_weight = (!empty($btn_typo['weight']) ? $btn_typo['weight'] : null);

                $css .= "#{$ID} .fmp-iso-filter button,";
                $css .= "#{$ID} .fmp-btn-read-more,";
                $css .= "#{$ID} .fmp-wc-add-to-cart-btn { ";

                if ($btn_size) {
                    $css .= "font-size:" . $btn_size . "px;";
                }
                if ($btn_weight) {
                    $css .= "font-weight:" . $btn_weight . ";";
                }
                $css .= "}";
            }

            ob_start();
            do_action('fmp_sc_preview_css', $ID, $scMeta);
            $css .= ob_get_clean();

            $css .= "</style>";

            return $css;
        }
    }

endif;