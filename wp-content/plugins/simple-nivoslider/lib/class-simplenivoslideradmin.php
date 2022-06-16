<?php
/**
 * Simple NivoSlider
 *
 * @package    Simple NivoSlider
 * @subpackage SimpleNivoSliderAdmin Management screen
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

$simplenivoslideradmin = new SimpleNivoSliderAdmin();

/** ==================================================
 * Management screen
 *
 * @since 1.00
 */
class SimpleNivoSliderAdmin {

	/** ==================================================
	 * Construct
	 *
	 * @since 5.04
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'register_settings' ) );

		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_custom_wp_admin_style' ) );
		add_filter( 'plugin_action_links', array( $this, 'settings_link' ), 10, 2 );
		add_action( 'admin_print_footer_scripts', array( $this, 'simplenivoslider_add_quicktags' ) );

	}

	/** ==================================================
	 * Add a "Settings" link to the plugins page
	 *
	 * @param  array  $links  links array.
	 * @param  string $file   file.
	 * @return array  $links  links array.
	 * @since 1.00
	 */
	public function settings_link( $links, $file ) {
		static $this_plugin;
		if ( empty( $this_plugin ) ) {
			$this_plugin = 'simple-nivoslider/simplenivoslider.php';
		}
		if ( $file == $this_plugin ) {
			$links[] = '<a href="' . admin_url( 'options-general.php?page=simplenivoslider' ) . '">' . __( 'Settings' ) . '</a>';
		}
			return $links;
	}

	/** ==================================================
	 * Settings page
	 *
	 * @since 1.00
	 */
	public function plugin_menu() {
		add_options_page( 'Simple NivoSlider Options', 'Simple NivoSlider', 'manage_options', 'simplenivoslider', array( $this, 'plugin_options' ) );
	}

	/** ==================================================
	 * Add Css and Script
	 *
	 * @since 2.00
	 */
	public function load_custom_wp_admin_style() {
		if ( $this->is_my_plugin_screen() ) {
			wp_enqueue_style( 'simple-nivoslider', plugin_dir_url( __DIR__ ) . 'css/simple-nivoslider.css', array(), '1.00' );
			wp_enqueue_media();
		}
	}

	/** ==================================================
	 * For only admin style
	 *
	 * @since 3.30
	 */
	private function is_my_plugin_screen() {
		$screen = get_current_screen();
		if ( is_object( $screen ) && 'settings_page_simplenivoslider' == $screen->id ) {
			return true;
		} else {
			return false;
		}
	}

	/** ==================================================
	 * Settings page
	 *
	 * @since 1.00
	 */
	public function plugin_options() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}

		$this->options_updated();

		$scriptname = admin_url( 'options-general.php?page=simplenivoslider' );
		$simplenivoslider_settings = get_option( 'simplenivoslider_settings' );

		?>

		<div class="wrap">
		<h2>Simple NivoSlider</h2>

			<details>
			<summary><strong><?php esc_html_e( 'Various links of this plugin', 'simple-nivoslider' ); ?></strong></summary>
			<?php $this->credit(); ?>
			</details>

			<details style="margin-bottom: 5px;">
			<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><strong>NivoSlider <?php esc_html_e( 'Settings' ); ?>(<a href="https://github.com/Codeinwp/Nivo-Slider-jQuery" target="_blank" rel="noopener noreferrer" style="text-decoration: none; word-break: break-all;"><?php esc_html_e( 'Website' ); ?></a>)</strong></summary>
			<h4><?php esc_html_e( 'Shortcode attributes take precedence. If the attribute is omitted, the following settings are applied.', 'simple-nivoslider' ); ?></h4>
				<form method="post" action="<?php echo esc_url( $scriptname ); ?>">
				<?php wp_nonce_field( 'snsl_set', 'simplenivoslider_set' ); ?>

				<div class="submit">
					<?php submit_button( __( 'Save Changes' ), 'large', 'Simplenivoslider_set_Save', false ); ?>
					<?php submit_button( __( 'Default' ), 'large', 'Default', false ); ?>
				</div>

				<div id="container-simplenivoslider-settings">

					<?php $shortocode_attr_html = '<a href="' . __( 'https://codex.wordpress.org/Shortcode_API#Handling_Attributes', 'simple-nivoslider' ) . '" target="_blank" rel="noopener noreferrer" style="text-decoration: none; word-break: break-all;">' . __( 'specification', 'simple-nivoslider' ) . '</a>'; ?>

					<h4><span style="color: green;">
					<?php
					/* translators: Shortcode attribute */
					echo wp_kses_post( sprintf( __( 'Shortcode attribute is green. It will be lowercase. It is the %1$s of WordPress.', 'simple-nivoslider' ), $shortocode_attr_html ) );
					?>
					</span></h4>

					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>theme&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">theme</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(default)</div>
						<div>
						<?php $target_settings_theme = $simplenivoslider_settings['theme']; ?>
						<select id="simplenivoslider_settings_theme" name="simplenivoslider_settings_theme">
							<option 
							<?php
							if ( 'default' == $target_settings_theme ) {
								echo 'selected="selected"';}
							?>
							>default</option>
							<option 
							<?php
							if ( 'dark' == $target_settings_theme ) {
								echo 'selected="selected"';}
							?>
							>dark</option>
							<option 
							<?php
							if ( 'light' == $target_settings_theme ) {
								echo 'selected="selected"';}
							?>
							>light</option>
							<option 
							<?php
							if ( 'bar' == $target_settings_theme ) {
								echo 'selected="selected"';}
							?>
							>bar</option>
						</select>
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( 'Using themes', 'simple-nivoslider' ); ?></li></div>
					</div>
					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>effect&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">effect</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(random)</div>
						<div>
						<?php $target_settings_effect = $simplenivoslider_settings['effect']; ?>
						<select id="simplenivoslider_settings_effect" name="simplenivoslider_settings_effect">
							<option 
							<?php
							if ( 'sliceDown' == $target_settings_effect ) {
								echo 'selected="selected"';}
							?>
							>sliceDown</option>
							<option 
							<?php
							if ( 'sliceDownLeft' == $target_settings_effect ) {
								echo 'selected="selected"';}
							?>
							>sliceDownLeft</option>
							<option 
							<?php
							if ( 'sliceUp' == $target_settings_effect ) {
								echo 'selected="selected"';}
							?>
							>sliceUp</option>
							<option 
							<?php
							if ( 'sliceUpLeft' == $target_settings_effect ) {
								echo 'selected="selected"';}
							?>
							>sliceUpLeft</option>
							<option 
							<?php
							if ( 'sliceUpDown' == $target_settings_effect ) {
								echo 'selected="selected"';}
							?>
							>sliceUpDown</option>
							<option 
							<?php
							if ( 'sliceUpDownLeft' == $target_settings_effect ) {
								echo 'selected="selected"';}
							?>
							>sliceUpDownLeft</option>
							<option 
							<?php
							if ( 'fold' == $target_settings_effect ) {
								echo 'selected="selected"';}
							?>
							>fold</option>
							<option 
							<?php
							if ( 'fade' == $target_settings_effect ) {
								echo 'selected="selected"';}
							?>
							>fade</option>
							<option 
							<?php
							if ( 'random' == $target_settings_effect ) {
								echo 'selected="selected"';}
							?>
							>random</option>
							<option 
							<?php
							if ( 'slideInRight' == $target_settings_effect ) {
								echo 'selected="selected"';}
							?>
							>slideInRight</option>
							<option 
							<?php
							if ( 'slideInLeft' == $target_settings_effect ) {
								echo 'selected="selected"';}
							?>
							>slideInLeft</option>
							<option 
							<?php
							if ( 'boxRandom' == $target_settings_effect ) {
								echo 'selected="selected"';}
							?>
							>boxRandom</option>
							<option 
							<?php
							if ( 'boxRain' == $target_settings_effect ) {
								echo 'selected="selected"';}
							?>
							>boxRain</option>
							<option 
							<?php
							if ( 'boxRainReverse' == $target_settings_effect ) {
								echo 'selected="selected"';}
							?>
							>boxRainReverse</option>
							<option 
							<?php
							if ( 'boxRainGrow' == $target_settings_effect ) {
								echo 'selected="selected"';}
							?>
							>boxRainGrow</option>
							<option 
							<?php
							if ( 'boxRainGrowReverse' == $target_settings_effect ) {
								echo 'selected="selected"';}
							?>
							>boxRainGrowReverse</option>
						</select>
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( 'Specify sets like', 'simple-nivoslider' ); ?></li></div>
					</div>
					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>slices&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">slices</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(15)</div>
						<div>
							<input type="text" id="simplenivoslider_settings_slices" name="simplenivoslider_settings_slices" value="<?php echo esc_attr( $simplenivoslider_settings['slices'] ); ?>" style="width: 80px" />
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( 'For slice animations', 'simple-nivoslider' ); ?></li></div>
					</div>
					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>boxCols&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">boxcols</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(8)</div>
						<div>
							<input type="text" id="simplenivoslider_settings_boxCols" name="simplenivoslider_settings_boxCols" value="<?php echo esc_attr( $simplenivoslider_settings['boxCols'] ); ?>" style="width: 80px" />
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( 'For box animations cols', 'simple-nivoslider' ); ?></li></div>
					</div>
					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>boxRows&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">boxrows</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(4)</div>
						<div>
							<input type="text" id="simplenivoslider_settings_boxRows" name="simplenivoslider_settings_boxRows" value="<?php echo esc_attr( $simplenivoslider_settings['boxRows'] ); ?>" style="width: 80px" />
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( 'For box animations rows', 'simple-nivoslider' ); ?></li></div>
					</div>
					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>animSpeed&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">animspeed</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(500)</div>
						<div>
							<input type="text" id="simplenivoslider_settings_animSpeed" name="simplenivoslider_settings_animSpeed" value="<?php echo esc_attr( $simplenivoslider_settings['animSpeed'] ); ?>" style="width: 80px" />msec
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( 'Slide transition speed', 'simple-nivoslider' ); ?></li></div>
					</div>
					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>pauseTime&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">pausetime</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(3000)</div>
						<div>
							<input type="text" id="simplenivoslider_settings_pauseTime" name="simplenivoslider_settings_pauseTime" value="<?php echo esc_attr( $simplenivoslider_settings['pauseTime'] ); ?>" style="width: 80px" />msec
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( 'How long each slide will show', 'simple-nivoslider' ); ?></li></div>
					</div>
					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>startSlide&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">startslide</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(0)</div>
						<div>
							<input type="text" id="simplenivoslider_settings_startSlide" name="simplenivoslider_settings_startSlide" value="<?php echo esc_attr( $simplenivoslider_settings['startSlide'] ); ?>" style="width: 80px" />
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( 'Set starting Slide (0 index)', 'simple-nivoslider' ); ?></li></div>
					</div>
					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>directionNav&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">directionnav</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(true)</div>
						<div>
						<?php $target_settings_directionnav = $simplenivoslider_settings['directionNav']; ?>
						<select id="simplenivoslider_settings_directionNav" name="simplenivoslider_settings_directionNav">
							<option 
							<?php
							if ( 'true' == $target_settings_directionnav ) {
								echo 'selected="selected"';}
							?>
							>true</option>
							<option value="" 
							<?php
							if ( ! $target_settings_directionnav ) {
								echo 'selected="selected"';}
							?>
							>false</option>
						</select>
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( 'Next & Prev navigation', 'simple-nivoslider' ); ?></li></div>
					</div>
					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>controlNav&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">controlnav</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(true)</div>
						<div>
						<?php $target_settings_controlnav = $simplenivoslider_settings['controlNav']; ?>
						<select id="simplenivoslider_settings_controlNav" name="simplenivoslider_settings_controlNav">
							<option 
							<?php
							if ( 'true' == $target_settings_controlnav ) {
								echo 'selected="selected"';}
							?>
							>true</option>
							<option value="" 
							<?php
							if ( ! $target_settings_controlnav ) {
								echo 'selected="selected"';}
							?>
							>false</option>
						</select>
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( '1,2,3... navigation', 'simple-nivoslider' ); ?></li></div>
					</div>
					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>controlNavThumbs&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">controlnavthumbs</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(false)</div>
						<div>
						<?php $target_settings_controlnavthumbs = $simplenivoslider_settings['controlNavThumbs']; ?>
						<select id="simplenivoslider_settings_controlNavThumbs" name="simplenivoslider_settings_controlNavThumbs">
							<option 
							<?php
							if ( 'true' == $target_settings_controlnavthumbs ) {
								echo 'selected="selected"';}
							?>
							>true</option>
							<option value="" 
							<?php
							if ( ! $target_settings_controlnavthumbs ) {
								echo 'selected="selected"';}
							?>
							>false</option>
						</select>
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( 'Use thumbnails for Control Nav', 'simple-nivoslider' ); ?></li></div>
					</div>
					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>thumbswidth&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">thumbswidth</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(40)</div>
						<div>
							<input type="text" id="simplenivoslider_settings_thumbswidth" name="simplenivoslider_settings_thumbswidth" value="<?php echo esc_attr( $simplenivoslider_settings['thumbswidth'] ); ?>" style="width: 80px" />px
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( 'Width of thumbnails', 'simple-nivoslider' ); ?></li></div>
					</div>
					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>pauseOnHover&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">pauseonhover</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(true)</div>
						<div>
						<?php $target_settings_pauseonhover = $simplenivoslider_settings['pauseOnHover']; ?>
						<select id="simplenivoslider_settings_pauseOnHover" name="simplenivoslider_settings_pauseOnHover">
							<option 
							<?php
							if ( 'true' == $target_settings_pauseonhover ) {
								echo 'selected="selected"';}
							?>
							>true</option>
							<option value="" 
							<?php
							if ( ! $target_settings_pauseonhover ) {
								echo 'selected="selected"';}
							?>
							>false</option>
						</select>
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( 'Stop animation while hovering', 'simple-nivoslider' ); ?></li></div>
					</div>
					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>manualAdvance&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">manualadvance</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(false)</div>
						<div>
						<?php $target_settings_manualadvance = $simplenivoslider_settings['manualAdvance']; ?>
						<select id="simplenivoslider_settings_manualAdvance" name="simplenivoslider_settings_manualAdvance">
							<option 
							<?php
							if ( 'true' == $target_settings_manualadvance ) {
								echo 'selected="selected"';}
							?>
							>true</option>
							<option value="" 
							<?php
							if ( ! $target_settings_manualadvance ) {
								echo 'selected="selected"';}
							?>
							>false</option>
						</select>
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( 'Force manual transitions', 'simple-nivoslider' ); ?></li></div>
					</div>
					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>prevText&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">prevtext</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(Prev)</div>
						<div>
							<input type="text" id="simplenivoslider_settings_prevText" name="simplenivoslider_settings_prevText" value="<?php echo esc_attr( $simplenivoslider_settings['prevText'] ); ?>" />
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( 'Prev directionNav text', 'simple-nivoslider' ); ?></li></div>
					</div>
					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>nextText&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">nexttext</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(Next)</div>
						<div>
							<input type="text" id="simplenivoslider_settings_nextText" name="simplenivoslider_settings_nextText" value="<?php echo esc_attr( $simplenivoslider_settings['nextText'] ); ?>" />
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( 'Next directionNav text', 'simple-nivoslider' ); ?></li></div>
					</div>
					<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
						<div>randomStart&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">randomstart</span></div>
						<div><?php esc_html_e( 'Default' ); ?>&nbsp(false)</div>
						<div>
						<?php $target_settings_randomstart = $simplenivoslider_settings['randomStart']; ?>
						<select id="simplenivoslider_settings_randomStart" name="simplenivoslider_settings_randomStart">
							<option 
							<?php
							if ( 'true' == $target_settings_randomstart ) {
								echo 'selected="selected"';}
							?>
							>true</option>
							<option value="" 
							<?php
							if ( ! $target_settings_randomstart ) {
								echo 'selected="selected"';}
							?>
							>false</option>
						</select>
						</div>
						<div style="padding: 0px 10px"><li><?php esc_html_e( 'Start on a random slide', 'simple-nivoslider' ); ?></li></div>
					</div>

				</div>
				<div style="clear:both"></div>

				<?php submit_button( __( 'Save Changes' ), 'large', 'Simplenivoslider_set_Save', true ); ?>

				</form>
			</details>

			<details style="margin-bottom: 5px;">
			<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><strong><?php esc_html_e( 'Settings' ); ?></strong></summary>
			<h4><?php esc_html_e( 'Can specify without writing the shortcode.', 'simple-nivoslider' ); ?></h4>
				<form method="post" action="<?php echo esc_url( $scriptname . '#simplenivoslider-admin-tabs-2' ); ?>" />
				<?php wp_nonce_field( 'add_cbo', 'add_code_body_open' ); ?>

					<div style="display: block;padding:5px 5px">
					<input type="radio" name="simple_nivoslider_insert" value="body_open" 
					<?php
					if ( 'body_open' === get_option( 'simplenivoslider_insert_position', 'body_open' ) ) {
						echo 'checked';}
					?>
					>
					<?php esc_html_e( 'Begining', 'simple-nivoslider' ); ?>&nbsp;&nbsp;
					<input type="radio" name="simple_nivoslider_insert" value="before" 
					<?php
					if ( 'before' === get_option( 'simplenivoslider_insert_position', 'body_open' ) ) {
						echo 'checked';}
					?>
					>
					<?php esc_html_e( 'Before post', 'simple-nivoslider' ); ?>&nbsp;&nbsp;
					<input type="radio" name="simple_nivoslider_insert" value="after" 
					<?php
					if ( 'after' === get_option( 'simplenivoslider_insert_position', 'body_open' ) ) {
						echo 'checked';}
					?>
					>
					<?php esc_html_e( 'After post', 'simple-nivoslider' ); ?>
					</div>

					<div style="display: block;padding:5px 5px">
					<button type="button" id="insert-media-button" class="button insert-media add_media" data-editor="content"><span class="dashicons dashicons-admin-media"></span> <?php esc_html_e( 'Create Gallery' ); ?></button>
					<input type="text" class="wp-editor-area" autocomplete="off" style="width: 300px;" name="content" id="content">
					</div>
					<div style="padding: 10px 10px">
					<?php submit_button( __( 'Register' ) . ' & ' . __( 'Save Changes' ), 'primary', 'BodyOpenCode', false ); ?>
					<?php submit_button( __( 'Remove' ), 'primary', 'RemoveCode', false ); ?>
					</div>

				</form>
				<hr>
				<?php
				if ( get_option( 'simplenivoslider_gallery' ) ) {
					echo do_shortcode( get_option( 'simplenivoslider_gallery' ) );
				}
				?>
			</details>

			<?php
			$screenshot_html = '<a href="' . __( 'https://wordpress.org/plugins/simple-nivoslider/screenshots/', 'simple-nivoslider' ) . '" target="_blank" rel="noopener noreferrer" style="text-decoration: none; word-break: break-all;">' . __( 'Screenshots', 'simple-nivoslider' ) . '</a>';
			?>
			<details style="margin-bottom: 5px;">
			<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><strong><?php esc_html_e( 'Documents' ); ?>(<?php echo wp_kses_post( $screenshot_html ); ?>)</strong></summary>
				<li style="margin: 0px 40px;">
				<h4><?php esc_html_e( 'Write a Shortcode. The following text field. Enclose image tags and gallery shortcode.', 'simple-nivoslider' ); ?></h4>
				<h4><?php esc_html_e( 'example:', 'simple-nivoslider' ); ?></h4>
				<h4><code>&#91simplenivoslider&#93&lt;a href="http://blog3.localhost.localdomain/wp-content/uploads/sites/8/2017/01/f8e6a6a7.jpg"&gt;&lt;img src="http://blog3.localhost.localdomain/wp-content/uploads/sites/8/2017/01/f8e6a6a7.jpg" alt="" width="1000" height="626" class="alignnone size-full wp-image-275" /&gt;&lt;/a&gt;
	&lt;a href="http://blog3.localhost.localdomain/wp-content/uploads/sites/8/2017/01/f878ff71.jpg"&gt;&lt;img src="http://blog3.localhost.localdomain/wp-content/uploads/sites/8/2017/01/f878ff71.jpg" alt="" width="1000" height="666" class="alignnone size-full wp-image-274" /&gt;&lt;/a&gt;&#91gallery size="full" ids="273,272,271,270"&#93&#91/simplenivoslider&#93</code></h4>
				</li>
				<li style="margin: 0px 40px;">
				<h4><?php esc_html_e( 'Write a Shortcode. The following template. Enclose image tags and gallery shortcode.', 'simple-nivoslider' ); ?></h4>
				<h4><?php esc_html_e( 'example:', 'simple-nivoslider' ); ?></h4>
				<h4><code>&lt;?php echo do_shortcode('&#91simplenivoslider controlnav="false"&#93&#91gallery link="none" size="full" ids="271,270,269,268"&#93&#91/simplenivoslider&#93'); ?&gt;</code></h4>
				</h4>
				</li>
				<li style="margin: 0px 40px;">
				<h4><?php esc_html_e( '"Simple NivoSlider" activation, you to include additional buttons for Shortcode in the Text (HTML) mode of the WordPress editor.', 'simple-nivoslider' ); ?>
				</h4>
				</li>
				<li style="margin: 0px 40px;">
				<h4><?php esc_html_e( 'Within the Shortcode, it is possible to describe multiple galleries and multiple media.', 'simple-nivoslider' ); ?>
				</h4>
				</li>
			</details>

		</div>
		<?php
	}

	/** ==================================================
	 * Credit
	 *
	 * @since 1.00
	 */
	private function credit() {

		$plugin_name    = null;
		$plugin_ver_num = null;
		$plugin_path    = plugin_dir_path( __DIR__ );
		$plugin_dir     = untrailingslashit( wp_normalize_path( $plugin_path ) );
		$slugs          = explode( '/', $plugin_dir );
		$slug           = end( $slugs );
		$files          = scandir( $plugin_dir );
		foreach ( $files as $file ) {
			if ( '.' === $file || '..' === $file || is_dir( $plugin_path . $file ) ) {
				continue;
			} else {
				$exts = explode( '.', $file );
				$ext  = strtolower( end( $exts ) );
				if ( 'php' === $ext ) {
					$plugin_datas = get_file_data(
						$plugin_path . $file,
						array(
							'name'    => 'Plugin Name',
							'version' => 'Version',
						)
					);
					if ( array_key_exists( 'name', $plugin_datas ) && ! empty( $plugin_datas['name'] ) && array_key_exists( 'version', $plugin_datas ) && ! empty( $plugin_datas['version'] ) ) {
						$plugin_name    = $plugin_datas['name'];
						$plugin_ver_num = $plugin_datas['version'];
						break;
					}
				}
			}
		}
		$plugin_version = __( 'Version:' ) . ' ' . $plugin_ver_num;
		/* translators: FAQ Link & Slug */
		$faq       = sprintf( __( 'https://wordpress.org/plugins/%s/faq', 'simple-nivoslider' ), $slug );
		$support   = 'https://wordpress.org/support/plugin/' . $slug;
		$review    = 'https://wordpress.org/support/view/plugin-reviews/' . $slug;
		$translate = 'https://translate.wordpress.org/projects/wp-plugins/' . $slug;
		$facebook  = 'https://www.facebook.com/katsushikawamori/';
		$twitter   = 'https://twitter.com/dodesyo312';
		$youtube   = 'https://www.youtube.com/channel/UC5zTLeyROkvZm86OgNRcb_w';
		$donate    = __( 'https://shop.riverforest-wp.info/donate/', 'simple-nivoslider' );

		?>
		<span style="font-weight: bold;">
		<div>
		<?php echo esc_html( $plugin_version ); ?> | 
		<a style="text-decoration: none;" href="<?php echo esc_url( $faq ); ?>" target="_blank" rel="noopener noreferrer">FAQ</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $support ); ?>" target="_blank" rel="noopener noreferrer">Support Forums</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $review ); ?>" target="_blank" rel="noopener noreferrer">Reviews</a>
		</div>
		<div>
		<a style="text-decoration: none;" href="<?php echo esc_url( $translate ); ?>" target="_blank" rel="noopener noreferrer">
		<?php
		/* translators: Plugin translation link */
		echo esc_html( sprintf( __( 'Translations for %s' ), $plugin_name ) );
		?>
		</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-facebook"></span></a> | <a style="text-decoration: none;" href="<?php echo esc_url( $twitter ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-twitter"></span></a> | <a style="text-decoration: none;" href="<?php echo esc_url( $youtube ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-video-alt3"></span></a>
		</div>
		</span>

		<div style="width: 250px; height: 180px; margin: 5px; padding: 5px; border: #CCC 2px solid;">
		<h3><?php esc_html_e( 'Please make a donation if you like my work or would like to further the development of this plugin.', 'simple-nivoslider' ); ?></h3>
		<div style="text-align: right; margin: 5px; padding: 5px;"><span style="padding: 3px; color: #ffffff; background-color: #008000">Plugin Author</span> <span style="font-weight: bold;">Katsushi Kawamori</span></div>
		<button type="button" style="margin: 5px; padding: 5px;" onclick="window.open('<?php echo esc_url( $donate ); ?>')"><?php esc_html_e( 'Donate to this plugin &#187;' ); ?></button>
		</div>

		<?php

	}

	/** ==================================================
	 * Update wp_options table.
	 *
	 * @since 1.00
	 */
	private function options_updated() {

		if ( isset( $_POST['Default'] ) && ! empty( $_POST['Default'] ) ) {
			if ( check_admin_referer( 'snsl_set', 'simplenivoslider_set' ) ) {
				$settings_tbl = array(
					'theme' => 'default',
					'effect' => 'random',
					'slices' => 15,
					'boxCols' => 8,
					'boxRows' => 4,
					'animSpeed' => 500,
					'pauseTime' => 3000,
					'startSlide' => 0,
					'directionNav' => 'true',
					'controlNav' => 'true',
					'controlNavThumbs' => null,
					'thumbswidth' => 40,
					'pauseOnHover' => 'true',
					'manualAdvance' => null,
					'prevText' => 'Prev',
					'nextText' => 'Next',
					'randomStart' => null,
				);
				update_option( 'simplenivoslider_settings', $settings_tbl );
				echo '<div class="notice notice-success is-dismissible"><ul><li>NivoSlider ' . esc_html( __( 'Settings' ) . ' --> ' . __( 'Default' ) ) . '</li></ul></div>';
			}
		}

		if ( isset( $_POST['Simplenivoslider_set_Save'] ) && ! empty( $_POST['Simplenivoslider_set_Save'] ) ) {
			if ( check_admin_referer( 'snsl_set', 'simplenivoslider_set' ) ) {
				$simplenivoslider_settings = get_option( 'simplenivoslider_settings' );
				if ( ! empty( $_POST['simplenivoslider_settings_theme'] ) ) {
					$simplenivoslider_settings['theme'] = sanitize_text_field( wp_unslash( $_POST['simplenivoslider_settings_theme'] ) );
				}
				if ( ! empty( $_POST['simplenivoslider_settings_effect'] ) ) {
					$simplenivoslider_settings['effect'] = sanitize_text_field( wp_unslash( $_POST['simplenivoslider_settings_effect'] ) );
				}
				if ( ! empty( $_POST['simplenivoslider_settings_slices'] ) ) {
					$simplenivoslider_settings['slices'] = intval( $_POST['simplenivoslider_settings_slices'] );
				}
				if ( ! empty( $_POST['simplenivoslider_settings_boxCols'] ) ) {
					$simplenivoslider_settings['boxCols'] = intval( $_POST['simplenivoslider_settings_boxCols'] );
				}
				if ( ! empty( $_POST['simplenivoslider_settings_boxRows'] ) ) {
					$simplenivoslider_settings['boxRows'] = intval( $_POST['simplenivoslider_settings_boxRows'] );
				}
				if ( ! empty( $_POST['simplenivoslider_settings_animSpeed'] ) ) {
					$simplenivoslider_settings['animSpeed'] = intval( $_POST['simplenivoslider_settings_animSpeed'] );
				}
				if ( ! empty( $_POST['simplenivoslider_settings_pauseTime'] ) ) {
					$simplenivoslider_settings['pauseTime'] = intval( $_POST['simplenivoslider_settings_pauseTime'] );
				}
				if ( ! empty( $_POST['simplenivoslider_settings_startSlide'] ) ) {
					$simplenivoslider_settings['startSlide'] = intval( $_POST['simplenivoslider_settings_startSlide'] );
				}
				if ( ! empty( $_POST['simplenivoslider_settings_directionNav'] ) ) {
					$simplenivoslider_settings['directionNav'] = sanitize_text_field( wp_unslash( $_POST['simplenivoslider_settings_directionNav'] ) );
				} else {
					$simplenivoslider_settings['directionNav'] = null;
				}
				if ( ! empty( $_POST['simplenivoslider_settings_controlNav'] ) ) {
					$simplenivoslider_settings['controlNav'] = sanitize_text_field( wp_unslash( $_POST['simplenivoslider_settings_controlNav'] ) );
				} else {
					$simplenivoslider_settings['controlNav'] = null;
				}
				if ( ! empty( $_POST['simplenivoslider_settings_controlNavThumbs'] ) ) {
					$simplenivoslider_settings['controlNavThumbs'] = sanitize_text_field( wp_unslash( $_POST['simplenivoslider_settings_controlNavThumbs'] ) );
				} else {
					$simplenivoslider_settings['controlNavThumbs'] = null;
				}
				if ( ! empty( $_POST['simplenivoslider_settings_thumbswidth'] ) ) {
					$simplenivoslider_settings['thumbswidth'] = sanitize_text_field( wp_unslash( $_POST['simplenivoslider_settings_thumbswidth'] ) );
				}
				if ( ! empty( $_POST['simplenivoslider_settings_pauseOnHover'] ) ) {
					$simplenivoslider_settings['pauseOnHover'] = sanitize_text_field( wp_unslash( $_POST['simplenivoslider_settings_pauseOnHover'] ) );
				} else {
					$simplenivoslider_settings['pauseOnHover'] = null;
				}
				if ( ! empty( $_POST['simplenivoslider_settings_manualAdvance'] ) ) {
					$simplenivoslider_settings['manualAdvance'] = sanitize_text_field( wp_unslash( $_POST['simplenivoslider_settings_manualAdvance'] ) );
				} else {
					$simplenivoslider_settings['manualAdvance'] = null;
				}
				if ( ! empty( $_POST['simplenivoslider_settings_prevText'] ) ) {
					$simplenivoslider_settings['prevText'] = sanitize_text_field( wp_unslash( $_POST['simplenivoslider_settings_prevText'] ) );
				}
				if ( ! empty( $_POST['simplenivoslider_settings_nextText'] ) ) {
					$simplenivoslider_settings['nextText'] = sanitize_text_field( wp_unslash( $_POST['simplenivoslider_settings_nextText'] ) );
				}
				if ( ! empty( $_POST['simplenivoslider_settings_randomStart'] ) ) {
					$simplenivoslider_settings['randomStart'] = sanitize_text_field( wp_unslash( $_POST['simplenivoslider_settings_randomStart'] ) );
				} else {
					$simplenivoslider_settings['randomStart'] = null;
				}
				update_option( 'simplenivoslider_settings', $simplenivoslider_settings );
				echo '<div class="notice notice-success is-dismissible"><ul><li>NivoSlider ' . esc_html( __( 'Settings' ) . ' --> ' . __( 'Settings saved.' ) ) . '</li></ul></div>';
			}
		}

		if ( isset( $_POST['BodyOpenCode'] ) && ! empty( $_POST['BodyOpenCode'] ) ) {
			if ( check_admin_referer( 'add_cbo', 'add_code_body_open' ) ) {
				if ( isset( $_POST['content'] ) ) {
					if ( ! empty( $_POST['content'] ) ) {
						$body_open_content = sanitize_text_field( ( wp_unslash( $_POST['content'] ) ) );
						if ( strpos( $body_open_content, '[gallery' ) === false ) {
							$body_open_content = null;
						}
						if ( ! empty( $body_open_content ) ) {
							update_option( 'simplenivoslider_gallery', $body_open_content );
							echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html( __( 'Gallery' ) . ' --> ' . __( 'Register' ) ) . '</li></ul></div>';
						} else {
							echo '<div class="notice notice-error is-dismissible"><ul><li>' . esc_html__( 'Please Create Gallery.', 'simple-nivoslider' ) . '</li></ul></div>';
						}
					}
				}
				if ( isset( $_POST['simple_nivoslider_insert'] ) && ! empty( $_POST['simple_nivoslider_insert'] ) ) {
					$simple_nivoslider_insert = sanitize_text_field( ( wp_unslash( $_POST['simple_nivoslider_insert'] ) ) );
					update_option( 'simplenivoslider_insert_position', $simple_nivoslider_insert );
					echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html( __( 'Settings' ) . ' --> ' . __( 'Settings saved.' ) ) . '</li></ul></div>';
				}
			}
		}

		if ( isset( $_POST['RemoveCode'] ) && ! empty( $_POST['RemoveCode'] ) ) {
			if ( check_admin_referer( 'add_cbo', 'add_code_body_open' ) ) {
				delete_option( 'simplenivoslider_gallery' );
				echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html( __( 'Gallery' ) . ' --> ' . __( 'Remove' ) ) . '</li></ul></div>';
			}
		}

	}

	/** ==================================================
	 * Settings register
	 *
	 * @since 1.00
	 */
	public function register_settings() {

		if ( ! get_option( 'simplenivoslider_settings' ) ) {
			$settings_tbl = array(
				'theme' => 'default',
				'effect' => 'random',
				'slices' => 15,
				'boxCols' => 8,
				'boxRows' => 4,
				'animSpeed' => 500,
				'pauseTime' => 3000,
				'startSlide' => 0,
				'directionNav' => 'true',
				'controlNav' => 'true',
				'controlNavThumbs' => null,
				'thumbswidth' => 40,
				'pauseOnHover' => 'true',
				'manualAdvance' => null,
				'prevText' => 'Prev',
				'nextText' => 'Next',
				'randomStart' => null,
			);
			update_option( 'simplenivoslider_settings', $settings_tbl );
		}

	}

	/** ==================================================
	 * Add Quick Tag
	 *
	 * @since 4.00
	 */
	public function simplenivoslider_add_quicktags() {
		if ( wp_script_is( 'quicktags' ) ) {
			?>
		<script type="text/javascript">
			QTags.addButton( 'simplenivoslider', 'simplenivoslider', '[simplenivoslider]', '[/simplenivoslider]' );
		</script>
			<?php
		}
	}

}


