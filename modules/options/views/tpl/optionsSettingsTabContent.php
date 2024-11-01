<section class="supsystic-bar">
	<ul class="supsystic-bar-controls">
		<li title="<?php _e('Save all options')?>">
			<button class="button button-primary" id="wcpSettingsSaveBtn" data-toolbar-button>
				<i class="fa fa-fw fa-save"></i>
				<?php _e('Save', WCP_LANG_CODE)?>
			</button>
		</li>
	</ul>
	<div style="clear: both;"></div>
	<hr />
</section>
<section>
	<form id="wcpSettingsForm" class="wcpInputsWithDescrForm">
		<div class="supsystic-item supsystic-panel">
			<div id="containerWrapper">
				<table class="form-table">
					<?php foreach($this->options as $optCatKey => $optCatData) { ?>
						<?php if(isset($optCatData['opts']) && !empty($optCatData['opts'])) { ?>
							<?php foreach($optCatData['opts'] as $optKey => $opt) { ?>
								<?php
									$htmlType = isset($opt['html']) ? $opt['html'] : false;
									if(empty($htmlType)) continue;
									$opt['value'] = is_array($opt['value'])
										? array_map('stripslashes', $opt['value'])
										: stripslashes( $opt['value'] );
									$htmlOpts = array('value' => is_array($opt['value']) ? array_map('esc_html', $opt['value']) : esc_html($opt['value']), 'attrs' => 'data-optkey="'. $optKey. '"');
									if(in_array($htmlType, array('selectbox', 'selectlist', 'draggableCheckboxList')) && isset($opt['options'])) {
										if(is_callable($opt['options'])) {
											$htmlOpts['options'] = call_user_func( $opt['options'] );
										} elseif(is_array($opt['options'])) {
											$htmlOpts['options'] = $opt['options'];
										}
									}
//									if(isset($opt['pro']) && !empty($opt['pro'])) {
//										$htmlOpts['attrs'] .= ' class="wcpProOpt"';
//									}
									if(!empty($opt['specOpts']) && is_array($opt['specOpts'])) {
										if(!empty($opt['specOpts']['attrs'])) {

											if(is_array($opt['specOpts']['attrs'])) {
												$additionalAttributes = implode(' ', $opt['specOpts']['attrs']);
											} else {
												$additionalAttributes = $opt['specOpts']['attrs'];
											}
											$htmlOpts['attrs'] .= ' ' . $additionalAttributes;
										}
									}
								?>
								<tr
									<?php if(isset($opt['connect']) && $opt['connect']) { ?>
										data-connect="<?php echo $opt['connect'];?>" style="display: none;"
									<?php }?>
								>
									<th scope="row" class="">
										<!-- not show hidden rows -->
										<?php if(!in_array($htmlType, array('hidden'))): ?>
											<?php echo $opt['label']?>
											<?php if(!empty($opt['changed_on'])) {?>
												<br />
												<span class="description">
													<?php
													$opt['value']
														? printf(__('Turned On %s', WCP_LANG_CODE), dateWcp::_($opt['changed_on']))
														: printf(__('Turned Off %s', WCP_LANG_CODE), dateWcp::_($opt['changed_on']))
													?>
												</span>
											<?php }?>
											<?php if(isset($opt['pro']) && !empty($opt['pro'])) { ?>
												<span class="wcpProOptMiniLabel">
													<a href="<?php echo $opt['pro']?>" target="_blank">
														<?php _e('PRO option', WCP_LANG_CODE)?>
													</a>
												</span>
											<?php }?>
										<?php endif; ?>
									</th>
									<td class="col-w-1perc">
										<?php if(!empty($opt['desc'])): ?>
											<i class="fa fa-question wcp-tooltip" title="<?php echo esc_html($opt['desc'])?>"></i>
										<?php endif; ?>
									</td>
									<td class="">
										<?php echo htmlWcp::$htmlType('opt_values['. $optKey. ']', $htmlOpts)?>
										<?php switch( $optKey ) {
											case 'send_engine_test': ?>
												<a href="#" id="wcpSendTestMailBtn" class="button"><i class="fa fa-paper-plane-o"></i><?php _e('Send test', WCP_LANG_CODE);?></a>
												<div id="wcpSendTestMailBtnMsg"></div>
											<?php break;
										}?>
									</td>
									<td class="">
										<div id="wcpFormOptDetails_<?php echo $optKey?>" class="wcpOptDetailsShell">
										<?php
											if(isset($opt['add_sub_opts']) && !empty($opt['add_sub_opts'])) {
												if(is_string($opt['add_sub_opts'])) {
													echo $opt['add_sub_opts'];
												} elseif(is_callable($opt['add_sub_opts'])) {
													echo call_user_func_array($opt['add_sub_opts'], array($this->options));
												}
											}
										?>
										</div>
									</td>
								</tr>
							<?php }?>
						<?php }?>
					<?php }?>
				</table>
				<div style="clear: both;"></div>
			</div>
		</div>
		<?php echo htmlWcp::hidden('mod', array('value' => 'options'))?>
		<?php echo htmlWcp::hidden('action', array('value' => 'saveGroup'))?>
	</form>
</section>