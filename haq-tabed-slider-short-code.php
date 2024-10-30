<?php 
/**
 * Short view page to show HAQ tabed slider
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function HAQTS_Script_Functions() {
		
		wp_enqueue_script( 'haq-tabed-slider', plugin_dir_url( __FILE__ ) . '/js/haqts.slider.min.js' );
		wp_enqueue_script( 'haqts-custom-js',  plugin_dir_url( __FILE__ ) . '/js/custom-tabed-script.js' );
		wp_register_style( 'haqts-add-bx-css', plugin_dir_url( __FILE__ ) . '/css/custom-stylesheet.css','','', 'screen' );
		wp_enqueue_style( 'haqts-add-bx-css' );	
	}
	add_action('wp_enqueue_scripts', 'HAQTS_Script_Functions');
	
function HAQTS_ShortCode($sid){ 

?>
    <div id="jssor_1" class="main_body_section">
        <div data-u="loading" class="jssorl-009-spin">
        </div>
        <div data-u="slides" class="slide_outer_section">
			<?php
				/* load saved slides */
				$HAQTS_slide_Details = unserialize(base64_decode(get_post_meta( $sid['id'], 'haqts_total_slide_details', true)));
				$i = 0;
				$totalSlides =  get_post_meta( $sid['id'], 'haqts_total_images_no', true );
				if($totalSlides) {
					if(is_array($HAQTS_slide_Details)){
						foreach($HAQTS_slide_Details as $haqtsSlideDetails) {
							$name 		= 	$haqtsSlideDetails['slide_label_tital'];
							$desc 		= 	$haqtsSlideDetails['slide_desc'];
							$url 		= 	$haqtsSlideDetails['image_url'];
							$url1 		= 	$haqtsSlideDetails['thumbnail_image_url'];
							$url3 		= 	$haqtsSlideDetails['full_image_url']; ?>
							<div>
								<img data-u="image" src="<?php echo esc_url( $url ); ?>" />
								<div data-u="thumb">
									<img data-u="thumb" src="<?php echo esc_url( $url1 ); ?>" />
									<div class="ti"><?php echo $name; ?></div>
								</div>
							</div>
							<?php
							$i++;
						} 
					}
				} else {
					echo 'Please Add Slides';
				}
				?>
        </div>
        <!-- Thumbnail Navigator -->
        <div data-u="thumbnavigator" class="jssort111" data-autocenter="1" data-scale-bottom="0.75">
            <div data-u="slides">
                <div data-u="prototype" class="p">
                    <div data-u="thumbnailtemplate" class="t"></div>
                </div>
            </div>
        </div>
        <!-- Arrow Navigator -->
        <div data-u="arrowleft" class="jssora051 left" data-autocenter="2" data-scale="0.75" data-scale-left="0.75">
            <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                <polyline class="a" points="11040,1920 4960,8000 11040,14080 "></polyline>
            </svg>
        </div>
        <div data-u="arrowright" class="jssora051 right" data-autocenter="2" data-scale="0.75" data-scale-right="0.75">
            <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                <polyline class="a" points="4960,1920 11040,8000 4960,14080 "></polyline>
            </svg>
        </div>
    </div>
    <script type="text/javascript">jssor_1_slider_init();</script>
 
	
<?php }



