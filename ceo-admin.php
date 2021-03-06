<?php

// actions
add_action('admin_menu', 'ceo_add_menu_pages');

if (ceo_pluginfo('add_dashboard_frumph_feed_widget'))
	add_action('wp_dashboard_setup', 'ceo_add_dashboard_widgets' );

// add_action( 'admin_notices', 'ceo_test_information' );

// INIT ComicPress Manager pages & hook activation of scripts per page.
function ceo_add_menu_pages() {
	global $pagenow, $post_type;
	
	$menu_location = 'edit.php?post_type=comic';
	$plugin_title = __('Comic Easel', 'comiceasel');
	$config_title = __('Config', 'comiceasel');
	$debug_title = __('Debug', 'comiceasel');
	$import_title = __('Import', 'comiceasel');
	
	// the ceo_pluginfo used here actually initiates it.
	$import_hook = add_submenu_page($menu_location, $plugin_title . ' - ' . $import_title, $import_title, 'edit_theme_options', 'comiceasel-import', 'ceo_import');	
	$config_hook = add_submenu_page($menu_location, $plugin_title . ' - ' . $config_title, $config_title, 'edit_theme_options', 'comiceasel-config', 'ceo_manager_config');
	$debug_hook = add_submenu_page($menu_location, $plugin_title . ' - ' . $debug_title, $debug_title, 'edit_theme_options', 'comiceasel-debug', 'ceo_debug');
	add_action('admin_head-' . $config_hook, 'ceo_admin_page_head');
	add_action('admin_print_scripts-' . $config_hook, 'ceo_admin_print_scripts');
	add_action('admin_print_styles-' . $config_hook, 'ceo_admin_print_styles');
	ceo_enqueue_admin_cpt_style('comic', 'comic-admin-editor-style', ceo_pluginfo('plugin_url').'/css/admin-editor.css');	
}

function ceo_load_scripts_chapter_manager() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-sortable');
}

function ceo_admin_print_scripts() {
	wp_enqueue_script('utils');
	wp_enqueue_script('jquery');
}

function ceo_admin_print_styles() {
	wp_admin_css('css/global');
	wp_admin_css('css/colors');
	wp_admin_css('css/ie');
	wp_enqueue_style('comiceasel-options-style', ceo_pluginfo('plugin_url') . '/css/config.css');
}


function ceo_admin_page_head() { ?>
	<!--[if lt ie 8]> <style> div.show { position: static; margin-top: 1px; } #eadmin div.off { height: 22px; } </style> <![endif]-->
<?php }

// This is done this way to *not* load pages unless they are called, self sufficient code,
// but since attached to the ceo-admin it can use the library in core. so the global functions used in multiple areas
// go into the ceo-admin.php file, while local functions that are only run on the individual pages go on those pages
// the "forms" if there are any call the same page back up. - phil

function ceo_manager_config() {
	require_once('ceo-config.php');
}

function ceo_debug() {
	require_once('ceo-debug.php');
}

function ceo_import() {
	require_once('ceo-import.php');
}

/**
 * This set of functions is for displaying the dashboard feed widget.
 *
 */
function ceo_dashboard_feed_widget() {
	wp_widget_rss_output('http://comiceasel.com/?feed=rss2', array('items' => 3, 'show_summary' => true));
} 

function ceo_add_dashboard_widgets() {
	wp_add_dashboard_widget('ceo_dashboard_widget', 'Comic Easel News', 'ceo_dashboard_feed_widget');	
}

function ceo_enqueue_admin_cpt_style( $cpt, $handle, $src = false, $deps = array(), $ver = false, $media = 'all' ) {
 
	/* Check the admin page we are on. */
	global $pagenow;
 
	/* Default to null to prevent enqueuing. */
	$enqueue = null;
 
	/* Enqueue style only if we are on the correct CPT editor page. */
	if ( isset($_GET['post_type']) && $_GET['post_type'] == $cpt && $pagenow == "post-new.php" ) {
		$enqueue = true;
	}
 
	/* Enqueue style only if we are on the correct CPT editor page. */
	if ( isset($_GET['post']) && $pagenow == "post.php" ) {
		$post_id = $_GET['post'];
		$post_obj = get_post( $post_id );
		if( $post_obj->post_type == $cpt )
			$enqueue = true;
	}
 
	/* Only enqueue if editor page is the correct CPT. */
	if( $enqueue )
		wp_enqueue_style( $handle, $src, $deps, $ver, $media );
}

?>