<?php
class supsystic_promoViewWcp extends viewWcp {
    public function displayAdminFooter() {
        parent::display('adminFooter');
    }
	public function showAdditionalmainAdminShowOnOptions($newsletters) {
		$this->assign('promoLink', $this->getModule()->generateMainLink('utm_source=plugin&utm_medium=onexit&utm_campaign=newsletters'));
		parent::display('additionalmainAdminShowOnOptions');
	}
	public function getOverviewTabContent() {
		frameWcp::_()->getModule('templates')->loadJqueryUi();
		
		frameWcp::_()->getModule('templates')->loadSlimscroll();
		frameWcp::_()->addScript('admin.overview', $this->getModule()->getModPath(). 'js/admin.overview.js');
		frameWcp::_()->addStyle('admin.overview', $this->getModule()->getModPath(). 'css/admin.overview.css');
		$this->assign('mainLink', $this->getModule()->getMainLink());
		$this->assign('faqList', $this->getFaqList());
		$this->assign('serverSettings', $this->getServerSettings());
		$this->assign('news', $this->getNewsContent());
		$this->assign('contactFields', $this->getModule()->getContactFormFields());
		return parent::getContent('overviewTabContent');
	}
	public function getFaqList() {
		return array(
			__('Why Form by Supsystic is "must have" for your website?', WCP_LANG_CODE) 
				=> sprintf(__('Increase your sales by 500%% using Form by Supsystic! More subscribers = more sales! It\'s that simple!<br />More info you can find here <a target="_blank" href="%s">Form by Supsystic is "must have" for your website</a>', WCP_LANG_CODE), 'http://woobewoo.com/why-newsletters-by-supsystic-is-must-have-for-your-website/'),
			__('What is A/B testing?', WCP_LANG_CODE) 
				=> sprintf(__('A/B testing is one of the easiest ways to increase conversion rates and learn more about your audience!<br />A/B test in Form plugin involves testing two or more versions of a newsletters window - an A version (original) and a B versions (the variation) - with live traffic and measuring the effect each version has on your conversion rate.<br />To know more detail – click <a target="_blank" href="%s">here</a>', WCP_LANG_CODE), 'http://woobewoo.com/what-is-ab-testing/'),
			__('How to enable subscription to Aweber?', WCP_LANG_CODE)
				=> sprintf(__('In order to subscribe to Aweber you need to know unique list id of your aweber account - check this <a target="_blank" href="%s">page</a> for more details.', WCP_LANG_CODE), 'http://woobewoo.com/what-is-the-unique-list-id/'),
			__('How to subscribe to MailChimp?', WCP_LANG_CODE)
				=> __('To subscribe to MailChimp you need enter your MailChimp API key and name of list for subscription. To find your MailChimp API key - follow the instructions below:<br />
				1. Login to your mailchimp account at http://mailchimp.com<br />
				2. From the left main menu, click on your Username, then select "Account" in the flyout menu.<br />
				3. From the account page select "Extras" -> "API Keys".<br />
				4. Your API Key will be listed in the table labeled "Your API Keys".<br />
				5. Copy / Paste your API key into "MailChimp API key" field in Form edit screen -> Subscribe section.', WCP_LANG_CODE),
			__('Where to find css code for the pop-up window?', WCP_LANG_CODE)
				=> __('With Form by Supsystic you can edit CSS style directly from the plugin. <br />
				In WordPress admin area - 
go to Form by Supsystic -> choose a newsletters, what you need -> click Code tab. <br />
Here you can edit css style of the pop-up window.', WCP_LANG_CODE),
			__('How to get PRO version of plugin for FREE?', WCP_LANG_CODE)
				=> sprintf(__('You have an incredible opportunity to get PRO version for free. Make Translation of the plugin! It will be amazing if you take advantage of this offer!<br />
					More info you can find here <a target="_blank" href="%s">“Get PRO version of any plugin for FREE”</a>', WCP_LANG_CODE), 'http://woobewoo.com/get-pro-version-of-any-plugin-for-free/'),
			__('Translation', WCP_LANG_CODE)
				=> sprintf(__('All available languages are provided with the Supsystic Form plugin. If your language isn’t available, your plugin will be in English by default.<br />
					Available Translations: English, French, German, Spanish, Russian<br />
					Translate or update a translation Form WordPress plugin in your language and get a Premium license for FREE. <a target="_blank" href="%s">Contact us.</a>', WCP_LANG_CODE), $this->getModule()->getContactLink()),
		);
	}
	public function getMostFaqList() {
		return array(
			__("Where's my subscribers?", WCP_LANG_CODE) 
				=> sprintf(__("By default all subscribers add to the WordPress. 
					To find your subscribers go to Users tab on the left navigation menu of WordPress admin area. 
					Also available subscription to the Aweber, MailChimp, MailPoet <a href='%s' target='_blank'>and other</a>. 
					If you want to add another subscription service - just <a href='%s' target='_blank'>contact us</a> and provide URL of the subscription service.", WCP_LANG_CODE), $this->getModule()->getMainLink(). '#subscribe-to-email-newsletters-settings', $this->getModule()->getContactLink()),
			__("Form doesn't appear on the website", WCP_LANG_CODE) 
				=> sprintf(__("If you setup you're Form properly, and it still doesn't show on the page - there are can be conflict with your WordPress theme or other plugins. <a href='%s' target='_blank'>Contact us</a> with the URL of the webpage you add newsletters and screenshots / text of the error messages, if you have one - and we will help you resolve your issue.", WCP_LANG_CODE), $this->getModule()->getContactLink()),
		);
	}
	public function getNewsContent() {
		$getData = wp_remote_get('http://woobewoo.com/news/main.html');
		$content = '';
		if($getData 
			&& is_array($getData) 
			&& isset($getData['response']) 
			&& isset($getData['response']['code']) 
			&& $getData['response']['code'] == 200
			&& isset($getData['body'])
			&& !empty($getData['body'])
		) {
			$content = $getData['body'];
		} else {
			$content = sprintf(__('There were some problems while trying to retrieve our news, but you can always check all list <a target="_blank" href="%s">here</a>.', WCP_LANG_CODE), 'http://woobewoo.com/news');
		}
		return $content;
	}
	public function getServerSettings() {
		return array(
			'Operating System' => array('value' => PHP_OS),
            'PHP Version' => array('value' => PHP_VERSION),
            'Server Software' => array('value' => $_SERVER['SERVER_SOFTWARE']),
            'MySQL' => array('value' => function_exists('mysql_get_server_info') ? @mysql_get_server_info() : __('Undefined', WCP_LANG_CODE)),
            'PHP Safe Mode' => array('value' => ini_get('safe_mode') ? __('Yes', WCP_LANG_CODE) : __('No', WCP_LANG_CODE), 'error' => ini_get('safe_mode')),
            'PHP Allow URL Fopen' => array('value' => ini_get('allow_url_fopen') ? __('Yes', WCP_LANG_CODE) : __('No', WCP_LANG_CODE)),
            'PHP Memory Limit' => array('value' => ini_get('memory_limit')),
            'PHP Max Post Size' => array('value' => ini_get('post_max_size')),
            'PHP Max Upload Filesize' => array('value' => ini_get('upload_max_filesize')),
            'PHP Max Script Execute Time' => array('value' => ini_get('max_execution_time')),
            'PHP EXIF Support' => array('value' => extension_loaded('exif') ? __('Yes', WCP_LANG_CODE) : __('No', WCP_LANG_CODE)),
            'PHP EXIF Version' => array('value' => phpversion('exif')),
            'PHP XML Support' => array('value' => extension_loaded('libxml') ? __('Yes', WCP_LANG_CODE) : __('No', WCP_LANG_CODE), 'error' => !extension_loaded('libxml')),
            'PHP CURL Support' => array('value' => extension_loaded('curl') ? __('Yes', WCP_LANG_CODE) : __('No', WCP_LANG_CODE), 'error' => !extension_loaded('curl')),
		);
	}
	public function getLayeredStylePromo() {
		$this->assign('promoLink', $this->getModule()->generateMainLink('utm_source=plugin&utm_medium=layered&utm_campaign=newsletters'));
		return parent::getContent('layeredStylePromo');
	}
	public function showWelcomePage() {
		frameWcp::_()->getModule('templates')->loadJqueryUi();
		frameWcp::_()->addStyle('sup.bootstrap', frameWcp::_()->getModule('newsletters')->getAssetsUrl(). 'css/bootstrap.partial.min.css'/*'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'*/);
		frameWcp::_()->addStyle('admin.welcome', $this->getModule()->getModPath(). 'css/admin.welcome.css');
		$createNewLink = frameWcp::_()->getModule('options')->getTabUrl('newsletters_add_new');
		$goToAdminLink = frameWcp::_()->getModule('options')->getTabUrl('newsletters');
		$skipTutorLink = uriWcp::_(array('baseUrl' => $goToAdminLink, 'skip_tutorial' => 1));
		$this->assign('createNewLink', $this->_makeWelcomeLink( $createNewLink ));
		$this->assign('skipTutorLink', $this->_makeWelcomeLink( $skipTutorLink ));
		$this->assign('faqList', $this->getMostFaqList());
		$this->assign('mainLink', $this->getModule()->getMainLink());
		parent::display('welcomePage');
	}
	private function _makeWelcomeLink($link) {
		return uriWcp::_(array('baseUrl' => $link, 'from' => 'welcome-page', 'pl' => WCP_CODE));
	}
	public function getTourHtml() {
		$this->assign('contactFormLink', $this->getModule()->getContactLink());
		$this->assign('finishSiteLink', $this->getModule()->generateMainLink('utm_source=plugin&utm_medium=final_step_b_step&utm_campaign=newsletters'));
		return parent::getContent('adminTour');
	}
}
