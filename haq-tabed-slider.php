<?php
/*
Plugin Name: HAQ Tabed Slider
Plugin URI: https://husain25.wordpress.com/plugins/haq-tabed-slider
Description: This plugin creates an image slideshow from the images you upload using the jQuery HAQ Tabed Slider plugin.
Version: 1.0.2
Author: Husain Ahmed
Author URI: https://husain25.wordpress.com/
*/

//Constant Variable

define("HAQTS_TD", "haq-tabed-slider" );
define("HAQTS_PLUGIN_URL", plugin_dir_url(__FILE__));

// add default settings on plugin activation
register_activation_hook( __FILE__, 'HAQTS_DefaultSettings' );
function HAQTS_DefaultSettings() {
    $haqtsSettingsArray = serialize( array(
		"HAQTS_Auto_Play"   			=> 1,
		"HAQTS_Transition"   			=> 1,
		"HAQTS_Arrow"   				=> 1,
    ));
    add_option("HAQTS_default_settings", $haqtsSettingsArray);
}

// Image Croping Function
add_image_size( 'thumbnail_image_url', 250, 250, true );
add_image_size( 'full_image_url', 500,9999 );

class HAQ_Tabed_Slider {

    private static $instance;
    private $admin_thumbnail_size = 150;
    private $thumbnail_size_w = 150;
    private $thumbnail_size_h = 150;
	var $counter;

    public static function haqtabeds() {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }

	private function __construct() {

		$this->counter = 0;

		// image croping
		add_image_size('haqts_admin_thumb', $this->admin_thumbnail_size, $this->admin_thumbnail_size, true);
		add_image_size('haqts_thumb', $this->thumbnail_size_w, $this->thumbnail_size_h, true);

		// custom post type
		add_action('init', array(&$this, 'HAQ_Tabed_Slider_Post_Type'),1);

		// Create metabox
		add_action('add_meta_boxes', array(&$this, 'HAQ_Tabed_Slider_Meta_Box'));
		add_action('admin_init', array(&$this, 'HAQ_Tabed_Slider_Meta_Box'), 1);

		// metabox settings
		add_action('save_post', array(&$this, 'HAQ_Tabed_Slider_Image_Meta_Box'), 9, 1);
		add_action('save_post', array(&$this, 'HAQ_Tabed_Slider_Settings_Meta_Box'), 9, 1);

		// Adding new slide
		add_action('wp_ajax_haqts_get_thumbnail', array(&$this, 'ajax_get_thumbnail_url'));

		add_shortcode('haq_tabed_slider', 'HAQTS_ShortCode');
		
    }


	// Register Custom Post Type
	public function HAQ_Tabed_Slider_Post_Type() {
		$labels = array(
			'name' => _x( 'HAQ Tabed Slider', HAQTS_TD ),
			'singular_name' => _x( 'HAQ Tabed Slider', HAQTS_TD ),
			'add_new' => __( 'Add New Slider', HAQTS_TD ),
			'add_new_item' => __( 'Add New Slider', HAQTS_TD ),
			'edit_item' => __( 'Edit Slider', HAQTS_TD ),
			'new_item' => __( 'New Slider', HAQTS_TD ),
			'view_item' => __( 'View Slider', HAQTS_TD ),
			'search_items' => __( 'Search Slider', HAQTS_TD ),
			'not_found' => __( 'No Slider found', HAQTS_TD ),
			'not_found_in_trash' => __( 'No Slider Found in Trash', HAQTS_TD ),
			'parent_item_colon' => __( 'Parent Slider:', HAQTS_TD ),
			'all_items' => __( 'All Sliders', HAQTS_TD ),
			'menu_name' => _x( 'HAQ Tabed Silder', HAQTS_TD ),
		);

		$args = array(
			'labels' => $labels,
			'hierarchical' => false,
			'supports' => array( 'title', 'revisions' ),
			'public' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 10,
			'menu_icon' => 'dashicons-format-gallery',
			'show_in_nav_menus' => false,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => false,
			'capability_type' => 'post'
		);

        register_post_type( 'haq_tabed_slider', $args );
        add_filter( 'manage_edit-haq_tabed_slider_columns', array(&$this, 'haq_tabed_slider_columns' )) ;
        add_action( 'manage_haq_tabed_slider_posts_custom_column', array(&$this, 'haq_tabed_slider_manage_columns' ), 10, 2 );
	}

	function haq_tabed_slider_columns( $columns ){
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __( 'Slider Title' ),
            'shortcode' => __( 'Short Code' ),
            'author' => __( 'Author' ),
            'status' => __( 'Status' ),
            'date' => __( 'Date' )
           
           
        );
        return $columns;
    }

    function haq_tabed_slider_manage_columns( $column, $post_id ){
        global $post;
        switch( $column ) {
          	case 'shortcode' :
            	echo '<input type="text" value="[haq_tabed_slider id='.$post_id.']" readonly="readonly" />';
           	break;
            case 'status' :
            	echo 'Published';
            break;
          	default :
            break;
        }
    }

	public function HAQ_Tabed_Slider_Meta_Box() {
		add_meta_box( __('Add Slides', HAQTS_TD), __('Add Slides', HAQTS_TD), array(&$this, 'HAQ_Tabed_Slider_Image_Metabox_Function'), 'haq_tabed_slider', 'normal', 'low' );
		add_meta_box( __('HAQ Tabed Slider Settings', HAQTS_TD), __('HAQ Tabed Slider Settings', HAQTS_TD), array(&$this, 'HAQ_Tabed_Slider_Metabox_Function'), 'haq_tabed_slider', 'normal', 'low');
		add_meta_box ( __('HAQ Tabed Slider Shortcode', HAQTS_TD), __('HAQ Tabed Slider Shortcode', HAQTS_TD), array(&$this, 'HAQ_Tabed_Slider_Shotcode_Metabox_Function'), 'haq_tabed_slider', 'side', 'low');
	}


    public function HAQ_Tabed_Slider_Image_Metabox_Function($post) { ?>
		<div id="haqts_wrapper">
			<input type="hidden" id="haqts_action" name="haqts_action" value="haqts-save-settings">
            <ul id="gallery_thumbnail" class="clearfix">
				<?php
				/* load saved photos */
				$HAQTS_slide_Details = unserialize(base64_decode(get_post_meta( $post->ID, 'haqts_total_slide_details', true)));

				//print_r($HAQTS_slide_Details);

				$i = 0;
				$totalSlides =  get_post_meta( $post->ID, 'haqts_total_images_no', true );
				if($totalSlides) {
					if(is_array($HAQTS_slide_Details)){
						
						foreach($HAQTS_slide_Details as $haqtsSlideDetails) {
							
							$name 		= 	$haqtsSlideDetails['slide_label_tital'];
							$desc 		= 	$haqtsSlideDetails['slide_desc'];
							$UniqueKey 	= 	substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
							$url 		= 	$haqtsSlideDetails['image_url'];
							$url1 		= 	$haqtsSlideDetails['thumbnail_image_url'];
							$url3 		= 	$haqtsSlideDetails['full_image_url']; ?>
							
							<li class="haqts-image-loop" id="haq_image">
								<a class="gallery_remove delete_slide" href="#gallery_remove">
									<img src="<?php echo  HAQTS_PLUGIN_URL.'css/delete-icon.png'; ?>" />
								</a>
								<div class="section-inner-div" >
									<img src="<?php echo esc_url ( $url1 ); ?>" class="haqts_thumbnail_image" alt=""  style="">
									<input type="hidden" id="unique_string[]" name="unique_string[]" value="<?php echo esc_attr( $UniqueKey ); ?>" />
								</div>
								<div class="section-inner-div" >
									<input type="text" id="image_url[]" name="image_url[]" class="haqts_text"  value="<?php echo esc_url( $url ); ?>"  readonly="readonly" style="display:none;" />
									<input type="text" id="thumbnail_image_url[]" name="thumbnail_image_url[]" class="haqts_text"  value="<?php echo esc_url( $url1 ); ?>"  readonly="readonly" style="display:none;" />
									<input type="text" id="full_image_url[]" name="full_image_url[]" class="haqts_text"  value="<?php echo esc_url( $url3 ); ?>"  readonly="readonly" style="display:none;" />
									<p>
										<label><?php _e('Slide Title', HAQTS_TD); ?></label>
										<input type="text" id="slide_label_tital[]" name="slide_label_tital[]" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php _e('Enter Slide Title', HAQTS_TD); ?>" class="haqts_text">
									</p>
									<p>
										<label><?php _e('Slide Descriptions', HAQTS_TD); ?></label>
										<textarea rows="2" cols="50" id="slide_desc[]" name="slide_desc[]" placeholder="<?php _e('Enter Slide Description', HAQTS_TD); ?>" class="haqts_text"><?php echo htmlentities( $desc ); ?></textarea>
										
									</p>
								</div>
							</li>
							<?php
							$i++;
						} 
					}
				} else {
					$totalSlides = 0;
				}
				?>
            </ul>

			
        </div>

		<!-- New Image Button-->
		<div style="clear:left;"></div>
		<div class="haqts-image-loop add_new_image" id="slide_upload_button" data-uploader_title="Upload Image" data-uploader_button_text="Select" >
			<div class="dashicons dashicons-plus"></div>
			<p><?php _e('Add New Slide', HAQTS_TD); ?></p>
		</div>
		<div class="haqts-image-loop del_slider_image" id="slide_delete_button">
			<div class="dashicons dashicons-trash"></div>
			<p><?php _e('Delete All Slides', HAQTS_TD); ?></p>
		</div>
		<div style="clear:left;"></div>
       
		<?php
    }


    public function HAQ_Tabed_Slider_Metabox_Function($post) {
		wp_enqueue_script('haqts-admin-script', HAQTS_PLUGIN_URL . 'js/haqts-admin-script.js', array('jquery'));
		wp_enqueue_media();
		wp_enqueue_style('haqts-admin-style', HAQTS_PLUGIN_URL.'css/haqts-admin-style.css', array(), '1.0');
		require_once('haq-tabed-slider-meta-box.php');
	}

	public function HAQ_Tabed_Slider_Shotcode_Metabox_Function() { ?>
		<p><?php _e("Use shortcode to where you want to show Slider", HAQTS_TD);?></p>
		<input readonly="readonly" type="text" value="<?php echo "[haq_tabed_slider id=".get_the_ID()."]"; ?>">
		<?php
	}


	public function admin_thumb_url($id) {
		$image  = wp_get_attachment_image_src($id, 'image_url', true);
        $image1 = wp_get_attachment_image_src($id, 'thumbnail_image_url', true);
        $image3 = wp_get_attachment_image_src($id, 'full_image_url', true);
		$UniqueString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
        ?>
		<li class="haqts-image-loop" id="haq_image">
			<a class="delete_slide" href="#gallery_remove">
				<img src="<?php echo  HAQTS_PLUGIN_URL.'css/delete-icon.png'; ?>" />
			</a>
			<div class="section-inner-div" >
				<img src="<?php echo esc_url( $image1[0] ); ?>" class="haqts_thumbnail_image" alt="">
				</div>
			<div class="section-inner-div">
				<input type="text" id="image_url[]" name="image_url[]" class="haqts_text"  value="<?php echo esc_url( $image[0] ); ?>"  readonly="readonly" style="display:none;" />
				<input type="text" id="thumbnail_image_url[]" name="thumbnail_image_url[]" class="haqts_text"  value="<?php echo esc_url( $image1[0] ); ?>"  readonly="readonly" style="display:none;" />
				<input type="text" id="full_image_url[]" name="full_image_url[]" class="haqts_text"  value="<?php echo esc_url( $image3[0] ); ?>"  readonly="readonly" style="display:none;" />
				<p>
					<label><?php _e('Slide Title', HAQTS_TD); ?></label>
					<input type="text" id="slide_label_tital[]" name="slide_label_tital[]" placeholder="<?php _e('Enter Slide Title Here', HAQTS_TD); ?>" class="haqts_text">
				</p>
				<p>
					<label><?php _e('Slide Description', HAQTS_TD); ?></label>
					<textarea rows="2" cols="50" id="slide_desc[]" name="slide_desc[]" placeholder="<?php _e('Enter Slide Description Here', HAQTS_TD); ?>" class="haqts_text"></textarea>
				</p>
			</div>
		</li>
        <?php
    }

    public function ajax_get_thumbnail_url() {
        echo $this->admin_thumb_url($_POST['imageid']);
        die;
    }

    public function HAQ_Tabed_Slider_Image_Meta_Box($PostID) {
		if(isset($PostID) && isset($_POST['haqts_action'])) {
			$TotalImages = count($_POST['image_url']);
			$ImagesArray = array();
			if($TotalImages) {
				for($i=0; $i < $TotalImages; $i++) {
					$image_label =stripslashes(sanitize_text_field($_POST['slide_label_tital'][$i]));
					$image_desc = stripslashes($_POST['slide_desc'][$i]);
					$url = sanitize_text_field( $_POST['image_url'][$i] );
					$url1 = sanitize_text_field($_POST['thumbnail_image_url'][$i] );
					$url3 = sanitize_text_field($_POST['full_image_url'][$i] );
					$ImagesArray[] = array(
						'slide_label_tital' => $image_label,
						'slide_desc' => $image_desc,
						'image_url' => $url,
						'thumbnail_image_url' => $url1,
						'full_image_url' => $url3,
					);
				}
				update_post_meta($PostID, 'haqts_total_slide_details', base64_encode(serialize($ImagesArray)));
				update_post_meta($PostID, 'haqts_total_images_no', $TotalImages);
			} else {
				$TotalImages = 0;
				update_post_meta($PostID, 'haqts_total_images_no', $TotalImages);
				$ImagesArray = array();
				update_post_meta($PostID, 'haqts_total_slide_details', base64_encode(serialize($ImagesArray)));
			}
		}
	}

	public function HAQ_Tabed_Slider_Settings_Meta_Box($PostID) {
		if(isset($PostID) && isset($_POST['haqts_metabox_action']) == "haqts-metabox-save-settings") {
			
			$HAQTS_Auto_Play			=	 sanitize_text_field ( $_POST['auto-plsy-slide'] );
			$HAQTS_Transition			=	 sanitize_text_field ( $_POST['slider-transition'] );
			$HAQTS_Arrow				=	 sanitize_text_field ( $_POST['sliding-arrow'] );
			
			$HAQTS_Array = serialize( array(
			
				'HAQTS_Auto_Play'  		=> 	$HAQTS_Auto_Play,
				'HAQTS_Transition'  	=> 	$HAQTS_Transition,
				'HAQTS_Arrow'  			=> 	$HAQTS_Arrow,
			));

			$HAQTS_Settings_By_Id = "HAQ_Slider_Settings_".$PostID;
			update_post_meta($PostID, $HAQTS_Settings_By_Id, $HAQTS_Array);
		}
	}
}

global $HAQTS;
$HAQTS = HAQ_Tabed_Slider::haqtabeds();
require_once("haq-tabed-slider-short-code.php");
