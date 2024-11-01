<?php
class installerWcp {
	static public $update_to_version_method = '';
	static private $_firstTimeActivated = false;
	static public function init( $isUpdate = false ) {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$current_version = get_option($wpPrefix. WCP_DB_PREF. 'db_version', 0);
		if(!$current_version)
			self::$_firstTimeActivated = true;
		/**
		 * modules 
		 */
		if (!dbWcp::exist("@__modules")) {
			dbDelta(dbWcp::prepareQuery("CREATE TABLE IF NOT EXISTS `@__modules` (
			  `id` smallint(3) NOT NULL AUTO_INCREMENT,
			  `code` varchar(32) NOT NULL,
			  `active` tinyint(1) NOT NULL DEFAULT '0',
			  `type_id` tinyint(1) NOT NULL DEFAULT '0',
			  `label` varchar(64) DEFAULT NULL,
			  `ex_plug_dir` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE INDEX `code` (`code`)
			) DEFAULT CHARSET=utf8;"));
			dbWcp::query("INSERT INTO `@__modules` (id, code, active, type_id, label) VALUES
				(NULL, 'adminmenu',1,1,'Admin Menu'),
				(NULL, 'options',1,1,'Options'),
				(NULL, 'user',1,1,'Users'),
				(NULL, 'pages',1,1,'Pages'),
				(NULL, 'templates',1,1,'templates'),
				(NULL, 'supsystic_promo',1,1,'supsystic_promo'),
				(NULL, 'woo', 1,1, 'woo'),
				(NULL, 'admin_nav',1,1,'admin_nav');");
		}

		/**
		 *  modules_type 
		 */
		if(!dbWcp::exist("@__modules_type")) {
			dbDelta(dbWcp::prepareQuery("CREATE TABLE IF NOT EXISTS `@__modules_type` (
			  `id` smallint(3) NOT NULL AUTO_INCREMENT,
			  `label` varchar(32) NOT NULL,
			  PRIMARY KEY (`id`)
			) AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;"));
			dbWcp::query("INSERT INTO `@__modules_type` VALUES
				(1,'system'),
				(6,'addons');");
		}
		/**
		 * Plugin usage statistics
		 */
		if(!dbWcp::exist("@__usage_stat")) {
			dbDelta(dbWcp::prepareQuery("CREATE TABLE `@__usage_stat` (
			  `id` INT(11) NOT NULL AUTO_INCREMENT,
			  `code` varchar(64) NOT NULL,
			  `visits` INT(11) NOT NULL DEFAULT '0',
			  `spent_time` INT(11) NOT NULL DEFAULT '0',
			  `modify_timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  UNIQUE INDEX `code` (`code`),
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8"));
			dbWcp::query("INSERT INTO `@__usage_stat` (code, visits) VALUES ('installed', 1)");
		}

		/**
		 * Insert default settings
		 */
		$optData = get_option(WCP_CODE. '_opts_data');
		if(!$optData) {
			update_option(WCP_CODE. '_opts_data', self::getDefaultSettings());
		}

		installerDbUpdaterWcp::runUpdate();
		if($current_version && !self::$_firstTimeActivated) {
			self::setUsed();
			// For users that just updated our plugin - don't need tp show step-by-step tutorial
			update_user_meta(get_current_user_id(), WCP_CODE . '-tour-hst', array('closed' => 1));
		}
		update_option($wpPrefix. WCP_DB_PREF. 'db_version', WCP_VERSION);
		add_option($wpPrefix. WCP_DB_PREF. 'db_installed', 1);
	}
	static public function setUsed() {
		update_option(WCP_DB_PREF. 'plug_was_used', 1);
	}
	static public function isUsed() {
		return true;	// No welcome page for now
		//return 0;
		return (int) get_option(WCP_DB_PREF. 'plug_was_used');
	}
	static public function delete( $full = false ) {
		self::_checkSendStat('delete');
		global $wpdb;
		$wpPrefix = $wpdb->prefix;
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.WCP_DB_PREF."modules`");
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.WCP_DB_PREF."modules_type`");
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.WCP_DB_PREF."usage_stat`");

		if($full) {
			delete_option(WCP_CODE. '_opts_data');
		}
		delete_option($wpPrefix. WCP_DB_PREF. 'db_version');
		delete_option($wpPrefix. WCP_DB_PREF. 'db_installed');
	}
	static public function deactivate() {
		self::_checkSendStat('deactivate');
	}
	static private function _checkSendStat($statCode) {
		if(class_exists('frameWcp')
			&& frameWcp::_()->getModule('supsystic_promo')
			&& frameWcp::_()->getModule('options')
		) {
			frameWcp::_()->getModule('supsystic_promo')->getModel()->saveUsageStat( $statCode );
			frameWcp::_()->getModule('supsystic_promo')->getModel()->checkAndSend( true );
		}
	}
	static public function update() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		$currentVersion = get_option($wpPrefix. WCP_DB_PREF. 'db_version', 0);
		if(!$currentVersion || version_compare(WCP_VERSION, $currentVersion, '>')) {
			self::init( true );
			update_option($wpPrefix. WCP_DB_PREF. 'db_version', WCP_VERSION);
		}
	}

	static public function installDataByUid($tbl, $uid, $data, $useBaseCol = false) {
		$baseColCond = $useBaseCol ? 'AND is_base = 1' : '';
		$id = (int) dbWcp::get("SELECT id FROM $tbl WHERE unique_id = '$uid' AND original_id = 0 $baseColCond", 'one');
		$action = $id ? 'UPDATE' : 'INSERT INTO';
		$values = array();
		foreach($data as $k => $v) {
			$values[] = "$k = \"$v\"";
		}
		$valuesStr = implode(',', $values);
		$query = "$action $tbl SET $valuesStr";
		if($action == 'UPDATE')
			$query .= " WHERE unique_id = '$uid' AND original_id = 0 $baseColCond";
		if(dbWcp::query($query)) {
			return $action == 'UPDATE' ? $id : dbWcp::insertID();
		}
		return false;
	}

	static public function getDefaultSettings() {
		return array(
			'button_name' => array(
				'value' => __('Compare', WCP_LANG_CODE),
			),
			'button_name_added' => array(
				'value' => __('Added', WCP_LANG_CODE),
			),
            'button_buy_now' => array(
                'value' => __('Buy', WCP_LANG_CODE),
            ),
			'show_single_product_page' => array(
				'value' => 1,
			),
			'show_list_product_page' => array(
				'value' => 1,
			),
			'show_in_types' => array(
				'value' => 'popup',
			),
			'table_template' => array(
				'value' => 1,
			),
			'field_list' => array(
				'value' => array(
					'title', 'price', 'image',
				),
			),
			'field_list_order' => array(
				'value' => 'title;;price;;image',
			),
		);
	}
}
