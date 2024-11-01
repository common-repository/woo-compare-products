var g_wcpCurrTour = null
,	g_wcpTourOpenedWithTab = false
,	g_wcpAdminTourDissmissed = false;
jQuery(document).ready(function(){
	setTimeout(function(){
		if(typeof(wcpAdminTourData) !== 'undefined' && wcpAdminTourData.tour) {
			jQuery('body').append( wcpAdminTourData.html );
			wcpAdminTourData._$ = jQuery('#supsystic-admin-tour');
			for(var tourId in wcpAdminTourData.tour) {
				if(wcpAdminTourData.tour[ tourId ].points) {
					for(var pointId in wcpAdminTourData.tour[ tourId ].points) {
						_wcpOpenPointer(tourId, pointId);
						break;	// Open only first one
					}
				}
			}
			for(var tourId in wcpAdminTourData.tour) {
				if(wcpAdminTourData.tour[ tourId ].points) {
					for(var pointId in wcpAdminTourData.tour[ tourId ].points) {
						if(wcpAdminTourData.tour[ tourId ].points[ pointId ].sub_tab) {
							var subTab = wcpAdminTourData.tour[ tourId ].points[ pointId ].sub_tab;
							jQuery('a[href="'+ subTab+ '"]')
								.data('tourId', tourId)
								.data('pointId', pointId);
							var tabChangeEvt = str_replace(subTab, '#', '')+ '_tabSwitch';
							jQuery(document).bind(tabChangeEvt, function(event, selector) {
								if(!g_wcpTourOpenedWithTab && !g_wcpAdminTourDissmissed) {
									var $clickTab = jQuery('a[href="'+ selector+ '"]');
									_wcpOpenPointer($clickTab.data('tourId'), $clickTab.data('pointId'));
								}
							});
						}
					}
				}
			}
		}
	}, 500);
});

function _wcpOpenPointerAndFormTab(tourId, pointId, tab) {
	g_wcpTourOpenedWithTab = true;
	jQuery('#wcpFormEditTabs').wpTabs('activate', tab);
	_wcpOpenPointer(tourId, pointId);
	g_wcpTourOpenedWithTab = false;
}
function _wcpOpenPointer(tourId, pointId) {
	var pointer = wcpAdminTourData.tour[ tourId ].points[ pointId ];
	var $content = wcpAdminTourData._$.find('#supsystic-'+ tourId+ '-'+ pointId);
	if(!jQuery(pointer.target) || !jQuery(pointer.target).size())
		return;
	if(g_wcpCurrTour) {
		_wcpTourSendNext(g_wcpCurrTour._tourId, g_wcpCurrTour._pointId);
		g_wcpCurrTour.element.pointer('close');
		g_wcpCurrTour = null;
	}
	if(pointer.sub_tab && jQuery('#wcpFormEditTabs').wpTabs('getActiveTab') != pointer.sub_tab) {
		return;
	}
	var options = jQuery.extend( pointer.options, {
		content: $content.find('.supsystic-tour-content').html()
	,	pointerClass: 'wp-pointer supsystic-pointer'
	,	close: function() {
			//console.log('closed');
		}
	,	buttons: function(event, t) {
			g_wcpCurrTour = t;
			g_wcpCurrTour._tourId = tourId;
			g_wcpCurrTour._pointId = pointId;
			var $btnsShell = $content.find('.supsystic-tour-btns')
			,	$closeBtn = $btnsShell.find('.close')
			,	$finishBtn = $btnsShell.find('.supsystic-tour-finish-btn');

			if($finishBtn && $finishBtn.size()) {
				$finishBtn.click(function(e){
					e.preventDefault();
					jQuery.sendFormWcp({
						msgElID: 'noMessages'
					,	data: {mod: 'supsystic_promo', action: 'addTourFinish', tourId: tourId, pointId: pointId}
					});
					g_wcpCurrTour.element.pointer('close');
				});
			}
			if($closeBtn && $closeBtn.size()) {
				$closeBtn.bind( 'click.pointer', function(e) {
					e.preventDefault();
					jQuery.sendFormWcp({
						msgElID: 'noMessages'
					,	data: {mod: 'supsystic_promo', action: 'closeTour', tourId: tourId, pointId: pointId}
					});
					t.element.pointer('close');
					g_wcpAdminTourDissmissed = true;
				});
			}
			return $btnsShell;
		}
	});
	jQuery(pointer.target).pointer( options ).pointer('open');
	var minTop = 10
	,	pointerTop = parseInt(g_wcpCurrTour.pointer.css('top'));
	if(!isNaN(pointerTop) && pointerTop < minTop) {
		g_wcpCurrTour.pointer.css('top', minTop+ 'px');
	}
}
function _wcpTourSendNext(tourId, pointId) {
	jQuery.sendFormWcp({
		msgElID: 'noMessages'
	,	data: {mod: 'supsystic_promo', action: 'addTourStep', tourId: tourId, pointId: pointId}
	});
}