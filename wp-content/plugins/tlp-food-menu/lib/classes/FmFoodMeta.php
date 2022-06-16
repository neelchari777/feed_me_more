<?php
if (!class_exists('FmFoodMeta')):

    /**
     *
     */
    class FmFoodMeta {

        function __construct() {

            if (!TLPFoodMenu::hasPro()) {
                add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
                add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_shortcode'));
                add_action('add_meta_boxes', array($this, 'food_menu_meta_boxs'));
                add_action('save_post', array($this, 'save_food_meta_data'), 10, 3);
                add_action('edit_form_after_title', array($this, 'food_menu_after_title'));
                add_action('quick_edit_custom_box', array($this, 'food_menu_add_to_bulk_quick_edit_custom_box'), 10, 2);
                add_action('save_post', array($this, 'food_menu_quick_edit_save'));
                add_action('admin_print_scripts-edit.php', array($this, 'food_menu_enqueue_edit_scripts'));

                add_filter('manage_edit-food-menu_columns', array($this, 'arrange_food_menu_columns'));
                add_action('manage_food-menu_posts_custom_column', array($this, 'manage_food_menu_columns'), 10, 2);
                add_action('restrict_manage_posts', array($this, 'add_taxonomy_filters'));
            }
        }

        function food_menu_enqueue_edit_scripts() {
            wp_enqueue_script( 'food-menu-admin-edit', TLPFoodMenu()->assetsUrl . 'js/quick_edit.js', array(
                'jquery',
                'inline-edit-post'
            ), '', true );
        }

        function food_menu_quick_edit_save( $post_id ) {
            $post = get_post( $post_id );
            // Criteria for not saving: Auto-saves, not post_type_characters, can't edit
            if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || TLPFoodMenu()->post_type != $post->post_type ) {
                return $post_id;
            }

            // RoleType
            if ( $post->post_type != 'revision' ) {
                $price = ( isset( $_POST['_regular_price'] ) ? sprintf( "%.2f",
                    floatval( sanitize_text_field( esc_attr( $_POST['_regular_price'] ) ) ) ) : null );;
                update_post_meta( $post_id, '_regular_price', $price );
            }

            // Sexuality went here

            // Gender went here
        }

        function food_menu_add_to_bulk_quick_edit_custom_box( $column_name, $post_type ) {
            switch ( $post_type ) {
                case TLPFoodMenu()->post_type:

                    switch ( $column_name ) {
                        case 'price':
                            global $post;
                            //$pid = get_the_ID();
                            $price = get_post_meta( $post->ID, '_regular_price', true );
                            ?>
                            <fieldset class="inline-edit-col-right">
                            <div class="inline-edit-group">
                                <label>
                                    <span class="title">Price</span>
                                    <span class="input-text-wrap">
                                            <input type="text" name="_regular_price" class="inline-edit-menu-order-input"
                                                   value="<?php echo $price; ?>"/>
                                        </span>
                                </label>
                            </div>
                            </fieldset><?php
                            break;
                    }
                    break;

            }
        }

        function admin_enqueue_scripts() {
            global $pagenow, $typenow;
            // validate page
            if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php', 'edit.php' ) ) ) {
                return;
            }

            if ( $typenow != TLPFoodMenu()->post_type ) {
                return;
            }

            wp_enqueue_style( array( 'wp-color-picker', 'fm-select2', 'fm-admin' ) );
            wp_enqueue_script( array( 'wp-color-picker', 'fm-select2', 'fm-admin' ) );
            $nonce = wp_create_nonce( TLPFoodMenu()->nonceText() );
            wp_localize_script( 'fm-admin', 'fmp_var',
                array(
                    'nonceID' => TLPFoodMenu()->nonceId(),
                    'nonce'   => $nonce,
                    'ajaxurl' => admin_url( 'admin-ajax.php' )
                )
            );
        }

        function admin_enqueue_scripts_shortcode() {
            global $pagenow, $typenow;
            // validate page
            if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php', 'edit.php' ) ) ) {
                return;
            }

            if ( $typenow != TLPFoodMenu()->getShortCodePT() ) {
                return;
            }

            wp_enqueue_style( array( 'wp-color-picker', 'fm-select2', 'fm-admin', 'fm-frontend' ) );
            wp_enqueue_script( array( 'wp-color-picker', 'fm-select2', 'fm-admin' ) );
            $nonce = wp_create_nonce( TLPFoodMenu()->nonceText() );
            wp_localize_script( 'fm-admin', 'fmp_var',
                array(
                    'nonceID' => TLPFoodMenu()->nonceId(),
                    'nonce'   => $nonce,
                    'ajaxurl' => admin_url( 'admin-ajax.php' )
                )
            );
        }

        function food_menu_after_title( $post ) {
            if ( TLPFoodMenu()->post_type !== $post->post_type ) {
                return;
            }
            $html = null;
            $html .= '<div class="postbox" style="margin-bottom: 0;"><div class="inside">';
            $html .= '<p style="text-align: center;"><a style="color: red; text-decoration: none; font-size: 14px;" href="https://www.radiustheme.com/downloads/food-menu-pro-wordpress/" target="_blank">Please check the pro features</a></p>';
            $html .= '</div></div>';

            echo $html;
        }

        function food_menu_meta_boxs() {
            add_meta_box( 'tlp_food_menu_meta_details', __( 'Food Details', 'tlp-food-menu' ),
                array( $this, 'food_menu_meta_option' ), TLPFoodMenu()->post_type, 'normal', 'high' );
        }

        function food_menu_meta_option( $post ) {
            wp_nonce_field(TLPFoodMenu()->nonceText(), TLPFoodMenu()->nonceId());

            $meta = get_post_meta( $post->ID );
            $price = ! isset( $meta['_regular_price'][0] ) ? '' : $meta['_regular_price'][0];

            ?>
            <table class="form-table">

                <tr>
                    <td class="team_meta_box_td" colspan="2">
                        <label for="price"><?php _e( 'Price', 'tlp-food-menu' ); ?></label>
                    </td>
                    <td colspan="4">
                        <input min="0" step="0.01" type="number" name="_regular_price" id="price" class="tlpfield"
                               value="<?php echo sprintf( "%.2f", $price ); ?>">
                        <p class="description"><?php _e( 'Insert the price, leave blank if it is free',
                                'tlp-food-menu' ); ?></p>
                    </td>
                </tr>
            </table>
            <?php
        }

        function save_food_meta_data( $post_id, $post, $update ) {

            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }


            if ( ! TLPFoodMenu()->verifyNonce() ) {
                return;
            }

            // Check permissions

            if ( TLPFoodMenu()->post_type != $post->post_type ) {
                return;
            }

            $meta['_regular_price'] = ( isset( $_POST['_regular_price'] ) ? sprintf( "%.2f",
                floatval( sanitize_text_field( esc_attr( $_POST['_regular_price'] ) ) ) ) : null );

            foreach ( $meta as $key => $value ) {
                update_post_meta( $post->ID, $key, $value );
            }
        }

        public function arrange_food_menu_columns( $columns ) {
            $column_thumbnail = array( 'thumbnail' => __( 'Image', 'tlp-food-menu' ) );
            $column_price = array( 'price' => __( 'Price', 'tlp-food-menu' ) );
            return array_slice( $columns, 0, 2, true ) + $column_thumbnail + $column_price + array_slice( $columns, 1, null, true );
        }

        public function manage_food_menu_columns( $column ) {
            // global $post;
            switch ( $column ) {
                case 'thumbnail':
                    echo get_the_post_thumbnail( get_the_ID(), array( 100, 100 ) );
                    break;
                case 'price':
                    echo sprintf("%.2f",get_post_meta( get_the_ID(), '_regular_price', true));
                    break;
            }
        }

        public function add_taxonomy_filters() {
            global $typenow;
            // Must set this to the post type you want the filter(s) displayed on
            if ( TLPFoodMenu()->post_type !== $typenow ) {
                return;
            }
            foreach ( TLPFoodMenu()->taxonomies as $tax_slug ) {
                echo $this->build_taxonomy_filter( $tax_slug );
            }
        }

        /**
         * Build an individual dropdown filter.
         *
         * @param  string $tax_slug Taxonomy slug to build filter for.
         *
         * @return string Markup, or empty string if taxonomy has no terms.
         */
        protected function build_taxonomy_filter( $tax_slug ) {
            $terms = get_terms( $tax_slug );
            if ( 0 == count( $terms ) ) {
                return '';
            }
            $tax_name         = $this->get_taxonomy_name_from_slug( $tax_slug );
            $current_tax_slug = isset( $_GET[$tax_slug] ) ? $_GET[$tax_slug] : false;
            $filter  = '<select name="' . esc_attr( $tax_slug ) . '" id="' . esc_attr( $tax_slug ) . '" class="postform">';
            $filter .= '<option value="0">' . esc_html( $tax_name ) .'</option>';
            $filter .= $this->build_term_options( $terms, $current_tax_slug );
            $filter .= '</select>';
            return $filter;
        }

        /**
         * Get the friendly taxonomy name, if given a taxonomy slug.
         *
         * @param  string $tax_slug Taxonomy slug.
         *
         * @return string Friendly name of taxonomy, or empty string if not a valid taxonomy.
         */
        protected function get_taxonomy_name_from_slug( $tax_slug ) {
            $tax_obj = get_taxonomy( $tax_slug );
            if ( ! $tax_obj )
                return '';
            return $tax_obj->labels->name;
        }

        /**
         * Build a series of option elements from an array.
         *
         * Also checks to see if one of the options is selected.
         *
         * @param  array  $terms            Array of term objects.
         * @param  string $current_tax_slug Slug of currently selected term.
         *
         * @return string Markup.
         */
        protected function build_term_options( $terms, $current_tax_slug ) {
            $options = '';
            foreach ( $terms as $term ) {
                $options .= sprintf(
                    "<option value='%s' %s />%s</option>",
                    esc_attr( $term->slug ),
                    selected( $current_tax_slug, $term->slug, false ),
                    esc_html( $term->name . '(' . $term->count . ')' )
                );
                // $options .= selected( $current_tax_slug, $term->slug );
            }
            return $options;
        }

    }

endif;
