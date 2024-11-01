<div class="wcpFormOptRow">
	<label>
		<a target="_blank" href="<?php echo $this->promoLink?>" class="sup-promolink-input">
			<?php echo htmlWcp::checkbox('layered_style_promo', array(
				'checked' => 1,
				//'attrs' => 'disabled="disabled"',
			))?>
			<?php _e('Enable Layered Form Style', WCP_LANG_CODE)?>
		</a>
		<a target="_blank" class="button" style="margin-top: -8px;" href="<?php echo $this->promoLink?>"><?php _e('Available in PRO', WCP_LANG_CODE)?></a>
	</label>
	<div class="description"><?php _e('By default all Forms have modal style: it appears on user screen over the whole site. Layered style allows you to show your Form - on selected position: top, bottom, etc. and not over your site - but right near your content.', WCP_LANG_CODE)?></div>
</div>
<span>
	<div class="wcpFormOptRow">
		<span class="wcpOptLabel"><?php _e('Select position for your Form', WCP_LANG_CODE)?></span>
		<br style="clear: both;" />
		<div id="wcpLayeredSelectPosShell">
			<div class="wcpLayeredPosCell" style="width: 30%;" data-pos="top_left"><span class="wcpLayeredPosCellContent"><?php _e('Top Left', WCP_LANG_CODE)?></span></div>
			<div class="wcpLayeredPosCell" style="width: 40%;" data-pos="top"><span class="wcpLayeredPosCellContent"><?php _e('Top', WCP_LANG_CODE)?></span></div>
			<div class="wcpLayeredPosCell" style="width: 30%;" data-pos="top_right"><span class="wcpLayeredPosCellContent"><?php _e('Top Right', WCP_LANG_CODE)?></span></div>
			<br style="clear: both;"/>
			<div class="wcpLayeredPosCell" style="width: 30%;" data-pos="center_left"><span class="wcpLayeredPosCellContent"><?php _e('Center Left', WCP_LANG_CODE)?></span></div>
			<div class="wcpLayeredPosCell" style="width: 40%;" data-pos="center"><span class="wcpLayeredPosCellContent"><?php _e('Center', WCP_LANG_CODE)?></span></div>
			<div class="wcpLayeredPosCell" style="width: 30%;" data-pos="center_right"><span class="wcpLayeredPosCellContent"><?php _e('Center Right', WCP_LANG_CODE)?></span></div>
			<br style="clear: both;"/>
			<div class="wcpLayeredPosCell" style="width: 30%;" data-pos="bottom_left"><span class="wcpLayeredPosCellContent"><?php _e('Bottom Left', WCP_LANG_CODE)?></span></div>
			<div class="wcpLayeredPosCell" style="width: 40%;" data-pos="bottom"><span class="wcpLayeredPosCellContent"><?php _e('Bottom', WCP_LANG_CODE)?></span></div>
			<div class="wcpLayeredPosCell" style="width: 30%;" data-pos="bottom_right"><span class="wcpLayeredPosCellContent"><?php _e('Bottom Right', WCP_LANG_CODE)?></span></div>
			<br style="clear: both;"/>
		</div>
		<?php echo htmlWcp::hidden('params[tpl][layered_pos]')?>
	</div>
</span>
<style type="text/css">
	#wcpLayeredSelectPosShell {
		max-width: 560px;
		height: 380px;
	}
	.wcpLayeredPosCell {
		float: left;
		cursor: pointer;
		height: 33.33%;
		text-align: center;
		vertical-align: middle;
		line-height: 110px;
	}
	.wcpLayeredPosCellContent {
		border: 1px solid #a5b6b2;
		margin: 5px;
		display: block;
		font-weight: bold;
		box-shadow: -3px -3px 6px #a5b6b2 inset;
		color: #739b92;
	}
	.wcpLayeredPosCellContent:hover, .wcpLayeredPosCell.active .wcpLayeredPosCellContent {
		background-color: #e7f5f6; /*rgba(165, 182, 178, 0.3);*/
		color: #00575d;
	}
</style>
<script type="text/javascript">
	jQuery(document).ready(function(){
		var proExplainContent = jQuery('#wcpLayeredProExplainWnd').dialog({
			modal:    true
		,	autoOpen: false
		,	width: 460
		,	height: 180
		});
		jQuery('.wcpLayeredPosCell').click(function(){
			proExplainContent.dialog('open');
		});
	});
</script>
<!--PRO explanation Wnd-->
<div id="wcpLayeredProExplainWnd" style="display: none;" title="<?php _e('Improve Free version', WCP_LANG_CODE)?>">
	<p>
		<?php printf(__('This functionality and more - is available in PRO version. <a class="button button-primary" target="_blank" href="%s">Get it</a> today for 29$', WCP_LANG_CODE), $this->promoLink)?>
	</p>
</div>