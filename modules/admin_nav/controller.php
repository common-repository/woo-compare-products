<?php
class admin_navControllerWcp extends controllerWcp {
	public function getPermissions() {
		return array(
			WCP_USERLEVELS => array(
				WCP_ADMIN => array()
			),
		);
	}
}