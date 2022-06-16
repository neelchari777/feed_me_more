<?php

if (!class_exists('FmShortCode')):

    /**
     *
     */
    class FmShortCode {
        private $scId;
        public $scA;

        function __construct() {
            add_shortcode('foodmenu', array($this, 'foodmenu_shortcode'));
            add_shortcode( 'rt-foodmenu', array( $this, 'foodmenu_shortcode' ) );
            add_shortcode('foodmenu-single', array($this, 'foodmenu_single'));
        }

        function foodmenu_shortcode($atts, $content = "") {
            $rand = mt_rand();
            $layoutID = "fmp-container-" . $rand;
            $html = null;
            $arg = array();

            $this->scId = $scID = isset( $atts['id'] ) ? absint( $atts['id'] ) : 0;

            if ( $scID ) {

                $scMeta = get_post_meta($scID);

                $arg['class'] = '';

                $layout = (!empty($scMeta['fmp_layout'][0]) ? $scMeta['fmp_layout'][0] : 'layout-free');
                if (!in_array($layout, array_keys(TLPFoodMenu()->scLayout()))) {
                    $layout = 'layout-free';
                }
                $dCol = (isset($scMeta['fmp_desktop_column'][0]) ? absint($scMeta['fmp_desktop_column'][0]) : 3);
                $tCol = (isset($scMeta['fmp_tab_column'][0]) ? absint($scMeta['fmp_tab_column'][0]) : 2);
                $mCol = (isset($scMeta['fmp_mobile_column'][0]) ? absint($scMeta['fmp_mobile_column'][0]) : 1);
                if (!in_array($dCol, array_keys(TLPFoodMenu()->scColumns()))) {
                    $dCol = 3;
                }
                if (!in_array($tCol, array_keys(TLPFoodMenu()->scColumns()))) {
                    $tCol = 2;
                }
                if (!in_array($dCol, array_keys(TLPFoodMenu()->scColumns()))) {
                    $mCol = 1;
                }
                $imgSize = (!empty($scMeta['fmp_image_size'][0]) ? $scMeta['fmp_image_size'][0] : "medium");
                $excerpt_limit = (!empty($scMeta['fmp_excerpt_limit'][0]) ? absint($scMeta['fmp_excerpt_limit'][0]) : 0);

                $isIsotope = preg_match('/isotope/', $layout);
                $isCat = preg_match('/grid-by-cat/', $layout);
                $isCarousel = preg_match('/carousel/', $layout);

                $isoClass = '';
                if ($isIsotope) {
                    $isoClass = 'fmp-isotope-layout';
                }

                /* Argument create */
                $containerDataAttr = false;
                $args = array();
                $source = get_post_meta($scID, 'fmp_source', true);
                $args['post_type'] = ($source && in_array($source, array_keys(TLPFoodMenu()->scProductSource()))) ? $source : TLPFoodMenu()->post_type;
                $categoryTaxonomy = ($args['post_type'] == 'product') ? 'product_cat' : TLPFoodMenu()->taxonomies['category'];
                $arg['taxonomy'] = $categoryTaxonomy;
                // Common filter
                /* post__in */
                $post__in = (isset($scMeta['fmp_post__in'][0]) ? $scMeta['fmp_post__in'][0] : null);
                if ($post__in) {
                    $post__in = explode(',', $post__in);
                    $args['post__in'] = $post__in;
                }
                /* post__not_in */
                $post__not_in = (isset($scMeta['fmp_post__not_in'][0]) ? $scMeta['fmp_post__not_in'][0] : null);
                if ($post__not_in) {
                    $post__not_in = explode(',', $post__not_in);
                    $args['post__not_in'] = $post__not_in;
                }

                /* LIMIT */
                $limit = ((empty($scMeta['fmp_limit'][0]) || $scMeta['fmp_limit'][0] === '-1') ? 10000000 : (int)$scMeta['fmp_limit'][0]);
                $args['posts_per_page'] = $limit;

                // Taxonomy
                $cats = (isset($scMeta['fmp_categories']) ? array_filter($scMeta['fmp_categories']) : array());
                if(!empty($cats) && apply_filters('tlp_fmp_has_multiple_meta_issue', false)) {
                    $cats = unserialize($cats[0]);
                }
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
                $order_by = (isset($scMeta['fmp_order_by'][0]) ? $scMeta['fmp_order_by'][0] : null);
                $order = (isset($scMeta['fmp_order'][0]) ? $scMeta['fmp_order'][0] : null);
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
                $containerDataAttr .= "data-sc-id='{$scID}' data-layout='{$layout}' data-desktop-col='{$dCol}'  data-tab-col='{$tCol}'  data-mobile-col='{$mCol}'";
                $dCol = round(12 / $dCol);
                $tCol = round(12 / $tCol);
                $mCol = round(12 / $mCol);
                if ($isCarousel) {
                    $dCol = $tCol = $mCol = 12;
                }

                $gridExtra = null;

                $arg['grid'] = "fmp-col-lg-{$dCol} fmp-col-md-{$dCol} fmp-col-sm-{$tCol} fmp-col-xs-{$mCol}";
                $arg['class'] .= " fmp-grid-item";

                $rowClass = [
                    'masonryG'  => '',
                    'preLoader' => ''
                ];

                $arg['items'] = !empty($scMeta['fmp_item_fields']) ? $scMeta['fmp_item_fields'] : array();
                if(!empty($arg['items']) && apply_filters('tlp_fmp_has_multiple_meta_issue', false)) {
                    $arg['items'] = unserialize($arg['items'][0]);
                }
                $arg['anchorClass'] = null;
                $link = !empty($scMeta['fmp_detail_page_link'][0]) ? true : false;
                if ($link) {
                    $arg['link'] = true;
                } else {
                    $arg['link'] = false;
                    $arg['anchorClass'] .= ' fmp-disable';
                }

                $parentClass = (!empty($scMeta['fmp_parent_class'][0]) ? trim($scMeta['fmp_parent_class'][0]) : null);

                $rowClass = apply_filters('rt_fm_row_class', $rowClass, $scMeta);
                // Start layout
                $html .= $this->layoutStyle($layoutID, $scMeta);
                $html .= "<div class='fmp-container-fluid fmp-wrapper fmp {$parentClass}' id='{$layoutID}' {$containerDataAttr}>";
                $html .= "<div class='fmp-row fmp-{$layout}{$rowClass["masonryG"]} {$rowClass["preLoader"]} {$isoClass}'>";

                $arg['wc'] = class_exists('WooCommerce') ? true : false;
                $arg['source'] = $args['post_type'];

                $args = apply_filters('rt_fm_sc_query_args', $args, $scID);
                $arg = apply_filters('rt_fm_shortcode_data', $arg, $scMeta, $scID);

                if ($isCat) {
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
                            if (!empty($cats) && is_array($cats) && !in_array($term->term_id, $cats)) {
                                continue;
                            }
                            $taxQ = array();
                            $taxQ[] = array(
                                'taxonomy' => $categoryTaxonomy,
                                'field'    => 'term_id',
                                'terms'    => array($term->term_id),
                            );
                            $args['tax_query'] = $taxQ;
                            $data['args'] = $args;

                            $data['taxonomy'] = $categoryTaxonomy;
                            $data['excerpt_limit'] = $excerpt_limit;
                            $data['imgSize'] = $imgSize;
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
                        do_action('rt_fm_sc_before_loop', $scMeta, $rand);
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
                        do_action('rt_fm_sc_after_loop', $scMeta);
                        $html .= ob_get_contents();
                        ob_end_clean();

                    } else {
                        $html .= "<p>" . __('No post found...', 'tlp-food-menu') . "</p>";
                    }
                }
                $html .= "</div>"; // End row

                ob_start();
                do_action('rt_fm_pagination', $args, $scID);
                $html .= ob_get_contents();
                ob_end_clean();

                $html .= "</div>"; // container fmp-fmp

                wp_reset_postdata();

                $scriptGenerator = array();
                $this->scA[] = $scriptGenerator;
                add_action('wp_footer', array($this, 'register_sc_scripts'));
            } else {
                //$html .= "<p>" . __("No shortCode found.", "tlp-food-menu") . "</p>";
                return $this->get_old_layout( $atts );
            }

            return $html;
        }

        function styleGenerator( $title_color ) {
            $html = null;
            if ( ! empty( $title_color ) ) {
                $html .= "<style type='text/css'>";
                $html .= ".fmp-wrapper h3,.fmp-wrapper h3 a{ color:{$title_color}; }";
                $html .= "</style>";
            }

            return $html;
        }

        private function get_old_layout( $atts ) {

            $atts = shortcode_atts( array(
                'col'          => 2,
                'orderby'      => 'date',
                'order'        => 'DESC',
                'cat'          => 'all',
                'hide-img'     => false,
                'disable-link' => false,
                'title-color'  => null,
                'class'        => null,
            ), $atts, 'foodmenu' );

            @$rawCat = ( $atts['cat'] == 'all' ? null : $atts['cat'] );
            $settings   = get_option( TLPFoodMenu()->options['settings'] );

            $col        = in_array( $atts['col'], array( 1, 2, 3, 4 ) ) ? $atts['col'] : 2;
            $grid       = 12 / $col;

            $bss = "tlp-col-md-{$grid} tlp-col-lg-{$grid} tlp-col-sm-12 fmp-item";

            $cat = array();
            if ( isset( $rawCat ) ) {
                $rca = explode( ",", $rawCat );
                if ( ! empty( $rca ) ) {
                    foreach ( $rca as $c ) {
                        $cat[] = $c;
                    }
                }
            }
            $html  = null;
            $class = array(
                'fmp-container-fluid',
                'fmp-wrapper',
                'fmp'
            );
            if ( ! empty( $atts['class'] ) ) {
                $class[] = $atts['class'];
            }
            $class = implode( ' ', $class );
            $html  .= '<div class="' . esc_attr( $class ) . '">';
            if ( ! empty( $cat ) && is_array( $cat ) ) {
                foreach ( $cat as $c ) {
                    $args = array(
                        'post_type'      => TLPFoodMenu()->post_type,
                        'post_status'    => 'publish',
                        'posts_per_page' => - 1,
                        'orderby'        => $atts['orderby'],
                        'order'          => $atts['order'],
                        'tax_query'      => array(
                            array(
                                'taxonomy' => TLPFoodMenu()->taxonomies['category'],
                                'field'    => 'term_id',
                                'terms'    => array( $c ),
                                'operator' => 'IN',
                            ),
                        )
                    );

                    $foodQuery = new WP_Query( $args );

                    $term      = get_term_by( 'id', $c, TLPFoodMenu()->taxonomies['category'] );
                    if ( $foodQuery->have_posts() ) {
                        $html .= $this->styleGenerator( $atts['title-color'] );
                        $html .= "<h2 class='fmp-category-title'>{$term->name}</h2>";
                        $html .= '<div class="fmp-row fmp-grid-by-cat-free">';
                        while ( $foodQuery->have_posts() ) : $foodQuery->the_post();
                            $html .= "<div class='{$bss}'>";
                            $html .= "<div class='fmp-food-item'>";
                            if ( ! $atts['hide-img'] ) {
                                $html .= '<div class="fmp-image-wrap">';
                                if ( has_post_thumbnail() ) {
                                    $img = get_the_post_thumbnail( get_the_ID(), 'medium' );
                                } else {
                                    $img = "<img src='" . TLPFoodMenu()->getAssetsUrl() . 'images/demo-55x55.png' . "' alt='" . get_the_title() . "' />";
                                }
                                if ( $atts['disable-link'] ) {
                                    $html .= $img;
                                } else {
                                    $html .= '<a href="' . get_permalink() . '" title="' . get_the_title() . '">' . $img . '</a>';
                                }
                                $html .= '</div>';
                            }
                            $html .= '<div class="fmp-content-wrap">';
                            $html .= "<div class='fmp-title'>";
                            if ( $atts['disable-link'] ) {
                                $html .= '<h3>' . get_the_title() . '</h3>';
                            } else {
                                $html .= '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
                            }
                            $gTotal = TLPFoodMenu()->getPriceWithLabel();
                            $html   .= '<span class="price">' . $gTotal . '</span>';
                            $html   .= "</div>";
                            $html   .= '<p>' . TLPFoodMenu()->the_excerpt_max_charlength(80) . '</p>';
                            $html   .= '</div>';
                            $html   .= '</div>';
                            $html   .= "</div>";
                        endwhile;
                        wp_reset_postdata();
                        $html .= '</div>';
                    }
                }
            } else {
                $html      .= '<div class="fmp-row fmp-grid-by-cat-free">';
                $args      = array(
                    'post_type'      => TLPFoodMenu()->post_type,
                    'post_status'    => 'publish',
                    'posts_per_page' => - 1,
                    'orderby'        => $atts['orderby'],
                    'order'          => $atts['order']
                );
                $foodQuery = new WP_Query( $args );
                if ( $foodQuery->have_posts() ) {
                    $html .= $this->styleGenerator( $atts['title-color'] );
                    while ( $foodQuery->have_posts() ) : $foodQuery->the_post();
                        $html .= "<div class='{$bss}'>";
                        $html .= "<div class='fmp-food-item'>";
                        if ( ! $atts['hide-img'] ) {
                            $html .= '<div class="fmp-image-wrap">';
                            if ( has_post_thumbnail() ) {
                                $img = get_the_post_thumbnail( get_the_ID(), 'medium' );
                            } else {
                                $img = "<img src='" . TLPFoodMenu()->getAssetsUrl() . 'images/demo-55x55.png' . "' alt='" . get_the_title() . "' />";
                            }
                            if ( $atts['disable-link'] ) {
                                $html .= $img;
                            } else {
                                $html .= '<a href="' . get_permalink() . '" title="' . get_the_title() . '">' . $img . '</a>';
                            }
                            $html .= '</div>';
                        }
                        $html .= '<div class="fmp-content-wrap">';
                        $html .= "<div class='fmp-title'>";
                        if ( $atts['disable-link'] ) {
                            $html .= '<h3>' . get_the_title() . '</h3>';
                        } else {
                            $html .= '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
                        }
                        $gTotal = TLPFoodMenu()->getPriceWithLabel();
                        $html   .= '<span class="price">' . $gTotal . '</span>';
                        $html   .= '</div>';
                        $html   .= '<p>' . TLPFoodMenu()->the_excerpt_max_charlength(80) . '</p>';
                        $html   .= '</div>';
                        $html   .= '</div>';
                        $html   .= "</div>";
                    endwhile;
                    wp_reset_postdata();

                } else {
                    $html .= "<p>" . __( 'No food found.', 'tlp-food-menu' ) . "</p>";
                }
                $html .= '</div>';
            }

            $html .= '</div>';

            return $html;
        }

        function register_sc_scripts() {

            $script = array();
            $style = array();
            array_push($script, 'jquery');
            if (count($this->scA)) {
                wp_enqueue_script('jquery');
                array_push($script, 'fm-frontend');
                wp_enqueue_style($style);
                wp_enqueue_script($script);
                $nonce = wp_create_nonce(TLPFoodMenu()->nonceText());
                wp_localize_script('fm-frontend', 'fmp',
                    array(
                        'nonceID'     => TLPFoodMenu()->nonceId(),
                        'nonce'       => $nonce,
                        'ajaxurl'     => admin_url('admin-ajax.php'),
                    ));
                do_action('fmp_sc_custom_script', $this->scId, $this->scA);
            }
        }

        function foodmenu_single($atts, $content = "") {
            /**
             * Shortcode attribute desctiption
             *
             * @var [type]
             */

            $html = null;

            $atts = shortcode_atts(array(
                'id' => null,
            ), $atts, 'foodmenu-single');

            return $html;
        }

        function layoutStyle($ID, $scMeta) {

            $css = null;
            $css .= "<style type='text/css' media='all'>";

            // Title
            $title = (!empty($scMeta['fmp_title_style'][0]) ? unserialize($scMeta['fmp_title_style'][0]) : array());

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
            $price = (!empty($scMeta['fmp_price_style'][0]) ? unserialize($scMeta['fmp_price_style'][0]) : array());

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
            $btnBg = (!empty($scMeta['fmp_button_bg_color'][0]) ? TLPFoodMenu()->sanitize_hex_color($scMeta['fmp_button_bg_color'][0]) : null);
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
            $btnText = (!empty($scMeta['fmp_button_text_color'][0]) ? TLPFoodMenu()->sanitize_hex_color($scMeta['fmp_button_text_color'][0]) : null);
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
            $btnHbg = (!empty($scMeta['fmp_button_hover_bg_color'][0]) ? TLPFoodMenu()->sanitize_hex_color($scMeta['fmp_button_hover_bg_color'][0]) : null);
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
            $btnHtext = (!empty($scMeta['fmp_button_hover_text_color'][0]) ? TLPFoodMenu()->sanitize_hex_color($scMeta['fmp_button_hover_text_color'][0]) : null);
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
            $btn_typo = (!empty($scMeta['fmp_button_typo'][0]) ? unserialize($scMeta['fmp_button_typo'][0]) : array());

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
            do_action('fmp_sc_custom_css', $ID, $scMeta);
            $css .= ob_get_clean();

            $css .= "</style>";

            return $css;
        }

    }

endif;