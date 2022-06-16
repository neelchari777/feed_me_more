<?php

if (!class_exists('FmFunction')):

    /**
     *
     */
    class FmFunction {

        function __construct() {

        }

        function getPrice($id = null) {
            if ($id) {
                $id = absint($id);
            } else {
                global $post;
                $id = $post->ID;
            }

            $regular_price = get_post_meta($id, '_regular_price', true);

            return $regular_price;

        }

        function getCurrency() {
            $settings = get_option(TLPFoodMenu()->options['settings']);
            $currency = ($settings['currency'] ? esc_attr($settings['currency']) : "USD");
            return $currency;
        }

        function getCurrencySymbol() {
            $currency = $this->getCurrency();
            $cList = TLPFoodMenu()->currency_list();
            return $cList[$currency]['symbol'];
        }

        function getCurrencyPosition() {
            $settings = get_option(TLPFoodMenu()->options['settings']);
            return (!empty($settings['currency_position']) ? esc_attr($settings['currency_position']) : "left");
        }

        function getPriceWithLabel() {
            $price = $this->getPrice();
            if ($price) {
                $symbol = $this->getCurrencySymbol();
                $currencyP = $this->getCurrencyPosition();

                switch ($currencyP) {
                    case 'left':
                        $price = $symbol.$price;
                        break;

                    case 'right':
                        $price = $price.$symbol;
                        break;

                    case 'left_space':
                        $price = $symbol. " " .$price;
                        break;

                    case 'right_space':
                        $price = $price . " " . $symbol;
                        break;

                    default:

                        break;
                }
            }

            return $price;
        }
    }

endif;