<?php
extract($arg);
$gridQuery = new WP_Query($args);

$html = "<h2 class='fmp-category-title'>{$term->name}</h2>";

while ($gridQuery->have_posts()) : $gridQuery->the_post();
    $id = get_the_ID();
    $image = TLPFoodMenu()->getFeatureImage($id, $imgSize);
    $excerpt = TLPFoodMenu()->strip_tags_content(get_the_excerpt(), $excerpt_limit);
    $add_to_cart = null;

    if ($source == 'product' && $wc == true) {
        $_product = wc_get_product($id);
        $price = $_product->get_price_html();
        if ($_product->is_purchasable()) {
            if ($_product->is_in_stock()) {
                ob_start();
                woocommerce_template_loop_add_to_cart();
                $add_to_cart .= ob_get_contents();
                ob_end_clean();
            }
        }
    } else {
        $price = TLPFoodMenu()->getPriceWithLabel($id);
    }

    $html .= "<div class='{$grid} {$class}'>";
    $html .= "<div class='fmp-food-item {$source}'>";
    if (in_array('image', $items)) {
        $html .= '<div class="fmp-image-wrap">';
        if (!$link) {
            $html .= $image;
        } else {
            $html .= '<a href="' . get_permalink() . '" title="' . get_the_title() . '">' . $image . '</a>';
        }
        $html .= '</div>';
    }
    $html .= '<div class="fmp-content-wrap">';
    $html .= "<div class='fmp-title'>";

    if (in_array('title', $items)) {
        if (!$link) {
            $html .= '<h3>' . get_the_title() . '</h3>';
        } else {
            $html .= '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
        }
    }

    if (in_array('price', $items)) {
        $html .= '<span class="price">' . wp_kses_post($price) . '</span>';
    }
    $html .= "</div>";

    if (in_array('excerpt', $items)) {
        $html .= '<p>' . $excerpt . '</p>';
    }

    $html .= stripslashes_deep($add_to_cart);

    $html .= '</div>';
    $html .= '</div>';
    $html .= "</div>";
endwhile;
wp_reset_postdata();
echo $html;