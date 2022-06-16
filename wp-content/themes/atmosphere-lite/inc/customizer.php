<?php
/**
 * Atmosphere Lite Theme Customizer
 *
 * @package Atmosphere Lite
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function atmosphere_lite_customize_register( $wp_customize ) {
	
function atmosphere_lite_sanitize_checkbox( $checked ) {
	// Boolean check.
	return ( ( isset( $checked ) && true == $checked ) ? true : false );
}
	
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		
	$wp_customize->add_setting('color_scheme', array(
		'default' => '#a7753a',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'color_scheme',array(
			'label' => __('Color Scheme','atmosphere-lite'),
			'description'	=> __('Select color from here.','atmosphere-lite'),
			'section' => 'colors',
			'settings' => 'color_scheme'
		))
	);
	
	$wp_customize->add_setting('header_color', array(
		'default' => '#000000',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'header_color',array(
			'label' => __('Header Backround Color','atmosphere-lite'),
			'description'	=> __('Select color from here.','atmosphere-lite'),
			'section' => 'colors',
			'settings' => 'header_color'
		))
	);
	
	// Slider Section Start		
	$wp_customize->add_section(
        'slider_section',
        array(
            'title' => __('Slider Settings', 'atmosphere-lite'),
            'priority' => null,
			'description'	=> __('Recommended image size (1520x814). Slider will be visible only when you select static front page.','atmosphere-lite'),	
        )
    );
	
	$wp_customize->add_setting('page-setting7',array(
			'default' => '0',
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting7',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide one:','atmosphere-lite'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('page-setting8',array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting8',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide two:','atmosphere-lite'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('page-setting9',array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting9',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide three:','atmosphere-lite'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('slide_year',array(
			'default' => __('1994','atmosphere-lite'),
			'sanitize_callback' => 'sanitize_text_field',
	));	 

	$wp_customize->add_control( 'slide_year', array(
		   'settings' => 'slide_year',
    	   'section'   => 'slider_section',
    	   'label'     => __('Add year.','atmosphere-lite'),
    	   'type'      => 'text'
     ));
	
	$wp_customize->add_setting('slide_text',array(
			'default' => __('Read More','atmosphere-lite'),
			'sanitize_callback' => 'sanitize_text_field',
	));	 

	$wp_customize->add_control( 'slide_text', array(
		   'settings' => 'slide_text',
    	   'section'   => 'slider_section',
    	   'label'     => __('Add slider button text','atmosphere-lite'),
    	   'type'      => 'text'
     ));
	
	
	$wp_customize->add_setting('hide_slider',array(
			'default' => true,
			'sanitize_callback' => 'atmosphere_lite_sanitize_checkbox',
			'capability' => 'edit_theme_options',
	));	 

	$wp_customize->add_control( 'hide_slider', array(
		   'settings' => 'hide_slider',
    	   'section'   => 'slider_section',
    	   'label'     => __('Check this to hide slider','atmosphere-lite'),
    	   'type'      => 'checkbox'
     ));	
	
	// Slider Section End
	
	// Homepage Section Start		
	$wp_customize->add_section(
        'homepage_section',
        array(
            'title' => __('Who We Are', 'atmosphere-lite'),
            'priority' => null,
			'description'	=> __('Select page for Who We Are section. This section will be visible only when you select static front page and uncheck the below checkbox.','atmosphere-lite'),	
        )
    );
	
	$wp_customize->add_setting('section_title',array(
			'default' => 'We Are Atmosphere Lite',
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('section_title',array(
			'type'	=> 'text',
			'label'	=> __('Add section title','atmosphere-lite'),
			'section'	=> 'homepage_section'
	));	
	
	$wp_customize->add_setting('page-setting1',array(
			'default' => '0',
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting1',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for  this section.','atmosphere-lite'),
			'section'	=> 'homepage_section'
	));	
	
	$wp_customize->add_setting('hide_boxes',array(
			'default' => true,
			'sanitize_callback' => 'atmosphere_lite_sanitize_checkbox',
			'capability' => 'edit_theme_options',
	));	 

	$wp_customize->add_control( 'hide_boxes', array(
		   'settings' => 'hide_boxes',
    	   'section'   => 'homepage_section',
    	   'label'     => __('Check this to hide this section','atmosphere-lite'),
    	   'type'      => 'checkbox'
     ));
		
	
}
	
	
add_action( 'customize_register', 'atmosphere_lite_customize_register' );	

function atmosphere_lite_css(){
		?>
        <?php 
	   		$headbg = get_theme_mod('header_color',true); 
			list($r,$g,$b) = sscanf($headbg,'#%02x%02x%02x');
			
			echo "<style>";
				echo ".header, .sitenav ul li:hover > ul, .header_top.headcontact{background-color:rgba(".esc_html($r).",".esc_html($g).",".esc_html($b).",0.7)}";
			echo "</style>";
		?>

        <style>
				a, 
				.tm_client strong,
				.postmeta a:hover,
				#sidebar ul li a:hover,
				.blog-post h3.entry-title,
				a.morebutton{
					color:<?php echo esc_html(get_theme_mod('color_scheme','#a7753a')); ?>;
				}
				.header-top,
				a.blog-more:hover,
				.nav-links .current, 
				.nav-links a:hover,
				#commentform input#submit,
				input.search-submit,
				.nivo-controlNav a.active,
				.blog-date .date,
				a.read-more,
				#sidebar .search-form input.search-submit{
					background-color:<?php echo esc_html(get_theme_mod('color_scheme','#a7753a')); ?>;
				}
				h2.section-title{
					border-bottom:2px solid <?php echo esc_html(get_theme_mod('color_scheme','#a7753a')); ?>;
				}
				a.morebutton{
					border-color:<?php echo esc_html(get_theme_mod('color_scheme','#a7753a')); ?>;
				}
		</style>
       	<?php }
add_action('wp_head','atmosphere_lite_css');

function atmosphere_lite_customize_preview_js() {
	wp_enqueue_script( 'atmosphere-lite-customize-preview', get_template_directory_uri() . '/js/customize-preview.js', array( 'customize-preview' ), '20141216', true );
}
add_action( 'customize_preview_init', 'atmosphere_lite_customize_preview_js' );