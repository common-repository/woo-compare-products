<?php
class templatesWcp extends moduleWcp {
	private $_cdnUrl = '';
	
	public function __construct($d) {
		parent::__construct($d);
		$this->getCdnUrl();	// Init CDN URL
	}
	public function getCdnUrl() {
		if(empty($this->_cdnUrl)) {
			if((int) frameWcp::_()->getModule('options')->get('use_local_cdn')) {
				$uploadsDir = wp_upload_dir( null, false );
				$this->_cdnUrl = $uploadsDir['baseurl']. '/'. WCP_CODE. '/';
				if(uriWcp::isHttps()) {
					$this->_cdnUrl = str_replace('http://', 'https://', $this->_cdnUrl);
				}
				dispatcherWcp::addFilter('externalCdnUrl', array($this, 'modifyExternalToLocalCdn'));
			} else {
				$this->_cdnUrl = /*(uriWcp::isHttps() ? 'https' : 'http'). */'https://woobewoo-14700.kxcdn.com/';
			}
		}
		return $this->_cdnUrl;
	}
	public function modifyExternalToLocalCdn( $url ) {
		$url = str_replace(
			array('https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css'), 
			array(frameWcp::_()->getModule('templates')->getModPath() . 'css'),
			$url);
		return $url;
	}
    public function init() {
        if (is_admin()) {
			if($isAdminPlugOptsPage = frameWcp::_()->isAdminPlugOptsPage()) {
				$this->loadCoreJs();
				$this->loadAdminCoreJs();
				$this->loadCoreCss();
				$this->loadAdminCoreCss();
				$this->loadChosenSelects();
				frameWcp::_()->addScript('adminOptionsWcp', WCP_JS_PATH. 'admin.options.js', array(), false, true);
				add_action('admin_enqueue_scripts', array($this, 'loadMediaScripts'));
				add_action('init', array($this, 'connectAdditionalAdminAssets'));
			}
			// Some common styles - that need to be on all admin pages - be careful with them
			frameWcp::_()->addStyle('supsystic-for-all-admin-'. WCP_CODE, WCP_CSS_PATH. 'supsystic-for-all-admin.css');
		}
        parent::init();
    }
	public function connectAdditionalAdminAssets() {
		if(is_rtl()) {
			frameWcp::_()->addStyle('styleWcp-rtl', WCP_CSS_PATH. 'style-rtl.css');
		}
	}
	public function loadMediaScripts() {
		if(function_exists('wp_enqueue_media')) {
			wp_enqueue_media();
		}
	}
	public function loadAdminCoreJs() {
		frameWcp::_()->addScript('jquery-ui-dialog');
		frameWcp::_()->addScript('jquery-ui-slider');
		frameWcp::_()->addScript('wp-color-picker');
		frameWcp::_()->addScript('icheck', WCP_JS_PATH. 'icheck.min.js');
		$this->loadTooltipster();
	}
	public function loadCoreJs() {
		static $loaded = false;
		if(!$loaded) {
			frameWcp::_()->addScript('jquery');

			frameWcp::_()->addScript('commonWcp', WCP_JS_PATH. 'common.js');
			frameWcp::_()->addScript('coreWcp', WCP_JS_PATH. 'core.js');

			$ajaxurl = admin_url('admin-ajax.php');
			$jsData = array(
				'siteUrl'					=> WCP_SITE_URL,
				'imgPath'					=> WCP_IMG_PATH,
				'cssPath'					=> WCP_CSS_PATH,
				'loader'					=> WCP_LOADER_IMG, 
				'close'						=> WCP_IMG_PATH. 'cross.gif', 
				'ajaxurl'					=> $ajaxurl,
				'options'					=> frameWcp::_()->getModule('options')->getAllowedPublicOptions(),
				'WCP_CODE'					=> WCP_CODE,
				//'ball_loader'				=> WCP_IMG_PATH. 'ajax-loader-ball.gif',
				//'ok_icon'					=> WCP_IMG_PATH. 'ok-icon.png',
				'jsPath'					=> WCP_JS_PATH,
			);
			if(is_admin()) {
				$jsData['isPro'] = frameWcp::_()->getModule('supsystic_promo')->isPro();
			}
			$jsData = dispatcherWcp::applyFilters('jsInitVariables', $jsData);
			frameWcp::_()->addJSVar('coreWcp', 'WCP_DATA', $jsData);
			$loaded = true;
		}
	}
	
	public function loadTooltipster() {
		frameWcp::_()->addScript('tooltipster', frameWcp::_()->getModule('templates')->getModPath(). 'lib/tooltipster/jquery.tooltipster.min.js');
		frameWcp::_()->addStyle('tooltipster', frameWcp::_()->getModule('templates')->getModPath(). 'lib/tooltipster/tooltipster.css');
	}
	public function loadSlimscroll() {
		// Local copy is modified specially for Octo builder
		frameWcp::_()->addScript('jquery.slimscroll', WCP_JS_PATH. 'jquery.slimscroll.js');
		//frameWcp::_()->addScript('jquery.slimscroll', $this->_cdnUrl. 'js/jquery.slimscroll.js');
	}
	public function loadCodemirror() {
		$modPath = frameWcp::_()->getModule('templates')->getModPath();
		frameWcp::_()->addStyle('wcpCodemirror', $modPath. 'lib/codemirror/codemirror.css');
		frameWcp::_()->addStyle('codemirror-addon-hint', $modPath. 'lib/codemirror/addon/hint/show-hint.css');
		frameWcp::_()->addScript('wcpCodemirror', $modPath. 'lib/codemirror/codemirror.js');
		frameWcp::_()->addScript('codemirror-addon-show-hint', $modPath. 'lib/codemirror/addon/hint/show-hint.js');
		frameWcp::_()->addScript('codemirror-addon-xml-hint', $modPath. 'lib/codemirror/addon/hint/xml-hint.js');
		frameWcp::_()->addScript('codemirror-addon-html-hint', $modPath. 'lib/codemirror/addon/hint/html-hint.js');
		frameWcp::_()->addScript('codemirror-mode-xml', $modPath. 'lib/codemirror/mode/xml/xml.js');
		frameWcp::_()->addScript('codemirror-mode-javascript', $modPath. 'lib/codemirror/mode/javascript/javascript.js');
		frameWcp::_()->addScript('codemirror-mode-css', $modPath. 'lib/codemirror/mode/css/css.js');
		frameWcp::_()->addScript('codemirror-mode-htmlmixed', $modPath. 'lib/codemirror/mode/htmlmixed/htmlmixed.js');
	}
	public function loadCoreCss() {
		$styles = array(
			'styleWcp'			=> array('path' => WCP_CSS_PATH. 'style.css', 'for' => 'admin'), 
		);
		foreach($styles as $s => $sInfo) {
			if(!empty($sInfo['path'])) {
				frameWcp::_()->addStyle($s, $sInfo['path']);
			} else {
				frameWcp::_()->addStyle($s);
			}
		}
	}
	public function loadAdminCoreCss() {
		$styles = array(
			'supsystic-uiWcp'	=> array('path' => WCP_CSS_PATH. 'supsystic-ui.css', 'for' => 'admin'), 
			'dashicons'			=> array('for' => 'admin'),
			'bootstrap-alerts'	=> array('path' => WCP_CSS_PATH. 'bootstrap-alerts.css', 'for' => 'admin'),
			'icheck'			=> array('path' => WCP_CSS_PATH. 'jquery.icheck.css', 'for' => 'admin'),
			'wp-color-picker'	=> array('for' => 'admin'),
		);
		foreach($styles as $s => $sInfo) {
			if(!empty($sInfo['path'])) {
				frameWcp::_()->addStyle($s, $sInfo['path']);
			} else {
				frameWcp::_()->addStyle($s);
			}
		}
		$this->loadFontAwesome();
	}
	public function loadJqueryUi() {
		static $loaded = false;
		if(!$loaded) {
			frameWcp::_()->addStyle('jquery-ui', WCP_CSS_PATH. 'jquery-ui.min.css');
			frameWcp::_()->addStyle('jquery-ui.structure', WCP_CSS_PATH. 'jquery-ui.structure.min.css');
			frameWcp::_()->addStyle('jquery-ui.theme', WCP_CSS_PATH. 'jquery-ui.theme.min.css');
			frameWcp::_()->addStyle('jquery-slider', WCP_CSS_PATH. 'jquery-slider.css');
			$loaded = true;
		}
	}
	public function loadJqGrid() {
		static $loaded = false;
		if(!$loaded) {
			$this->loadJqueryUi();
			frameWcp::_()->addScript('jq-grid', frameWcp::_()->getModule('templates')->getModPath(). 'lib/jqgrid/jquery.jqGrid.min.js');
			frameWcp::_()->addStyle('jq-grid', frameWcp::_()->getModule('templates')->getModPath(). 'lib/jqgrid/ui.jqgrid.css');
			$langToLoad = utilsWcp::getLangCode2Letter();
			$availableLocales = array('ar','bg','bg1251','cat','cn','cs','da','de','dk','el','en','es','fa','fi','fr','gl','he','hr','hr1250','hu','id','is','it','ja','kr','lt','mne','nl','no','pl','pt','pt','ro','ru','sk','sr','sr','sv','th','tr','tw','ua','vi');
			if(!in_array($langToLoad, $availableLocales)) {
				$langToLoad = 'en';
			}
			frameWcp::_()->addScript('jq-grid-lang', frameWcp::_()->getModule('templates')->getModPath(). 'lib/jqgrid/i18n/grid.locale-'. $langToLoad. '.js');
			$loaded = true;
		}
	}
	public function loadFontAwesome() {
		frameWcp::_()->addStyle('fontAwesome_v.4.7.0', $this->getModPath() . 'css/font-awesome.min.css');
	}
	public function loadFontAwesomeFromCdn() {
		frameWcp::_()->addStyle('font-awesomeWcp', dispatcherWcp::applyFilters('externalCdnUrl', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'));
	}
	public function loadChosenSelects() {
		frameWcp::_()->addStyle('jquery.chosen', frameWcp::_()->getModule('templates')->getModPath(). 'lib/chosen/chosen.min.css');
		frameWcp::_()->addScript('jquery.chosen', frameWcp::_()->getModule('templates')->getModPath(). 'lib/chosen/chosen.jquery.min.js');
	}
	public function loadDatePicker() {
		frameWcp::_()->addScript('jquery-ui-datepicker');
	}
	public function loadJqplot() {
		static $loaded = false;
		if(!$loaded) {
			$jqplotDir = frameWcp::_()->getModule('templates')->getModPath(). 'lib/jqplot/';

			frameWcp::_()->addStyle('jquery.jqplot', $jqplotDir. 'jquery.jqplot.min.css');

			frameWcp::_()->addScript('jplot', $jqplotDir. 'jquery.jqplot.min.js');
			frameWcp::_()->addScript('jqplot.canvasAxisLabelRenderer', $jqplotDir. 'jqplot.canvasAxisLabelRenderer.min.js');
			frameWcp::_()->addScript('jqplot.canvasTextRenderer', $jqplotDir. 'jqplot.canvasTextRenderer.min.js');
			frameWcp::_()->addScript('jqplot.dateAxisRenderer', $jqplotDir. 'jqplot.dateAxisRenderer.min.js');
			frameWcp::_()->addScript('jqplot.canvasAxisTickRenderer', $jqplotDir. 'jqplot.canvasAxisTickRenderer.min.js');
			frameWcp::_()->addScript('jqplot.highlighter', $jqplotDir. 'jqplot.highlighter.min.js');
			frameWcp::_()->addScript('jqplot.cursor', $jqplotDir. 'jqplot.cursor.min.js');
			frameWcp::_()->addScript('jqplot.barRenderer', $jqplotDir. 'jqplot.barRenderer.min.js');
			frameWcp::_()->addScript('jqplot.categoryAxisRenderer', $jqplotDir. 'jqplot.categoryAxisRenderer.min.js');
			frameWcp::_()->addScript('jqplot.pointLabels', $jqplotDir. 'jqplot.pointLabels.min.js');
			frameWcp::_()->addScript('jqplot.pieRenderer', $jqplotDir. 'jqplot.pieRenderer.min.js');
			$loaded = true;
		}
	}
	public function loadSortable() {
		static $loaded = false;
		if(!$loaded) {
			frameWcp::_()->addScript('jquery-ui-core');
			frameWcp::_()->addScript('jquery-ui-widget');
			frameWcp::_()->addScript('jquery-ui-mouse');

			frameWcp::_()->addScript('jquery-ui-draggable');
			frameWcp::_()->addScript('jquery-ui-sortable');
			$loaded = true;
		}
	}
	public function loadMagicAnims() {
		static $loaded = false;
		if(!$loaded) {
			frameWcp::_()->addStyle('magic.anim', frameWcp::_()->getModule('templates')->getModPath(). 'css/magic.min.css');
			$loaded = true;
		}
	}
	public function loadCssAnims() {
		static $loaded = false;
		if(!$loaded) {
			frameWcp::_()->addStyle('animate.styles', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.4.0/animate.min.css');
			$loaded = true;
		}
	}
	public function loadBootstrapPartial() {
		static $loaded = false;
		if(!$loaded) {
			$this->loadBootstrapPartialOnlyCss();
			frameWcp::_()->addScript('bootstrap', WCP_JS_PATH. 'bootstrap.min.js');
			frameWcp::_()->addStyle('jasny-bootstrap', WCP_CSS_PATH. 'jasny-bootstrap.min.css');
			frameWcp::_()->addScript('jasny-bootstrap', WCP_JS_PATH. 'jasny-bootstrap.min.js');
			$loaded = true;
		}
	}
	public function loadBootstrapPartialOnlyCss() {
		static $loaded = false;
		if(!$loaded) {
			frameWcp::_()->addStyle('bootstrap.partial', frameWcp::_()->getModule('woo')->getAssetsUrl(). 'css/bootstrap.partial.min.css');
			$loaded = true;
		}
	}
	public function connectWpMceEditor() {
		static $loaded = false;
		if(!$loaded) {
			frameWcp::_()->addScript('tiny_mce');
			$loaded = true;
		}
	}
	public function loadSerializeJson() {
		static $loaded = false;
		if(!$loaded) {
			frameWcp::_()->addScript('jquery.serializejson', WCP_JS_PATH. 'jquery.serializejson.min.js');
			$loaded = true;
		}
	}
	public function loadTimePicker() {
		static $loaded = false;
		if(!$loaded) {
			frameWcp::_()->addStyle('jquery.timepicker', WCP_CSS_PATH. 'jquery.timepicker.css');
			frameWcp::_()->addScript('jquery.timepicker', WCP_JS_PATH. 'jquery.timepicker.min.js');
			$loaded = true;
		}
	}
	public function loadDateTimePicker() {
		frameWcp::_()->addScript('jquery-datetimepicker', WCP_JS_PATH . 'datetimepicker/jquery.datetimepicker.min.js');
		frameWcp::_()->addStyle('jquery-datetimepicker', WCP_JS_PATH . 'datetimepicker/jquery.datetimepicker.css');
	}
	public function loadBootstrap() {
		static $loaded = false;
		if(!$loaded) {
			frameWcp::_()->addStyle('bootstrap', frameWcp::_()->getModule('octo')->getAssetsUrl(). 'css/bootstrap.min.css');
			frameWcp::_()->addStyle('bootstrap-theme', frameWcp::_()->getModule('octo')->getAssetsUrl(). 'css/bootstrap-theme.min.css');
			frameWcp::_()->addScript('bootstrap', WCP_JS_PATH. 'bootstrap.min.js');
			
			frameWcp::_()->addStyle('jasny-bootstrap', WCP_CSS_PATH. 'jasny-bootstrap.min.css');
			frameWcp::_()->addScript('jasny-bootstrap', WCP_JS_PATH. 'jasny-bootstrap.min.js');
			$loaded = true;
		}
	}
	public function loadTinyMce() {
		static $loaded = false;
		if(!$loaded) {
			frameWcp::_()->addScript('wcp.tinymce', WCP_JS_PATH. 'tinymce/tinymce.min.js');
			frameWcp::_()->addScript('wcp.jquery.tinymce', WCP_JS_PATH. 'tinymce/jquery.tinymce.min.js');
			$loaded = true;
		}
	}
	public function loadCustomColorpicker() {
		static $loaded = false;
		if(!$loaded) {
			frameWcp::_()->addScript('jquery.colorpicker.spectrum', WCP_JS_PATH. 'jquery.colorpicker/spectrum.js');
			frameWcp::_()->addStyle('jquery.colorpicker.spectrum', WCP_JS_PATH. 'jquery.colorpicker/spectrum.css');
			$loaded = true;
		}
	}
	public function loadCustomBootstrapColorpicker() {
		static $loaded = false;
		if(!$loaded) {
			frameWcp::_()->addScript('oct.colors.script', WCP_JS_PATH. 'colorPicker/color.all.min.js');
			frameWcp::_()->addStyle('oct.colors.style', WCP_JS_PATH. 'colorPicker/color.css');
			
			frameWcp::_()->addScript('jquery.bootstrap.colorpicker.tinycolor', WCP_JS_PATH. 'jquery.bootstrap.colorpicker/tinycolor.js');
			frameWcp::_()->addScript('jquery.bootstrap.colorpicker', WCP_JS_PATH. 'jquery.bootstrap.colorpicker/jquery.colorpickersliders.js');
			frameWcp::_()->addStyle('jquery.bootstrap.colorpicker', WCP_JS_PATH. 'jquery.bootstrap.colorpicker/jquery.colorpickersliders.css');
			$loaded = true;
		}
	}
	public function loadContextMenu() {
		static $loaded = false;
		if(!$loaded) {
			frameWcp::_()->addScript('jquery-ui-position');
			frameWcp::_()->addScript('jquery.contextMenu', WCP_JS_PATH. 'jquery.context-menu/jquery.contextMenu.js');
			frameWcp::_()->addStyle('jquery.contextMenu', WCP_JS_PATH. 'jquery.context-menu/jquery.contextMenu.css');
			$loaded = true;
		}
	}
	/**
	 * Load JS lightbox plugin, for now - this is prettyphoto
	 */
	public function loadLightbox() {
		static $loaded = false;
		if(!$loaded) {
			frameWcp::_()->addScript('prettyphoto', WCP_JS_PATH. 'prettyphoto/js/jquery.prettyPhoto.js');
			frameWcp::_()->addStyle('prettyphoto', WCP_JS_PATH. 'prettyphoto/css/prettyPhoto.css');
			$loaded = true;
		}
	}
	public function loadTooltipstered() {
		frameWcp::_()->addScript('tooltipster', frameWcp::_()->getModule('templates')->getModPath(). 'lib/tooltipster/jquery.tooltipster.min.js');
		frameWcp::_()->addStyle('tooltipster', frameWcp::_()->getModule('templates')->getModPath(). 'lib/tooltipster/tooltipster.css');
		frameWcp::_()->addScript('tooltipsteredWcp', WCP_JS_PATH. 'tooltipstered.js', array('jquery'));
	}
	public function loadBootstrapSimple() {
		static $loaded = false;
		if(!$loaded) {
			frameWcp::_()->addStyle('bootstrap-simple', WCP_CSS_PATH. 'bootstrap-simple.css');
			$loaded = true;
		}
	}
	public function loadGoogleCharts() {
		frameWcp::_()->addScript('google.charts', 'https://www.gstatic.com/charts/loader.js');
	}
	public function loadGoogleFont( $font ) {
		static $loaded = array();
		if(!isset($loaded[ $font ])) {
			frameWcp::_()->addStyle('google.font.'. str_replace(array(' '), '-', $font), 'https://fonts.googleapis.com/css?family='. urlencode($font));
			$loaded[ $font ] = 1;
		}
	}
}
