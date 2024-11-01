<?php
class cookieProductListModelWcp extends modelWcp {
	protected $cookieName = 'wooCompareProductIdsList';
	protected $cookieExpire = 0;
	protected $cookieDomain = '/';

	public function getProductListFromCookie() {
		$productArr = array();
		if(!empty($_COOKIE[$this->cookieName])) {
			$tmpArr = json_decode($_COOKIE[$this->cookieName], true);
			if($tmpArr && is_array($tmpArr) && count($tmpArr)) {
				$productArr = $tmpArr;
			}
		}
		return $productArr;
	}

	public function addProductIdToCookie($productId) {
		$productArr = $this->getProductListFromCookie();
		$productId = (int) $productId;
		if($productId && !in_array($productId, $productArr)) {
			$productArr[] = $productId;
			setcookie($this->cookieName, json_encode($productArr), $this->cookieExpire, $this->cookieDomain);
		}
		return true;
	}

	public function removeProductIdFromCookie($productId) {
		$productArr = $this->getProductListFromCookie();
		$productId = (int) $productId;
		$arrInd = array_search($productId, $productArr);

		if($arrInd !== false) {
			// get Key position
			$indexPosition = array_search($arrInd, array_keys($productArr));
			if($indexPosition !== false) {
				array_splice($productArr, $indexPosition, 1);

				if(count($productArr)) {
					setcookie($this->cookieName, json_encode($productArr), $this->cookieExpire, $this->cookieDomain);
				} else {
					// remove cookie
					setcookie($this->cookieName, json_encode($productArr), time() - 84200, $this->cookieDomain);
				}
				return 1;
			}
		}
		return 0;
	}
}