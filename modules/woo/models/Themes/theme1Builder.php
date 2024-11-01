<?php

class Theme1Builder extends ThemesBuilder {

	public function getHeaderRows() {
		$row1Html = '<div class="wcpProductCell wcpHeaderColumnCell"></div>';	//' . $this->fieldList['title'] . '
		$row2Html = '<div class="wcpProductCell wcpHeaderColumnCell"></div>';	//' . $this->fieldList['price'] . '

		foreach($this->productList as $oneProduct) {
			$row1Html .= '<div class="wcpProductCell wcpColHeader" data-product-id="' . $oneProduct['id'] . '">'
				. '<i class="fa fa-times wcpProductRemove" aria-hidden="true" data-product-id="' . $oneProduct['id'] . '"></i>';
			if(isset($oneProduct['title'])) {
				$row1Html .= $oneProduct['title'];
			}
			$row1Html .= '</div>';

			$row2Html .= '<div class="wcpProductCell wcpColDesc" data-product-id="' . $oneProduct['id'] . '">';
			if(isset($oneProduct['price'])) {
				$row2Html .= $oneProduct['price'];
			}
			$row2Html .= '</div>';
		}

		return '<div class="wcpProductRow">' . $row1Html . '</div>'
			. '<div class="wcpProductRow">' . $row2Html . '</div>';
	}

	public function getFooterRows() {
		$rowHtml = '<div class="wcpProductCell wcpHeaderColumnCell"></div>';

		foreach($this->productList as $oneProduct) {
			$rowHtml .= '<div class="wcpProductCell wcpColFooter" data-product-id="' . $oneProduct['id'] . '">';
			if(isset($oneProduct['wcpProductLink'])) {
			    $buyTxt = !empty($this->buyNowButtonText) ? $this->buyNowButtonText : __('Buy', WCP_LANG_CODE);
				$rowHtml .= '<a href="' . $oneProduct['wcpProductLink'] . '" class="wcpBuyBtnLink">'
					. $buyTxt . '</a>';
			}
			$rowHtml .= '</div>';
		}

		return '<div class="wcpProductRow">' . $rowHtml . '</div>';
	}
}