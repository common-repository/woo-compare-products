<?php
class wooModelWcp extends modelWcp {
	public static $wcProductWeightUnit = null;

	public function getDefaultProductProperties() {
		$list = array(
			'title' => $this->translate('Title'),
			'price' => $this->translate('Price'),
			'image' => $this->translate('Image'),
			'description' => $this->translate('Description'),
			'stock' => $this->translate('Stock'),
			'sku' => $this->translate('SKU'),
			'weight' => $this->translate('Weight'),
			'dimensions' => $this->translate('Dimensions'),
		);
		return $list;
	}

	public function getMetaCodesForAllProducts() {
		global $wpdb;
		$query = "SELECT DISTINCT pm.meta_key
				FROM " . $wpdb->prefix . "postmeta pm
				LEFT JOIN " . $wpdb->prefix . "posts p
					ON p.ID = pm.post_id
				WHERE p.post_type = 'product'";

		$prodCodes = $wpdb->get_results($query, OBJECT_K);
		return $prodCodes;
	}

	public static function getWooCommerceTaxonomies() {
		global $woocommerce;

		if(!isset($woocommerce)) {
			return array();
		}
		$attributes = array();

		if(function_exists('wc_get_attribute_taxonomies') && function_exists('wc_attribute_taxonomy_name')) {
			$attribute_taxonomies = wc_get_attribute_taxonomies();
			if(empty($attribute_taxonomies)) {
				return array();
			}
			foreach($attribute_taxonomies as $attribute) {
				$tax = wc_attribute_taxonomy_name($attribute->attribute_label);
				if(taxonomy_exists($tax)) {
					$attributes[$tax] = ucfirst($attribute->attribute_label);
				}
			}
		}
		else {
			$attribute_taxonomies = $woocommerce->get_attribute_taxonomies();
			if(empty($attribute_taxonomies)) {
				return array();
			}
			foreach($attribute_taxonomies as $attribute) {
				$tax = $woocommerce->attribute_taxonomy_name($attribute->attribute_label);
				if(taxonomy_exists($tax)) {
					$attributes[$tax] = ucfirst($attribute->attribute_label);
				}
			}
		}
		return $attributes;
	}

	public function getProductsByIds($ids, $fieldList, $fieldOrder) {
		$productList = array();

		if(is_array($ids) && count($ids) && is_array($fieldList) && count($fieldList)) {
			$funcName = $this->getWooCommerceFuncName();
			foreach($ids as $productId) {
				$productItem = array();
				$productObj = call_user_func($funcName, $productId);
				if($productObj) {
					// id
					$productItem['id'] = $productObj->get_id();

					foreach($fieldOrder as $fieldCode) {
						$productItem[$fieldCode] = $this->getProductValueByCode($productObj, $fieldCode);
					}
					// required fields
					if(!isset($productItem['title'])) {
						$productItem['title'] = $this->getProductValueByCode($productObj, 'title');
					}
					if(!isset($productItem['price'])) {
						$productItem['price'] = $this->getProductValueByCode($productObj, 'price');
					}
					$productItem['wcpProductLink'] = get_permalink($productObj->get_id());
					//
					$productList[] = $productItem;
				}
			}
		}
		return $productList;
	}

	public function getProductValueByCode($productObj, $code) {
		$fieldValue = null;

		switch($code) {
			case 'title':
				$fieldValue = $productObj->get_title();
				break;
			case 'price':
				$fieldValue = $productObj->get_price_html();
				break;
			case 'image':
				$thumbnailId = (int) $productObj->get_image_id();
				if($thumbnailId) {
					$imgUrl = wp_get_attachment_url($thumbnailId);
					if($imgUrl) {
						$fieldValue = $imgUrl;
					}
				}
				break;
			case 'description':
				$fieldValue = $productObj->get_short_description();
				break;
			case 'stock':
				$availability = $productObj->get_availability();
				if(empty($availability['availability'])) {
					$availability['availability'] = $this->translate('In stock');
				}
				$fieldValue = $availability['availability'];
				break;
			case 'sku':
				$sku = $productObj->get_sku();
				if(empty($sku)) {
					$sku = '-';
				}
				$fieldValue = $sku;
				break;
			case 'weight':
				$weight = $productObj->get_weight();
				if(!empty($weight)) {
					$weight = wc_format_localized_decimal($weight) . ' ' . $this->getWcWeightUnit();
				} else {
					$weight = '-';
				}
				$fieldValue = $weight;
				break;
			case 'dimensions':
				if(function_exists('wc_format_dimensions')) {
					$dimensions = wc_format_dimensions($productObj->get_dimensions(false));
				} else {
					$dimensions = $productObj->get_dimensions();
				}
				if(!$dimensions) {
					$dimensions = '-';
				}
				$fieldValue = $dimensions;
				break;
				/*
			case 'notTermAttribute':
				print_r2($productObj->get_attributes());
				break;
				/**/
			default:
				if(taxonomy_exists($code)) {
					$taxValArr = array();
					$productId = $productObj->get_id();
					$terms = get_the_terms($productId, $code);
					//var_dump($code, $terms);
					if(!empty($terms)) {
						foreach($terms as $term) {
							$term = sanitize_term($term, $code);
							$taxValArr[] = $term->name;
						}
						$fieldValue = implode(', ', $taxValArr);
					}
				}
				break;
		}
		//print_r2($productObj->get_default_attributes());
		return $fieldValue;
	}

	public function getWcWeightUnit() {
		if(!self::$wcProductWeightUnit) {
			self::$wcProductWeightUnit = esc_attr(get_option('woocommerce_weight_unit'));
		}
		return self::$wcProductWeightUnit;
	}

	public function getWooCommerceFuncName() {
		$funcName = 'get_product';
		if(function_exists('wc_get_product')) {
			$funcName = 'wc_get_product';
		}
		return $funcName;
	}
}
