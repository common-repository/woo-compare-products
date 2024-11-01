<?php
class wooControllerWcp extends controllerWcp {

	public function addCompareProductId() {
		$res = new responseWcp();

		$productId = (int) reqWcp::getVar('productId');
		if($productId) {
			$cplModel = $this->getModel('cookieProductList');
			$cplModel->addProductIdToCookie($productId);
			$res->addData('success', 1);
		}
		return $res->ajaxExec();
	}

	public function removeCompareProductById() {
		$res = new responseWcp();
		$productId = (int) reqWcp::getVar('productId');
		if($productId) {
			$cplModel = $this->getModel('cookieProductList');
			$isRemoveSuccessfull = $cplModel->removeProductIdFromCookie($productId);
			$res->addData('success', $isRemoveSuccessfull);
		}
		return $res->ajaxExec();
	}

	public function renderProductList() {
		$res = new responseWcp();

		$cplModel = $this->getModel('cookieProductList');
		$productIdList = $cplModel->getProductListFromCookie();

		if(count($productIdList)) {
			$htmlStr = $this->getView()->compareProductsList();
			$res->addData('html', $htmlStr);
		} else {
			$res->addData('html', $this->translate('There are no products to compare.'));
		}
		$res->addData('success',1);

		return $res->ajaxExec();
	}
}