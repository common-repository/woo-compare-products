jQuery(document).ready(function(){
	// Tooltipster initialization
	var tooltipsterSettings = {
		contentAsHTML: true
	,	interactive: true
	,	speed: 250
	,	delay: 0
	,	animation: 'swing'
	,	maxWidth: 450
	};
	if(jQuery('.wcp-tooltip').size()) {
		tooltipsterSettings.position = 'top-left';
		jQuery('.wcp-tooltip').tooltipster( tooltipsterSettings );
	}
	if(jQuery('.wcp-tooltip-bottom').size()) {
		tooltipsterSettings.position = 'bottom-left';
		jQuery('.wcp-tooltip-bottom').tooltipster( tooltipsterSettings );
	}
	if(jQuery('.wcp-tooltip-left').size()) {
		tooltipsterSettings.position = 'left';
		jQuery('.wcp-tooltip-left').tooltipster( tooltipsterSettings );
	}
	if(jQuery('.wcp-tooltip-right').size()) {
		tooltipsterSettings.position = 'right';
		jQuery('.wcp-tooltip-right').tooltipster( tooltipsterSettings );
	}
});