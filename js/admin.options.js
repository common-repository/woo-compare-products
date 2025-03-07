var wcpAdminFormChanged = [];
window.onbeforeunload = function(){
	// If there are at lease one unsaved form - show message for confirnation for page leave
	if(wcpAdminFormChanged.length)
		return 'Some changes were not-saved. Are you sure you want to leave?';
};
jQuery(document).ready(function(){
	wcpInitMainPromoForm();
	if(typeof(wcpActiveTab) != 'undefined' && wcpActiveTab != 'main_page' && jQuery('#toplevel_page_newsletters-supsystic').hasClass('wp-has-current-submenu')) {
		var subMenus = jQuery('#toplevel_page_newsletters-supsystic').find('.wp-submenu li');
		subMenus.removeClass('current').each(function(){
			if(jQuery(this).find('a[href$="&tab='+ wcpActiveTab+ '"]').size()) {
				jQuery(this).addClass('current');
			}
		});
	}
	
	// Timeout - is to count only user changes, because some changes can be done auto when form is loaded
	setTimeout(function() {
		// If some changes was made in those newsletters and they were not saved - show message for confirnation before page reload
		var newslettersPreventLeave = [];
		if(newslettersPreventLeave && newslettersPreventLeave.length) {
			jQuery('#'+ newslettersPreventLeave.join(', #')).find('input,select').change(function(){
				var formId = jQuery(this).parents('form:first').attr('id');
				changeAdminFormWcp(formId);
			});
			jQuery('#'+ newslettersPreventLeave.join(', #')).find('input[type=text],textarea').keyup(function(){
				var formId = jQuery(this).parents('form:first').attr('id');
				changeAdminFormWcp(formId);
			});
			jQuery('#'+ newslettersPreventLeave.join(', #')).submit(function(){
				adminFormSavedWcp( jQuery(this).attr('id') );
			});
		}
	}, 1000);

	if(jQuery('.wcpInputsWithDescrForm').size()) {
		jQuery('.wcpInputsWithDescrForm').find('input[type=checkbox][data-optkey]').change(function(){
			var optKey = jQuery(this).data('optkey')
			,	descShell = jQuery('#wcpFormOptDetails_'+ optKey);
			if(descShell.size()) {
				if(jQuery(this).attr('checked')) {
					descShell.slideDown( 300 );
				} else {
					descShell.slideUp( 300 );
				}
			}
		}).trigger('change');
	}
	wcpInitStickyItem();
	wcpInitCustomCheckRadio();
	//wcpInitCustomSelect();
	
	jQuery('.wcpFieldsetToggled').each(function(){
		var self = this;
		jQuery(self).find('.wcpFieldsetContent').hide();
		jQuery(self).find('.wcpFieldsetToggleBtn').click(function(){
			var icon = jQuery(this).find('i')
			,	show = icon.hasClass('fa-plus');
			show ? icon.removeClass('fa-plus').addClass('fa-minus') : icon.removeClass('fa-minus').addClass('fa-plus');
			jQuery(self).find('.wcpFieldsetContent').slideToggle( 300, function(){
				if(show) {
					jQuery(this).find('textarea').each(function(i, el){
						if(typeof(this.CodeMirrorEditor) !== 'undefined') {
							this.CodeMirrorEditor.refresh();
						}
					});
				}
			} );
			return false;
		});
	});
	// Go to Top button init
	if(jQuery('.wcpGoToTopBtn').size()) {
		jQuery('.wcpGoToTopBtn').click(function(){
			jQuery('html, body').animate({
				scrollTop: 0
			}, 1000);
			jQuery(this).parents('#wcpGoToTop:first').hide();
			return false;
		});
	}
	// Tooltipster initialization
	wcpInitTooltips();
	if(jQuery('.wcpCopyTextCode').size()) {
		var cloneWidthElement =  jQuery('<span class="sup-shortcode" />').appendTo('.supsystic-plugin');
		jQuery('.wcpCopyTextCode').attr('readonly', 'readonly').bind('click focus', function(){
			this.setSelectionRange(0, this.value.length);
		});
		jQuery('.wcpCopyTextCode').each(function(){
			cloneWidthElement.html( str_replace(jQuery(this).val(), '<', 'P') );
			jQuery(this).width( cloneWidthElement.width() );
		});
		cloneWidthElement.remove();
	}
	// Check for showing review notice after a week usage
    wcpInitPlugNotices();
	jQuery(".supsystic-plugin .tooltipstered").removeAttr("title");
});
function wcpInitTooltips( selector ) {
	var tooltipsterSettings = {
		contentAsHTML: true
	,	interactive: true
	,	speed: 0
	,	delay: 0
	//,	animation: 'swing'
	,	maxWidth: 450
	}
	,	findPos = {
		'.wcp-tooltip': 'top-left'
	,	'.wcp-tooltip-bottom': 'bottom-left'
	,	'.wcp-tooltip-left': 'left'
	,	'.wcp-tooltip-right': 'right'
	}
	,	$findIn = selector ? jQuery( selector ) : false;
	for(var k in findPos) {
		if(typeof(k) === 'string') {
			var $tips = $findIn ? $findIn.find( k ) : jQuery( k ).not('.sup-no-init');
			if($tips && $tips.size()) {
				tooltipsterSettings.position = findPos[ k ];
				// Fallback for case if library was not loaded
				if(!$tips.tooltipster) continue;
				$tips.tooltipster( tooltipsterSettings );
			}
		}
	}
}
function changeAdminFormWcp(formId) {
	if(jQuery.inArray(formId, wcpAdminFormChanged) == -1)
		wcpAdminFormChanged.push(formId);
}
function adminFormSavedWcp(formId) {
	if(wcpAdminFormChanged.length) {
		for(var i in wcpAdminFormChanged) {
			if(wcpAdminFormChanged[i] == formId) {
				wcpAdminFormChanged.pop(i);
			}
		}
	}
}
function checkAdminFormSaved() {
	if(wcpAdminFormChanged.length) {
		if(!confirm(toeLangWcp('Some changes were not-saved. Are you sure you want to leave?'))) {
			return false;
		}
		wcpAdminFormChanged = [];	// Clear unsaved newsletters array - if user wanted to do this
	}
	return true;
}
function isAdminFormChanged(formId) {
	if(wcpAdminFormChanged.length) {
		for(var i in wcpAdminFormChanged) {
			if(wcpAdminFormChanged[i] == formId) {
				return true;
			}
		}
	}
	return false;
}
/*Some items should be always on users screen*/
function wcpInitStickyItem() {
	jQuery(window).scroll(function(){
		var stickiItemsSelectors = [/*'.ui-jqgrid-hdiv', */'.supsystic-sticky']
		,	elementsUsePaddingNext = [/*'.ui-jqgrid-hdiv', */'.supsystic-bar']	// For example - if we stick row - then all other should not offest to top after we will place element as fixed
		,	wpTollbarHeight = 32
		,	wndScrollTop = jQuery(window).scrollTop() + wpTollbarHeight
		,	footer = jQuery('.wcpAdminFooterShell')
		,	footerHeight = footer && footer.size() ? footer.height() : 0
		,	docHeight = jQuery(document).height()
		,	wasSticking = false
		,	wasUnSticking = false;
		/*if(jQuery('#wpbody-content .update-nag').size()) {	// Not used for now
			wpTollbarHeight += parseInt(jQuery('#wpbody-content .update-nag').outerHeight());
		}*/
		for(var i = 0; i < stickiItemsSelectors.length; i++) {
			jQuery(stickiItemsSelectors[ i ]).each(function(){
				var element = jQuery(this);
				if(element && element.size() && !element.hasClass('sticky-ignore')) {
					var scrollMinPos = element.offset().top
					,	prevScrollMinPos = parseInt(element.data('scrollMinPos'))
					,	useNextElementPadding = toeInArray(stickiItemsSelectors[ i ], elementsUsePaddingNext) !== -1 || element.hasClass('sticky-padd-next')
					,	currentScrollTop = wndScrollTop
					,	calcPrevHeight = element.data('prev-height')
					,	currentBorderHeight = wpTollbarHeight
					,	usePrevHeight = 0;
					if(calcPrevHeight) {
						usePrevHeight = jQuery(calcPrevHeight).outerHeight();
						currentBorderHeight += usePrevHeight;
					}
					if(currentScrollTop > scrollMinPos && !element.hasClass('supsystic-sticky-active')) {	// Start sticking
						if(element.hasClass('sticky-save-width')) {
							element.width( element.width() );
							//element.addClass('sticky-full-width');
						}
						element.addClass('supsystic-sticky-active').data('scrollMinPos', scrollMinPos).css({
							'top': currentBorderHeight
						});
						if(useNextElementPadding) {
							//element.addClass('supsystic-sticky-active-bordered');
							var nextElement = element.next();
							if(nextElement && nextElement.size()) {
								nextElement.data('prevPaddingTop', nextElement.css('padding-top'));
								var addToNextPadding = parseInt(element.data('next-padding-add'));
								addToNextPadding = addToNextPadding ? addToNextPadding : 0;
								nextElement.css({
									'padding-top': (element.hasClass('sticky-outer-height') ? element.outerHeight() : element.height()) + usePrevHeight + addToNextPadding
								});
							}
						}
						wasSticking = true;
						element.trigger('startSticky');
					} else if(!isNaN(prevScrollMinPos) && currentScrollTop <= prevScrollMinPos) {	// Stop sticking
						element.removeClass('supsystic-sticky-active').data('scrollMinPos', 0).css({
							//'top': 0
						});
						if(element.hasClass('sticky-save-width')) {
							if(element.hasClass('sticky-base-width-auto')) {
								element.css('width', 'auto');
							}
							//element.removeClass('sticky-full-width');
						}
						if(useNextElementPadding) {
							//element.removeClass('supsystic-sticky-active-bordered');
							var nextElement = element.next();
							if(nextElement && nextElement.size()) {
								var nextPrevPaddingTop = parseInt(nextElement.data('prevPaddingTop'));
								if(isNaN(nextPrevPaddingTop))
									nextPrevPaddingTop = 0;
								nextElement.css({
									'padding-top': nextPrevPaddingTop
								});
							}
						}
						element.trigger('stopSticky');
						wasUnSticking = true;
					} else {	// Check new stick position
						if(element.hasClass('supsystic-sticky-active')) {
							if(footerHeight) {
								var elementHeight = element.height()
								,	heightCorrection = 32
								,	topDiff = docHeight - footerHeight - (currentScrollTop + elementHeight + heightCorrection);
								if(topDiff < 0) {
									element.css({
										'top': currentBorderHeight + topDiff
									});
								} else {
									element.css({
										'top': currentBorderHeight
									});
								}
							}
							// If at least on element is still sticking - count it as all is working
							wasSticking = wasUnSticking = false;
						}
					}
				}
			});
		}
		if(wasSticking) {
			if(jQuery('#wcpFormGoToTop').size())
				jQuery('#wcpFormGoToTop').show();
		} else if(wasUnSticking) {
			if(jQuery('#wcpFormGoToTop').size())
				jQuery('#wcpFormGoToTop').hide();
		}
	});
}
function wcpGetTxtEditorVal(id) {
	if(typeof(tinyMCE) !== 'undefined' 
		&& tinyMCE.get( id ) 
		&& !jQuery('#'+ id).is(':visible') 
		&& tinyMCE.get( id ).getDoc 
		&& typeof(tinyMCE.get( id ).getDoc) == 'function' 
		&& tinyMCE.get( id ).getDoc()
	)
		return tinyMCE.get( id ).getContent();
	else
		return jQuery('#'+ id).val();
}
function wcpSetTxtEditorVal(id, content) {
	if(typeof(tinyMCE) !== 'undefined' 
		&& tinyMCE 
		&& tinyMCE.get( id ) 
		&& !jQuery('#'+ id).is(':visible')
		&& tinyMCE.get( id ).getDoc 
		&& typeof(tinyMCE.get( id ).getDoc) == 'function' 
		&& tinyMCE.get( id ).getDoc()
	)
		tinyMCE.get( id ).setContent(content);
	else
		jQuery('#'+ id).val( content );
}
/**
 * Add data to jqGrid object post params search
 * @param {object} param Search params to set
 * @param {string} gridSelectorId ID of grid table html element
 */
function wcpGridSetListSearch(param, gridSelectorId) {
	jQuery('#'+ gridSelectorId).setGridParam({
		postData: {
			search: param
		}
	});
}
/**
 * Set data to jqGrid object post params search and trigger search
 * @param {object} param Search params to set
 * @param {string} gridSelectorId ID of grid table html element
 */
function wcpGridDoListSearch(param, gridSelectorId) {
	wcpGridSetListSearch(param, gridSelectorId);
	jQuery('#'+ gridSelectorId).trigger( 'reloadGrid' );
}
/**
 * Get row data from jqGrid
 * @param {number} id Item ID (from database for example)
 * @param {string} gridSelectorId ID of grid table html element
 * @return {object} Row data
 */
function wcpGetGridDataById(id, gridSelectorId) {
	var rowId = getGridRowId(id, gridSelectorId);
	if(rowId) {
		return jQuery('#'+ gridSelectorId).jqGrid ('getRowData', rowId);
	}
	return false;
}
/**
 * Get cell data from jqGrid
 * @param {number} id Item ID (from database for example)
 * @param {string} column Column name
 * @param {string} gridSelectorId ID of grid table html element
 * @return {string} Cell data
 */
function wcpGetGridColDataById(id, column, gridSelectorId) {
	var rowId = getGridRowId(id, gridSelectorId);
	if(rowId) {
		return jQuery('#'+ gridSelectorId).jqGrid ('getCell', rowId, column);
	}
	return false;
}
/**
 * Get grid row ID (ID of table row) from item ID (from database ID for example)
 * @param {number} id Item ID (from database for example)
 * @param {string} gridSelectorId ID of grid table html element
 * @return {number} Table row ID
 */
function getGridRowId(id, gridSelectorId) {
	var rowId = parseInt(jQuery('#'+ gridSelectorId).find('[aria-describedby='+ gridSelectorId+ '_id][title='+ id+ ']').parent('tr:first').index());
	if(!rowId) {
		console.log('CAN NOT FIND ITEM WITH ID  '+ id);
		return false;
	}
	return rowId;
}
function prepareToPlotDate(data) {
	if(typeof(data) === 'string') {
		if(data) {
			data = str_replace(data, '/', '-');
			return (new Date(data)).getTime();
		}
	}
	return data;
}
function wcpInitPlugNotices() {
	var $notices = jQuery('.supsystic-admin-notice');
	if($notices && $notices.size()) {
		$notices.each(function(){
			jQuery(this).find('.notice-dismiss').click(function(){
				var $notice = jQuery(this).parents('.supsystic-admin-notice');
				if(!$notice.data('stats-sent')) {
					// User closed this message - that is his choise, let's respect this and save it's saved status
					jQuery.sendFormWcp({
						data: {mod: 'supsystic_promo', action: 'addNoticeAction', code: $notice.data('code'), choice: 'hide'}
					});
				}
			});
			jQuery(this).find('[data-statistic-code]').click(function(){
				var href = jQuery(this).attr('href')
				,	$notice = jQuery(this).parents('.supsystic-admin-notice');
				jQuery.sendFormWcp({
					data: {mod: 'supsystic_promo', action: 'addNoticeAction', code: $notice.data('code'), choice: jQuery(this).data('statistic-code')}
				});
				$notice.data('stats-sent', 1).find('.notice-dismiss').trigger('click');
				if(!href || href === '' || href === '#')
					return false;
			});
		});
	}
}
/**
 * Main promo newsletters will show each time user will try to modify PRO option with free version only
 */
function wcpGetMainPromoForm() {
	if(jQuery('#wcpOptInProWnd').hasClass('ui-dialog-content')) {
		return jQuery('#wcpOptInProWnd');
	}
	return jQuery('#wcpOptInProWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 540
	,	height: 200
	,	open: function() {
			jQuery('#wcpOptWndTemplateTxt').hide();
			jQuery('#wcpOptWndOptionTxt').show();
		}
	});
}
function wcpInitMainPromoForm() {
	if(!WCP_DATA.isPro) {
		var $proOptWnd = wcpGetMainPromoForm();
		jQuery('.wcpProOpt').change(function(e){
			e.stopPropagation();
			var needShow = true
			,	isRadio = jQuery(this).attr('type') == 'radio'
			,	isCheck = jQuery(this).attr('type') == 'checkbox';
			if(isRadio && !jQuery(this).attr('checked')) {
				needShow = false;
			}
			if(!needShow) {
				return;
			}
			if(isRadio) {
				jQuery('input[name="'+ jQuery(this).attr('name')+ '"]:first').parents('label:first').click();
				if(jQuery(this).parents('.iradio_minimal:first').size()) {
					var self = this;
					setTimeout(function(){
						jQuery(self).parents('.iradio_minimal:first').removeClass('checked');
					}, 10);
				}
			}
			var parent = null;
			if(jQuery(this).parents('#wcpFormMainOpts').size()) {
				parent = jQuery(this).parents('label:first');
			} else if(jQuery(this).parents('.wcpFormOptRow:first').size()) {
				parent = jQuery(this).parents('.wcpFormOptRow:first');
			} else {
				parent = jQuery(this).parents('tr:first');
			}
			if(!parent.size()) return;
			var promoLink = parent.find('.wcpProOptMiniLabel a').attr('href');
			if(promoLink && promoLink != '') {
				jQuery('#wcpOptInProWnd a').attr('href', promoLink);
			}
			$proOptWnd.dialog('open');
			return false;
		});
	}
}