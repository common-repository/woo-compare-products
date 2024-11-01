<?php
class wooWcp extends moduleWcp {

	public static $optArr = array();
	public static $isPopupRendered = false;

	public function init() {
		// only for frontend
		if(!is_admin()) {
			// get options
			$optModule = frameWcp::_()->getModule('options');
			$options = $optModule->getAll();

			// check if table Template exists
			if(!empty($options['table']['opts']['table_template']['value'])
				&& in_array($options['table']['opts']['table_template']['value'], array(1, ))
			) {
				// show link on "Single product page"
				if(!empty($options['general']['opts']['show_single_product_page']['value'])) {
					add_action('woocommerce_single_product_summary', array($this, 'compareButtonHandler' ), 35);
				}
				// show link on "Product list page"
				if(!empty($options['general']['opts']['show_list_product_page']['value'])) {
					add_action('woocommerce_after_shop_loop_item', array($this, 'compareButtonHandler' ), 20);
				}
				// draw Popup data
				//not in all themes works?!
				/*if(!empty($options['general']['opts']['show_in_types']['value']) && $options['general']['opts']['show_in_types']['value'] == 'popup') {
					add_action('woocommerce_after_main_content', array($this, 'renderPopupHandler'), 20);
				}*/
			}
		} else {
			if(defined('WCP_DEBUG') && WCP_DEBUG == true) {
				dispatcherWcp::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
			}
		}
	}

	public function compareButtonHandler() {
		global $product;
		if(empty($product)) {
			return '';
		}
		frameWcp::_()->getModule('templates')->loadFontAwesome();
		$optModule = frameWcp::_()->getModule('options');
		$options = $optModule->getAll();
		echo $this->getview()->renderOneButton($options, $product);

		if(!empty($options['general']['opts']['show_in_types']['value']) && $options['general']['opts']['show_in_types']['value'] == 'popup') {
			$this->renderPopupHandler();
		}
		return true;
	}

	public function renderPopupHandler() {
		if(!self::$isPopupRendered) {
			$optModule = frameWcp::_()->getModule('options');
			$options = $optModule->getAll();

			echo $this->getView()->renderPopup($options);
			self::$isPopupRendered = true;
		}
		return true;
	}
	public function addAdminTab($tabs) {
		$tabs['preview'] = array(
			'label' => __('Preview', WCP_LANG_CODE), 'callback' => array($this, 'getSettingsTabContent'), 'fa_icon' => 'fa-wpforms', 'sort_order' => 100,
		);
		return $tabs;
	}

	public function getSettingsTabContent() {
		// its like controller
		return $this->getView()->compareProductsList();
	}
}