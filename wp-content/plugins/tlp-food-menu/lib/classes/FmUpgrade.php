<?php

if ( ! class_exists('FmUpgrade') ):

	class FmUpgrade {

        function assign_term_to_post($posts, $term) {
            if ( ! empty($posts) && ! is_wp_error($term) ) {
                foreach ($posts as $post_id) {
                    $term_taxonomy_ids = wp_set_object_terms( $post_id, $term['term_id'], TLPFoodMenu()->taxonomies['category'], true );

                    if (is_wp_error($term_taxonomy_ids)) {
                        return false;
                    }
                }
            }
        }

        function insert_category_taxonomy( $term, $parent ) {
            $termArgs = [
                'description' => $term->description,
                'slug' => $term->slug,
                'parent' => $parent
            ];
            $term_id = wp_insert_term($term->name, TLPFoodMenu()->taxonomies['category'], $termArgs);

            return $term_id;
        }

        function get_post_id( $term ) {
            $args = array(
                'post_type' => TLPFoodMenu()->post_type,
                'fields' => 'ids',
                'tax_query' => array(
                    array(
                        'taxonomy' => $term->taxonomy,
                        'field' => 'slug',
                        'terms' => $term->slug
                    )
                )
            );

            return get_posts($args);
        }

        function get_sc_post_id( $term ) {
            $args = array(
                'post_type'   => 'fmsc',
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'key'   => 'fmp_categories',
                        'value' => $term->term_id,
                    )
                )
            );

            return get_posts($args);
        }

        function assign_sc_cat_meta($posts, $term) {
            if ( ! empty($posts) && ! is_wp_error($term) ) {
                foreach ($posts as $post_id) {
                    add_post_meta( $post_id, 'fmp_categories', $term['term_id']);
                }
            }
        }

        function remove_old_cat_meta($posts, $term) {
            if ( ! empty($posts) && ! is_wp_error($term) ) {
                foreach ($posts as $post_id) {
                    delete_post_meta( $post_id, 'fmp_categories', $term->term_id);
                }
            }
        }

        function migrateTaxonomy() {
            $termPost = [];
            $terms = get_terms(
                'food-menu-category',
                [
                    'parent'  => 0,
                    'hide_empty'    => false
                ]
            );
            if (!empty($terms)) {
                $termList = [];
                foreach ( $terms as $term ) {

                    $post_id = $this->get_post_id($term);
                    $sc_id = $this->get_sc_post_id($term);

                    // $termList[$term->term_id]['post_id'] = $this->get_post_id($term);

                    $parentTermId = $this->insert_category_taxonomy($term, 0);
                    $this->assign_term_to_post( $post_id, $parentTermId);

                    $this->assign_sc_cat_meta( $sc_id, $parentTermId);
                    $this->remove_old_cat_meta( $sc_id, $term);

                    $subterms = get_terms(
                        $term->taxonomy,
                        [
                            'parent'   => $term->term_id,
                            'hide_empty' => false
                        ]
                    );
                    if (!empty($subterms)) {
                        foreach ($subterms as $subterm) {
                            $post_id = $this->get_post_id($subterm);
                            $sc_id = $this->get_sc_post_id($subterm);
                            // $termList[$term->term_id]['child'][$subterm->term_id]['post_id'] = $this->get_post_id($subterm);

                            $childTermId = [];
                            if (!is_wp_error($parentTermId)) {
                                $parent = $parentTermId['term_id'];
                                $childTermId = $this->insert_category_taxonomy($subterm, $parent);

                                $this->assign_term_to_post( $post_id, $childTermId);

                                $this->assign_sc_cat_meta( $sc_id, $childTermId);
                                $this->remove_old_cat_meta( $sc_id, $subterm);
                            }

                            $subsubterms = get_terms(
                                $subterm->taxonomy,
                                [
                                    'parent'   => $subterm->term_id,
                                    'hide_empty' => false
                                ]
                            );

                            if (!empty($subsubterms)) {
                                foreach ($subsubterms as $subsubterm) {
                                    $post_id = $this->get_post_id($subsubterm);
                                    $sc_id = $this->get_sc_post_id($subsubterm);
                                    // $termList[$term->term_id]['child'][$subterm->term_id]['child'][$subsubterm->term_id]['post_id'] = $this->get_post_id($subsubterm);
                                    if (!is_wp_error($childTermId) && !empty($childTermId)) {
                                        $parent = $childTermId['term_id'];
                                        $subSubTermId = $this->insert_category_taxonomy($subsubterm, $parent);

                                        $this->assign_term_to_post( $post_id, $subSubTermId);

                                        $this->assign_sc_cat_meta( $sc_id, $subSubTermId);
                                        $this->remove_old_cat_meta( $sc_id, $subsubterm);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        function migrateData() {
            $activeVersion     = get_option( 'tlp-food-menu-installed-version' );
            $migrateFlag = get_option('tlp_fm_m_3_0');

			if (version_compare($activeVersion, '3.0.0', '<')) {
                try {
                    // Get settings
                    $exData = get_option( TLPFoodMenu()->options['settings'] );
                    $slug = !empty($exData['general']['slug']) ? $exData['general']['slug'] : TLPFoodMenu()->options['slug'];
                    $currency = !empty($exData['general']['currency']) ? $exData['general']['currency'] : "USD";
                    $currency_position = !empty($exData['general']['currency_position']) ? $exData['general']['currency_position'] : "left";
                    $data = array(
                        'currency'           => $currency,
                        'currency_position'  => $currency_position,
                        'price_thousand_sep' => ',',
                        'price_decimal_sep'  => '.',
                        'price_num_decimals' => 2,
                        'slug'               => $slug
                    );
                    update_option( TLPFoodMenu()->options['settings'], $data );

                    // Get all post
                    $allFreeMenu = get_posts( array(
                        'post_type'      => TLPFoodMenu()->post_type,
                        'posts_per_page' => - 1,
                        'post_status'    => 'publish'
                    ) );

                    if ( ! empty( $allFreeMenu ) ) {
                        foreach ( $allFreeMenu as $post ) {
                            $price = get_post_meta( $post->ID, 'price', true );
                            if ( $price ) {
                                update_post_meta( $post->ID, '_regular_price', TLPFoodMenu()->format_decimal( $price ) );
                            }

                        }
                    }

                    add_action('admin_init', [$this, 'migrateTaxonomy']);

                    update_option('tlp_fm_m_3_0', 1);
                    flush_rewrite_rules();

                } catch(Exception $e) {
                    $GLOBALS['errors'][] = $e;
                }

			}

		}

	}

endif;