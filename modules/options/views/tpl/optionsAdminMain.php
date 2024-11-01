<style type="text/css">
	.wcpAdminMainLeftSide {
		width: 56%;
		float: left;
	}
	.wcpAdminMainRightSide {
		width: <?php echo (empty($this->optsDisplayOnMainPage) ? 100 : 40)?>%;
		float: left;
		text-align: center;
	}
	#wcpMainOccupancy {
		box-shadow: none !important;
	}
</style>
<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<?php _e('Main page Go here!!!!', WCP_LANG_CODE)?>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>