<?php 
	// fa-check		fa-plus		fa-spinner
	$fontIcon = 'fa-plus';
	$currentButtonText = $this->notAddedButtonText;
	$buttonState = 0;
	if($this->isProductAdded) {
		$fontIcon = 'fa-check';
		$currentButtonText = $this->addedButtonText;
		$buttonState = 1;
	}
?>
<a
	href="#"
	class="button wcpCompareButton"
	data-state-type="<?php echo $buttonState; ?>"
	data-state-text0="<?php echo $this->notAddedButtonText; ?>"
	data-state-text1="<?php echo $this->addedButtonText; ?>"
	data-show-in-type="<?php echo $this->showInType; ?>"
	data-product-id="<?php echo $this->productId; ?>"
>
	<i class="wcpButtonIcon fa <?php echo $fontIcon; ?>"></i>
	<i class="wcpButtonText"><?php echo $currentButtonText; ?></i>
</a>
