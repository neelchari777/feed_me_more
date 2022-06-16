<?php
if (!class_exists('FmWidget')):

    /**
     *
     */
    class FmWidget extends WP_Widget {

        /**
         * TLP TEAM widget setup
         */
        function __construct() {

            $widget_ops = array('classname' => 'widget_fmp', 'description' => __('Display Food menu', 'tlp-food-menu'));
            parent::__construct('widget_fmp', __('Food Menu', 'tlp-food-menu'), $widget_ops);
        }

        /**
         * display the widgets on the screen.
         */
        function widget($args, $instance) {
            extract($args);
            $id = (!empty($instance['id']) ? absint($instance['id']) : null);
            echo $before_widget;
            if (!empty($instance['title'])) {
                echo $args['before_title'] . apply_filters('widget_title',
                        (isset($instance['title']) ? $instance['title'] : "Food Menu")) . $args['after_title'];
            }
            if (!empty($id)) {
                echo do_shortcode("[foodmenu id='{$id}' ]");
            }
            echo $after_widget;
        }

        function form($instance) {
            $scList = TLPFoodMenu()->get_shortCode_list();
            $defaults = array(
                'title' => __("Food Menu", "tlp-food-menu"),
                'id'    => null
            );

            $instance = wp_parse_args((array) $instance, $defaults); ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:',
                        'tlp-food-menu'); ?></label>
                <input type="text" id="<?php echo $this->get_field_id('title'); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>"
                       style="width:100%;"/></p>

            <p><label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('Select food menu',
                        'tlp-food-menu'); ?></label>
                <select id="<?php echo $this->get_field_id('id'); ?>"
                        name="<?php echo $this->get_field_name('id'); ?>">
                    <option value="">Select one</option>
                    <?php
                    if (!empty($scList)) {
                        foreach ($scList as $scId => $sc) {
                            $selected = ($scId == $instance['id'] ? "selected" : null);
                            echo "<option value='{$scId}' {$selected}>{$sc}</option>";
                        }
                    }
                    ?>
                </select></p>
            <?php
        }

        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
            $instance['id'] = (!empty($new_instance['id'])) ? (int) ($new_instance['id']) : null;
            return $instance;
        }
    }
endif;