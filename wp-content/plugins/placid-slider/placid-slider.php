<?php
/*
Plugin Name: Placid Slider
Plugin URI: http://slidervilla.com/placid/
Description: Placid Slider adds a smoothly scrolling horizontal slideshow to any location of your blog.
Version: 1.1	
Author: Tejaswini Deshpande
Author URI: http://slidervilla.com/
Wordpress version supported: 3.0 and above
*/
/*  Copyright 2010-2013  Slider Villa  (email : tedeshpa@gmail.com)
*/
//defined global variables and constants here
global $placid_slider,$default_placid_slider_settings;
$placid_slider = get_option('placid_slider_options');
$default_placid_slider_settings = array('speed'=>'1', 
	                       'no_posts'=>'10',
						   'bg_color'=>'#222222', 
						   'height'=>'180',
						   'width'=>'500',
						   'iwidth'=>'200',
						   'border'=>'0',
						   'brcolor'=>'#dddddd',
						   'prev_next'=>'0',
						   'title_text'=>'',
						   'title_from'=>'0',
						   'title_font'=>'Trebuchet MS,sans-serif',
						   'title_fsize'=>'18',
						   'title_fstyle'=>'bold',
						   'title_fcolor'=>'#3F4C6B',
						   'ptitle_font'=>'Georgia,serif',
						   'ptitle_fsize'=>'12',
						   'ptitle_fstyle'=>'normal',
						   'ptitle_fcolor'=>'#ffffff',
						   'img_align'=>'none',
						   'img_height'=>'180',
						   'img_width'=>'200',
						   'img_border'=>'0',
						   'img_brcolor'=>'#D8E7EE',
						   'show_content'=>'0',
						   'content_font'=>'Verdana,Geneva,sans-serif',
						   'content_fsize'=>'11',
						   'content_fstyle'=>'normal',
						   'content_fcolor'=>'#cccccc',
						   'content_from'=>'content',
						   'content_chars'=>'',
						   'bg'=>'0',
						   'image_only'=>'0',
						   'allowable_tags'=>'',
						   'more'=>'',
						   'img_pick'=>array('1','slider_thumbnail','1','1','1','1'), //use custom field/key, name of the key, use post featured image, pick the image attachment, attachment order,scan images
						   'user_level'=>'edit_others_posts',
						   'custom_nav'=>'',
						   'crop'=>'0',
						   'multiple_sliders'=>'1',
						   'content_limit'=>'10',
						   'stylesheet'=>'default',
						   'shortcode'=>'1',
						   'rand'=>'0',
						   'ver'=>'1',
						   'support'=>'1',
						   'timthumb'=>'0',
						   'fouc'=>'0',
						   'css'=>'',
						   'preview'=>'0',
						   'slider_id'=>'1',
						   'catg_slug'=>'',
						   'estimatedwidth'=>'200',
						   'setname'=>'Set',
						   'disable_preview'=>'0',
						   'orientation'=>'0',
						   'tot_height'=>'540',
						   'a_attr'=>'',
						   'fields'=>'',
						   'pphoto'=>'0',
						   'custom_post'=>'0',
						   'noscript'=>'This page is having a slideshow that uses Javascript. Your browser either doesn\'t support Javascript or you have it turned off. To see this page as it is meant to appear please use a Javascript enabled browser.'
			              );
define('PLACID_SLIDER_TABLE','placid_slider'); //Slider TABLE NAME
define('PLACID_SLIDER_META','placid_slider_meta'); //Meta TABLE NAME
define('PLACID_SLIDER_POST_META','placid_slider_postmeta'); //Meta TABLE NAME
define("PLACID_SLIDER_VER","1.1",false);//Current Version of Placid Slider
if ( ! defined( 'PLACID_SLIDER_PLUGIN_BASENAME' ) )
	define( 'PLACID_SLIDER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
if ( ! defined( 'PLACID_SLIDER_CSS_DIR' ) ){
	define( 'PLACID_SLIDER_CSS_DIR', WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/css/skins/' );
}
// Create Text Domain For Translations
load_plugin_textdomain('placid-slider', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');

function install_placid_slider() {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.PLACID_SLIDER_TABLE;
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "CREATE TABLE $table_name (
					id int(5) NOT NULL AUTO_INCREMENT,
					post_id int(11) NOT NULL,
					date datetime NOT NULL,
					slider_id int(5) NOT NULL DEFAULT '1',
					slide_order int(5) NOT NULL DEFAULT '0',
					UNIQUE KEY id(id)
				);";
		$rs = $wpdb->query($sql);
	}

   	$meta_table_name = $table_prefix.PLACID_SLIDER_META;
	if($wpdb->get_var("show tables like '$meta_table_name'") != $meta_table_name) {
		$sql = "CREATE TABLE $meta_table_name (
					slider_id int(5) NOT NULL AUTO_INCREMENT,
					slider_name varchar(100) NOT NULL default '',
					UNIQUE KEY slider_id(slider_id)
				);";
		$rs2 = $wpdb->query($sql);
		
		$sql = "INSERT INTO $meta_table_name (slider_id,slider_name) VALUES('1','Placid Slider');";
		$rs3 = $wpdb->query($sql);
	}
	
	$slider_postmeta = $table_prefix.PLACID_SLIDER_POST_META;
	if($wpdb->get_var("show tables like '$slider_postmeta'") != $slider_postmeta) {
		$sql = "CREATE TABLE $slider_postmeta (
					post_id int(11) NOT NULL,
					slider_id int(5) NOT NULL default '1',
					UNIQUE KEY post_id(post_id)
				);";
		$rs4 = $wpdb->query($sql);
	}
   // Placid Slider Settings and Options
   $default_slider = array();
   global $default_placid_slider_settings;
   $default_slider = $default_placid_slider_settings;
   
   	   $default_scounter='1';
	   $scounter=get_option('placid_slider_scounter');
	   if(!isset($scounter) or $scounter=='' or empty($scounter)){
	      update_option('placid_slider_scounter',$default_scounter);
		  $scounter=$default_scounter;
	   }
	   
	   for($i=1;$i<=$scounter;$i++){
	       if ($i==1){
		    $placid_slider_options='placid_slider_options';
		   }
		   else{
		    $placid_slider_options='placid_slider_options'.$i;
		   }
		   $placid_slider_curr=get_option($placid_slider_options);
	   				 
		   if(!$placid_slider_curr) {
			 $placid_slider_curr = array();
		   }
			  
		   foreach($default_slider as $key=>$value) {
			  if(!isset($placid_slider_curr[$key])) {
				 $placid_slider_curr[$key] = $value;
			  }
		   }
		   delete_option($placid_slider_options);	  
		   update_option($placid_slider_options,$placid_slider_curr);
	   } //end for loop
}
register_activation_hook( __FILE__, 'install_placid_slider' );
require_once (dirname (__FILE__) . '/includes/placid-slider-functions.php');
require_once (dirname (__FILE__) . '/includes/sslider-get-the-image-functions.php');

//This adds the post to the slider
function placid_add_to_slider($post_id) {
global $placid_slider;
 if(isset($_POST['placid-sldr-verify']) and current_user_can( $placid_slider['user_level'] ) ) {
	global $wpdb, $table_prefix, $post;
	$table_name = $table_prefix.PLACID_SLIDER_TABLE;
	
	if(isset($_POST['placid-slider']) and !isset($_POST['placid_slider_name'])) {
	  $slider_id = '1';
	  if(is_post_on_any_placid_slider($post_id)){
	     $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		 $wpdb->query($sql);
	  }
	  
	  if(isset($_POST['placid-slider']) and $_POST['placid-slider'] == "placid-slider" and !placid_slider($post_id,$slider_id)) {
		$dt = date('Y-m-d H:i:s');
		$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$post_id', '$dt', '$slider_id')";
		$wpdb->query($sql);
	  }
	}
	if(isset($_POST['placid-slider']) and $_POST['placid-slider'] == "placid-slider" and isset($_POST['placid_slider_name'])){
	  $slider_id_arr = $_POST['placid_slider_name'];
	  $post_sliders_data = placid_ss_get_post_sliders($post_id);
	  
	  foreach($post_sliders_data as $post_slider_data){
		if(!in_array($post_slider_data['slider_id'],$slider_id_arr)) {
		  $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		  $wpdb->query($sql);
		}
	  }

		foreach($slider_id_arr as $slider_id) {
			if(!placid_slider($post_id,$slider_id)) {
				$dt = date('Y-m-d H:i:s');
				$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$post_id', '$dt', '$slider_id')";
				$wpdb->query($sql);
			}
		}
	}
	
	$table_name = $table_prefix.PLACID_SLIDER_POST_META;
	if(isset($_POST['placid_display_slider']) and !isset($_POST['placid_display_slider_name'])) {
	  $slider_id = '1';
	}
	if(isset($_POST['placid_display_slider']) and isset($_POST['placid_display_slider_name'])){
	  $slider_id = $_POST['placid_display_slider_name'];
	}
  	if(isset($_POST['placid_display_slider'])){	
		  if(!placid_ss_post_on_slider($post_id,$slider_id)) {
		    $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		    $wpdb->query($sql);
			$sql = "INSERT INTO $table_name (post_id, slider_id) VALUES ('$post_id', '$slider_id')";
			$wpdb->query($sql);
		  }
	}
	$placid_slider_style = get_post_meta($post_id,'_placid_slider_style',true);
	$post_placid_slider_style=$_POST['_placid_slider_style'];
	if($placid_slider_style != $post_placid_slider_style and isset($post_placid_slider_style) and !empty($post_placid_slider_style)) {
	  update_post_meta($post_id, '_placid_slider_style', $_POST['_placid_slider_style']);	
	}
	
	$thumbnail_key = $placid_slider['img_pick'][1];
	$placid_sslider_thumbnail = get_post_meta($post_id,$thumbnail_key,true);
	$post_slider_thumbnail=$_POST['placid_sslider_thumbnail'];
	if($placid_sslider_thumbnail != $post_slider_thumbnail and isset($post_slider_thumbnail) and !empty($post_slider_thumbnail)) {
	  update_post_meta($post_id, $thumbnail_key, $_POST['placid_sslider_thumbnail']);	
	}
	
	$placid_link_attr = get_post_meta($post_id,'placid_link_attr',true);
	$link_attr=htmlentities($_POST['placid_link_attr'],ENT_QUOTES);
	if($placid_link_attr != $link_attr) {
	  update_post_meta($post_id, 'placid_link_attr', $link_attr);	
	}
	
	$placid_sslider_link = get_post_meta($post_id,'placid_slide_redirect_url',true);
	$link=$_POST['placid_sslider_link'];
	if($placid_sslider_link != $link) {
	  update_post_meta($post_id, 'placid_slide_redirect_url', $link);	
	}
	
	$placid_sslider_nolink = get_post_meta($post_id,'placid_sslider_nolink',true);
	$post_placid_sslider_nolink = $_POST['placid_sslider_nolink'];
	if($placid_sslider_nolink != $post_placid_sslider_nolink) {
	  update_post_meta($post_id, 'placid_sslider_nolink', $_POST['placid_sslider_nolink']);	
	}
	
  } //placid-sldr-verify
}

//Removes the post from the slider, if you uncheck the checkbox from the edit post screen
function placid_remove_from_slider($post_id) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.PLACID_SLIDER_TABLE;
	
	// authorization
	if (!current_user_can('edit_post', $post_id))
		return $post_id;
	// origination and intention
	if (!wp_verify_nonce($_POST['placid-sldr-verify'], 'PlacidSlider'))
		return $post_id;
	
    if(empty($_POST['placid-slider']) and is_post_on_any_placid_slider($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
	
	$display_slider = $_POST['placid_display_slider'];
	$table_name = $table_prefix.PLACID_SLIDER_POST_META;
	if(empty($display_slider) and placid_ss_slider_on_this_post($post_id)){
	  $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		    $wpdb->query($sql);
	}
} 
  
function placid_delete_from_slider_table($post_id){
    global $wpdb, $table_prefix;
	$table_name = $table_prefix.PLACID_SLIDER_TABLE;
    if(is_post_on_any_placid_slider($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
	$table_name = $table_prefix.PLACID_SLIDER_POST_META;
    if(placid_ss_slider_on_this_post($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
}

// Slider checkbox on the admin page

function placid_slider_edit_custom_box(){
   placid_add_to_slider_checkbox();
}

function placid_slider_add_custom_box() {
 global $placid_slider;
 if (current_user_can( $placid_slider['user_level'] )) {
	if( function_exists( 'add_meta_box' ) ) {
	    $post_types=get_post_types(); 
		$remove_post_type_arr=$placid_slider['remove_metabox'];
		if(!isset($remove_post_type_arr) or !is_array($remove_post_type_arr) ) $remove_post_type_arr=array();
		foreach($post_types as $post_type) {
			if(!in_array($post_type,$remove_post_type_arr)){
				add_meta_box( 'placid_slider_box', __( 'Placid Slider' , 'placid-slider'), 'placid_slider_edit_custom_box', $post_type, 'advanced' );
			}
		}
		//add_meta_box( $id,   $title,     $callback,   $page, $context, $priority ); 
	} 
 }
}
/* Use the admin_menu action to define the custom boxes */
add_action('admin_menu', 'placid_slider_add_custom_box');

function placid_add_to_slider_checkbox() {
	global $post, $placid_slider;
	if (current_user_can( $placid_slider['user_level'] )) {
		$extra = "";
		
		$post_id = $post->ID;
		
		if(isset($post->ID)) {
			$post_id = $post->ID;
			if(is_post_on_any_placid_slider($post_id)) { $extra = 'checked="checked"'; }
		} 
		
		$post_slider_arr = array();
		
		$post_sliders = placid_ss_get_post_sliders($post_id);
		if($post_sliders) {
			foreach($post_sliders as $post_slider){
			   $post_slider_arr[] = $post_slider['slider_id'];
			}
		}
		
		$sliders = placid_ss_get_sliders();
?>
		<div class="slider_checkbox">
		<table class="form-table">
				
				<tr valign="top">
				<th scope="row"><input type="checkbox" class="sldr_post" name="placid-slider" value="placid-slider" <?php echo $extra;?> />
				<label for="placid-slider"><?php _e('Add this post/page to','placid-slider'); ?> </label></th>
				<td><select name="placid_slider_name[]" multiple="multiple" size="3" >
                <?php foreach ($sliders as $slider) { ?>
                  <option value="<?php echo $slider['slider_id'];?>" <?php if(in_array($slider['slider_id'],$post_slider_arr)){echo 'selected';} ?>><?php echo $slider['slider_name'];?></option>
                <?php } ?>
                </select>
				<input type="hidden" name="placid-sldr-verify" id="placid-sldr-verify" value="<?php echo wp_create_nonce('PlacidSlider');?>" />
				</td>
				</tr>
                
         <?php if($placid_slider['multiple_sliders'] == '1') {?>
                <tr valign="top">
				<th scope="row">				
				<label for="placid_display_slider"><?php _e('Display ','placid-slider'); ?></label>
				<select name="placid_display_slider_name">
                <?php foreach ($sliders as $slider) { ?>
                  <option value="<?php echo $slider['slider_id'];?>" <?php if(placid_ss_post_on_slider($post_id,$slider['slider_id'])){echo 'selected';} ?>><?php echo $slider['slider_name'];?></option>
                <?php } ?>
                </select> 
				<?php _e('on this Post/Page','placid-slider'); ?></th>
				<td><input type="checkbox" class="sldr_post" name="placid_display_slider" value="1" <?php if(placid_ss_slider_on_this_post($post_id)){echo "checked";}?> /> 
				<?php _e('(Add the ','placid-slider'); ?><a href="http://guides.slidervilla.com/placid-slider/template-tags/simple-template-tag/" target="_blank"><?php _e('Placid Slider template tag','placid-slider'); ?></a> <?php _e('manually on your page.php/single.php or another page template file)','placid-slider'); ?></td>
				</tr>
          <?php } ?>
	    </div>
        <div>
        <?php
        $placid_slider_style = get_post_meta($post->ID,'_placid_slider_style',true);
        ?>
         <tr valign="top">
		 <th scope="row"><label for="_placid_slider_style"><?php _e('Stylesheet to use if slider is displayed on this Post/Page','placid-slider'); ?> </label></th>
		 <td><select name="_placid_slider_style" >
			<?php 
            $directory = PLACID_SLIDER_CSS_DIR;
            if ($handle = opendir($directory)) {
                while (false !== ($file = readdir($handle))) { 
                 if($file != '.' and $file != '..') { ?>
                  <option value="<?php echo $file;?>" <?php if (($placid_slider_style == $file) or (empty($placid_slider_style) and $placid_slider['stylesheet'] == $file)){ echo "selected";}?> ><?php echo $file;?></option>
             <?php  } }
                closedir($handle);
            }
            ?>
        </select></td>
		</tr>
        
  <?php         $thumbnail_key = $placid_slider['img_pick'][1];
                $placid_sslider_thumbnail= get_post_meta($post_id, $thumbnail_key, true); 
				$placid_sslider_link= get_post_meta($post_id, 'placid_slide_redirect_url', true);  
				$placid_sslider_nolink=get_post_meta($post_id, 'placid_sslider_nolink', true);
				$placid_link_attr=get_post_meta($post_id, 'placid_link_attr', true);
  ?>
				<tr valign="top">
				<th scope="row"><label for="placid_sslider_thumbnail"><?php _e('Custom Thumbnail Image(url)','placid-slider'); ?></label></th>
                <td><input type="text" name="placid_sslider_thumbnail" class="placid_sslider_thumbnail" value="<?php echo $placid_sslider_thumbnail;?>" size="50" /></td>
				</tr>
				
				<tr valign="top">
                <th scope="row"><label for="placid_link_attr"><?php _e('Slide Link (anchor) attributes ','placid-slider'); ?></label></th>
                <td><input type="text" name="placid_link_attr" class="placid_link_attr" value="<?php echo $placid_link_attr;?>" size="50" /><small><?php _e('e.g. target="_blank" rel="external nofollow"','placid-slider'); ?></small></td>
				</tr>
				
				<tr valign="top">
                <th scope="row"><label for="placid_sslider_link"><?php _e('Slide Link URL ','placid-slider'); ?></label></th>
                <td><input type="text" name="placid_sslider_link" class="placid_sslider_link" value="<?php echo $placid_sslider_link;?>" size="50" /><small><?php _e('If left empty, it will be by default linked to the permalink.','placid-slider'); ?></small> </td>
				</tr>
				
                <tr valign="top">
				<th scope="row"><label for="placid_sslider_nolink"><?php _e('Do not link this slide to any page(url)','placid-slider'); ?> </label></th>
                <td><input type="checkbox" name="placid_sslider_nolink" class="placid_sslider_nolink" value="1" <?php if($placid_sslider_nolink=='1'){echo "checked";}?>  /></td>
				</tr>
				</table>
				
                 </div>
        
<?php }
}

//CSS for the checkbox on the admin page
function placid_slider_checkbox_css() {
?><style type="text/css" media="screen">.slider_checkbox{margin: 5px 0 10px 0;padding:3px;font-weight:bold;}.slider_checkbox input,.slider_checkbox select{font-weight:bold;}.slider_checkbox label,.slider_checkbox input,.slider_checkbox select{vertical-align:top;}</style>
<?php
}

add_action('publish_post', 'placid_add_to_slider');
add_action('publish_page', 'placid_add_to_slider');
add_action('edit_post', 'placid_add_to_slider');
add_action('publish_post', 'placid_remove_from_slider');
add_action('edit_post', 'placid_remove_from_slider');
add_action('deleted_post','placid_delete_from_slider_table');

add_action('edit_attachment', 'placid_add_to_slider');
add_action('delete_attachment','placid_delete_from_slider_table');

function placid_slider_plugin_url( $path = '' ) {
	global $wp_version;
	if ( version_compare( $wp_version, '2.8', '<' ) ) { // Using WordPress 2.7
		$folder = dirname( plugin_basename( __FILE__ ) );
		if ( '.' != $folder )
			$path = path_join( ltrim( $folder, '/' ), $path );

		return plugins_url( $path );
	}
	return plugins_url( $path, __FILE__ );
}

function placid_get_string_limit($output, $max_char)
{
    $output = str_replace(']]>', ']]&gt;', $output);
    $output = strip_tags($output);

  	if ((strlen($output)>$max_char) && ($espacio = strpos($output, " ", $max_char )))
	{
        $output = substr($output, 0, $espacio).'...';
		return $output;
   }
   else
   {
      return $output;
   }
}

function placid_slider_get_first_image($post) {
	$first_img = '';
	ob_start();
	ob_end_clean();
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	$first_img = $matches [1] [0];
	return $first_img;
}
add_filter( 'plugin_action_links', 'placid_sslider_plugin_action_links', 10, 2 );

function placid_sslider_plugin_action_links( $links, $file ) {
	if ( $file != PLACID_SLIDER_PLUGIN_BASENAME )
		return $links;

	$url = placid_sslider_admin_url( array( 'page' => 'placid-slider-settings' ) );

	$settings_link = '<a href="' . esc_attr( $url ) . '">'
		. esc_html( __( 'Settings') ) . '</a>';

	array_unshift( $links, $settings_link );

	return $links;
}

//New Custom Post Type
if( $placid_slider['custom_post'] == '1' and !post_type_exists('slidervilla') ){
	add_action( 'init', 'placid_post_type', 11 );
	function placid_post_type() {
			$labels = array(
			'name' => _x('SliderVilla Slides', 'post type general name'),
			'singular_name' => _x('SliderVilla Slide', 'post type singular name'),
			'add_new' => _x('Add New', 'placid'),
			'add_new_item' => __('Add New SliderVilla Slide'),
			'edit_item' => __('Edit SliderVilla Slide'),
			'new_item' => __('New SliderVilla Slide'),
			'all_items' => __('All SliderVilla Slides'),
			'view_item' => __('View SliderVilla Slide'),
			'search_items' => __('Search SliderVilla Slides'),
			'not_found' =>  __('No SliderVilla slides found'),
			'not_found_in_trash' => __('No SliderVilla slides found in Trash'), 
			'parent_item_colon' => '',
			'menu_name' => 'SliderVilla Slides'

			);
			$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => true, 
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title','editor','thumbnail','excerpt','custom-fields')
			); 
			register_post_type('slidervilla',$args);
	}

	//add filter to ensure the text SliderVilla, or slidervilla, is displayed when user updates a slidervilla 
	add_filter('post_updated_messages', 'placid_updated_messages');
	function placid_updated_messages( $messages ) {
	  global $post, $post_ID;

	  $messages['placid'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('SliderVilla Slide updated. <a href="%s">View SliderVilla slide</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('SliderVilla Slide updated.'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('SliderVilla Slide restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('SliderVilla Slide published. <a href="%s">View SliderVilla slide</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Placid saved.'),
		8 => sprintf( __('SliderVilla Slide submitted. <a target="_blank" href="%s">Preview SliderVilla slide</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('SliderVilla Slides scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview SliderVilla slide</a>'),
		  // translators: Publish box date format, see http://php.net/date
		  date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('SliderVilla Slide draft updated. <a target="_blank" href="%s">Preview SliderVilla slide</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	  );

	  return $messages;
	}
} //if custom_post is true

require_once (dirname (__FILE__) . '/slider_versions/placid_1.php');
require_once (dirname (__FILE__) . '/settings/settings.php');
require_once (dirname (__FILE__) . '/includes/media-images.php');

// Load the update-notification class
add_action('init', 'placid_update_notification');
function placid_update_notification()
{
    require_once (dirname (__FILE__) . '/includes/upgrade.php');
    $placid_upgrade_remote_path = 'http://support.slidervilla.com/sv-updates/placid.php';
    new placid_update_class ( PLACID_SLIDER_VER, $placid_upgrade_remote_path, PLACID_SLIDER_PLUGIN_BASENAME );
}
?>