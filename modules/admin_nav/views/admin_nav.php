<?php
class admin_navViewWcp extends viewWcp {
	public function getBreadcrumbs() {
		$this->assign('breadcrumbsList', dispatcherWcp::applyFilters('mainBreadcrumbs', $this->getModule()->getBreadcrumbsList()));
		return parent::getContent('adminNavBreadcrumbs');
	}
}
