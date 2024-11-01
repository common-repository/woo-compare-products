<?php
class wooViewWcp extends viewWcp {

	public function renderOneButton($options, $product) {
		$notAddedButtonText = $this->translate('Compare');
		$addedButtonText = $this->translate('Added');
		$showInType = 'popup';
		$productId = $product->get_id();

		if(!empty($options['general']['opts']['button_name']['value'])) {
			$notAddedButtonText = $options['general']['opts']['button_name']['value'];
		}
		if(!empty($options['general']['opts']['button_name_added']['value'])) {
			$addedButtonText = $options['general']['opts']['button_name_added']['value'];
		}
		if(!empty($options['general']['opts']['show_in_types']['value'])) {
			$showInType = $options['general']['opts']['show_in_types']['value'];
		}
		$cplModel = $this->getModel('cookieProductList');
		$cookieProductIdList = $cplModel->getProductListFromCookie();
		$isCurrentButtonClicked = in_array($productId, $cookieProductIdList);

		$this->assign('notAddedButtonText', $notAddedButtonText);
		$this->assign('addedButtonText', $addedButtonText);
		$this->assign('showInType', $showInType);
		$this->assign('productId', $productId);
		$this->assign('isProductAdded', $isCurrentButtonClicked);

		frameWcp::_()->addScript('frontend.wcp.compare',$this->getModule()->getModPath(). 'assets/js/frontend.wcp.compare.js', array('jquery-ui-dialog'));
		frameWcp::_()->addStyle('wp-jquery-ui-dialog');
		frameWcp::_()->addStyle('frontend.wcp.compare', $this->getModule()->getModPath() . 'assets/css/frontend.wcp.compare.css');
		frameWcp::_()->addJSVar('frontend.wcp.compare', 'wcpAjaxUrl', admin_url('admin-ajax.php'));

		return parent::getContent('frontendButton');
	}

	public function renderPopup($options) {

		// add css file
		$tableTemplate = 1;
		if(!empty($options['table']['opts']['table_template']['value'])) {
			$tableTemplate = $options['table']['opts']['table_template']['value'];
		}
		$themeName = 'theme' . $tableTemplate;
		frameWcp::_()->addStyle('wcpThemedTable',$this->getModule()->getModPath() . 'assets/css/themes/' . $themeName . '.css');
		return parent::getContent('frontendPopup');
	}

	public function compareProductsList() {

		$tableTemplate = 1;
		$fieldList = array();
		$fieldOrder = array();

		$optModule = frameWcp::_()->getModule('options');
		$wooModel = $optModule->getModel('woo');
		$cplModel = $this->getModel('cookieProductList');

		$cookieProductIdList = $cplModel->getProductListFromCookie();
		$options = $optModule->getAll();

        $buyNowButtonText = $this->translate('Buy Now');
        if(!empty($options['general']['opts']['button_buy_now']['value'])) {
            $buyNowButtonText = $options['general']['opts']['button_buy_now']['value'];
        }

		if(!empty($options['table']['opts']['table_template']['value'])) {
			$tableTemplate = $options['table']['opts']['table_template']['value'];
		}
		if(!empty($options['table']['opts']['field_list']['options'])) {
			$fieldList = $options['table']['opts']['field_list']['options'];
		}
		if(!empty($options['table']['opts']['field_list_order']['value'])) {
			$fieldOrder = explode(';;', $options['table']['opts']['field_list_order']['value']);
		}

		$themeName = 'theme' . $tableTemplate;
		$preparedProducts = $wooModel->getProductsByIds($cookieProductIdList, $fieldList, $fieldOrder);

		require WCP_MODULES_DIR . 'woo/models/Themes/themesBuilder.php';
		require WCP_MODULES_DIR . 'woo/models/Themes/' . $themeName . 'Builder.php';

		$themeBuilder = new Theme1Builder($preparedProducts, $fieldOrder, $fieldList, $themeName, $buyNowButtonText);
		$this->assign('themeBuilder', $themeBuilder);
		// used for testing in admin page
		if(defined('WCP_DEBUG') && WCP_DEBUG == true && is_admin()) {
			frameWcp::_()->addStyle('wcpThemedTable',$this->getModule()->getModPath() . 'assets/css/themes/' . $themeName . '.css');
			frameWcp::_()->addStyle('wcpBackendTable',$this->getModule()->getModPath() . 'assets/css/backend.wcp.compare.css');
		}

		return parent::getContent('frontentTable');
	}
}