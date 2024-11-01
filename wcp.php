<?php
/**
 * Plugin Name: Woo Compare Products
 * Description: Compare products
 * Version: 1.2.9
 * Author: woobewoo.com
 * Author URI: https://woobewoo.com
 * WC requires at least: 3.4.0
 * WC tested up to: 4.3.1
 * Text Domain: woo-compare-products
 * Domain Path: /languages
 **/
	/**
	 * Base config constants and functions
	 */
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'config.php');
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'functions.php');
	/**
	 * Connect all required core classes
	 */
    importClassWcp('dbWcp');
    importClassWcp('installerWcp');
    importClassWcp('baseObjectWcp');
    importClassWcp('moduleWcp');
	importClassWcp('moduleWidgetWcp');
    importClassWcp('modelWcp');
    importClassWcp('viewWcp');
    importClassWcp('controllerWcp');
    importClassWcp('helperWcp');
    importClassWcp('dispatcherWcp');
    importClassWcp('fieldWcp');
    importClassWcp('tableWcp');
    importClassWcp('frameWcp');
	/**
	 * @deprecated since version 1.0.1
	 */
    importClassWcp('langWcp');
    importClassWcp('reqWcp');
    importClassWcp('uriWcp');
    importClassWcp('htmlWcp');
    importClassWcp('responseWcp');
    importClassWcp('fieldAdapterWcp');
    importClassWcp('validatorWcp');
    importClassWcp('errorsWcp');
    importClassWcp('utilsWcp');
    importClassWcp('modInstallerWcp');
	importClassWcp('installerDbUpdaterWcp');
	importClassWcp('dateWcp');
	/**
	 * Check plugin version - maybe we need to update database, and check global errors in request
	 */
    installerWcp::update();
    errorsWcp::init();
    /**
	 * Start application
	 */
    frameWcp::_()->parseRoute();
    frameWcp::_()->init();
    frameWcp::_()->exec();
	
	//var_dump(frameWcp::_()->getActivationErrors()); exit();
