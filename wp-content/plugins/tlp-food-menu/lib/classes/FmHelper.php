<?php
if ( ! class_exists( 'FmHelper' ) ) :

	class FmHelper {

		/**
		 * Nonce verify upon activity
		 *
		 * @return bool
		 */
		function verifyNonce() {
			$nonce     = isset( $_REQUEST[ $this->nonceId() ] ) ? $_REQUEST[ $this->nonceId() ] : null;
			$nonceText = TLPFoodMenu()->nonceText();
			if ( ! wp_verify_nonce( $nonce, $nonceText ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Generate nonce text
		 *
		 * @return string
		 */
		function nonceText() {
			return "fmp_nonce_secret";
		}

		/**
		 * Nonce Id generation
		 *
		 * @return string
		 */
		function nonceId() {
			return "fmp_nonce";
		}


		/**
		 * MetaField list for food Page
		 *
		 * @return array
		 */
		function singleFoodMetaFields() {
			return array_merge( TLPFoodMenu()->foodGeneralOptions(), TLPFoodMenu()->foodAdvancedOptions() );
		}

		/**
		 * MetaField list for food Page
		 *
		 * @return array
		 */
		function fmpAllSettingsFields() {
			$allSettings = array_merge(
                TLPFoodMenu()->generalSettings(),
                TLPFoodMenu()->detailPageSettings()
			);
			return apply_filters('rt_fm_setting_fields', $allSettings);
		}

		/**
		 * Generate MetaField Name list for shortCode Page
		 *
		 * @return array
		 */
		function fmpScMetaFields() {
			return array_merge(
                TLPFoodMenu()->scLayoutMetaFields(),
                TLPFoodMenu()->scFilterMetaFields(),
                TLPFoodMenu()->scItemFields(),
                TLPFoodMenu()->scStyleFields() );
		}


		/**
		 * This function will generate meta or setting field
		 *
		 * @param array $fields
		 *
		 * @return null|string
		 */
		function rtFieldGenerator( $fields = array() ) {
			$html = null;
			if ( is_array( $fields ) && ! empty( $fields ) ) {
				$fmField = new FmpField();
				foreach ( $fields as $fieldKey => $field ) {
					$html .= $fmField->Field( $fieldKey, $field );
				}
			}

			return $html;
		}

		/**
		 * Sanitize field value
		 *
		 * @param array $field
		 * @param null $value
		 *
		 * @return array|null
		 * @internal param $value
		 */
		function sanitize( $field = array(), $value = null ) {
			$newValue = null;
			if ( is_array( $field ) ) {
				$type = ( ! empty( $field['type'] ) ? $field['type'] : 'text' );
				if ( empty( $field['multiple'] ) ) {
					if ( $type == 'text' || $type == 'number' || $type == 'select' || $type == 'checkbox' || $type == 'radio' ) {
						$newValue = sanitize_text_field( $value );
					} else if ( $type == 'price' ) {
						$newValue = ( '' === $value ) ? '' : TLPFoodMenu()->format_decimal( $value );
					} else if ( $type == 'url' ) {
						$newValue = esc_url( $value );
					} else if ( $type == 'slug' ) {
						$newValue = sanitize_title_with_dashes( $value );
					} else if ( $type == 'textarea' ) {
						$newValue = wp_kses_post( $value );
					} else if ( $type == 'custom_css' ) {
						$newValue = esc_attr( $value );
					} else if ( $type == 'colorpicker' ) {
						$newValue = $this->sanitize_hex_color( $value );
					} else if ( $type == 'image_size' ) {
						$newValue = array();
						foreach ( $value as $k => $v ) {
							$newValue[ $k ] = esc_attr( $v );
						}
					} else if ( $type == 'style' ) {
						$newValue = array();
						foreach ( $value as $k => $v ) {
							if ( $k == 'color' ) {
								$newValue[ $k ] = $this->sanitize_hex_color( $v );
							} else {
								$newValue[ $k ] = $this->sanitize( array( 'type' => 'text' ), $v );
							}
						}
					} else {
						$newValue = sanitize_text_field( $value );
					}

				} else {
					$newValue = array();
					if ( ! empty( $value ) ) {
						if ( is_array( $value ) ) {
							foreach ( $value as $key => $val ) {
								if ( $type == 'style' && $key == 0 ) {
									if ( function_exists( 'sanitize_hex_color' ) ) {
										$newValue = sanitize_hex_color( $val );
									} else {
										$newValue[] = $this->sanitize_hex_color( $val );
									}
								} else {
									$newValue[] = sanitize_text_field( $val );
								}
							}
						} else {
							$newValue[] = sanitize_text_field( $value );
						}
					}
				}
			}

			return $newValue;
		}

		function sanitize_hex_color( $color ) {
			if ( function_exists( 'sanitize_hex_color' ) ) {
				return sanitize_hex_color( $color );
			} else {
				if ( '' === $color ) {
					return '';
				}

				// 3 or 6 hex digits, or the empty string.
				if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
					return $color;
				}
			}
		}

		/* Convert hexdec color string to rgb(a) string */
		function rtHex2rgba( $color, $opacity = .5 ) {

			$default = 'rgb(0,0,0)';

			//Return default if no color provided
			if ( empty( $color ) ) {
				return $default;
			}

			//Sanitize $color if "#" is provided
			if ( $color[0] == '#' ) {
				$color = substr( $color, 1 );
			}

			//Check if color has 6 or 3 characters and get values
			if ( strlen( $color ) == 6 ) {
				$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
			} elseif ( strlen( $color ) == 3 ) {
				$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
			} else {
				return $default;
			}

			//Convert hexadec to rgb
			$rgb = array_map( 'hexdec', $hex );

			//Check if opacity is set(rgba or rgb)
			if ( $opacity ) {
				if ( abs( $opacity ) > 1 ) {
					$opacity = 1.0;
				}
				$output = 'rgba(' . implode( ",", $rgb ) . ',' . $opacity . ')';
			} else {
				$output = 'rgb(' . implode( ",", $rgb ) . ')';
			}

			//Return rgb(a) color string
			return $output;
		}

		/**
		 *  Get all Category list for food-menu
		 *
		 * @return array
		 */
		function getAllFmpCategoryList() {
			global $post;
			$taxonomy = TLPFoodMenu()->taxonomies['category'];
			if ( $post ) {
				$source = get_post_meta( $post->ID, 'fmp_source', true );
                $source = ( $source && in_array( $source, array_keys( TLPFoodMenu()->scProductSource() ) ) ) ? $source : TLPFoodMenu()->post_type;
                if ( $source == 'product' && TLPFoodMenu()->isWcActive() ) {
                    $taxonomy = 'product_cat';
                }
			}
			$terms    = array();
			$termList = get_terms( $taxonomy, array( 'hide_empty' => 0 ) );
			if ( is_array( $termList ) && ! empty( $termList ) && empty( $termList['errors'] ) ) {
				foreach ( $termList as $term ) {
					$terms[ $term->term_id ] = $term->name;
				}
			}

			return $terms;
		}

		function placeholder_img_src() {
			return TLPFoodMenu()->assetsUrl . 'images/placeholder.png';
		}

		function get_image_sizes() {
			global $_wp_additional_image_sizes;

			$sizes = array();

			foreach ( get_intermediate_image_sizes() as $_size ) {
				if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
					$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
					$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
					$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
				} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
					$sizes[ $_size ] = array(
						'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
						'height' => $_wp_additional_image_sizes[ $_size ]['height'],
						'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
					);
				}
			}

			$imgSize = array();
			foreach ( $sizes as $key => $img ) {
				$imgSize[ $key ] = ucfirst( $key ) . " ({$img['width']}*{$img['height']})";
			}

			return apply_filters('fmp_image_size', $imgSize);
		}

		function getCurrencyList() {
			$currencyList = array();
			foreach ( TLPFoodMenu()->currency_list() as $key => $currency ) {
				$currencyList[ $key ] = $currency['name'] . " (" . $currency['symbol'] . ")";
			}

			return $currencyList;
		}

		function the_excerpt_max_charlength( $charLength ) {
			$excerpt = get_the_excerpt();
			$charLength ++;
			$html = null;
			if ( mb_strlen( $excerpt ) > $charLength ) {
				$subex   = mb_substr( $excerpt, 0, $charLength - 5 );
				$exwords = explode( ' ', $subex );
				$excut   = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
				if ( $excut < 0 ) {
					$html .= mb_substr( $subex, 0, $excut );
				} else {
					$html .= $subex;
				}
			} else {
				$html .= $excerpt;
			}

			return $html;
		}


		function t( $text ) {
			return __( $text, 'tlp-food-menu' );
		}


		function string_limit_words( $string, $word_limit ) {
			$words = explode( ' ', $string );

			return implode( ' ', array_slice( $words, 0, $word_limit ) );
		}


		function format_decimal( $number, $dp = false, $trim_zeros = false ) {
			$locale   = localeconv();
			$decimals = array(
				$locale['decimal_point'],
				$locale['mon_decimal_point']
			);

			if ( $dp !== false ) {
				$dp     = intval( $dp == "" ? $this->get_price_decimals() : $dp );
				$number = number_format( floatval( $number ), $dp, '.', '' );

				// DP is false - don't use number format, just return a string in our format
			}

			if ( $trim_zeros && strstr( $number, '.' ) ) {
				$number = rtrim( rtrim( $number, '0' ), '.' );
			}

			return $number;
		}

        function get_price_decimal_separator() {
            $settings  = get_option( TLPFoodMenu()->options['settings'] );
            $separator = ! empty( $settings['price_decimal_sep'] ) ? stripslashes( $settings['price_decimal_sep'] ) : null;

            return $separator ? $separator : '.';
        }

        function get_price_decimals() {
            $settings = get_option( TLPFoodMenu()->options['settings'] );

            return ( ! empty( $settings['price_num_decimals'] ) ? ( ansint( $settings['price_num_decimals'] ) > 0 ? ansint( $settings['price_num_decimals'] ) : 2 ) : 2 );
        }

		function strip_tags_content( $text, $limit = 0, $tags = '', $invert = false ) {

			preg_match_all( '/<(.+?)[\s]*\/?[\s]*>/si', trim( $tags ), $tags );
			$tags = array_unique( $tags[1] );

			if ( is_array( $tags ) AND count( $tags ) > 0 ) {
				if ( $invert == false ) {
					$text = preg_replace( '@<(?!(?:' . implode( '|', $tags ) . ')\b)(\w+)\b.*?>.*?</\1>@si', '',
						$text );
				} else {
					$text = preg_replace( '@<(' . implode( '|', $tags ) . ')\b.*?>.*?</\1>@si', '', $text );
				}
			} else if ( $invert == false ) {
				$text = preg_replace( '@<(\w+)\b.*?>.*?</\1>@si', '', $text );
			}
			if ( $limit > 0 && strlen( $text ) > $limit ) {
				$text = substr( $text, 0, $limit );
			}

			return $text;
		}

		/**
		 * Call the Image resize model for resize function
		 *
		 * @param            $url
		 * @param null $width
		 * @param null $height
		 * @param null $crop
		 * @param bool|true $single
		 * @param bool|false $upscale
		 *
		 * @return array|bool|string
		 * @throws FmpException
		 */
		function rtImageReSize( $url, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {
			$rtResize = new FmReSizer();

			return $rtResize->process( $url, $width, $height, $crop, $single, $upscale );
		}


		function getFeatureImage(
			$post_id = null,
			$fImgSize = 'medium',
			$defaultImgId = 0,
			$customImgSize = array()
		) {
			global $post;
			$img_class = "fmp-feature-img";
			$imgSrc    = $image = null;
			$cSize     = false;
			$post_id   = ( $post_id ? absint( $post_id ) : $post->ID );
			// $alt       = esc_url( get_the_title( $post_id ) );
			$thumb_id = get_post_thumbnail_id( $post_id );
			$alt      = trim( wp_strip_all_tags( get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ) );

			if ( empty( $alt ) ) {
				$alt = esc_html( get_the_title( $post_id ) );
			}

			$actual_dimension = wp_get_attachment_metadata( $thumb_id, true );

			if ( empty( $actual_dimension ) && $defaultImgId ) {
				$actual_dimension = wp_get_attachment_metadata( $defaultImgId, true );
			}

			$actual_w = $actual_dimension['width'];
			$actual_h = $actual_dimension['height'];

			if ( $fImgSize == 'fmp_custom' ) {
				$fImgSize = 'full';
				$cSize    = true;
			}
			if ( $thumb_id ) {
				$image  = wp_get_attachment_image( $thumb_id, $fImgSize, '', array( "class" => $img_class ) );
				$imageS = wp_get_attachment_image_src( $thumb_id, $fImgSize );
				$imgSrc = $imageS[0];
			} else if ( $defaultImgId ) {
				$image  = wp_get_attachment_image( $defaultImgId, $fImgSize, '', array( "class" => $img_class ) );
				$imageS = wp_get_attachment_image_src( $defaultImgId, $fImgSize );
				$imgSrc = $imageS[0];
			} else {
				$imgSrc = esc_url( TLPFoodMenu()->placeholder_img_src() );
				$image  = "<img alt='{$alt}' class='{$img_class}' src='{$imgSrc}' />";
			}

			if ( $imgSrc && $cSize ) {
				$w = ( ! empty( $customImgSize['width'] ) ? absint( $customImgSize['width'] ) : null );
				$h = ( ! empty( $customImgSize['height'] ) ? absint( $customImgSize['height'] ) : null );
				$c = ( ! empty( $customImgSize['crop'] ) && $customImgSize['crop'] == 'soft' ? false : true );

				if ( $w && $h ) {
					if( $w >= $actual_w || $h >= $actual_h ) {
						$w = 150;
						$h = 150;
						$c = true;
					}

					$imgSrc = esc_url( TLPFoodMenu()->rtImageReSize( $imgSrc, $w, $h, $c ) );
					$image  = "<img alt='{$alt}' width='{$w}' height='{$h}' class='{$img_class}' src='{$imgSrc}' />";
				}
			}

			return $image;
		}

		function getAttachedImage(
			$attach_id,
			$fImgSize = 'medium',
			$customImgSize = array()
		) {
			$imgSrc = $image = null;
			$cSize  = false;
			if ( $fImgSize == 'fmp_custom' ) {
				$fImgSize = 'full';
				$cSize    = true;
			}
			if ( $attach_id ) {
				$image  = wp_get_attachment_image( $attach_id, $fImgSize );
				$imageS = wp_get_attachment_image_src( $attach_id, $fImgSize );
				// $imgSrc = $imageS[0];
				$imgSrc = ! empty( $imageS[0] ) ? $imageS[0] : '';
			} else {
				$imgSrc = TLPFoodMenu()->placeholder_img_src();
				$image  = "<img src='{$imgSrc}' />";
			}

			if ( $imgSrc && $cSize ) {
				$w = ( ! empty( $customImgSize['width'] ) ? absint( $customImgSize['width'] ) : null );
				$h = ( ! empty( $customImgSize['height'] ) ? absint( $customImgSize['height'] ) : null );
				$c = ( ! empty( $customImgSize['crop'] ) && $customImgSize['crop'] == 'soft' ? false : true );
				if ( $w && $h ) {
					$imgSrc = TLPFoodMenu()->rtImageReSize( $imgSrc, $w, $h, $c );
					$image  = "<img src='{$imgSrc}' />";
				}
			}

			return $image;
		}

		/**
		 * Returns the product categories.
		 *
		 * @param        $id
		 * @param string $sep (default: ', ')
		 * @param string $before (default: '')
		 * @param string $after (default: '')
		 *
		 * @return string
		 */
		public function get_categories( $id, $sep = ', ', $before = '', $after = '' ) {
			return get_the_term_list( $id, TLPFoodMenu()->taxonomies['category'], $before, $sep, $after );
		}

		function get_shortCode_list() {

			$scList = null;
			$scQ    = get_posts( array(
				'post_type'      => TLPFoodMenu()->shortCodePT,
				'order_by'       => 'title',
				'order'          => 'ASC',
				'post_status'    => 'publish',
				'posts_per_page' => - 1
			) );
			if ( ! empty( $scQ ) ) {
				foreach ( $scQ as $sc ) {
					$scList[ $sc->ID ] = $sc->post_title;
				}
			}

			return $scList;
		}

		// Promotion Product

        function get_product_list_html( $products = array() ) {
            $html = null;
            if ( ! empty( $products ) ) {
                foreach ( $products as $type => $list ) {
                    if ( ! empty( $list ) ) {
                        $htmlProducts = null;
                        foreach ( $list as $product ) {
                            $image_url       = isset( $product['image_url'] ) ? $product['image_url'] : null;
                            $image_thumb_url = isset( $product['image_thumb_url'] ) ? $product['image_thumb_url'] : null;
                            $image_thumb_url = $image_thumb_url ? $image_thumb_url : $image_url;
                            $price           = isset( $product['price'] ) ? $product['price'] : null;
                            $title           = isset( $product['title'] ) ? $product['title'] : null;
                            $url             = isset( $product['url'] ) ? $product['url'] : null;
                            $buy_url         = isset( $product['buy_url'] ) ? $product['buy_url'] : null;
                            $buy_url         = $buy_url ? $buy_url : $url;
                            $doc_url         = isset( $product['doc_url'] ) ? $product['doc_url'] : null;
                            $demo_url        = isset( $product['demo_url'] ) ? $product['demo_url'] : null;
                            $feature_list    = null;
                            $info_html       = sprintf( '<div class="rt-product-info">%s%s%s</div>',
                                $title ? sprintf( "<h3 class='rt-product-title'><a href='%s' target='_blank'>%s%s</a></h3>", esc_url( $url ), $title, $price ? " ($" . $price . ")" : null ) : null,
                                $feature_list,
                                $buy_url || $demo_url || $doc_url ?
                                    sprintf(
                                        '<div class="rt-product-action">%s%s%s</div>',
                                        $buy_url ? sprintf( '<a class="rt-admin-btn button-primary" href="%s" target="_blank">%s</a>', esc_url( $buy_url ), esc_html__( 'Buy', 'tlp-food-menu' ) ) : null,
                                        $demo_url ? sprintf( '<a class="rt-admin-btn" href="%s" target="_blank">%s</a>', esc_url( $demo_url ), esc_html__( 'Demo', 'tlp-food-menu' ) ) : null,
                                        $doc_url ? sprintf( '<a class="rt-admin-btn" href="%s" target="_blank">%s</a>', esc_url( $doc_url ), esc_html__( 'Documentation', 'tlp-food-menu' ) ) : null
                                    )
                                    : null
                            );

                            $htmlProducts .= sprintf(
                                '<div class="rt-product">%s%s</div>',
                                $image_thumb_url ? sprintf(
                                    '<div class="rt-media"><img src="%s" alt="%s" /></div>',
                                    esc_url( $image_thumb_url ),
                                    esc_html( $title )
                                ) : null,
                                $info_html
                            );

                        }
                        $html .= sprintf( '<div class="rt-product-list">%s</div>', $htmlProducts );

                    }
                }
            }

            return $html;
        }

	}
endif;
