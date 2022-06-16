<?php
/**
 * Field Generator Class
 *
 */

if(!class_exists('FmpField')):
	class FmpField {
		private $type;
		private $name;
		private $value;
		private $default;
		private $label;
		private $class;
		private $id;
		private $holderClass;
		private $description;
		private $options;
		private $option;
		private $optionLabel;
		private $attr;
		private $multiple;
		private $alignment;
		private $placeholder;
		private $blank;

		function __construct(){
		}

		/**
		 *
		 * Initiate the predefined property for the field object
		 *
		 * @param $attr
		 */
		private function setArgument($key, $attr) {
			$this->type = isset($attr['type']) ? ($attr['type'] ? $attr['type'] : 'text') : 'text';
			$this->multiple = isset($attr['multiple']) ? ($attr['multiple'] ? $attr['multiple'] : false) : false;
			$this->name = !empty($key) ? $key : null;
			$this->default = isset($attr['default']) ? $attr['default'] : null;
            if ($key === "menu_order") {
                global $post;
                $this->value = $post->menu_order;
            } else {
                $this->value = isset($attr['value']) ? ($attr['value'] ? $attr['value'] : null) : null;
            }

			if(!$this->value){
				$post_id = get_the_ID();
				if(!$this->meta_exist($post_id, $this->name)){
					$this->value = $this->default;
				}else{
                    if($this->multiple && !apply_filters('tlp_fmp_has_multiple_meta_issue', false)) {
						$this->value = get_post_meta($post_id, $this->name);
					} else {
						$this->value = get_post_meta($post_id, $this->name, true);
					}
				}
			}

			$this->label = isset($attr['label']) ? ($attr['label'] ? $attr['label'] : null) : null;
			$this->class = isset($attr['class']) ? ($attr['class'] ? $attr['class'] : null) : null;
			$this->holderClass = isset($attr['holderClass']) ? ($attr['holderClass'] ? $attr['holderClass'] : null) : null;
			$this->placeholder = isset($attr['placeholder']) ? ($attr['placeholder'] ? $attr['placeholder'] : null) : null;
			$this->description = isset($attr['description']) ? ($attr['description'] ? $attr['description'] : null) : null;
			$this->options = isset($attr['options']) ? ($attr['options'] ? $attr['options'] : array()) : array();
			$this->option = isset($attr['option']) ? ($attr['option'] ? $attr['option'] : null) : null;
			$this->optionLabel = isset($attr['optionLabel']) ? ($attr['optionLabel'] ? $attr['optionLabel'] : null) : null;
			$this->attr = isset($attr['attr']) ? ($attr['attr'] ? $attr['attr'] : null) : null;
			$this->alignment = isset($attr['alignment']) ? ($attr['alignment'] ? $attr['alignment'] : null) : null;
			$this->blank = !empty($attr['blank']) ? $attr['blank'] : null;
            $this->class       = $this->class ? $this->class . " rt-form-control" : "rt-form-control";

		}

		/**
		 * Create field
		 *
		 * @param $key
		 * @param $attr
		 *
		 * @return null|string
		 */
		public function Field($key, $attr = array()) {
			$this->setArgument($key, $attr);
			$holderId = $this->name."_holder";

			$html = null;
            $html .= "<div class='rt-field-wrapper {$this->holderClass}' id='{$holderId}'>";
            $html .= sprintf( '<div class="rt-label">%s</div>',
                $this->label ? sprintf( '<label for="">%s</label>', $this->label ) : ''
            );
            $html .= "<div class='rt-field'>";
			switch($this->type){
				case 'text':
					$html .= $this->text();
					break;

				case 'slug':
					$html .= $this->slug();
					break;

				case 'price':
					$html .= $this->price();
					break;

				case 'url':
					$html .= $this->url();
					break;

				case 'number':
					$html .= $this->number();
					break;

				case 'select':
					$html .= $this->select();
					break;

				case 'textarea':
					$html .= $this->textArea();
					break;

				case 'checkbox':
					$html .= $this->checkbox();
					break;

				case 'radio':
					$html .= $this->radioField();
					break;

				case 'colorpicker':
					$html .= $this->colorPicker();
					break;

				case 'custom_css':
					$html .= $this->customCss();
					break;

				case 'style':
					$html .= $this->smartStyle();
					break;

				case 'image':
					$html .= $this->image();
					break;

				case 'image_size':
					$html .= $this->imageSize();
					break;
			}
			if($this->description) {
				$html .= "<p class='description'>{$this->description}</p>";
			}
			$html .="</div>"; // field
			$html .="</div>"; // field holder

			return $html;
		}

		/**
		 * Generate text field
		 * @return null|string
		 */
		private function text() {
			$h = null;
			$h .= "<input
                    type='text'
                    class='{$this->class}'
                    id='{$this->name}'
                    value='{$this->value}'
                    name='{$this->name}'
                    placeholder='{$this->placeholder}'
                    {$this->attr}
                    />";
			return $h;
		}

		/**
		 * Generate text field
		 * @return null|string
		 */
		private function price() {
			$h = null;
			$h .= "<input
                    type='text'
                    class='{$this->class}'
                    id='{$this->name}'
                    value='{$this->value}'
                    name='{$this->name}'
                    placeholder='{$this->placeholder}'
                    {$this->attr}
                    />";
			return $h;
		}

		/**
		 * Generate text field
		 * @return null|string
		 */
		private function slug() {
			$h = null;
			$h .= "<input
                    type='text'
                    class='{$this->class}'
                    id='{$this->name}'
                    value='{$this->value}'
                    name='{$this->name}'
                    placeholder='{$this->placeholder}'
                    {$this->attr}
                    />";
			return $h;
		}

		/**
		 * Generate color picker
		 * @return null|string
		 */
		private function colorPicker() {
			$h = null;
			$h .= "<input
                    type='text'
                    class='{$this->class} fmp-color'
                    id='{$this->name}'
                    value='{$this->value}'
                    name='{$this->name}'
                    placeholder='{$this->placeholder}'
                    {$this->attr}
                    />";
			return $h;
		}

		/**
		 * Custom css field
		 * @return null|string
		 */
		private  function customCss() {
			$h = null;
			$h .= '<div class="rt-custom-css">';
			$h .= '<div class="custom_css_container">';
			$h .= "<div name='{$this->name}' id='ret-".mt_rand()."' class='custom-css'>";
			$h .= '</div>';
			$h .= '</div>';

			$h .= "<textarea
                        style='display: none;'
                        class='custom_css_textarea'
                        id='{$this->name}'
                        name='{$this->name}'
                        >{$this->value}</textarea>";
			$h .= '</div>';

			return $h;
		}

		/**
		 * Generate URL field
		 * @return null|string
		 */
		private function url() {
			$h = null;
			$h .= "<input
                    type='url'
                    class='{$this->class}'
                    id='{$this->name}'
                    value='{$this->value}'
                    name='{$this->name}'
                    placeholder='{$this->placeholder}'
                    {$this->attr}
                    />";
			return $h;
		}

		/**
		 * Generate number field
		 * @return null|string
		 */
		private function number() {
			$h = null;
			$h .= "<input
                    type='number'
                    class='{$this->class}'
                    id='{$this->name}'
                    value='{$this->value}'
                    name='{$this->name}'
                    placeholder='{$this->placeholder}'
                    {$this->attr}
                    />";
			return $h;
		}

		/**
		 * Generate Drop-down field
		 * @return null|string
		 */
		private function select() {
			$h = null;
			if($this->multiple){
				$this->attr = " style='min-width:160px;'";
				$this->name = $this->name."[]";
				$this->attr = $this->attr." multiple='multiple'";
				$this->value = (is_array($this->value) && !empty($this->value) ? $this->value : array());
			}else{
				$this->value = array($this->value);
			}

			$h .= "<select name='{$this->name}' id='{$this->name}' class='{$this->class}' {$this->attr}>";
			if($this->blank){
				$h .= "<option value=''>{$this->blank}</option>";
			}
			if(is_array($this->options) && !empty($this->options)){
				foreach($this->options as $key => $value){
					$slt = (in_array($key, $this->value) ? "selected" : null);
					$h .= "<option {$slt} value='{$key}'>{$value}</option>";
				}
			}
			$h .= "</select>";
			return $h;
		}

		/**
		 * Generate textArea field
		 * @return null|string
		 */
		private function textArea() {
			$h = null;
			$h .= "<textarea
                    class='{$this->class} rt-textarea'
                    id='{$this->name}'
                    name='{$this->name}'
                    placeholder='{$this->placeholder}'
                    {$this->attr}
                    >{$this->value}</textarea>";
			return $h;
		}

		/**
		 * Generate check box
		 * @return null|string
		 */
		private function checkbox() {
			$h = null;
			$id = $this->name;
			if($this->multiple){
				$this->name = $this->name."[]";
				$this->value = (is_array($this->value) && !empty($this->value) ? $this->value : array());
			}
			if($this->multiple) {
				$h .= "<div class='checkbox-group {$this->alignment}' id='{$id}'>";
				if (is_array($this->options) && !empty($this->options)) {
					foreach ($this->options as $key => $value) {
						$checked = (in_array($key, $this->value) ? "checked" : null);
						$h .= "<label for='{$id}-{$key}'>
                                <input type='checkbox' id='{$id}-{$key}' {$checked} name='{$this->name}' value='{$key}'>{$value}
                                </label>";
					}
				}
				$h .= "</div>";
			}else{
				$checked = ($this->value == $this->option ? "checked" : null);
				$h .= "<label><input type='checkbox' {$checked} id='{$this->name}' name='{$this->name}' value='{$this->option}' />{$this->optionLabel}</label>";
			}
			return $h;
		}

		/**
		 * Generate Radio field
		 * @return null|string
		 */
		private function radioField() {
			if($this->value === ''){
				$this->value = $this->default;
			}
			$h = null;
			$h .= "<div class='radio-group {$this->alignment}' id='{$this->name}'>";
			if (is_array($this->options) && !empty($this->options)) {
				foreach ($this->options as $key => $value) {
					$checked = ($key == $this->value ? "checked" : null);
					$h .= "<label for='{$this->name}-{$key}'>
                            <input type='radio' id='{$this->name}-{$key}' {$checked} name='{$this->name}' value='{$key}'>{$value}
                            </label>";
				}
			}
			$h .= "</div>";
			return $h;
		}

		private function smartStyle() {
			$h = null;
			$sColor = !empty($this->value['color']) ? $this->value['color'] : null;
            $sHoverColor = !empty($this->value['hover_color']) ? $this->value['hover_color'] : null;
			$sSize = !empty($this->value['size']) ? $this->value['size'] : null;
			$sWeight = !empty($this->value['weight']) ? $this->value['weight'] : null;
			$sAlign = !empty($this->value['align']) ? $this->value['align'] : null;
			$h .= "<div class='rt-multiple-field-container'>";
				// color
				$h .= "<div class='rt-inner-field color rt-col-4'>";
					$h .= "<div class='rt-inner-field-container'>";
						$h .= "<span class='label'>Color</span>";
						$h .= "<input type='text' value='".esc_attr($sColor)."' class='fmp-color' name='{$this->name}[color]'>";
					$h .= "</div>";
				$h .= "</div>";

                // Hover Color
                $h .= "<div class='rt-inner-field hover-color rt-col-4'>";
                $h .= "<div class='rt-inner-field-container'>";
                $h .= "<span class='label'>Hover Color</span>";
                $h .= "<input type='text' value='".esc_attr($sHoverColor)."' class='fmp-color' name='{$this->name}[hover_color]'>";
                $h .= "</div>";
                $h .= "</div>";

				// Font size
				$h .= "<div class='rt-inner-field size rt-col-4'>";
					$h .= "<div class='rt-inner-field-container'>";
						$h .= "<span class='label'>Font size</span>";
						$h .= "<select name='{$this->name}[size]' class='fmp-select2'>";
							$fSizes = $this->scFontSize();
							$h .= "<option value=''>Default</option>";
							foreach ( $fSizes as $size => $label ) {
								$sSlt = ( $size == $sSize ? "selected" : null );
								$h .= "<option value='{$size}' {$sSlt}>{$label}</option>";
							}
						$h .= "</select>";
					$h .= "</div>";
				$h .= "</div>";

				// Weight
				$h .= "<div class='rt-inner-field weight rt-col-4'>";
					$h .= "<div class='rt-inner-field-container'>";
						$h .= "<span class='label'>Weight</span>";
						$h .= "<select name='{$this->name}[weight]' class='fmp-select2'>";
							$h .= "<option value=''>Default</option>";
							$weights = $this->scTextWeight();
							foreach ( $weights as $weight => $label ) {
								$wSlt = ( $weight == $sWeight ? "selected" : null );
								$h .= "<option value='{$weight}' {$wSlt}>{$label}</option>";
							}
						$h .= "</select>";
					$h .= "</div>";
				$h .= "</div>";

				// Alignment
				$h .= "<div class='rt-inner-field alignment rt-col-4'>";
				$h .= "<div class='rt-inner-field-container'>";
				$h .= "<span class='label'>Alignment</span>";
				$h .= "<select name='{$this->name}[align]' class='fmp-select2'>";
				$h .= "<option value=''>Default</option>";
				$aligns = $this->scAlignment();
				foreach ( $aligns as $align => $label ) {
					$aSlt = ( $align == $sAlign ? "selected" : null );
					$h .= "<option value='{$align}' {$aSlt}>{$label}</option>";
				}
				$h .= "</select>";
				$h .= "</div>";
				$h .= "</div>";
			$h .= "</div>";
			return $h;
		}


		private function image() {
			$h = null;
			$h .= "<div class='rt-image-holder'>";
				$h .= "<input type='hidden' name='{$this->name}' value='{$this->value}' id='{$this->name}' class='hidden-image-id' />";
				$img = null;
				$c = "hidden";
				if($id = absint($this->value)){
					$aImg = wp_get_attachment_image_src( $id, 'thumbnail' );
					$img = "<img src='{$aImg[0]}' >";
					$c = null;
				}else{
					$aImg = TLPFoodMenu()->placeholder_img_src();
					$img = "<img src='{$aImg}' >";
				}
				$h .= "<div class='rt-image-preview'>{$img}<span class='dashicons dashicons-plus-alt rtAddImage'></span><span class='dashicons dashicons-trash rtRemoveImage {$c}'></span></div>";
			$h .= "</div>";
			return $h;
		}

		private function imageSize() {
			$width = (!empty($this->value['width']) ? absint($this->value['width']) : null);
			$height = (!empty($this->value['height']) ? absint($this->value['height']) : null);
			$cropV = (!empty($this->value['crop']) ? $this->value['crop'] : 'soft');
			$h = null;
			$h .= "<div class='rt-image-size-holder'>";
				$h .= "<div class='rt-image-size-width rt-image-size'>";
					$h .= "<label>Width</label>";
					$h .= "<input type='number' name='{$this->name}[width]' value='{$width}' />";
				$h .= "</div>";
				$h .= "<div class='rt-image-size-height rt-image-size'>";
					$h .= "<label>Height</label>";
					$h .= "<input type='number' name='{$this->name}[height]' value='{$height}' />";
				$h .= "</div>";
				$h .= "<div class='rt-image-size-crop rt-image-size'>";
					$h .= "<label>Crop</label>";
					$h .= "<select name='{$this->name}[crop]' class='fmp-select2'>";
						$cropList = TLPFoodMenu()->imageCropType();
						foreach ($cropList as $crop => $cropLabel){
							$cSl = ($crop == $cropV ? "selected" : null);
							$h .= "<option value='{$crop}' {$cSl}>{$cropLabel}</option>";
						}
					$h .= "</select>";
				$h .= "</div>";
			$h .= "</div>";
			return $h;
		}

		private function scFontSize() {
			$num = array();
			for ( $i = 10; $i <= 60; $i ++ ) {
				$num[ $i ] = $i . "px";
			}

			return $num;
		}

		private function scAlignment() {
			return array(
				'left'    => "Left",
				'right'   => "Right",
				'center'  => "Center",
				'justify' => "Justify"
			);
		}


		private function scTextWeight() {
			return array(
				'normal'  => "Normal",
				'bold'    => "Bold",
				'bolder'  => "Bolder",
				'lighter' => "Lighter",
				'inherit' => "Inherit",
				'initial' => "Initial",
				'unset'   => "Unset",
				100       => '100',
				200       => '200',
				300       => '300',
				400       => '400',
				500       => '500',
				600       => '600',
				700       => '700',
				800       => '800',
				900       => '900',
			);
		}

		private function meta_exist($post_id = null, $meta_key, $type = "post") {
			if(!$post_id) return false;
			return metadata_exists( $type, $post_id, $meta_key );
		}

	}
endif;