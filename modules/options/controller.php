<?php
class optionsControllerWcp extends controllerWcp {
	public function saveGroup() {
		$res = new responseWcp();
		if($this->getModel()->saveGroup(reqWcp::get('post'))) {
			$res->addMessage(__('Done', WCP_LANG_CODE));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function fullDbUninstall() {
		if(frameWcp::_()->getModule('user')->isAdmin()) {
			installerWcp::delete( true );
		}
	}
	public function getPermissions() {
		return array(
			WCP_USERLEVELS => array(
				WCP_ADMIN => array('saveGroup', 'fullDbUninstall')
			),
		);
	}
}

