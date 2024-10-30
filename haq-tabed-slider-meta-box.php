<?php
/**
 * Load Saved HAQ Tabed Slider Settings by meta boxes
 */
 
 if ( ! defined( 'ABSPATH' ) ) exit;
 
 $PostId = $post->ID;
$HAQTS_setting_key = "HAQ_Slider_Settings_".$PostId;
$HAQTS_settings = unserialize(get_post_meta( $PostId, $HAQTS_setting_key, true)); 

if(isset($HAQTS_settings['HAQTS_Auto_Play'])) 
	$HAQTS_Auto_Play		= $HAQTS_settings['HAQTS_Auto_Play'];
else
	$HAQTS_Auto_Play		= 1;

if(isset($HAQTS_settings['HAQTS_Transition']))
	$HAQTS_Transition   	= $HAQTS_settings['HAQTS_Transition'];
else
	$HAQTS_Transition   	= 1;

if(isset($HAQTS_settings['HAQTS_Arrow']))
	$HAQTS_Arrow   			= $HAQTS_settings['HAQTS_Arrow'];
else
	$HAQTS_Arrow   			= 1;

?>
	
<input type="hidden" id="haqts_metabox_action" name="haqts_metabox_action" value="haqts-metabox-save-settings">

<table class="form-table">
	<tbody>
		
		<tr>
			<th scope="row"><label><?php _e('Auto Play', HAQTS_TD); ?></label></th>
			<td>
				<?php if(!isset($HAQTS_Auto_Play)) $HAQTS_Auto_Play = 1; ?>
				<input type="radio" name="auto-plsy-slide" id="auto-plsy-slide" value="1" <?php if($HAQTS_Auto_Play == 1 ) { echo "checked"; } ?>> <?php _e('Yes', HAQTS_TD); ?> &nbsp;&nbsp;
			
				<input type="radio" name="auto-plsy-slide" id="auto-plsy-slide" value="2" <?php if($HAQTS_Auto_Play == 2 ) { echo "checked"; } ?>> <?php _e('No', HAQTS_TD); ?>
				<p class="description">
					<?php _e('Select Yes/No option to auto play enable or disable', HAQTS_TD); ?>.
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label><?php _e('Slide Transition', HAQTS_TD); ?></label></th>
			<td>
				<?php if(!isset($HAQTS_Transition)) $HAQTS_Transition = 1; ?>
				<input type="radio" name="slider-transition" id="slider-transition" value="1" <?php if($HAQTS_Transition == 1 ) { echo "checked"; } ?>> Fade &nbsp;&nbsp;
				<input type="radio" name="slider-transition" id="slider-transition" value="0" <?php if($HAQTS_Transition == 0 ) { echo "checked"; } ?>> Slide
				<p class="description">
					<?php _e('Select a transition effect you want to apply on slides', HAQTS_TD); ?>.
				</p>
			</td>
		</tr>		
	
		
		<tr>
			<th scope="row"><label><?php _e('Sliding Arrow', HAQTS_TD); ?></label></th>
			<td>
				<?php if(!isset($HAQTS_Arrow)) $HAQTS_Arrow = 1; ?>
				<input type="radio" name="sliding-arrow" id="sliding-arrow" value="1" <?php if($HAQTS_Arrow == 1 ) { echo "checked"; } ?>> <i class="fa fa-check fa-2x"></i> &nbsp;&nbsp;
				<input type="radio" name="sliding-arrow" id="sliding-arrow" value="0" <?php if($HAQTS_Arrow == 0 ) { echo "checked"; } ?>> <i class="fa fa-times fa-2x"></i>
				<p class="description">
					<?php _e('Select Yes/No option to show or hide arrows on mouse hover on slide', HAQTS_TD); ?>
				</p>
			</td>
		</tr>
	</tbody>
</table>
