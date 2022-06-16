<?php

if (!class_exists('FmActionHook')):

    class FmActionHook {

        public function __construct() {
            add_action('fmp_single_summery', array($this, 'fmp_single_images'), 10);
            add_action('fmp_single_summery', array($this, 'fmp_before_summery'), 20);
            add_action('fmp_single_summery', array($this, 'fmp_summery_title'), 30);
            add_action('fmp_single_summery', array($this, 'fmp_summery_price'), 30);
            add_action('fmp_single_summery', array($this, 'fmp_summery'), 30);
            add_action('fmp_single_summery', array($this, 'fmp_summery_meta'), 30);
            add_action('fmp_single_summery', array($this, 'fmp_after_summery'), 50);
        }

        function fmp_single_images() {
            $settings = get_option(TLPFoodMenu()->options['settings']);
            $hiddenOptions = !empty($settings['hide_options']) ? $settings['hide_options'] : array();
            global $post;
            $html = null;
            if (!in_array('image', $hiddenOptions)) {
                $html .= '<div class="fmp-col-md-5 fmp-col-lg-5 fmp-col-sm-6 fmp-images">';
                $html .= '<div id="fmp-images">';

                if (TLPFoodMenu()->hasPro()) {
                    $attachments = get_post_meta($post->ID, '_fmp_image_gallery', true);

                    $attachments = is_array($attachments) ? $attachments : array();
                    if (has_post_thumbnail()) {
                        array_unshift($attachments, get_post_thumbnail_id($post->ID));
                    }

                    if (!empty($attachments)) {
                        if (count($attachments) > 1) {
                            $thumbnails = null;
                            $slides = null;
                            foreach ($attachments as $attachment) {
                                $slides .= "<li class='fmp-slide'>" . TLPFoodMenu()->getAttachedImage($attachment,
                                        'full') . "</li>";
                                $thumbnails .= "<li class='fmp-slide-thumb'>" . TLPFoodMenu()->getAttachedImage($attachment,
                                        'thumbnail') . "</li>";
                            }

                            $slider = null;
                            $slider .= "<div id='fmp-slide-wrapper'>";
                            $slider .= "<div id='fmp-slider' class='flexslider'><ul class='slides'>{$slides}</ul></div>";
                            if (in_array($post->post_type, array(TLPFoodMenu()->post_type, 'product'))) {
                                $slider .= "<div id='fmp-carousel' class='flexslider'><ul class='slides'>{$thumbnails}</ul></div>";
                            }
                            $slider .= "</div>"; // #end fmp-slider

                            $html .= $slider;

                        } else {
                            $html .= "<div class='fmp-single-food-img-wrapper'>";
                            $html .= TLPFoodMenu()->getAttachedImage($attachments[0]);
                            $html .= "</div>";
                        }
                    } else {
                        $imgSrc = TLPFoodMenu()->placeholder_img_src();
                        $html .= "<div class='fmp-single-food-img-wrapper'>";
                        $html .= "<img class='fmp-single-food-img' alt='Place holder image' src='{$imgSrc}' />";
                        $html .= "</div>";
                    }
                } else {
                    if ( has_post_thumbnail() ) {
                        $html .= get_the_post_thumbnail( $post->ID, array( 500, 500 ) );
                    } else {
                        $html .= "<img src='" . TLPFoodMenu()->assetsUrl . 'images/demo-100x100.png' . "' alt='" . get_the_title($post->ID) . "' />";
                    }
                }
                $html .= '</div>'; // #images
                $html .= '</div>'; // fmp-images
            }
            echo $html;
        }

        function fmp_before_summery() {
            $settings = get_option(TLPFoodMenu()->options['settings']);
            $hiddenOptions = !empty($settings['hide_options']) ? $settings['hide_options'] : array();
            if (in_array('image', $hiddenOptions)) {
                echo '<div class="fmp-col-md-12 paddingr0 fmp-summery" id="fmp-summery">';
            } else {
                echo '<div class="fmp-col-md-7 fmp-col-lg-7 fmp-col-sm-6 paddingr0 fmp-summery" id="fmp-summery">';
            }
        }

        function fmp_after_summery() {
            echo '</div>';
        }

        function fmp_summery_title() {
            ?>
            <h2 class><?php the_title(); ?></h2>
            <?php
        }

        function fmp_summery_price() {
            $settings = get_option(TLPFoodMenu()->options['settings']);
            $hiddenOptions = !empty($settings['hide_options']) ? $settings['hide_options'] : array();
            if (!in_array('price', $hiddenOptions)) {
                $gTotal = TLPFoodMenu()->getPriceWithLabel();
                echo "<div class='offers'>{$gTotal}</div>";
            }
        }

        function fmp_summery() {
            $settings = get_option(TLPFoodMenu()->options['settings']);
            $hiddenOptions = !empty($settings['hide_options']) ? $settings['hide_options'] : array();
            if (!in_array('summery', $hiddenOptions) || (wp_doing_ajax() && !in_array('description', $hiddenOptions))) {
                ?>
                <div class="fmp-short-description summery entry-summery ">
                    <?php global $post;
                    if (!in_array('summery', $hiddenOptions)) {
                        the_excerpt();
                    }
                    if (wp_doing_ajax() && !in_array('description', $hiddenOptions)) {
                        the_content();
                    }
                    ?>
                </div>
                <?php
            }
        }

        function fmp_summery_meta() {
            $settings = get_option(TLPFoodMenu()->options['settings']);
            $hiddenOptions = !empty($settings['hide_options']) ? $settings['hide_options'] : array();
            if (!in_array('taxonomy', $hiddenOptions)) {
                global $post;
                $cat = get_the_terms($post->ID, TLPFoodMenu()->taxonomies['category']);
                $cat_count = is_array($cat) ? sizeof($cat) : 0;
                ?>
                <div class="fmp-meta">

                    <?php do_action('fmp_meta_start'); ?>

                    <?php echo TLPFoodMenu()->get_categories($post->ID, ', ',
                        '<span class="posted_in">' . _n('Category:', 'Categories:', $cat_count,
                            'tlp-food-menu') . ' ',
                        '</span>'); ?>
                    <?php
                        if (TLPFoodMenu::hasPro()) {
                            $tag = get_the_terms($post->ID, TLPFoodMenu()->taxonomies['tag']);
                            $tag_count = is_array($tag) ? sizeof($cat) : 0;
                            echo TLPFoodMenu()->get_tags($post->ID, ', ',
                                '<span class="tagged_as">' . _n('Tag:', 'Tags:', $tag_count, 'tlp-food-menu') . ' ',
                                '</span>');
                        }
                    ?>
                    <?php do_action('fmp_meta_end'); ?>

                </div>
                <?php
            }
        }
    }
endif;