<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header();
?>
<div class="fmp-container-fluid fmp-wrapper fmp-archive" data-desktop-col="2" data-tab-col="1" data-mobile-col="1">
	<?php
	$html     = null;
	$settings = get_option( TLPFoodMenu()->options['settings'] );
	$colClass = "fmp-col-lg-6 fmp-col-md-6 fmp-col-sm-12 fmp-col-xs-12 fmp-grid-item";
	if ( have_posts() ) {
		$html  .= '<div class="fmp-row fmp-grid-by-cat-free">';
		$html .= '<h2 class="fmp-category-title">'.single_cat_title( "", false ).'</h2>';
		$count = 0;
		while ( have_posts() ) : the_post();
			$html .= '<div class="' . esc_attr( $colClass ) . '">';
                $html .= '<div class="fmp-food-item">';
                    $html .= '<div class="fmp-image-wrap"><a href="' . get_permalink() . '" title="' . get_the_title() . '">';
                    if ( has_post_thumbnail() ) {
                        $html .= get_the_post_thumbnail( get_the_ID(), 'medium' );
                    } else {
                        $html .= "<img src='" . TLPFoodMenu()->assetsUrl . 'images/demo-100x100.png' . "' alt='" . get_the_title() . "' />";
                    }
                    $html   .= '</a></div>';
                    $html   .= '<div class="fmp-content-wrap">';
                        $html   .= '<div class="fmp-title">';
                            $html   .= '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
                            $gTotal = TLPFoodMenu()->getPriceWithLabel(get_the_ID());
                            $html   .= '<span class="price">' . $gTotal . '</span>';
                        $html   .= '</div>';
                        $html   .= '<p>' . TLPFoodMenu()->string_limit_words( get_the_content(), 5 ) . '</p>';
                    $html .= '</div>';
                $html .= '</div>';
			$html .= '</div>';
		endwhile;
		$html .= '</div>';
	} else {
		$html .= "<p>" . __( 'No food found.', 'tlp-food-menu' ) . "</p>";
	}
	echo $html;
	?>
</div>
<?php get_footer(); ?>
