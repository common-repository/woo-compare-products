<?php
class supsystic_promoControllerWcp extends controllerWcp {
    public function welcomePageSaveInfo() {
		$res = new responseWcp();
		installerWcp::setUsed();
		if($this->getModel()->welcomePageSaveInfo(reqWcp::get('get'))) {
			$res->addMessage(__('Information was saved. Thank you!', WCP_LANG_CODE));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		$originalPage = reqWcp::getVar('original_page');
		$http = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
		if(strpos($originalPage, $http. $_SERVER['HTTP_HOST']) !== 0) {
			$originalPage = '';
		}
		redirectWcp($originalPage);
	}
	public function sendContact() {
		$res = new responseWcp();
		$time = time();
		$prevSendTime = (int) get_option(WCP_CODE. '_last__time_contact_send');
		if($prevSendTime && ($time - $prevSendTime) < 5 * 60) {	// Only one message per five minutes
			$res->pushError(__('Please don\'t send contact requests so often - wait for response for your previous requests.'));
			$res->ajaxExec();
		}
        $data = reqWcp::get('post');
        $fields = $this->getModule()->getContactFormFields();
		foreach($fields as $fName => $fData) {
			$validate = isset($fData['validate']) ? $fData['validate'] : false;
			$data[ $fName ] = isset($data[ $fName ]) ? trim($data[ $fName ]) : '';
			if($validate) {
				$error = '';
				foreach($validate as $v) {
					if(!empty($error))
						break;
					switch($v) {
						case 'notEmpty':
							if(empty($data[ $fName ])) {
								$error = $fData['html'] == 'selectbox' ? __('Please select %s', WCP_LANG_CODE) : __('Please enter %s', WCP_LANG_CODE);
								$error = sprintf($error, $fData['label']);
							}
							break;
						case 'email':
							if(!is_email($data[ $fName ])) 
								$error = __('Please enter valid email address', WCP_LANG_CODE);
							break;
					}
					if(!empty($error)) {
						$res->pushError($error, $fName);
					}
				}
			}
		}
		if(!$res->error()) {
			$msg = 'Message from: '. get_bloginfo('name').', Host: '. $_SERVER['HTTP_HOST']. '<br />';
			$msg .= 'Plugin: '. WCP_WP_PLUGIN_NAME. '<br />';
			foreach($fields as $fName => $fData) {
				if(in_array($fName, array('name', 'email', 'subject'))) continue;
				if($fName == 'category')
					$data[ $fName ] = $fData['options'][ $data[ $fName ] ];
                $msg .= '<b>'. $fData['label']. '</b>: '. nl2br($data[ $fName ]). '<br />';
            }
			if(frameWcp::_()->getModule('mail')->send('support@supsystic.zendesk.com', $data['subject'], $msg, array(
				'from_name' => $data['name'], 
				'from_email' => $data['email'],
			))) {
				update_option(WCP_CODE. '_last__time_contact_send', $time);
			} else {
				$res->pushError( frameWcp::_()->getModule('mail')->getMailErrors() );
			}
			
		}
        $res->ajaxExec();
	}
	public function addNoticeAction() {
		$res = new responseWcp();
		$code = reqWcp::getVar('code', 'post');
		$choice = reqWcp::getVar('choice', 'post');
		if(!empty($code) && !empty($choice)) {
			$optModel = frameWcp::_()->getModule('options')->getModel();
			switch($choice) {
				case 'hide':
					$optModel->save('hide_'. $code, 1);
					break;
				case 'later':
					$optModel->save('later_'. $code, time());
					break;
				case 'done':
					$optModel->save('done_'. $code, 1);
					if($code == 'enb_promo_link_msg') {
						$optModel->save('add_love_link', 1);
					}
					break;
			}
			$this->getModel()->saveUsageStat($code. '.'. $choice, true);
			$this->getModel()->checkAndSend( true );
		}
		$res->ajaxExec();
	}
	public function addTourStep() {
		$res = new responseWcp();
		if($this->getModel()->addTourStep(reqWcp::get('post'))) {
			$res->addMessage(__('Information was saved. Thank you!', WCP_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function closeTour() {
		$res = new responseWcp();
		if($this->getModel()->closeTour(reqWcp::get('post'))) {
			$res->addMessage(__('Information was saved. Thank you!', WCP_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function addTourFinish() {
		$res = new responseWcp();
		if($this->getModel()->addTourFinish(reqWcp::get('post'))) {
			$res->addMessage(__('Information was saved. Thank you!', WCP_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			WCP_USERLEVELS => array(
				WCP_ADMIN => array('welcomePageSaveInfo', 'sendContact', 'addNoticeAction', 
					'addStep', 'closeTour', 'addTourFinish')
			),
		);
	}
}