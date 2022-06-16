<?php
$add_to_cart = null;

if ($source == 'product' && $wc == true) {
    global $product;

    $product = $_product = wc_get_product($pID);
    $price = $_product->get_price_html();
    $pType = $_product->get_type();
    if ($_product->is_purchasable()) {
        if ($_product->is_in_stock()) {
            ob_start();
            woocommerce_template_loop_add_to_cart();
            $add_to_cart .= ob_get_contents();
            ob_end_clean();
        }
    }
} else {
    $price = TLPFoodMenu()->getPriceWithLabel($pID);
}
$class .= " fmp-item-" . $pID;
?>
<div class="<?php echo esc_attr($grid . " " . $class); ?>">
    <div class='fmp-food-item <?php echo esc_attr($source); ?>'>
        <?php
        $html = '';
        if (in_array('image', $items)) {
            $html .= '<div class="fmp-image-wrap">';
            $image = TLPFoodMenu()->getFeatureImage($pID, $imgSize);
            if (!$link) {
                $html .= $image;
            } else {
                $html .= '<a href="' . get_permalink() . '" title="' . get_the_title() . '">' . $image . '</a>';
            }
            $html .= '</div>';
        }

        $html .= '<div class="fmp-content-wrap">';
        if (in_array('title', $items)) {
            $html .= "<div class='fmp-title'>";
            if (!$link) {
                $html .= '<h3>' . get_the_title() . '</h3>';
            } else {
                $html .= '<h3><a data-id="' . esc_attr($pID) . '" href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
            }
        }
        if (in_array('price', $items)) {
            $html .= '<span class="price">' . wp_kses_post($price) . '</span>';
        }
        $html .= '</div>';
        if (in_array('excerpt', $items)) {
            $html .= '<p>' . $excerpt . '</p>';
        }

        $html .= stripslashes_deep($add_to_cart);

        $html .= '</div>';
        echo $html;

        ?>
    </div>
</div>
