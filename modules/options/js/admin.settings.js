(function($) {

	function wcpAdminSettings() {

	}

	wcpAdminSettings.prototype.init = (function() {
		this.initForm();
	});

	wcpAdminSettings.prototype.saveOrderToInput = (function() {
		var itemOrder = '';

		$('.wcpCheckboxDrListContainter .wcpCheckBoxListItem .wcpCheckboxCblItem').each(function(ind1, item1) {
			var $currItem = $(item1);
			if($currItem.is(':checked')) {
				itemOrder += $currItem.attr('value') + ';;';
			}
		});

		if(itemOrder.length) {
			// remove last ';;'
			itemOrder = itemOrder.slice(0, -2);
		}
		this.$settForm.find('.wcpCblItemOrderInp').val(itemOrder);
	});

	wcpAdminSettings.prototype.initForm = (function() {
		this.$saveBtn = $('#wcpSettingsSaveBtn');
		this.$settForm = $('#wcpSettingsForm');
		this.$prodPropContainer = $('.wcpCheckboxDrListContainter');
		var selfWas = this;

		this.$saveBtn.click(function(){
			selfWas.wcpSaveSettings();
			return false;
		});

		this.$settForm.submit(function(){
			selfWas.wcpSaveSettings();
			return false;
		});

		this.$prodPropContainer.sortable({
			'placeholder': 'wcpSortableHightLight',//
			'axis': 'y',
			'handle': '.wcpOnePpHandle',
			'stop': function(event, ui) {
				selfWas.saveOrderToInput();
			},
		});
	});

	wcpAdminSettings.prototype.wcpSaveSettings = (function(funcHandler) {
		// generate sort event
		this.saveOrderToInput();
		this.$settForm.sendFormWcp({
				'btn': this.$saveBtn
			,	'onSuccess': function(res) {
					if(funcHandler && typeof(funcHandler) === 'function') {
						funcHandler(res);
					}
				}
		});
	});

	var was = new wcpAdminSettings();
	jQuery(document).ready(function(){
		was.init();
	});
})(jQuery);