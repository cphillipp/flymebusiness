<?php
function return_global_placid_slider($slider_handle,$r_array,$placid_slider_curr,$set,$echo='0',$data=array()){
	$slider_html='';
	$slider_html=get_global_placid_slider($slider_handle,$r_array,$placid_slider_curr,$set,$echo,$data);
	return $slider_html;
}
function return_placid_slider($slider_id='',$set='',$offset=0, $data=array()) {
	global $placid_slider; 
 	$placid_slider_options='placid_slider_options'.$set;
    $placid_slider_curr=get_option($placid_slider_options);
	if(!isset($placid_slider_curr) or !is_array($placid_slider_curr) or empty($placid_slider_curr)){$placid_slider_curr=$placid_slider;$set='';}
 
	if($placid_slider['multiple_sliders'] == '1' and is_singular() and (empty($slider_id) or !isset($slider_id))){
		global $post;
		$post_id = $post->ID;
		$slider_id = get_placid_slider_for_the_post($post_id);
	}
	if(empty($slider_id) or !isset($slider_id))  $slider_id = '1';
	if( !$offset or empty($offset) or !is_numeric($offset)  ) $offset=0;
	$slider_html='';
	if(!empty($slider_id)){
		$slider_handle='placid_slider_'.$slider_id;
		$data['slider_handle']=$slider_handle;
		$r_array = placid_carousel_posts_on_slider($placid_slider_curr['no_posts'], $offset, $slider_id, $echo = '0', $set,$data); 
		$slider_html=return_global_placid_slider($slider_handle,$r_array,$placid_slider_curr,$set,$echo='0',$data);
	} //end of not empty slider_id condition
	
	return $slider_html;
}

function placid_slider_simple_shortcode($atts) {
	extract(shortcode_atts(array(
		'id' => '',
		'set' => '',
		'offset' => '',
	), $atts));
	$data=array();
	return return_placid_slider($id,$set,$offset,$data);
}
add_shortcode('placidslider', 'placid_slider_simple_shortcode');

//Category shortcode
function return_placid_slider_category($catg_slug='',$set='',$offset=0,$data=array()) {
	global $placid_slider; 
 	$placid_slider_options='placid_slider_options'.$set;
    $placid_slider_curr=get_option($placid_slider_options);
	if(!isset($placid_slider_curr) or !is_array($placid_slider_curr) or empty($placid_slider_curr)){$placid_slider_curr=$placid_slider;$set='';}
	if( !$offset or empty($offset) or !is_numeric($offset)  ) $offset=0;
	$slider_handle='placid_slider_'.$catg_slug;
	$data['slider_handle']=$slider_handle;
    $r_array = placid_carousel_posts_on_slider_category($placid_slider_curr['no_posts'], $catg_slug, $offset, '0', $set, $data); 
	//get slider 
	$slider_html=return_global_placid_slider($slider_handle,$r_array,$placid_slider_curr,$set,$echo='0',$data);
	
	return $slider_html;
}

function placid_slider_category_shortcode($atts) {
	extract(shortcode_atts(array(
		'catg_slug' => '',
		'set' => '',
		'offset' => '',
	), $atts));
	$data=array();
	return return_placid_slider_category($catg_slug,$set,$offset,$data);
}
add_shortcode('placidcategory', 'placid_slider_category_shortcode');

//Recent Posts Shortcode
function return_placid_slider_recent($set='',$offset=0, $data=array()) {
	global $placid_slider; 
 	$placid_slider_options='placid_slider_options'.$set;
    $placid_slider_curr=get_option($placid_slider_options);
	if(!isset($placid_slider_curr) or !is_array($placid_slider_curr) or empty($placid_slider_curr)){$placid_slider_curr=$placid_slider;$set='';}
	if( !$offset or empty($offset) or !is_numeric($offset)  ) $offset=0;
	$slider_handle='placid_slider_recent';
	$data['slider_handle']=$slider_handle;
	$r_array = placid_carousel_posts_on_slider_recent($placid_slider_curr['no_posts'], $offset, '0', $set,$data); 
	//get slider 
	$slider_html=return_global_placid_slider($slider_handle,$r_array,$placid_slider_curr,$set,$echo='0',$data);
	
	return $slider_html;
}

function placid_slider_recent_shortcode($atts) {
	extract(shortcode_atts(array(
		'set' => '',
		'offset' => '',
	), $atts));
	$data=array();
	return return_placid_slider_recent($set,$offset,$data);
}
add_shortcode('placidrecent', 'placid_slider_recent_shortcode');
?>