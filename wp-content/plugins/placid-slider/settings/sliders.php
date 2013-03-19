<?php // This function displays the page content for the Placid Slider Options submenu
function placid_slider_create_multiple_sliders() {
global $placid_slider;
?>

<div class="wrap" style="clear:both;">

<h2 style="float:left;"><?php _e('Sliders Created','placid-slider'); ?></h2>
<?php 
if ($_POST['remove_posts_slider']) {
   if ( $_POST['slider_posts'] ) {
       global $wpdb, $table_prefix;
       $table_name = $table_prefix.PLACID_SLIDER_TABLE;
	   $current_slider = $_POST['current_slider_id'];
	   foreach ( $_POST['slider_posts'] as $post_id=>$val ) {
		   $sql = "DELETE FROM $table_name WHERE post_id = '$post_id' AND slider_id = '$current_slider' LIMIT 1";
		   $wpdb->query($sql);
	   }
   }
   if ($_POST['remove_all'] == __('Remove All at Once','placid-slider')) {
       global $wpdb, $table_prefix;
       $table_name = $table_prefix.PLACID_SLIDER_TABLE;
	   $current_slider = $_POST['current_slider_id'];
	   if(is_placid_slider_on_slider_table($current_slider)) {
		   $sql = "DELETE FROM $table_name WHERE slider_id = '$current_slider';";
		   $wpdb->query($sql);
	   }
   }
   if ($_POST['remove_all'] == __('Delete Slider','placid-slider')) {
       $slider_id = $_POST['current_slider_id'];
       global $wpdb, $table_prefix;
       $slider_table = $table_prefix.PLACID_SLIDER_TABLE;
       $slider_meta = $table_prefix.PLACID_SLIDER_META;
	   $slider_postmeta = $table_prefix.PLACID_SLIDER_POST_META;
	   if(is_placid_slider_on_slider_table($slider_id)) {
		   $sql = "DELETE FROM $slider_table WHERE slider_id = '$slider_id';";
		   $wpdb->query($sql);
	   }
	   if(is_placid_slider_on_meta_table($slider_id)) {
		   $sql = "DELETE FROM $slider_meta WHERE slider_id = '$slider_id';";
		   $wpdb->query($sql);
	   }
	   if(is_placid_slider_on_postmeta_table($slider_id)) {
		   $sql = "DELETE FROM $slider_postmeta WHERE slider_id = '$slider_id';";
		   $wpdb->query($sql);
	   }
   }
}
if ($_POST['create_new_slider']) {
   $slider_name = $_POST['new_slider_name'];
   global $wpdb,$table_prefix;
   $slider_meta = $table_prefix.PLACID_SLIDER_META;
   $sql = "INSERT INTO $slider_meta (slider_name) VALUES('$slider_name');";
   $result = $wpdb->query($sql);
}
if ($_POST['reorder_posts_slider']) {
   $i=1;
   global $wpdb, $table_prefix;
   $table_name = $table_prefix.PLACID_SLIDER_TABLE;
   $slider_id=$_POST['current_slider_id'];
   foreach ($_POST['order'] as $slide_order) {
    $slide_order = intval($slide_order);
    $sql = 'UPDATE '.$table_name.' SET slide_order='.$i.' WHERE post_id='.$slide_order.' and slider_id='.$slider_id;
    $wpdb->query($sql);
    $i++;
  }
}
?>
<div style="clear:both"></div>
<?php $url = placid_sslider_admin_url( array( 'page' => 'placid-slider-settings' ) );?>
<a href="<?php echo $url; ?>" title="<?php _e('Settings Page for Placid Slider where you can change the color, font etc. for the sliders','placid-slider'); ?>"><?php _e('Go to Placid Slider Settings page','placid-slider'); ?></a>
<?php $sliders = placid_ss_get_sliders(); ?>

<div id="slider_tabs">
        <ul class="ui-tabs" style="width:15%;margin-right:2%;">
        <?php foreach($sliders as $slider){?>
            <li><a href="#tabs-<?php echo $slider['slider_id'];?>"><?php echo $slider['slider_name'];?></a></li>
        <?php } ?>
        <?php if($placid_slider['multiple_sliders'] == '1') {?>
            <li><a href="#new_slider"><?php _e('Create New Slider','placid-slider'); ?></a></li>
        <?php } ?>
        </ul>

<?php foreach($sliders as $slider){?>
<div id="tabs-<?php echo $slider['slider_id'];?>">
<form action="" method="post">
<?php settings_fields('placid-slider-group'); ?>

<input type="hidden" name="remove_posts_slider" value="1" />
<div id="tabs-<?php echo $slider['slider_id'];?>">
<h3><?php _e('Posts/Pages Added To','placid-slider'); ?> <?php echo $slider['slider_name'];?><?php _e('(Slider ID','placid-slider'); ?> = <?php echo $slider['slider_id'];?>)</h3>
<p><em><?php _e('Check the Post/Page and Press "Remove Selected" to remove them From','placid-slider'); ?> <?php echo $slider['slider_name'];?>. <?php _e('Press "Remove All at Once" to remove all the posts from the','placid-slider'); ?> <?php echo $slider['slider_name'];?>.</em></p>

    <table class="widefat">
    <thead><tr><th><?php _e('Post/Page Title','placid-slider'); ?></th><th><?php _e('Author','placid-slider'); ?></th><th><?php _e('Post Date','placid-slider'); ?></th><th><?php _e('Remove Post','placid-slider'); ?></th></tr></thead><tbody>

<?php  
	/*global $wpdb, $table_prefix;
	$table_name = $table_prefix.PLACID_SLIDER_TABLE;*/
	$slider_id = $slider['slider_id'];
	//$slider_posts = $wpdb->get_results("SELECT post_id FROM $table_name WHERE slider_id = '$slider_id'", OBJECT); 
    $slider_posts=placid_get_slider_posts_in_order($slider_id); ?>
	
    <input type="hidden" name="current_slider_id" value="<?php echo $slider_id;?>" />
    
<?php    $count = 0;	
	foreach($slider_posts as $slider_post) {
	  $slider_arr[] = $slider_post->post_id;
	  $post = get_post($slider_post->post_id);	  
	  if ( in_array($post->ID, $slider_arr) ) {
		  $count++;
		  $sslider_author = get_userdata($post->post_author);
          $sslider_author_dname = $sslider_author->display_name;
		  echo '<tr' . ($count % 2 ? ' class="alternate"' : '') . '><td><strong>' . $post->post_title . '</strong><a href="'.get_edit_post_link( $post->ID, $context = 'display' ).'" target="_blank"> '.__( '(Edit)', 'placid-slider' ).'</a> <a href="'.get_permalink( $post->ID ).'" target="_blank"> '.__( '(View)', 'placid-slider' ).' </a></td><td>By ' . $sslider_author_dname . '</td><td>' . date('l, F j. Y',strtotime($post->post_date)) . '</td><td><input type="checkbox" name="slider_posts[' . $post->ID . ']" value="1" /></td></tr>'; 
	  }
	}
		
	if ($count == 0) {
		echo '<tr><td colspan="4">'.__( 'No posts/pages have been added to the Slider - You can add respective post/page to slider on the Edit screen for that Post/Page', 'placid-slider' ).'</td></tr>';
	}
	echo '</tbody><tfoot><tr><th>'.__( 'Post/Page Title', 'placid-slider' ).'</th><th>'.__( 'Author', 'placid-slider' ).'</th><th>'.__( 'Post Date', 'placid-slider' ).'</th><th>'.__( 'Remove Post', 'placid-slider' ).'</th></tr></tfoot></table>'; 
    
	echo '<div class="submit">';
	
	if ($count) {echo '<input type="submit" value="'.__( 'Remove Selected', 'placid-slider' ).'" onclick="return confirmRemove()" /><input type="submit" name="remove_all" value="'.__( 'Remove All at Once', 'placid-slider' ).'" onclick="return confirmRemoveAll()" />';}
	
	if($slider_id != '1') {
	   echo '<input type="submit" value="'.__( 'Delete Slider', 'placid-slider' ).'" name="remove_all" onclick="return confirmSliderDelete()" />';
	}
	
	echo '</div>';
?>    
    </tbody></table>
 </form>
 
 
 <form action="" method="post">
    <input type="hidden" name="reorder_posts_slider" value="1" />
    <h3><?php _e('Reorder the Posts/Pages Added To','placid-slider'); ?> <?php echo $slider['slider_name'];?>(Slider ID = <?php echo $slider['slider_id'];?>)</h3>
    <p><em><?php _e('Click on and drag the post/page title to a new spot within the list, and the other items will adjust to fit.','placid-slider'); ?> </em></p>
    <ul id="sslider_sortable_<?php echo $slider['slider_id'];?>" style="color:#326078">    
    <?php  
	$slider_id = $slider['slider_id'];
    $slider_posts=placid_get_slider_posts_in_order($slider_id);?>
        <input type="hidden" name="current_slider_id" value="<?php echo $slider_id;?>" />
        
    <?php    $count = 0;	
        foreach($slider_posts as $slider_post) {
          $slider_arr[] = $slider_post->post_id;
          $post = get_post($slider_post->post_id);	  
          if ( in_array($post->ID, $slider_arr) ) {
              $count++;
              $sslider_author = get_userdata($post->post_author);
              $sslider_author_dname = $sslider_author->display_name;
              echo '<li id="'.$post->ID.'"><input type="hidden" name="order[]" value="'.$post->ID.'" /><strong> &raquo; &nbsp; ' . $post->post_title . '</strong></li>'; 
          }
        }
            
        if ($count == 0) {
            echo '<li>'.__( 'No posts/pages have been added to the Slider - You can add respective post/page to slider on the Edit screen for that Post/Page', 'placid-slider' ).'</li>';
        }
		        
        echo '</ul><div class="submit">';
        
        if ($count) {echo '<input type="submit" value="Save the order"  />';}
                
        echo '</div>';
    ?>    
       </div>       
  </form>
</div> 
 
<?php } ?>

<?php if($placid_slider['multiple_sliders'] == '1') {?>
    <div id="new_slider">
    <form action="" method="post" onsubmit="return slider_checkform(this);" >
    <h3><?php _e('Enter New Slider Name','placid-slider'); ?></h3>
    <input type="hidden" name="create_new_slider" value="1" />
    
    <input name="new_slider_name" class="regular-text code" value="" style="clear:both;" />
    
    <div class="submit"><input type="submit" value="<?php _e('Create New','placid-slider'); ?>" name="create_new" /></div>
    
    </form>
    </div>
<?php }?> 
<div style="clear:left;"></div>
</div>

<div id="poststuff" class="metabox-holder has-right-sidebar"> 
<?php if ($placid_slider['support'] == "1"){ ?>
<h2 style="text-align:left;margin:0 0 10px 0">Recommendations</h3>
     <div>
        <a href="http://tabbervilla.com/wordpress-post-tabs-pro/" title="Premium WordPress Tabs Plugin" target="_blank"><img src="<?php echo placid_slider_plugin_url('images/tabbervilla_980.png');?>" alt="Premium WordPress Tabs Plugin" style="width:100%;max-width:980px;"/></a>
     </div>

<?php } ?>
     <div style="clear:left;"></div>
 </div> <!--end of poststuff --> 


</div> <!--end of float wrap -->
<?php	
}
?>