<?php
class ThemesBuilder {
	protected $productList;
	protected $orderedProperty;
	protected $fieldList;
	protected $themeName;

	public function __construct($productList, $orderedProps, $fieldList, $themeName, $buyNowButtonText = '') {
		$this->productList = $productList;
		$this->orderedProperty = $orderedProps;
		$this->fieldList = $fieldList;
		$this->themeName = $themeName;
		$this->buyNowButtonText = $buyNowButtonText;
	}

	public function drawTable() {
		$tableHtml = '';

		if(is_array($this->productList) && count($this->productList)
			&& is_array($this->orderedProperty) && count($this->orderedProperty)
			&& is_array($this->fieldList)
		) {
			$tableHtml .= '<div class="wcpCompareProductTable wcpTblT' . $this->themeName .'">';
			$tableHtml .= $this->getHeaderRows();

			$rowsCount = count($this->orderedProperty) - 1;
			foreach($this->orderedProperty as $propKey => $oneProperty) {
				$rowSpecClass = '';
				switch($propKey) {
					case 0:
						$rowSpecClass = ' wcpFirstRow';
						break;
					case $rowsCount:
						$rowSpecClass = ' wcpLastRow';
						break;
					default:
						break;
				}
				$tableHtml .= '<div class="wcpProductRow' . $rowSpecClass . '">';

				// description column
				$tableHtml .= '<div class="wcpProductCell">';
				if(!empty($this->fieldList[$oneProperty])) {
					$tableHtml .= $this->fieldList[$oneProperty];
				}
				$tableHtml .= '</div>';

				foreach($this->productList as $productKey => $oneProduct) {
					$cellEntry = '';
					if(!empty($oneProduct[$oneProperty])) {
						switch($oneProperty) {
							case 'image':
								$cellEntry = '<img src="' . $oneProduct[$oneProperty] . '" class="wcpProductLogoImg">';
								break;
							default:
								$cellEntry = $oneProduct[$oneProperty];
								break;
						}
					}
					$tableHtml .= '<div class="wcpProductCell" data-product-id="' . $oneProduct['id'] . '">';
					$tableHtml .= $cellEntry;
					$tableHtml .= '</div>';
				}
				$tableHtml .= '</div>';
			}

			$tableHtml .= $this->getFooterRows();
			$tableHtml .= "</div>";
		} else {
			$tableHtml = '<div>' . __('Please, configure settings and check fields to show.', WCP_LANG_CODE) . '</div>';
		}
		return $tableHtml;
	}

	public function getHeaderRows() {
		return '';
	}

	public function getFooterRows() {
		return '';
	}
}