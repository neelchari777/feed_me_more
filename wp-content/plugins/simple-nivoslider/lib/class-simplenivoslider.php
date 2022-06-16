<?php
/**
 * Simple NivoSlider
 *
 * @package    Simple NivoSlider
 * @subpackage SimpleNivoSlider Main Functions
/*
	Copyright (c) 2014- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

$simplenivoslider = new SimpleNivoSlider();

/** ==================================================
 * Main Functions
 */
class SimpleNivoSlider {

	/** ==================================================
	 * Count
	 *
	 * @var $simplenivoslider_count  simplenivoslider_count.
	 */
	private $simplenivoslider_count;

	/** ==================================================
	 * Atts
	 *
	 * @var $simplenivoslider_atts  simplenivoslider_atts.
	 */
	private $simplenivoslider_atts;

	/** ==================================================
	 * Construct
	 *
	 * @since 5.04
	 */
	public function __construct() {

		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'add_title_to_attachment_image' ), 12, 2 );
		add_shortcode( 'simplenivoslider', array( $this, 'simplenivoslider_func' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_scripts' ) );
		add_action( 'wp_footer', array( $this, 'load_localize_scripts_styles' ) );

		if ( get_option( 'simplenivoslider_gallery' ) ) {
			if ( 'body_open' === get_option( 'simplenivoslider_insert_position' ) ) {
				add_action( 'wp_body_open', array( $this, 'insert_body_open' ) );
			} else {
				add_filter( 'the_content', array( $this, 'insert_post' ) );
			}
		}

	}

	/** ==================================================
	 * Add image tag
	 *
	 * @param string $content  content.
	 * @param int    $simplenivoslider_id  simplenivoslider_id.
	 * @param array  $atts  atts.
	 * @return string $content
	 * @since 1.00
	 */
	private function add_img_tag( $content, $simplenivoslider_id, $atts ) {

		remove_shortcode( 'gallery', 'gallery_shortcode' );
		add_shortcode( 'gallery', array( $this, 'simplenivoslider_gallery_shortcode' ) );

		$gallery_code = null;
		$pattern_gallery = '/\[' . preg_quote( 'gallery ' ) . '[^\]]*\]/im';
		if ( ! empty( $content ) && preg_match( $pattern_gallery, $content ) ) {
			preg_match_all( $pattern_gallery, $content, $retgallery );
			foreach ( $retgallery as $ret => $gals ) {
				foreach ( $gals as $gal ) {
					$gallery_code = do_shortcode( $gal );
					$content = str_replace( $gal, $gallery_code, $content );
				}
			}
		}
		remove_shortcode( 'gallery', array( $this, 'simplenivoslider_gallery_shortcode' ) );
		add_shortcode( 'gallery', 'gallery_shortcode' );

		$allowed_html = array(
			'img' => array(),
		);
		wp_kses( $content, $allowed_html );

		if ( preg_match_all( '/<img(.+?)>/mis', $content, $result ) !== false ) {

			$mimes = get_allowed_mime_types();
			$ext_types = wp_get_ext_types();
			$ext_image_types = $ext_types['image'];
			$post_mime_type = array();
			foreach ( $mimes as $type => $mime ) {
				$types = explode( '|', $type );
				foreach ( $types as $value ) {
					if ( in_array( $value, $ext_image_types ) ) {
						$post_mime_type[] = $mime;
					}
				}
			}

			$args = array(
				'post_type'      => 'attachment',
				'post_status'    => 'any',
				'post_mime_type' => $post_mime_type,
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			);
			$attachments = get_posts( $args );

			if ( 0 < count( $result[0] ) ) {
				$content = implode( "\n", $result[0] );
				foreach ( $result[1] as $value ) {
					preg_match( '/src=\"(.[^\"]*)\"/', $value, $src );
					$explode = explode( '/', $src[1] );
					$file_name = $explode[ count( $explode ) - 1 ];
					$title_name = preg_replace( '/(.+)(\.[^.]+$)/', '$1', $file_name );
					$title_name = preg_replace( '(-[0-9]*x[0-9]*)', '', $title_name );
					$image_thumb = null;
					foreach ( $attachments as $attachment ) {
						if ( strpos( wp_get_attachment_url( $attachment->ID ), $title_name ) ) {
							$title_name = $attachment->post_title;
							$image_thumb = wp_get_attachment_image_src( $attachment->ID, 'thumbnail', false );
							if ( ! strpos( $value, 'title=' ) ) {
								$title_name = ' title="' . $title_name . '" ';
								$content = str_replace( $value, $title_name . $value, $content );
							}
							if ( ! strpos( $value, 'data-thumb=' ) && $atts['controlnavthumbs'] ) {
								$thumb_data = ' data-thumb="' . $image_thumb[0] . '" ';
								$content = str_replace( $value, $thumb_data . $value, $content );
							}
						}
					}
				}
			}
		}

		$content = '<div class="slider-wrapper theme-' . $atts['theme'] . '"><div id="simplenivoslider-' . $simplenivoslider_id . '" class="nivoSlider">' . "\n" . $content . "\n" . '</div></div>' . "\n";

		return $content;

	}

	/** ==================================================
	 * Add title to attachment image
	 *
	 * @param array  $attr  attr.
	 * @param object $attachment  attachment.
	 * @return array $attr
	 * @since 1.00
	 */
	public function add_title_to_attachment_image( $attr, $attachment ) {

		$attr['title'] = esc_attr( $attachment->post_title );

		return $attr;

	}

	/**
	 * The Gallery shortcode.
	 *
	 * This implements the functionality of the Gallery Shortcode for displaying
	 * WordPress images on a post.
	 *
	 * @since 2.5.0
	 *
	 * @param array $attr {
	 *     Attributes of the gallery shortcode.
	 *
	 *     @type string $order      Order of the images in the gallery. Default 'ASC'. Accepts 'ASC', 'DESC'.
	 *     @type string $orderby    The field to use when ordering the images. Default 'menu_order ID'.
	 *                              Accepts any valid SQL ORDERBY statement.
	 *     @type int    $id         Post ID.
	 *     @type string $itemtag    HTML tag to use for each image in the gallery.
	 *                              Default 'dl', or 'figure' when the theme registers HTML5 gallery support.
	 *     @type string $icontag    HTML tag to use for each image's icon.
	 *                              Default 'dt', or 'div' when the theme registers HTML5 gallery support.
	 *     @type string $captiontag HTML tag to use for each image's caption.
	 *                              Default 'dd', or 'figcaption' when the theme registers HTML5 gallery support.
	 *     @type int    $columns    Number of columns of images to display. Default 3.
	 *     @type string $size       Size of the images to display. Default 'thumbnail'.
	 *     @type string $ids        A comma-separated list of IDs of attachments to display. Default empty.
	 *     @type string $include    A comma-separated list of IDs of attachments to include. Default empty.
	 *     @type string $exclude    A comma-separated list of IDs of attachments to exclude. Default empty.
	 *     @type string $link       What to link each image to. Default empty (links to the attachment page).
	 *                              Accepts 'file', 'none'.
	 * }
	 * @return string HTML content to display gallery.
	 */
	public function simplenivoslider_gallery_shortcode( $attr ) {

		$post = get_post();

		static $instance = 0;
		$instance++;

		if ( ! empty( $attr['ids'] ) ) {
			/* 'ids' is explicitly ordered, unless you specify otherwise. */
			if ( empty( $attr['orderby'] ) ) {
				$attr['orderby'] = 'post__in';
			}
			$attr['include'] = $attr['ids'];
		}

		/**
		 * Filter the default gallery shortcode output.
		 *
		 * If the filtered output isn't empty, it will be used instead of generating
		 * the default gallery template.
		 *
		 * @since 2.5.0
		 * @since 4.2.0 The `$instance` parameter was added.
		 *
		 * @see gallery_shortcode()
		 *
		 * @param string $output The gallery output. Default empty.
		 * @param array  $attr   Attributes of the gallery shortcode.
		 */
		$output = apply_filters( 'post_gallery', '', $attr, $instance );
		if ( '' != $output ) {
			return $output;
		}

		$html5 = current_theme_supports( 'html5', 'gallery' );
		$atts = shortcode_atts(
			array(
				'order'      => 'ASC',
				'orderby'    => 'menu_order ID',
				'id'         => $post ? $post->ID : 0,
				'itemtag'    => $html5 ? 'figure' : 'dl',
				'icontag'    => $html5 ? 'div' : 'dt',
				'captiontag' => $html5 ? 'figcaption' : 'dd',
				'columns'    => 3,
				'size'       => 'full',
				'include'    => '',
				'exclude'    => '',
				'link'       => 'none',
			),
			$attr,
			'gallery'
		);

		$id = intval( $atts['id'] );

		if ( ! empty( $atts['include'] ) ) {
			$_attachments = get_posts(
				array(
					'include' => $atts['include'],
					'post_status' => 'inherit',
					'post_type' => 'attachment',
					'post_mime_type' => 'image',
					'order' => $atts['order'],
					'orderby' => $atts['orderby'],
				)
			);

			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[ $val->ID ] = $_attachments[ $key ];
			}
		} elseif ( ! empty( $atts['exclude'] ) ) {
			$attachments = get_children(
				array(
					'post_parent' => $id,
					'exclude' => $atts['exclude'],
					'post_status' => 'inherit',
					'post_type' => 'attachment',
					'post_mime_type' => 'image',
					'order' => $atts['order'],
					'orderby' => $atts['orderby'],
				)
			);
		} else {
			$attachments = get_children(
				array(
					'post_parent' => $id,
					'post_status' => 'inherit',
					'post_type' => 'attachment',
					'post_mime_type' => 'image',
					'order' => $atts['order'],
					'orderby' => $atts['orderby'],
				)
			);
		}

		if ( empty( $attachments ) ) {
			return '';
		}

		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment ) {
				$output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
			}
			return $output;
		}

		$itemtag = tag_escape( $atts['itemtag'] );
		$captiontag = tag_escape( $atts['captiontag'] );
		$icontag = tag_escape( $atts['icontag'] );
		$valid_tags = wp_kses_allowed_html( 'post' );
		if ( ! isset( $valid_tags[ $itemtag ] ) ) {
			$itemtag = 'dl';
		}
		if ( ! isset( $valid_tags[ $captiontag ] ) ) {
			$captiontag = 'dd';
		}
		if ( ! isset( $valid_tags[ $icontag ] ) ) {
			$icontag = 'dt';
		}

		$columns = intval( $atts['columns'] );
		$itemwidth = $columns > 0 ? floor( 100 / $columns ) : 100;
		$float = is_rtl() ? 'right' : 'left';

		$selector = "gallery-{$instance}";

		$gallery_style = '';

		/**
		 * Filters whether to print default gallery styles.
		 *
		 * @since 3.1.0
		 *
		 * @param bool $print Whether to print default gallery styles.
		 *                    Defaults to false if the theme supports HTML5 galleries.
		 *                    Otherwise, defaults to true.
		 */
		if ( apply_filters( 'use_default_gallery_style', ! $html5 ) ) {
			$gallery_style = "
			<style type='text/css'>
				#{$selector} {
					margin: auto;
				}
				#{$selector} .gallery-item {
					float: {$float};
					margin-top: 10px;
					text-align: center;
					width: {$itemwidth}%;
				}
				#{$selector} img {
					border: 2px solid #cfcfcf;
				}
				#{$selector} .gallery-caption {
					margin-left: 0;
				}
				/* see gallery_shortcode() in wp-includes/media.php */
			</style>\n\t\t";
		}

		$size_class = sanitize_html_class( $atts['size'] );
		$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";

		/**
		 * Filters the default gallery shortcode CSS styles.
		 *
		 * @since 2.5.0
		 *
		 * @param string $gallery_style Default CSS styles and opening HTML div container
		 *                              for the gallery shortcode output.
		 */
		$output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );

		$i = 0;
		foreach ( $attachments as $id => $attachment ) {

			$attr = ( trim( $attachment->post_excerpt ) ) ? array( 'aria-describedby' => "$selector-$id" ) : '';
			if ( ! empty( $atts['link'] ) && 'file' === $atts['link'] ) {
				$image_output = wp_get_attachment_link( $id, $atts['size'], false, false, false, $attr );
			} elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {
				$image_output = wp_get_attachment_image( $id, $atts['size'], false, $attr );
			} else {
				$image_output = wp_get_attachment_link( $id, $atts['size'], true, false, false, $attr );
			}
			$image_meta  = wp_get_attachment_metadata( $id );

			$orientation = '';
			if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
				$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
			}
			$output .= "<{$itemtag} class='gallery-item'>";
			$output .= "
				<{$icontag} class='gallery-icon {$orientation}'>
					$image_output
				</{$icontag}>";
			if ( $captiontag && trim( $attachment->post_excerpt ) ) {
				$output .= "
					<{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
					" . wptexturize( $attachment->post_excerpt ) . "
					</{$captiontag}>";
			}
			$output .= "</{$itemtag}>";
			++$i;
			if ( ! $html5 && 0 < $columns && 0 == $i % $columns ) {
				$output .= '<br style="clear: both" />';
			}
		}

		if ( ! $html5 && 0 < $columns && 0 !== $i % $columns ) {
			$output .= "
				<br style='clear: both' />";
		}

		$output .= "
			</div>\n";

		return $output;

	}

	/** ==================================================
	 * Load Script
	 *
	 * @since 4.02
	 */
	public function load_frontend_scripts() {

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'nivo-slider', plugin_dir_url( __DIR__ ) . 'nivo-slider/jquery.nivo.slider.pack.js', null, '3.2', false );

	}

	/** ==================================================
	 * Load Localize Script and Style
	 *
	 * @since 5.00
	 */
	public function load_localize_scripts_styles() {

		if ( empty( $this->simplenivoslider_atts ) ) {
			return;
		}

		$localize_nivo_settings = array();
		wp_enqueue_script( 'nivo-slider-jquery', plugin_dir_url( __DIR__ ) . 'js/jquery.simplenivoslider.js', array( 'jquery' ), '1.00', false );
		foreach ( $this->simplenivoslider_atts as $key => $value ) {
			/* Script */
			$localize_nivo_settings = array_merge( $localize_nivo_settings, $value );
			/* Style */
			$theme = $value[ 'theme' . $key ];
			$thumbswidth = $value[ 'thumbswidth' . $key ];
			$simplenivoslider_id = $value[ 'id' . $key ];
			wp_enqueue_style( 'nivo-slider-themes' . $simplenivoslider_id, plugin_dir_url( __DIR__ ) . 'nivo-slider/themes/' . $theme . '/' . $theme . '.css', array(), '1.00' );
			wp_enqueue_style( 'nivo-slider' . $simplenivoslider_id, plugin_dir_url( __DIR__ ) . 'nivo-slider/nivo-slider.css', array(), '1.00' );

			$css = '.theme-' . $theme . ' .nivo-controlNav.nivo-thumbs-enabled img{ display: block; width: ' . $thumbswidth . 'px; height: auto; }';
			wp_add_inline_style( 'nivo-slider' . $simplenivoslider_id, $css );

		}
		/* Script */
		$maxcount = array( 'maxcount' => $this->simplenivoslider_count );
		$localize_nivo_settings = array_merge( $localize_nivo_settings, $maxcount );
		wp_localize_script( 'nivo-slider-jquery', 'nivo_settings', $localize_nivo_settings );

	}

	/** ==================================================
	 * Short code
	 *
	 * @param array  $atts  atts.
	 * @param string $content  content.
	 * @return string $content
	 * @since 4.00
	 */
	public function simplenivoslider_func( $atts, $content = null ) {

		$a = shortcode_atts(
			array(
				'theme' => '',
				'effect' => '',
				'slices' => '',
				'boxcols' => '',
				'boxrows' => '',
				'animspeed' => '',
				'pausetime' => '',
				'startslide' => '',
				'directionnav' => '',
				'controlnav' => '',
				'controlnavthumbs' => '',
				'thumbswidth' => '',
				'pauseonhover' => '',
				'manualadvance' => '',
				'prevtext' => '',
				'nexttext' => '',
				'randomstart' => '',
			),
			$atts
		);

		$settings_tbl = get_option( 'simplenivoslider_settings' );

		foreach ( $settings_tbl as $key => $value ) {
			$shortcodekey = strtolower( $key );
			if ( empty( $a[ $shortcodekey ] ) ) {
				$a[ $shortcodekey ] = $value;
			} else {
				if ( strtolower( $a[ $shortcodekey ] ) === 'false' ) {
					$a[ $shortcodekey ] = null;
				}
			}
		}

		++$this->simplenivoslider_count;
		$simplenivoslider_id = get_the_ID() . '-' . $this->simplenivoslider_count;

		$content = $this->add_img_tag( $content, $simplenivoslider_id, $a );

		$new_atts = array();
		foreach ( $a as $key => $value ) {
			$new_atts[ $key . $this->simplenivoslider_count ] = $value;
		}
		$id_count_tbl = array( 'id' . $this->simplenivoslider_count => $simplenivoslider_id );
		$this->simplenivoslider_atts[ $this->simplenivoslider_count ] = array_merge( $new_atts, $id_count_tbl );

		return do_shortcode( $content );

	}

	/** ==================================================
	 * Contents filter
	 *
	 * @param string $content  content.
	 * @return string $content
	 * @since 5.11
	 */
	public function insert_post( $content ) {

		$custom_content = do_shortcode( '[simplenivoslider]' . get_option( 'simplenivoslider_gallery' ) . '[/simplenivoslider]' );
		switch ( get_option( 'simplenivoslider_insert_position' ) ) {
			case 'before':
				$content = $custom_content . $content;
				break;
			case 'after':
				$content .= $custom_content;
				break;
		}

		return $content;

	}

	/** ==================================================
	 * Body open action
	 *
	 * @since 5.11
	 */
	public function insert_body_open() {

		echo do_shortcode( '[simplenivoslider]' . get_option( 'simplenivoslider_gallery' ) . '[/simplenivoslider]' );

	}

}


