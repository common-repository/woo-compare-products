<?php
class optionsWcp extends moduleWcp {
	private $_tabs = array();
	private $_options = array();
	private $_optionsToCategoires = array();	// For faster search

	public function init() {
		//dispatcherWcp::addAction('afterModulesInit', array($this, 'initAllOptValues'));
		add_action('init', array($this, 'initAllOptValues'), 99);	// It should be init after all languages was inited (frame::connectLang)
		dispatcherWcp::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
	}
	public function initAllOptValues() {
		// Just to make sure - that we loaded all default options values
		$this->getAll();
	}
    /**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
    public function get($code) {
        return $this->getModel()->get($code);
    }
	/**
     * This method provides fast access to options model method save
     * @see optionsModel::save($d)
     */
	public function save($optKey, $val, $ignoreDbUpdate = false) {
		return $this->getModel()->save($optKey, $val, $ignoreDbUpdate);
	}
	/**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
	public function isEmpty($code) {
		return $this->getModel()->isEmpty($code);
	}
	public function getAllowedPublicOptions() {
		$allowKeys = array('add_love_link', 'disable_autosave');
		$res = array();
		foreach($allowKeys as $k) {
			$res[ $k ] = $this->get($k);
		}
		return $res;
	}
	public function getAdminPage() {
		if(installerWcp::isUsed()) {
			return $this->getView()->getAdminPage();
		} else {
			installerWcp::setUsed();	// Show this welcome page - only one time
			return frameWcp::_()->getModule('supsystic_promo')->showWelcomePage();
		}
	}
	public function addAdminTab($tabs) {
		$tabs['settings'] = array(
			'label' => __('Settings', WCP_LANG_CODE), 'callback' => array($this, 'getSettingsTabContent'), 'fa_icon' => 'fa-gear', 'sort_order' => 100,
		);
		return $tabs;
	}
	public function getSettingsTabContent() {
		return $this->getView()->getSettingsTabContent();
	}
	public function getTabs() {
		if(empty($this->_tabs)) {
			$this->_tabs = dispatcherWcp::applyFilters('mainAdminTabs', array(
				//'main_page' => array('label' => __('Main Page', WCP_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'wp_icon' => 'dashicons-admin-home', 'sort_order' => 0),
			));
			foreach($this->_tabs as $tabKey => $tab) {
				if(!isset($this->_tabs[ $tabKey ]['url'])) {
					$this->_tabs[ $tabKey ]['url'] = $this->getTabUrl( $tabKey );
				}
			}
			uasort($this->_tabs, array($this, 'sortTabsClb'));
		}
		return $this->_tabs;
	}
	public function sortTabsClb($a, $b) {
		if(isset($a['sort_order']) && isset($b['sort_order'])) {
			if($a['sort_order'] > $b['sort_order'])
				return 1;
			if($a['sort_order'] < $b['sort_order'])
				return -1;
		}
		return 0;
	}
	public function getTab($tabKey) {
		$this->getTabs();
		return isset($this->_tabs[ $tabKey ]) ? $this->_tabs[ $tabKey ] : false;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function getActiveTab() {
		$reqTab = reqWcp::getVar('tab');
		return empty($reqTab) ? 'settings' : $reqTab;
	}
	public function getTabUrl($tab = '', $hash = '') {
		static $mainUrl;
		if(empty($mainUrl)) {
			$mainUrl = frameWcp::_()->getModule('adminmenu')->getMainLink();
		}
		$url = empty($tab) ? $mainUrl : $mainUrl. '&tab='. $tab;
		if(!empty( $hash )) {
			$url .= '#'. $hash;
		}
		return $url;
	}
	public function getRolesList() {
		if(!function_exists('get_editable_roles')) {
			require_once( ABSPATH . '/wp-admin/includes/user.php' );
		}
		return get_editable_roles();
	}
	public function getAvailableUserRolesSelect() {
		$rolesList = $this->getRolesList();
		$rolesListForSelect = array();
		foreach($rolesList as $rKey => $rData) {
			$rolesListForSelect[ $rKey ] = $rData['name'];
		}
		return $rolesListForSelect;
	}
	public function getAll() {
		if(empty($this->_options)) {
			$wooModel = $this->getModel('woo');
			$propertyList = array();
			// $propertyList = $wooModel->getMetaCodesForSelectList();
			$simplePropertyList = $wooModel->getDefaultProductProperties();
			$productPropList = $wooModel->getWooCommerceTaxonomies();
			$propertyList = array_merge_recursive($simplePropertyList, $productPropList);

			$this->_options = dispatcherWcp::applyFilters('optionsDefine', array(
				'general' => array(
					'label' => __('General', WCP_LANG_CODE),
					'opts' => array(

						'button_name' => array(
							'label' => __('Compare Button name', WCP_LANG_CODE),
							'desc' => __('Manage a text for adding a product to table button', WCP_LANG_CODE),
							'def' => __('Compare', WCP_LANG_CODE),
							'html' => 'text',
						),
						'button_name_added' => array(
							'label' => __('Added button name', WCP_LANG_CODE),
							'desc' => __('Manage a text to appear for an already added button', WCP_LANG_CODE),
							'def' => __('Added', WCP_LANG_CODE),
							'html' => 'text',
						),
                        'button_buy_now' => array(
                            'label' => __('Buy button name', WCP_LANG_CODE),
                            'desc' => __('Manage a text to appear for an already buy button', WCP_LANG_CODE),
                            'def' => __('Buy', WCP_LANG_CODE),
                            'html' => 'text',
                        ),
						'show_single_product_page' => array(
							'label' => __('Show on the product page', WCP_LANG_CODE),
							'desc' => __('Check this box, if you want to allow table appearing on the individual product pages', WCP_LANG_CODE),
							'def' => 1,
							'html' => 'checkboxHiddenVal',
						),
						'show_list_product_page' => array(
							'label' => __('Show on products list', WCP_LANG_CODE),
							'desc' => __('Check this box, if you want to allow table appearing on pages with product lists', WCP_LANG_CODE),
							'def' => 1,
							'html' => 'checkboxHiddenVal',
						),
						'show_in_types' => array(
							'label' => __('Display table as', WCP_LANG_CODE),
							'desc' => __('Choose a way for table displaying', WCP_LANG_CODE),
							'def' => 'popup',
							'html' => 'selectbox',
							'options' => array(
								'popup' => __('PopUp', WCP_LANG_CODE),
							),
						),
					),
				),
				'table' => array(
					'label' => __('Table', WCP_LANG_CODE),
					'opts' => array(
						'table_template' => array(
							'label' => __('Table template', WCP_LANG_CODE),
							'desc' => __('Select table preset from the list', WCP_LANG_CODE),
							'def' => 1,
							'html' => 'selectbox',
							'options' => array(
								'1' => 'Template 1',
							),
						),
						'field_list' => array(
							'label' => __('Fields', WCP_LANG_CODE),
							'desc' => __('Select fields to be shown in the comparison table. You can reorder them by dragging an arrow. Note - fields  content is filled automatically from WooCommerce attributes', WCP_LANG_CODE),
							// 'def' => __('', WCP_LANG_CODE),
							'options' => $propertyList,
							'html' => 'draggableCheckboxList',
						),
						'field_list_order' => array(
							'label' => __('', WCP_LANG_CODE),
							// 'desc' => __('', WCP_LANG_CODE),
							'def' => '',
							'html' => 'hidden',
							'specOpts' => array(
								'attrs' => 'class="wcpCblItemOrderInp"',
							),
						),
					),
				),
			));
			$isPro = frameWcp::_()->getModule('supsystic_promo')->isPro();
			foreach($this->_options as $catKey => $cData) {
				foreach($cData['opts'] as $optKey => $opt) {
					$this->_optionsToCategoires[ $optKey ] = $catKey;
					if(isset($opt['pro']) && !$isPro) {
						$this->_options[ $catKey ]['opts'][ $optKey ]['pro'] = frameWcp::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium='. $optKey. '&utm_campaign=popup');
					}
				}
			}
			$this->getModel()->fillInValues( $this->_options );

			// prepare property Order
			if(!empty($this->_options['table']['opts']['field_list']['options'])
				&& is_array($this->_options['table']['opts']['field_list']['options'])
				&& !empty($this->_options['table']['opts']['field_list_order']['value'])
			) {
				$newPropItems = array();
				$propItems = $this->_options['table']['opts']['field_list']['options'];
				$propOrder = explode(';;', $this->_options['table']['opts']['field_list_order']['value']);

				if(count($propOrder)) {
					foreach($propOrder as $keyVal) {
						if(isset($propItems[$keyVal])) {
							$newPropItems[$keyVal] = $propItems[$keyVal];
							unset($propItems[$keyVal]);
						}
					}
					if(count($propItems)) {
						foreach($propItems as $pKey => $pValue) {
							$newPropItems[$pKey] = $pValue;
						}
					}
					$this->_options['table']['opts']['field_list']['options'] = $newPropItems;
				}
			}
		}
		return $this->_options;
	}
	public function getFullCat($cat) {
		$this->getAll();
		return isset($this->_options[ $cat ]) ? $this->_options[ $cat ] : false;
	}
	public function getCatOpts($cat) {
		$opts = $this->getFullCat($cat);
		return $opts ? $opts['opts'] : false;
	}
}
