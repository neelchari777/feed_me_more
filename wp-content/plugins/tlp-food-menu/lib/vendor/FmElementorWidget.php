<?php

class FmElementorWidget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'food-menu';
    }

    public function get_title() {
        return __( 'Food Menu', 'tlp-food-menu' );
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Food Menu', 'tlp-food-menu' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'food_menu_id',
            array(
                'type'    => \Elementor\Controls_Manager::SELECT2,
                'id'      => 'style',
                'label'   => __( 'Select ShortCode', 'tlp-food-menu' ),
                'options' => TLPFoodMenu()->get_shortCode_list()
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if(isset($settings['food_menu_id']) && !empty($settings['food_menu_id']) && $id = absint($settings['food_menu_id'])){
            echo do_shortcode( '[foodmenu id="' . $id . '"]' );
        } else {
            _e("Please select a food menu shordcode", "tlp-food-menu");
        }
    }
}
