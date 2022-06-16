 <?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Atmosphere Lite
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php if(is_singular() && pings_open()) { ?>
<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' )); ?>">
<?php } ?>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php
	//wp_body_open hook from WordPress 5.2
	if ( function_exists( 'wp_body_open' ) ) {
	    wp_body_open();
	} else {
		do_action( 'wp_body_open' );
	}
?>
<a class="skip-link screen-reader-text" href="#sitemain">
	<?php _e( 'Skip to content', 'atmosphere-lite' ); ?>
</a>
<?php if ( is_active_sidebar( 'headtopleft' ) || is_active_sidebar( 'headtopright' ) ) : ?>
<div class="header_top">
	<div class="container">
    	<div class="top-left">
        <?php dynamic_sidebar('headtopleft'); ?>
        </div>
        <div class="top-right">
        <?php dynamic_sidebar('headtopright'); ?>
        </div>
        <div class="clear"></div>
	</div><!--container-->
</div><!--header_top--> 
 <?php endif; ?>
<div class="header">
	<div class="container">
      <div class="logo">
        <?php atmosphere_lite_the_custom_logo(); ?>
			    <h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php esc_html(bloginfo( 'name' )); ?></a></h1>
					<?php $description = get_bloginfo( 'description', 'display' );
						if ( $description || is_customize_preview() ) : ?>
						<p><?php echo esc_html( $description); ?></p>
					<?php endif; ?>
    </div><!-- .logo -->                 
    
    
    <div class="toggle">
            <a class="toggleMenu" href="#"><?php _e('Menu','atmosphere-lite'); ?></a>
    	</div><!-- toggle -->    
    <div class="sitenav">                   
   	 	<?php wp_nav_menu( array('theme_location' => 'primary') ); ?> 
    </div><!--.sitenav -->        
 <div class="clear"></div>
</div><!-- .container -->
</div><!-- .header -->

<div class="innerbanner">                 
          <?php
			$header_image = get_header_image();
			
			if( is_single() || is_archive() || is_category() || is_author()|| is_search() || is_404() || is_home() ) { 
				if(!empty($header_image)){
					echo '<img src="'.esc_url( $header_image ).'" width="'.esc_attr(get_custom_header()->width).'" height="'.esc_attr(get_custom_header()->height).'" alt="" />';
				} else {
        			echo '<img src="'.esc_url(get_template_directory_uri().'/images/inner-banner.jpg').'" alt="">';
				}
			}
			elseif( has_post_thumbnail() ) {
				$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
				$thumbnailSrc = $src[0];
				echo '<img src="'.esc_url($thumbnailSrc).'" alt="">';
			} 
			elseif ( ! empty( $header_image ) ) {
				echo '<img src="'.esc_url( $header_image ).'" width="'.esc_attr(get_custom_header()->width).'" height="'.esc_attr(get_custom_header()->height).'" alt="" />';
            }	
			 ?>
            
        </div> 