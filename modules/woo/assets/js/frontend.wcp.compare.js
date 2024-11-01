(function($) {
	function wcpCompareBtnStatusChanger() {

	}

	wcpCompareBtnStatusChanger.prototype.init = (function(buttonClickHandler) {
		var selfWcbsc = this;
		selfWcbsc.$wcpLinks = $('.wcpCompareButton:not(wcpInited)');
		if(buttonClickHandler) {
			selfWcbsc.$wcpLinks.on('click', buttonClickHandler);
			selfWcbsc.$wcpLinks.addClass('wcpInited');
		}
	});

	wcpCompareBtnStatusChanger.prototype.changeFontAwesomeClassForButton = (function($button, status) {
		if(!$button || $button.length == 0) return;
		// used classes
		var classesArr = ['fa-plus', 'fa-spinner', 'fa-check', 'fa-spin']
		,	ind1
		,	$buttonIcon = $button.find('.wcpButtonIcon')
		,	$buttonText = $button.find('.wcpButtonText')
		;
		// remove previous FontAwesome classes
		for(ind1 = 0; ind1 < classesArr.length; ind1++) {
			$buttonIcon.removeClass(classesArr[ind1]);
		}
		switch(status) {
				// "default" state
			default:
			case 0:
				$buttonIcon.addClass(classesArr[0]);
				$button.attr('data-state-type', '0');
				$buttonText.html($button.attr('data-state-text0'));
				break;
				// "waiting" state
			case 1:
				$buttonIcon.addClass(classesArr[1]);
				$buttonIcon.addClass(classesArr[3]);

				break;
				// "added to compare" state
			case 2:
				$buttonIcon.addClass(classesArr[2]);
				$button.attr('data-state-type', '1');
				$buttonText.html($button.attr('data-state-text1'));
				break;
		}
	});

	wcpCompareBtnStatusChanger.prototype.isButtonLocked = (function($button) {
		return $button.hasClass('wcpDisableBtn');
	});

	wcpCompareBtnStatusChanger.prototype.lockAllButtons = (function() {
		this.$wcpLinks.addClass('wcpDisableBtn');
	});

	wcpCompareBtnStatusChanger.prototype.unlockAllButtons = (function() {
		this.$wcpLinks.removeClass('wcpDisableBtn');
	});

	function wcpCompare() {

	}

	wcpCompare.prototype.init = (function() {
		this.$popupProductList = $('.wcpPopupProductCompareContainer');
		this.initPopup();
		this.initButtons();
	});

	wcpCompare.prototype.initButtons = (function() {
		this.buttonBehaviour = new wcpCompareBtnStatusChanger();
		var selfWcpc = this
		,	isProductAdded
		,	isProdListInserted
		,	ajaxData = {
				'mod': 'woo'
			,	'pl': 'wcp'
			,	'reqType': 'ajax'
			}
		;
		function buttonClickHandler(event) {
			event.preventDefault();
			var $selfBtn = $(this)
			;
			isProductAdded = false;
			ajaxData['productId'] = parseInt($selfBtn.attr('data-product-id'));
			if(ajaxData['productId'] && !selfWcpc.buttonBehaviour.isButtonLocked($selfBtn)) {
				if($selfBtn.attr('data-state-type') != '1') {
					selfWcpc.buttonBehaviour.lockAllButtons();

					selfWcpc.buttonBehaviour.changeFontAwesomeClassForButton($selfBtn, 1);
					ajaxData['action'] = 'addCompareProductId';
					$.ajax({
						'url': window.wcpAjaxUrl,
						'method': 'post',
						'dataType': 'json',
						'data': ajaxData,
						'success': function(resp, status, xhr) {
							if(resp && resp.data && resp.data.success) {
								isProductAdded = resp.data.success;
							}
						},
					}).done(function() {
						if(isProductAdded == 1) {
							selfWcpc.buttonBehaviour.changeFontAwesomeClassForButton($selfBtn, 2);
						} else {
							selfWcpc.buttonBehaviour.changeFontAwesomeClassForButton($selfBtn, 0);
						}
						selfWcpc.buttonBehaviour.unlockAllButtons();
					});
				} else {
					var $loaderCont = selfWcpc.$popupProductList.find('.wcpLoaderCont')
					,	$productListCont = selfWcpc.$popupProductList.find('.wcpProductsContainer')
					;
					$loaderCont.show();
					$productListCont.hide();

					isProdListInserted = false;
					ajaxData['action'] = 'renderProductList';
					$.ajax({
						'url': window.wcpAjaxUrl,
						'method': 'post',
						'dataType': 'json',
						'data': ajaxData,
						'success': function(resp, status, xhr) {
							if(resp && resp.data && resp.data.success && resp.data.html) {
								$productListCont.html(resp.data.html);
								selfWcpc.afterPopupContentShow();
								isProdListInserted = true;
								$productListCont.find('img').one('load', function() {
									selfWcpc.$popupProductList.dialog('option','position',{ my: "center center", at: "center center", of: window });
								});
							}
						},
					}).done(function(resp) {
						if(!isProdListInserted) {
							$productListCont.html('Error occured!');
						}
						$loaderCont.hide();
						$productListCont.show();
						selfWcpc.$popupProductList.dialog('option','position',{ my: "center center", at: "center center", of: window });
					});
					selfWcpc.$popupProductList.dialog('open');
				}
			}
		};
		this.buttonBehaviour.init(buttonClickHandler);
	});

	wcpCompare.prototype.afterPopupContentShow = (function() {
		var $closeBtns = this.$popupProductList.find('.wcpProductRemove:not(wpcInited)')
		,	selfWc = this
		;
		$closeBtns.on('click', function(event) {
			var $thisBtn = $(this)
			,	productId = $thisBtn.attr('data-product-id')
			,	$productColumnCells = selfWc.$popupProductList.find('.wcpProductCell[data-product-id="' + productId + '"]')
			,	ajaxData = {
					'mod': 'woo'
					,	'pl': 'wcp'
					,	'reqType': 'ajax'
					,	'action': 'removeCompareProductById'
					,	'productId': productId
				}
			;
			if(!$thisBtn.hasClass('wcpDisableBtn')) {
				$closeBtns.addClass('wcpDisableBtn');
				// remove column from cookie
				$.ajax({
					'url': window.wcpAjaxUrl,
					'method': 'post',
					'dataType': 'json',
					'data': ajaxData,
					'success': function(resp, status, xhr) {
						if(resp && resp.data && resp.data.success == 1) {
							// remove column
							$productColumnCells.hide('slow', function(){
								$(this).remove();
							});
							selfWc.buttonBehaviour.changeFontAwesomeClassForButton(selfWc.buttonBehaviour.$wcpLinks.filter('[data-product-id="'+productId+'"]'), 0);
						}
					},
				}).done(function() {
					$closeBtns.removeClass('wcpDisableBtn');
				});
			}
		});
		$closeBtns.addClass('wpcInited');
	});

	wcpCompare.prototype.initPopup = (function() {
		var selfWcpc = this
		,	wndWidth = parseInt(window.innerWidth)
		,	wndHeight = parseInt(window.innerHeight)
		,	popupWidth = Math.floor(wndWidth * 0.9)
		,	popupHeight = Math.floor(wndHeight * 0.9)
		,	$loaderCont = this.$popupProductList.find('.wcpLoaderCont')
		,	$productListCont = this.$popupProductList.find('.wcpProductsContainer')
		;
		
		this.$popupProductList.dialog({
			'autoOpen': false,
			//'resizable': false,
			'modal': true,
			'width': 'auto',
			//'height': 'auto',
			'autoResize': true,
			'maxHeight': popupHeight,
			'maxWidth': popupWidth,
			'buttons': {
				'Ok': function() {
					selfWcpc.$popupProductList.dialog('close');
				},
			},
			'open': function(event, ui) {
				$(event.target).parent().css({'maxWidth': popupWidth+'px', 'maxHeight': popupHeight+'px'});
			},
			'close': function(event, ui) {
				$loaderCont.show();
				$productListCont.hide();
			},
			'create': function(event, ui) {
				$(event.target).parent().css('position', 'fixed');
			},
			'resizeStop': function(event, ui) {
				var position = [
					(Math.floor(ui.position.left) - $(window).scrollLeft()),
					(Math.floor(ui.position.top) - $(window).scrollTop())
				];
				$(event.target).parent().css('position', 'fixed');
				selfWcpc.$popupProductList.dialog('option', 'position', position);
			}
		});
		/**/
	});

	var wcpComp = new wcpCompare();

	$(document).ready(function() {
		wcpComp.init();
	});
}) (jQuery);