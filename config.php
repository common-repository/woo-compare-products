<?php
    global $wpdb;
    if (!defined('WPLANG') || WPLANG == '') {
        define('WCP_WPLANG', 'en_GB');
    } else {
        define('WCP_WPLANG', WPLANG);
    }
    if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

    define('WCP_PLUG_NAME', basename(dirname(__FILE__)));
    define('WCP_DIR', WP_PLUGIN_DIR. DS. WCP_PLUG_NAME. DS);
    define('WCP_TPL_DIR', WCP_DIR. 'tpl'. DS);
    define('WCP_CLASSES_DIR', WCP_DIR. 'classes'. DS);
    define('WCP_TABLES_DIR', WCP_CLASSES_DIR. 'tables'. DS);
	define('WCP_HELPERS_DIR', WCP_CLASSES_DIR. 'helpers'. DS);
    define('WCP_LANG_DIR', WCP_DIR. 'languages'. DS);
    define('WCP_IMG_DIR', WCP_DIR. 'img'. DS);
    define('WCP_TEMPLATES_DIR', WCP_DIR. 'templates'. DS);
    define('WCP_MODULES_DIR', WCP_DIR. 'modules'. DS);
    define('WCP_FILES_DIR', WCP_DIR. 'files'. DS);
    define('WCP_ADMIN_DIR', ABSPATH. 'wp-admin'. DS);

	define('WCP_PLUGINS_URL', plugins_url());
	define('WCP_SITE_ROOT_URL', get_bloginfo('wpurl'));
    define('WCP_SITE_URL', WCP_SITE_ROOT_URL. '/');
    define('WCP_JS_PATH', WCP_PLUGINS_URL. '/'. WCP_PLUG_NAME. '/js/');
    define('WCP_CSS_PATH', WCP_PLUGINS_URL. '/'. WCP_PLUG_NAME. '/css/');
    define('WCP_IMG_PATH', WCP_PLUGINS_URL. '/'. WCP_PLUG_NAME. '/img/');
    define('WCP_MODULES_PATH', WCP_PLUGINS_URL. '/'. WCP_PLUG_NAME. '/modules/');
    define('WCP_TEMPLATES_PATH', WCP_PLUGINS_URL. '/'. WCP_PLUG_NAME. '/templates/');
    define('WCP_JS_DIR', WCP_DIR. 'js/');

    define('WCP_URL', WCP_SITE_URL);

    define('WCP_LOADER_IMG', WCP_IMG_PATH. 'loading.gif');
	define('WCP_TIME_FORMAT', 'H:i:s');
    define('WCP_DATE_DL', '/');
    define('WCP_DATE_FORMAT', 'm/d/Y');
    define('WCP_DATE_FORMAT_HIS', 'm/d/Y ('. WCP_TIME_FORMAT. ')');
    define('WCP_DATE_FORMAT_JS', 'mm/dd/yy');
    define('WCP_DATE_FORMAT_CONVERT', '%m/%d/%Y');
	define('WCP_DB_DATE_FORMAT', 'Y-m-d H:i:s');
    define('WCP_WPDB_PREF', $wpdb->prefix);
    define('WCP_DB_PREF', 'wcp_');
    define('WCP_MAIN_FILE', 'wcp.php');

    define('WCP_DEFAULT', 'default');
    define('WCP_CURRENT', 'current');
	
	define('WCP_EOL', "\n");
    
    define('WCP_PLUGIN_INSTALLED', true);
    define('WCP_VERSION', '1.2.9');
    define('WCP_USER', 'user');
    
    define('WCP_CLASS_PREFIX', 'wcpc');
    define('WCP_FREE_VERSION', false);
	define('WCP_TEST_MODE', true);
    
    define('WCP_SUCCESS', 'Success');
    define('WCP_FAILED', 'Failed');
	define('WCP_ERRORS', 'wcpErrors');
	
	define('WCP_ADMIN',	'admin');
	define('WCP_LOGGED','logged');
	define('WCP_GUEST',	'guest');
	
	define('WCP_ALL',		'all');
	
	define('WCP_METHODS',		'methods');
	define('WCP_USERLEVELS',	'userlevels');
	/**
	 * Framework instance code
	 */
	define('WCP_CODE', 'wcp');

	define('WCP_LANG_CODE', 'woo-compare-products');
	/**
	 * Plugin name
	 */
	define('WCP_WP_PLUGIN_NAME', 'Woo Compare Products');
	/**
	 * Custom defined for plugin
	 */
	define('WCP_COMMON', 'common');
	define('WCP_FB_LIKE', 'fb_like');
	define('WCP_VIDEO', 'video');
	define('WCP_IFRAME', 'iframe');
	define('WCP_SIMPLE_HTML', 'simple_html');
	define('WCP_PDF', 'pdf');
	define('WCP_AGE_VERIFY', 'age_verify');
	define('WCP_FULL_SCREEN', 'full_screen');
	define('WCP_LOGIN_REGISTER', 'login_register');
	define('WCP_BAR', 'bar');

	// define('WCP_FORM_SHORTCODE', 'woo-compare-products-form');
	// define('WCP_SUBSCRIBERS_PAGE_CONTENT_SHORTCODE', 'subscribers-page');
	/**
	 * Wordpress default subscribers list Unique ID
	 */
	define('WCP_WP_SUB_LIST', 'wpsub');
	/**
	 * Default theme width
	 */
	define('WCP_DEF_WIDTH', 600);
	define('WCP_DEF_WIDTH_UNITS', 'px');

