<?php
/**
 * Uninstall
 *
 * @package Simple NivoSlider
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;
$option_names = array();
$wp_options = $wpdb->get_results(
	"
	SELECT option_name
	FROM {$wpdb->prefix}options
	WHERE option_name LIKE '%%simplenivoslider_%%'
	"
);
foreach ( $wp_options as $wp_option ) {
	$option_names[] = $wp_option->option_name;
}

$apply_meta = 'simplenivoslider_apply';
$delete_meta = $wpdb->get_results(
	"
	SELECT	post_id, meta_value
	FROM	{$wpdb->prefix}postmeta
	WHERE	meta_key
	LIKE	'%%simplenivoslider_apply%%'
	"
);

/* For Single site */
if ( ! is_multisite() ) {
	foreach ( $option_names as $option_name ) {
		delete_option( $option_name );
	}
	foreach ( $delete_meta as $value ) {
		delete_post_meta( $value->post_id, $apply_meta );
	}
} else {
	/* For Multisite */
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->prefix}blogs" );
	$original_blog_id = get_current_blog_id();
	foreach ( $blog_ids as $blogid ) {
		switch_to_blog( $blogid );
		foreach ( $option_names as $option_name ) {
			delete_option( $option_name );
		}
		foreach ( $delete_meta as $value ) {
			delete_post_meta( $value->post_id, $apply_meta );
		}
	}
	switch_to_blog( $original_blog_id );

	/* For site options. */
	foreach ( $option_names as $option_name ) {
		delete_site_option( $option_name );
	}
	foreach ( $delete_meta as $value ) {
		delete_post_meta( $value->post_id, $apply_meta );
	}
}


