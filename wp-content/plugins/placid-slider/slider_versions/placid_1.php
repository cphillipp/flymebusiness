<?php 
function placid_global_posts_processor( $posts, $placid_slider_curr,$out_echo,$set,$data=array() ){
	global $placid_slider;
	$placid_slider_css = placid_get_inline_css($set);
	$html = '';
	$placid_sldr_j = 0;
	
	//Timthummb
	$timthumb='1';
	if($placid_slider_curr['timthumb']=='1'){
		$timthumb='0';
	}
	$slider_handle='';
	if ( is_array($data) and isset($data['slider_handle']) ) {
		$slider_handle=$data['slider_handle'];
	}
	foreach($posts as $post) {
		$id = $post->post_id;
		$post_title = stripslashes($post->post_title);
		$post_title = str_replace('"', '', $post_title);
		//filter hook
		$post_title=apply_filters('placid_post_title',$post_title,$post_id,$placid_slider_curr,$placid_slider_css);
		$slider_content = $post->post_content;
		$post_id = $post->ID;
		
		$placid_slide_redirect_url = get_post_meta($post_id, 'placid_slide_redirect_url', true);
		$placid_sslider_nolink = get_post_meta($post_id,'placid_sslider_nolink',true);
		trim($placid_slide_redirect_url);
		if(!empty($placid_slide_redirect_url) and isset($placid_slide_redirect_url)) {
		   $permalink = $placid_slide_redirect_url;
		}
		else{
		   $permalink = get_permalink($post_id);
		}
		if($placid_sslider_nolink=='1'){
		  $permalink='';
		}
			
		$html .= '<div class="placid_slideri" '.$placid_slider_css['placid_slideri'].'>
			<!-- placid_slideri -->';
			
		if($placid_slider_curr['show_content']=='1'){
			if ($placid_slider_curr['content_from'] == "slider_content") {
				$slider_content = get_post_meta($post_id, 'slider_content', true);
			}
			if ($placid_slider_curr['content_from'] == "excerpt") {
				$slider_content = $post->post_excerpt;
			}

			$slider_content = strip_shortcodes( $slider_content );

			$slider_content = stripslashes($slider_content);
			$slider_content = str_replace(']]>', ']]&gt;', $slider_content);
	
			$slider_content = str_replace("\n","<br />",$slider_content);
			$slider_content = strip_tags($slider_content, $placid_slider_curr['allowable_tags']);
			
			if(!$placid_slider_curr['content_limit'] or $placid_slider_curr['content_limit'] == '' or $placid_slider_curr['content_limit'] == ' ') 
			  $slider_excerpt = substr($slider_content,0,$placid_slider_curr['content_chars']);
			else 
			  $slider_excerpt = placid_slider_word_limiter( $slider_content, $limit = $placid_slider_curr['content_limit'] );
			//filter hook
			$slider_excerpt=apply_filters('placid_slide_excerpt',$slider_excerpt,$post_id,$placid_slider_curr,$placid_slider_css);
			$slider_excerpt='<span '.$placid_slider_css['placid_slider_span'].'> '.$slider_excerpt.'</span>';
		}
		else{
		    $slider_excerpt='';
		}
		//filter hook
			$slider_excerpt=apply_filters('placid_slide_excerpt_html',$slider_excerpt,$post_id,$placid_slider_curr,$placid_slider_css);
		
		$placid_fields=$placid_slider_curr['fields'];		
		$fields_html='';
		if($placid_fields and !empty($placid_fields) ){
			$fields=explode( ',', $placid_fields );
			if($fields){
				foreach($fields as $field) {
					$field_val = get_post_meta($post_id, $field, true);
					if( $field_val and !empty($field_val) )
						$fields_html .='<div class="placid_'.$field.' placid_fields">'.$field_val.'</div>';
				}
			}
		}

//All images
		$placid_media = get_post_meta($post_id,'placid_media',true);
		if($placid_slider_curr['img_pick'][0] == '1'){
		 $custom_key = array($placid_slider_curr['img_pick'][1]);
		}
		else {
		 $custom_key = '';
		}
		
		if($placid_slider_curr['img_pick'][2] == '1'){
		 $the_post_thumbnail = true;
		}
		else {
		 $the_post_thumbnail = false;
		}
		
		if($placid_slider_curr['img_pick'][3] == '1'){
		 $attachment = true;
		 $order_of_image = $placid_slider_curr['img_pick'][4];
		}
		else{
		 $attachment = false;
		 $order_of_image = '1';
		}
		
		if($placid_slider_curr['img_pick'][5] == '1'){
			 $image_scan = true;
		}
		else {
			 $image_scan = false;
		}
		
		$gti_width = $placid_slider_curr['img_width'];
	    $gti_height = $placid_slider_curr['img_height'];
		
		if($placid_slider_curr['crop'] == '0'){
		 $extract_size = 'full';
		}
		elseif($placid_slider_curr['crop'] == '1'){
		 $extract_size = 'large';
		}
		elseif($placid_slider_curr['crop'] == '2'){
		 $extract_size = 'medium';
		}
		else{
		 $extract_size = 'thumbnail';
		}
		//Slide link anchor attributes
		$a_attr='';$imglink='';
		$a_attr=get_post_meta($post_id,'placid_link_attr',true);
		if( empty($a_attr) and isset( $placid_slider_curr['a_attr'] ) ) $a_attr=$placid_slider_curr['a_attr'];
		if( isset($placid_slider_curr['pphoto']) ){
			if($placid_slider_curr['pphoto'] == '1') $a_attr.='rel="prettyPhoto"';
			if(!empty($placid_slide_redirect_url) and isset($placid_slide_redirect_url))
				$imglink=$placid_slide_redirect_url;
			else $imglink='1';
		}
		
		$img_args = array(
			'custom_key' => $custom_key,
			'post_id' => $post_id,
			'attachment' => $attachment,
			'size' => $extract_size,
			'the_post_thumbnail' => $the_post_thumbnail,
			'default_image' => false,
			'order_of_image' => $order_of_image,
			'link_to_post' => false,
			'image_class' => 'placid_slider_thumbnail',
			'image_scan' => $image_scan,
			'width' => $gti_width,
			'height' => $gti_height,
			'echo' => false,
			'permalink' => $permalink,
			'timthumb'=>$timthumb,
			'style'=> $placid_slider_css['placid_slider_thumbnail'],
			'a_attr'=> $a_attr,
			'imglink'=>$imglink
		);
		
		if( empty($placid_media) or $placid_media=='' or !($placid_media) ) {  
			$placid_large_image=placid_sslider_get_the_image($img_args);
		}
		else{
			$placid_large_image=$placid_media;
		}
		//filter hook
		$placid_large_image=apply_filters('placid_large_image',$placid_large_image,$post_id,$placid_slider_curr,$placid_slider_css);
		$html .= $placid_large_image;
		  		
		if ($placid_slider_curr['image_only'] == '1') { 
			$html .= '<!-- /placid_slideri -->
			</div>';
		}
		else {
		   if($permalink!='') {
			$slide_title = '<h2 '.$placid_slider_css['placid_slider_h2'].'><a href="'.$permalink.'" '.$placid_slider_css['placid_slider_h2_a'].' '.$a_attr.'>'.$post_title.'</a></h2>';
			//filter hook
		   $slide_title=apply_filters('placid_slide_title_html',$slide_title,$post_id,$placid_slider_curr,$placid_slider_css,$post_title);
			$html .= '<div class="placid_text" '.$placid_slider_css['placid_text'].'>'.$slide_title.$slider_excerpt.$fields_html;
			if($placid_slider_curr['show_content']=='1'){
			  $placid_more=$placid_slider_curr['more'];
			  if($placid_more and !empty($placid_more) ){
			      $html .= '<p class="more"><a href="'.$permalink.'" '.$placid_slider_css['placid_slider_p_more'].' '.$a_attr.'>'.$placid_slider_curr['more'].'</a></p>';
			  }
			}
			 $html .= '	<!-- /placid_slideri -->
			</div></div>'; }
		   else{
		   $slide_title = '<h2 '.$placid_slider_css['placid_slider_h2'].'>'.$post_title.'</h2>';
		   //filter hook
		   $slide_title=apply_filters('placid_slide_title_html',$slide_title,$post_id,$placid_slider_curr,$placid_slider_css,$post_title);
		   $html .= '<div class="placid_text" '.$placid_slider_css['placid_text'].'>'.$slide_title.$slider_excerpt.$fields_html.'
				<!-- /placid_slideri -->
			</div></div>';    }
		}
	}
	//filter hook
	$html=apply_filters('placid_extract_html',$html,$placid_sldr_j,$posts,$placid_slider_curr);
	if($out_echo == '1') {
	   echo $html;
	}
	$r_array = array( $placid_sldr_j, $html);
	$r_array=apply_filters('placid_r_array',$r_array,$posts, $placid_slider_curr,$set);
	return $r_array;
}
function get_global_placid_slider($slider_handle,$r_array,$placid_slider_curr,$set,$echo='1',$data=array() ){
	global $placid_slider;
	$placid_sldr_j = $r_array[0];
	$placid_slider_css = placid_get_inline_css($set);
	$html='';
	
	if ( is_array($data) and $data['title']!='' ) {
		$sldr_title=$data['title'];
	}
	else{
		if($placid_slider_curr['title_from']=='1') $sldr_title = get_placid_slider_name($slider_id);
		else $sldr_title = $placid_slider_curr['title_text']; 
	}
	//filter hook
	$sldr_title=apply_filters('placid_slider_title',$sldr_title,$slider_handle,$placid_slider_curr,$set);
	
	$placid_media_queries='';
	$slider_height=$placid_slider_curr['height'];
    if( $placid_slider_curr['responsive'] == '1' ) {
		if($placid_slider_curr['orientation']=="1"){
			$placid_media_queries='.placid_slider_set'.$set.'.placid_slider{width:100% !important;max-width:'.$placid_slider_curr['iwidth'].'!important;}.placid_slider_set'.$set.' .placid_slider_handle{width:100% !important;}.placid_slider_set'.$set.' .placid_slideri{width:100% !important;height:auto !important;}.placid_slider_set'.$set.' .placid_slider_thumbnail{max-width:100% !important;height:auto !important;}';
		}
		else{
			$placid_media_queries='.placid_slider_set'.$set.'.placid_slider{width:100% !important;}.placid_slider_set'.$set.' .placid_slider_handle{width:100% !important;}.placid_slider_set'.$set.' .placid_slideri{max-width:100% !important;}.placid_slider_set'.$set.' .placid_slider_thumbnail{max-width:100% !important;}';
		}
		//filter hook
		$placid_media_queries=apply_filters('placid_media_queries',$placid_media_queries,$placid_slider_curr,$set);
	}
	
	$iwidth=$placid_slider_curr['iwidth'];
	$height=$placid_slider_curr['height'];
	if( (empty($iwidth) or $iwidth='') and $placid_slider_curr['orientation']!="1" ) {
		$variable_slideri_js='jQuery("#'.$slider_handle.' .placid_slideri").each(function(){
					var img=jQuery(this).find(".placid_slider_thumbnail");
					if(img.length > 0 ) {
						img.load(function() {
							var imgwidth = img.width();
							jQuery(this).attr("style","width:"+imgwidth+"px !important;");
						});
					}
				});';
		$startonload='startOnLoad: true,';
	}
	elseif( (empty($height) or $height='') and $placid_slider_curr['orientation']=="1" ){
		$variable_slideri_js='jQuery("#'.$slider_handle.' .placid_slideri").each(function(){
					var img=jQuery(this).find(".placid_slider_thumbnail");
					if(img.length > 0 ) {
						img.load(function() {
							var imgheight = img.height();
							jQuery(this).attr("style","height:"+imgheight+"px !important;");
						});
					}
				});';
		$startonload='startOnLoad: true,';
	}
	else {
		$variable_slideri_js='';
		$startonload='';
	}
	$orientation='';$customClass='placid_slider_instance';
	if($placid_slider_curr['orientation']=="1")	{
	$orientation='orientation: "vertical",';
	$customClass='placid_slider_instance_vert';
	}
	
	$html.='<script type="text/javascript"> '.
	( (!isset($placid_slider_curr['fouc']) or $placid_slider_curr['fouc']=='0' ) ? 
	'jQuery("html").addClass("placid_slider_fouc"); jQuery(document).ready(function() {
	   jQuery(".placid_slider_fouc .placid_slider").css({"display" : "block"});
	});' : '' );
	
	$html.='jQuery(document).ready(function() {
		'.$variable_slideri_js.'
			jQuery("#'.$slider_handle.'").simplyScroll({
				customClass: "'.$customClass.'",
				'.$orientation.'
				'.$startonload.'
				'.( ( $placid_slider_curr['prev_next'] == 1 ) ? 'auto: false,' : 'autoMode: "loop",' ).'
				speed:'.$placid_slider_curr['speed'].'
		    });
		});';

	if($placid_slider_curr['pphoto'] == '1') {
		wp_enqueue_script( 'jquery.prettyPhoto', placid_slider_plugin_url( 'js/jquery.prettyPhoto.js' ),
							array('jquery'), PLACID_SLIDER_VER, false);
		wp_enqueue_style( 'prettyPhoto_css', placid_slider_plugin_url( 'css/prettyPhoto.css' ),
				false, PLACID_SLIDER_VER, 'all');
		$lightbox_script='jQuery(document).ready(function(){
			jQuery("a[rel^=\'prettyPhoto\']").prettyPhoto({deeplinking: false,social_tools:false});
		});';
		//filter hook
		   $lightbox_script=apply_filters('placid_lightbox_inline',$lightbox_script);
		$html.=$lightbox_script;
	}	
	if(!empty($placid_media_queries)){
			$html.='jQuery(document).ready(function() {jQuery("head").append("<style type=\"text/css\">'. $placid_media_queries .'</style>");});';
	}
	//action hook
	do_action('placid_global_script',$slider_handle,$placid_slider_curr);
	$html.='</script> <noscript><p><strong>'. $placid_slider['noscript'] .'</strong></p></noscript><div class="placid_slider placid_slider_set'. $set .'" '.$placid_slider_css['placid_slider'].'>'.
		( (!empty($sldr_title)) ? '<div class="sldr_title" '.$placid_slider_css['sldr_title'].'>'.$sldr_title.'</div>':'' ).'<div class="placid_slider_handle" '.$placid_slider_css['placid_slider_handle'].' >
	   <div id="'.$slider_handle.'" >
				'.$r_array[1].'
		</div>
	</div>
	</div>';
	$html=apply_filters('placid_slider_html',$html,$r_array,$placid_slider_curr,$set);
	if($echo == '1')  {echo $html; }
	else { return $html; }
}
function placid_carousel_posts_on_slider($max_posts, $offset=0, $slider_id = '1',$out_echo = '1',$set='', $data=array() ) {
    global $placid_slider;
	$placid_slider_options='placid_slider_options'.$set;
    $placid_slider_curr=get_option($placid_slider_options);
	if(!isset($placid_slider_curr) or !is_array($placid_slider_curr) or empty($placid_slider_curr)){$placid_slider_curr=$placid_slider;$set='';}
		
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.PLACID_SLIDER_TABLE;
	$post_table = $table_prefix."posts";
	$rand = $placid_slider_curr['rand'];
	if(isset($rand) and $rand=='1'){
	  $orderby = 'RAND()';
	}
	else {
	  $orderby = 'a.slide_order ASC, a.date DESC';
	}
	
	$posts = $wpdb->get_results("SELECT * FROM 
	                             $table_name a LEFT OUTER JOIN $post_table b 
								 ON a.post_id = b.ID 
								 WHERE (b.post_status = 'publish' OR (b.post_type='attachment' AND b.post_status = 'inherit')) AND a.slider_id = '$slider_id'  
	                             ORDER BY ".$orderby." LIMIT $offset, $max_posts", OBJECT);
	
	$r_array=placid_global_posts_processor( $posts, $placid_slider_curr, $out_echo, $set, $data );
	return $r_array;
}

function get_placid_slider($slider_id='',$set='',$offset=0, $title='', $data=array() ) {
	global $placid_slider; 
 	$placid_slider_options='placid_slider_options'.$set;
    $placid_slider_curr=get_option($placid_slider_options);
	if(!isset($placid_slider_curr) or !is_array($placid_slider_curr) or empty($placid_slider_curr)){$placid_slider_curr=$placid_slider;$set='';}
	$data['title']=$title;
	 if($placid_slider['multiple_sliders'] == '1' and is_singular() and (empty($slider_id) or !isset($slider_id))){
		global $post;
		$post_id = $post->ID;
		$slider_id = get_placid_slider_for_the_post($post_id);
	 }
	if(empty($slider_id) or !isset($slider_id))  $slider_id = '1';
	if( !$offset or empty($offset) or !is_numeric($offset)  ) $offset=0;
	if(!empty($slider_id)){
		$slider_handle='placid_slider_'.$slider_id;
		$data['slider_handle']=$slider_handle;
		$r_array = placid_carousel_posts_on_slider($placid_slider_curr['no_posts'], $offset, $slider_id, '0', $set, $data); 
		get_global_placid_slider($slider_handle,$r_array,$placid_slider_curr,$set,$echo='1',$data);
	} //end of not empty slider_id condition
}

//For displaying category specific posts in chronologically reverse order
function placid_carousel_posts_on_slider_category($max_posts='5', $catg_slug='', $offset=0, $out_echo = '1', $set='', $data=array() ) {
    global $placid_slider;
	$placid_slider_options='placid_slider_options'.$set;
    $placid_slider_curr=get_option($placid_slider_options);
	if(!isset($placid_slider_curr) or !is_array($placid_slider_curr) or empty($placid_slider_curr)){$placid_slider_curr=$placid_slider;$set='';}

	global $wpdb, $table_prefix;
	
	if (!empty($catg_slug)) {
		$category = get_category_by_slug($catg_slug); 
		$slider_cat = $category->term_id;
	}
	else {
		$category = get_the_category();
		$slider_cat = $category[0]->cat_ID;
	}
	
	$rand = $placid_slider_curr['rand'];
	if(isset($rand) and $rand=='1') $orderby = '&orderby=rand';
	else $orderby = '';
	
	//extract the posts
	$posts = get_posts('numberposts='.$max_posts.'&offset='.$offset.'&category='.$slider_cat.$orderby);
	
	$r_array=placid_global_posts_processor( $posts, $placid_slider_curr, $out_echo,$set,$data );
	return $r_array;
}

function get_placid_slider_category($catg_slug='', $set='', $offset=0,$title='', $data=array()) {
    global $placid_slider; 
 	$placid_slider_options='placid_slider_options'.$set;
    $placid_slider_curr=get_option($placid_slider_options);
	if(!isset($placid_slider_curr) or !is_array($placid_slider_curr) or empty($placid_slider_curr)){$placid_slider_curr=$placid_slider;$set='';}
	$data['title']=$title;
	if( !$offset or empty($offset) or !is_numeric($offset)  ) $offset=0;
   	$slider_handle='placid_slider_'.$catg_slug;
    $data['slider_handle']=$slider_handle;
	$r_array = placid_carousel_posts_on_slider_category($placid_slider_curr['no_posts'], $catg_slug, $offset, '0', $set, $data); 
	get_global_placid_slider($slider_handle,$r_array,$placid_slider_curr,$set,$echo='1',$data);
} 

//For displaying recent posts in chronologically reverse order
function placid_carousel_posts_on_slider_recent($max_posts='5', $offset=0, $out_echo = '1', $set='', $data=array()) {
     global $placid_slider;
	$placid_slider_options='placid_slider_options'.$set;
    $placid_slider_curr=get_option($placid_slider_options);
	if(!isset($placid_slider_curr) or !is_array($placid_slider_curr) or empty($placid_slider_curr)){$placid_slider_curr=$placid_slider;$set='';}
	$rand = $placid_slider_curr['rand'];
	if(isset($rand) and $rand=='1')	  $orderby = '&orderby=rand';
	else  $orderby = '';
	//extract posts data
	$posts = get_posts('numberposts='.$max_posts.'&offset='.$offset.$orderby);
	$r_array=placid_global_posts_processor( $posts, $placid_slider_curr, $out_echo,$set,$data );
	return $r_array;
}

function get_placid_slider_recent($set='',$offset=0,$title='',$data=array()) {
	global $placid_slider; 
 	$placid_slider_options='placid_slider_options'.$set;
    $placid_slider_curr=get_option($placid_slider_options);
	if(!isset($placid_slider_curr) or !is_array($placid_slider_curr) or empty($placid_slider_curr)){$placid_slider_curr=$placid_slider;$set='';}
	$data['title']=$title;
	if( !$offset or empty($offset) or !is_numeric($offset)  ) $offset=0;
	$slider_handle='placid_slider_recent';
	$data['slider_handle']=$slider_handle;
	$r_array = placid_carousel_posts_on_slider_recent($placid_slider_curr['no_posts'], $offset, '0', $set, $data);
	get_global_placid_slider($slider_handle,$r_array,$placid_slider_curr,$set,$echo='1',$data);
}
require_once (dirname (__FILE__) . '/shortcodes_1.php');
require_once (dirname (__FILE__) . '/widgets_1.php');

function placid_slider_enqueue_scripts() {
	wp_enqueue_script( 'placidSlider', placid_slider_plugin_url( 'js/placid.js' ),array('jquery'), PLACID_SLIDER_VER, false);
}

add_action( 'init', 'placid_slider_enqueue_scripts' );

function placid_slider_enqueue_styles() {	
  global $post, $placid_slider, $wp_registered_widgets,$wp_widget_factory;
  if(is_singular()) {
	 $placid_slider_style = get_post_meta($post->ID,'placid_slider_style',true);
	 if((is_active_widget(false, false, 'placid_sslider_wid', true) or isset($placid_slider['shortcode']) ) and (!isset($placid_slider_style) or empty($placid_slider_style))){
	   $placid_slider_style='default';
	 }
	 if (!isset($placid_slider_style) or empty($placid_slider_style) ) {
	     wp_enqueue_style( 'placid_slider_headcss', placid_slider_plugin_url( 'css/skins/'.$placid_slider['stylesheet'].'/style.css' ),
		false, PLACID_SLIDER_VER, 'all');
	 }
     else {
	     wp_enqueue_style( 'placid_slider_headcss', placid_slider_plugin_url( 'css/skins/'.$placid_slider_style.'/style.css' ),
		false, PLACID_SLIDER_VER, 'all');
	}
  }
  else {
     $placid_slider_style = $placid_slider['stylesheet'];
	wp_enqueue_style( 'placid_slider_headcss', placid_slider_plugin_url( 'css/skins/'.$placid_slider_style.'/style.css' ),
		false, PLACID_SLIDER_VER, 'all');
  }
}
add_action( 'wp', 'placid_slider_enqueue_styles' );

//admin settings
function placid_slider_admin_scripts() {
global $placid_slider;
  if ( is_admin() ){ // admin actions
  // Settings page only
	if ( isset($_GET['page']) && ('placid-slider-admin' == $_GET['page'] or 'placid-slider-settings' == $_GET['page'] )  ) {
	wp_register_script('jquery', false, false, false, false);
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'jquery.simplyScroll', placid_slider_plugin_url( 'js/placid.js' ),
		array('jquery'), PLACID_SLIDER_VER, false); 
	wp_enqueue_style( 'placid_slider_admin_head_css', placid_slider_plugin_url( 'css/skins/'.$placid_slider['stylesheet'].'/style.css' ),false, PLACID_SLIDER_VER, 'all');
	wp_enqueue_script( 'placid_slider_admin_js', placid_slider_plugin_url( 'js/admin.js' ),
		array('jquery'), PLACID_SLIDER_VER, false);
	wp_enqueue_style( 'placid_slider_admin_css', placid_slider_plugin_url( 'css/admin.css' ),
		false, PLACID_SLIDER_VER, 'all');
	}
  }
}

add_action( 'admin_init', 'placid_slider_admin_scripts' );

function placid_slider_admin_head() {
global $placid_slider;
if ( is_admin() ){ // admin actions
   
  // Sliders & Settings page only
    if ( isset($_GET['page']) && ('placid-slider-admin' == $_GET['page'] or 'placid-slider-settings' == $_GET['page']) ) {
	  $sliders = placid_ss_get_sliders(); 
	?>
		<script type="text/javascript">
            // <![CDATA[
        jQuery(document).ready(function() {
                jQuery(function() {
                    jQuery("#slider_tabs").tabs({fx: { opacity: "toggle", duration: 300}}).addClass( "ui-tabs-vertical-left ui-helper-clearfix" );jQuery( "#slider_tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
				<?php foreach($sliders as $slider){?>
                    jQuery("#sslider_sortable_<?php echo $slider['slider_id'];?>").sortable();
                    jQuery("#sslider_sortable_<?php echo $slider['slider_id'];?>").disableSelection();
			    <?php } ?>
                });
        });
		
        function confirmRemove()
        {
            var agree=confirm("This will remove selected Posts/Pages from Slider.");
            if (agree)
            return true ;
            else
            return false ;
        }
        function confirmRemoveAll()
        {
            var agree=confirm("Remove all Posts/Pages from Placid Slider??");
            if (agree)
            return true ;
            else
            return false ;
        }
        function confirmSliderDelete()
        {
            var agree=confirm("Delete this Slider??");
            if (agree)
            return true ;
            else
            return false ;
        }
        function slider_checkform ( form )
        {
          if (form.new_slider_name.value == "") {
            alert( "Please enter the New Slider name." );
            form.new_slider_name.focus();
            return false ;
          }
          return true ;
        }
        </script>
<?php
   } //Sliders page only
   
   // Settings page only
  if ( isset($_GET['page']) && 'placid-slider-settings' == $_GET['page']  ) {
		wp_print_scripts( 'farbtastic' );
		wp_print_styles( 'farbtastic' );
?>
<script type="text/javascript">
	// <![CDATA[
jQuery(document).ready(function() {
		jQuery('#colorbox_1').farbtastic('#color_value_1');
		jQuery('#color_picker_1').click(function () {
           if (jQuery('#colorbox_1').css('display') == "block") {
		      jQuery('#colorbox_1').fadeOut("slow"); }
		   else {
		      jQuery('#colorbox_1').fadeIn("slow"); }
        });
		var colorpick_1 = false;
		jQuery(document).mousedown(function(){
		    if (colorpick_1 == true) {
    			return; }
				jQuery('#colorbox_1').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
		    colorpick_1 = false;
		});
//for second color box
		jQuery('#colorbox_2').farbtastic('#color_value_2');
		jQuery('#color_picker_2').click(function () {
           if (jQuery('#colorbox_2').css('display') == "block") {
		      jQuery('#colorbox_2').fadeOut("slow"); }
		   else {
		      jQuery('#colorbox_2').fadeIn("slow"); }
        });
		var colorpick_2 = false;
		jQuery(document).mousedown(function(){
		    if (colorpick_2 == true) {
    			return; }
				jQuery('#colorbox_2').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
		    colorpick_2 = false;
		});
//for third color box
		jQuery('#colorbox_3').farbtastic('#color_value_3');
		jQuery('#color_picker_3').click(function () {
           if (jQuery('#colorbox_3').css('display') == "block") {
		      jQuery('#colorbox_3').fadeOut("slow"); }
		   else {
		      jQuery('#colorbox_3').fadeIn("slow"); }
        });
		var colorpick_3 = false;
		jQuery(document).mousedown(function(){
		    if (colorpick_3 == true) {
    			return; }
				jQuery('#colorbox_3').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
		    colorpick_3 = false;
		});
//for fourth color box
		jQuery('#colorbox_4').farbtastic('#color_value_4');
		jQuery('#color_picker_4').click(function () {
           if (jQuery('#colorbox_4').css('display') == "block") {
		      jQuery('#colorbox_4').fadeOut("slow"); }
		   else {
		      jQuery('#colorbox_4').fadeIn("slow"); }
        });
		var colorpick_4 = false;
		jQuery(document).mousedown(function(){
		    if (colorpick_4 == true) {
    			return; }
				jQuery('#colorbox_4').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
		    colorpick_4 = false;
		});
//for fifth color box
		jQuery('#colorbox_5').farbtastic('#color_value_5');
		jQuery('#color_picker_5').click(function () {
           if (jQuery('#colorbox_5').css('display') == "block") {
		      jQuery('#colorbox_5').fadeOut("slow"); }
		   else {
		      jQuery('#colorbox_5').fadeIn("slow"); }
        });
		var colorpick_5 = false;
		jQuery(document).mousedown(function(){
		    if (colorpick_5 == true) {
    			return; }
				jQuery('#colorbox_5').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
		    colorpick_5 = false;
		});
//for sixth color box
		jQuery('#colorbox_6').farbtastic('#color_value_6');
		jQuery('#color_picker_6').click(function () {
           if (jQuery('#colorbox_6').css('display') == "block") {
		      jQuery('#colorbox_6').fadeOut("slow"); }
		   else {
		      jQuery('#colorbox_6').fadeIn("slow"); }
        });
		var colorpick_6 = false;
		jQuery(document).mousedown(function(){
		    if (colorpick_6 == true) {
    			return; }
				jQuery('#colorbox_6').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
		    colorpick_6 = false;
		});
		jQuery('#sldr_close').click(function () {
			jQuery('#sldr_message').fadeOut("slow");
		});
});
function confirmSettingsCreate()
        {
            var agree=confirm("Create New Settings Set??");
            if (agree)
            return true ;
            else
            return false ;
}
</script>
<style type="text/css">
.color-picker-wrap {
		position: absolute;
 		display: none; 
		background: #fff;
		border: 3px solid #ccc;
		padding: 3px;
		z-index: 1000;
	}
</style>
<?php
   } //for placid slider option page
 }//only for admin
}
add_action('admin_head', 'placid_slider_admin_head');
function placid_get_inline_css($set='',$echo='0'){
    global $placid_slider;
	$placid_slider_options='placid_slider_options'.$set;
    $placid_slider_curr=get_option($placid_slider_options);
	if(!isset($placid_slider_curr) or !is_array($placid_slider_curr) or empty($placid_slider_curr)){$placid_slider_curr=$placid_slider;$set='';}
	
	global $post;
	if(is_singular()) {	$placid_slider_style = get_post_meta($post->ID,'placid_slider_style',true);}
	if((is_singular() and ($placid_slider_style == 'default' or empty($placid_slider_style) or !$placid_slider_style)) or (!is_singular() and $placid_slider['stylesheet'] == 'default')  )	{ $default=true;	}
	else{ $default=false;}
	
	$placid_slider_css=array();
	if($default){
		$style_start= ($echo=='0') ? 'style="':'';
		$style_end= ($echo=='0') ? '"':'';
	    //placid_slider
		if(isset($placid_slider_curr['width']) and $placid_slider_curr['width']!=0 and $placid_slider_curr['orientation']!="1") {
			$placid_slider_css['placid_slider']=$style_start.'width:'. $placid_slider_curr['width'].'px;'.$style_end;
		}
		else {
			if(isset($placid_slider_curr['tot_height']) and $placid_slider_curr['tot_height']!=0 and $placid_slider_curr['orientation']=="1") {
				$placid_slider_css['placid_slider']=$style_start.'height:'. $placid_slider_curr['tot_height'].'px;'.$style_end;
			}
		}
		//placid_slider_handle
		if(isset($placid_slider_curr['width']) and $placid_slider_curr['width']!=0 and $placid_slider_curr['orientation']!="1") {
			$placid_slider_css['placid_slider_handle']=$style_start.'width:'. $placid_slider_curr['width'].'px;height:'. $placid_slider_curr['height'].'px;'.$style_end;
		}
		elseif(isset($placid_slider_curr['tot_height']) and $placid_slider_curr['tot_height']!=0 and $placid_slider_curr['orientation']=="1") {
			$placid_slider_css['placid_slider_handle']=$style_start.'width:'. $placid_slider_curr['iwidth'].'px;height:'. $placid_slider_curr['tot_height'].'px;'.$style_end;
		}
		else{
		    $placid_slider_css['placid_slider_handle']=$style_start.'width:100%;height:'. $placid_slider_curr['height'].'px;'.$style_end;
		}
		
		if ($placid_slider_curr['title_fstyle'] == "bold" or $placid_slider_curr['title_fstyle'] == "bold italic" ){$slider_title_font = "bold";} else { $slider_title_font = "normal"; }
		if ($placid_slider_curr['title_fstyle'] == "italic" or $placid_slider_curr['title_fstyle'] == "bold italic" ){$slider_title_style = "italic";} else {$slider_title_style = "normal";}
		$sldr_title = $placid_slider_curr['title_text']; if(!empty($sldr_title)) { $slider_title_margin = "5px 0 10px 0"; } else {$slider_title_margin = "0";} 
	//sldr_title	
		$placid_slider_css['sldr_title']=$style_start.'font-family:'.$placid_slider_curr['title_font'].', Arial, Helvetica, sans-serif;font-size:'.$placid_slider_curr['title_fsize'].'px;font-weight:'.$slider_title_font.';font-style:'.$slider_title_style.';color:'.$placid_slider_curr['title_fcolor'].';margin:'.$slider_title_margin.''.$style_end;

		if ($placid_slider_curr['bg'] == '1') { $placid_slideri_bg = "transparent";} else { $placid_slideri_bg = $placid_slider_curr['bg_color']; }
		$placid_slideri_width_css='';
		$placid_slideri_width=$placid_slider_curr['iwidth'];
		if(!empty($placid_slideri_width) and $placid_slideri_width > 0 ) $placid_slideri_width_css='width:'. $placid_slider_curr['iwidth'].'px;';
	//placid_slideri
		$placid_slider_css['placid_slideri']=$style_start.'background-color:'.$placid_slideri_bg.';border:'.$placid_slider_curr['border'].'px solid '.$placid_slider_curr['brcolor'].';'.$placid_slideri_width_css.'height:'. $placid_slider_curr['height'].'px;'.$style_end;
		
		if ($placid_slider_curr['ptitle_fstyle'] == "bold" or $placid_slider_curr['ptitle_fstyle'] == "bold italic" ){$ptitle_fweight = "bold";} else {$ptitle_fweight = "normal";}
		if ($placid_slider_curr['ptitle_fstyle'] == "italic" or $placid_slider_curr['ptitle_fstyle'] == "bold italic"){$ptitle_fstyle = "italic";} else {$ptitle_fstyle = "normal";}
	//placid_slider_h2
		$placid_slider_css['placid_slider_h2']=$style_start.'clear:none;line-height:'. ($placid_slider_curr['ptitle_fsize'] + 5) .'px;font-family:'. $placid_slider_curr['ptitle_font'].', Arial, Helvetica, sans-serif;font-size:'.$placid_slider_curr['ptitle_fsize'].'px;font-weight:'.$ptitle_fweight.';font-style:'.$ptitle_fstyle.';color:'.$placid_slider_curr['ptitle_fcolor'].';margin:0 0 5px 0;'.$style_end;
		
	//placid_slider_h2 a
		$placid_slider_css['placid_slider_h2_a']=$style_start.'color:'.$placid_slider_curr['ptitle_fcolor'].';'.$style_end;
	
		if ($placid_slider_curr['content_fstyle'] == "bold" or $placid_slider_curr['content_fstyle'] == "bold italic" ){$content_fweight= "bold";} else {$content_fweight= "normal";}
		if ($placid_slider_curr['content_fstyle']=="italic" or $placid_slider_curr['content_fstyle'] == "bold italic"){$content_fstyle= "italic";} else {$content_fstyle= "normal";}
	//placid_slider_span
		$placid_slider_css['placid_slider_span']=$style_start.'font-family:'.$placid_slider_curr['content_font'].', Arial, Helvetica, sans-serif;font-size:'.$placid_slider_curr['content_fsize'].'px;font-weight:'.$content_fweight.';font-style:'.$content_fstyle.';color:'. $placid_slider_curr['content_fcolor'].';'.$style_end;
		
	//
		if($placid_slider_curr['img_align'] == "left") {$thumb_margin_right= "10";} else {$thumb_margin_right= "0";}
		if($placid_slider_curr['img_align'] == "right") {$thumb_margin_left = "10";} else {$thumb_margin_left = "0";}
		if($placid_slider_curr['img_size'] == '1'){ $thumb_width= 'width:'. $placid_slider_curr['img_width'].'px;';} else{$thumb_width='';}
	//placid_slider_thumbnail
		$placid_slider_css['placid_slider_thumbnail']=$style_start.'float:'.$placid_slider_curr['img_align'].';padding:0;margin:0 '.$thumb_margin_right.'px 0 '.$thumb_margin_left.'px;height:'.$placid_slider_curr['img_height'].'px;border:'.$placid_slider_curr['img_border'].'px solid '.$placid_slider_curr['img_brcolor'].';'.$thumb_width.''.$style_end;
	
	//placid_slider_p_more
		$placid_slider_css['placid_slider_p_more']=$style_start.'color:'.$placid_slider_curr['ptitle_fcolor'].';font-family:'.$placid_slider_curr['content_font'].', Arial, Helvetica, sans-serif;font-size:'.$placid_slider_curr['content_fsize'].'px;'.$style_end;
	//placid_slider_p_more
	    $placid_slider_css['placid_text']=$style_start.'max-width:'. ( $placid_slider_curr['iwidth'] - 20 ).'px;'.$style_end;
		
	}
	return $placid_slider_css;
}
function placid_slider_css() {
global $placid_slider;
$css=$placid_slider['css'];
if($css and !empty($css)){?>
 <style type="text/css"><?php echo $css;?></style>
<?php }
}
add_action('wp_head', 'placid_slider_css');
add_action('admin_head', 'placid_slider_css');
?>