<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Atmosphere Lite
 */
?>

</div><!-- main-container -->

<div class="copyright-wrapper">
                <div class="copyright">
                    	<p>&copy; <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php esc_html(bloginfo( 'name' )); ?></a>  <?php echo date_i18n( __( 'Y', 'atmosphere-lite' ) ); ?>. <?php _e('Powered by WordPress','atmosphere-lite'); ?></p>               
                </div><!-- copyright --><div class="clear"></div>           
        </div>
    </div>        
<?php wp_footer(); ?>

</body>
</html>