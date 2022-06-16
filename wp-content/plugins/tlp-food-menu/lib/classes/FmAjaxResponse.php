<?php

if (!class_exists('FmAjaxResponse')):

    class FmAjaxResponse {

        public function __construct() {
            add_action('wp_ajax_fmp_sc_source_change', array($this, 'fmp_sc_source_change'));
        }

        public function fmp_sc_source_change() {
            $catList = '';
            $source = esc_attr($_REQUEST['source']);
            $source = ($source && in_array($source, array_keys(TLPFoodMenu()->scProductSource()))) ? $source : TLPFoodMenu()->post_type;

            $terms = array();
            if ($source == 'product' && TLPFoodMenu()->isWcActive()) {
                $termList = get_terms('product_cat', array('hide_empty' => 0));
                if (is_array($termList) && !empty($termList) && empty($termList['errors'])) {
                    $terms = $termList;
                }
            } else {
                $termList = get_terms(TLPFoodMenu()->taxonomies['category'], array('hide_empty' => 0));
                if (is_array($termList) && !empty($termList) && empty($termList['errors'])) {
                    $terms = $termList;
                }
            }
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    $catList .= "<option value='{$term->term_id}'>{$term->name}</option>";
                }
            }

            wp_send_json(array(
                'cat_list' => $catList,
                'x'        => $_REQUEST
            ));
            die();
        }
    }
endif;