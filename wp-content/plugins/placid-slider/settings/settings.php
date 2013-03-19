<?php // Hook for adding admin menus
if ( is_admin() ){ // admin actions
  add_action('admin_menu', 'placid_slider_settings');
  add_action( 'admin_init', 'register_placid_settings' ); 
} 

// function for adding settings page to wp-admin
function placid_slider_settings() {
    // Add a new submenu under Options:
	add_menu_page( 'Placid Slider', 'Placid Slider', 'manage_options','placid-slider-admin', 'placid_slider_create_multiple_sliders', placid_slider_plugin_url( 'images/placid_slider_icon.gif' ) );
	add_submenu_page('placid-slider-admin', 'Placid Sliders', 'Sliders', 'manage_options', 'placid-slider-admin', 'placid_slider_create_multiple_sliders');
	add_submenu_page('placid-slider-admin', 'Placid Slider Settings', 'Settings', 'manage_options', 'placid-slider-settings', 'placid_slider_settings_page');
}
include('sliders.php');
// This function displays the page content for the Placid Slider Options submenu
function placid_slider_settings_page() {
global $placid_slider,$default_placid_slider_settings;
$scounter=get_option('placid_slider_scounter');
$cntr = $_GET['scounter'];

//Create Set
$new_settings_msg='';
if ($_POST['create_set'] and $_POST['create_set']=='Create New Settings Set') {
  $scounter++;
  update_option('placid_slider_scounter',$scounter);
  $options='placid_slider_options'.$scounter;
  update_option($options,$default_placid_slider_settings);
  $current_url = admin_url('admin.php?page=placid-slider-settings');
  $current_url = add_query_arg('scounter',$scounter,$current_url);
  $new_settings_msg='<div id="message" class="updated fade" style="clear:left;"><h3>'.sprintf(__('Settings Set %s created successfully. ','placid-slider'),$scounter).'<a href="'.$current_url.'">'.__('Click here to edit the new Settings set =&gt;','placid-slider').'</a></h3></div>';
}

//Reset Settings
if ( $_POST['placid_reset_settings_submit'] and $_POST['placid_reset_settings']!='n' ) {
  $placid_reset_settings=$_POST['placid_reset_settings'];
  $options='placid_slider_options'.$cntr;
  $optionsvalue=get_option($options);
  if( $placid_reset_settings == 'g' ){
	$new_settings_value=$default_placid_slider_settings;
	$new_settings_value['setname']=$optionsvalue['setname'];
	update_option($options,$new_settings_value);
  }
  else{
	if( $placid_reset_settings == '1' ){
		$new_settings_value=get_option('placid_slider_options');
		$new_settings_value['setname']=$optionsvalue['setname'];
		update_option($options,	$new_settings_value );
	}
	else{
		$new_option_name='placid_slider_options'.$placid_reset_settings;
		$new_settings_value=get_option($new_option_name);
		$new_settings_value['setname']=$optionsvalue['setname'];
		update_option($options,	$new_settings_value );
	}
  }
}

//Delete Set
if ($_POST['delete_set'] and $_POST['delete_set']=='Delete this Set' and isset($cntr) and !empty($cntr)) {
  $options='placid_slider_options'.$cntr;
  delete_option($options);
  $cntr='';
}

$group='placid-slider-group'.$cntr;
$placid_slider_options='placid_slider_options'.$cntr;
$placid_slider_curr=get_option($placid_slider_options);
if(!isset($cntr) or empty($cntr)){$curr = 'Default';}
else{$curr = $cntr;}
?>

<div class="wrap" style="clear:both;">
<h2 style="float:left;"><?php _e('Placid Slider Settings ','placid-slider'); echo $curr; ?> </h2>
<form style="float:left;margin:10px 20px" action="" method="post">
<?php if(isset($cntr) and !empty($cntr)){ ?>
<input type="submit" class="button-primary" value="Delete this Set" name="delete_set"  onclick="return confirmSettingsDelete()" />
<?php } ?>
</form>
<?php if($placid_slider_curr['disable_preview'] != '1'){?>
<div id="settings_preview"><h2 style="clear:left;"><?php _e('Preview','placid-slider'); ?></h2> 
<?php 
if ($placid_slider_curr['preview'] == "0")
	get_placid_slider($placid_slider_curr['slider_id'],$cntr);
elseif($placid_slider_curr['preview'] == "1")
	get_placid_slider_category($placid_slider_curr['catg_slug'],$cntr);
else
	get_placid_slider_recent($cntr);
?></div>
<?php } ?>

<?php echo $new_settings_msg;?>

<div id="placid_settings" style="float:left;width:70%;">
<form method="post" action="options.php" id="placid_slider_form">
<?php settings_fields($group); ?>

<?php
if(!isset($cntr) or empty($cntr)){}
else{?>
	<table class="form-table">
		<tr valign="top">
		<th scope="row"><h3><?php _e('Setting Set Name','placid-slider'); ?></h3></th>
		<td><h3><input type="text" name="<?php echo $placid_slider_options;?>[setname]" id="placid_slider_setname" class="regular-text" value="<?php echo $placid_slider_curr['setname']; ?>" /></h3></td>
		</tr>
	</table>
<?php }
?>

<div id="slider_tabs">
        <ul class="ui-tabs">
            <li style="font-weight:bold;font-size:12px;"><a href="#basic">Basic Settings</a></li>
            <li style="font-weight:bold;font-size:12px;"><a href="#slider_content">Slider Content</a></li>
            <li style="font-weight:bold;font-size:12px;"><a href="#slider_nav">Navigation Settings</a></li>
			<li style="font-weight:bold;font-size:12px;"><a href="#responsive">Responsiveness</a></li>
			<li style="font-weight:bold;font-size:12px;"><a href="#preview">Preview Settings</a></li>
			<li style="font-weight:bold;font-size:12px;"><a href="#cssvalues">Generated CSS</a></li>
        </ul>

<div id="basic">
<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:0;">
<h2><?php _e('Basic Settings','placid-slider'); ?></h2> 

<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Scrolling Speed','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[speed]" id="placid_slider_speed" class="small-text" value="<?php echo $placid_slider_curr['speed']; ?>" /><small style="color:#FF0000"><?php _e(' (IMP!! Enter Numberic value > 0)','placid-slider'); ?></small>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Speed of the slide animation, Higher value indicates faster. Enter value like 1 or 2 or 3 etc. Caution: Do not enter decimal values','placid-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Max. Number of Posts in the Placid Slider','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[no_posts]" id="placid_slider_no_posts" class="small-text" value="<?php echo $placid_slider_curr['no_posts']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Slider Orientation','placid-slider'); ?></th>
<td><select name="<?php echo $placid_slider_options;?>[orientation]" id="placid_slider_orientation">
<option value="0" <?php if ($placid_slider_curr['orientation'] == "0"){ echo "selected";}?> ><?php _e('Horizontal','placid-slider'); ?></option>
<option value="1" <?php if ($placid_slider_curr['orientation'] == "1"){ echo "selected";}?> ><?php _e('Vertical','placid-slider'); ?></option>
</select>
</td>
</tr>

<?php if($placid_slider_curr['orientation']!='1'){ ?>
<tr valign="top" class="show horz">
<th scope="row"><?php _e('Complete Slider Width','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[width]" id="placid_slider_width" class="small-text" value="<?php echo $placid_slider_curr['width']; ?>" />&nbsp;<?php _e('px','placid-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('If set to 0, will take the container\'s width','placid-slider'); ?>
	</div>
</span>
</td>
</tr>
<tr valign="top" class="hide vert">
<th scope="row"><?php _e('Complete Slider Height','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[tot_height]" id="placid_slider_tot_height" class="small-text" value="<?php echo $placid_slider_curr['tot_height']; ?>" />&nbsp;<?php _e('px','placid-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Visible height of the slider whent vertical orientation is selected. This height is excluding Slider title height','placid-slider'); ?>
	</div>
</span>
</td>
</tr>
<?php } else { ?>
<tr valign="top" class="hide horz">
<th scope="row"><?php _e('Complete Slider Width','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[width]" id="placid_slider_width" class="small-text" value="<?php echo $placid_slider_curr['width']; ?>" />&nbsp;<?php _e('px','placid-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('If set to 0, will take the container\'s width','placid-slider'); ?>
	</div>
</span>
</td>
</tr>
<tr valign="top" class="show vert">
<th scope="row"><?php _e('Complete Slider Height','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[tot_height]" id="placid_slider_tot_height" class="small-text" value="<?php echo $placid_slider_curr['tot_height']; ?>" />&nbsp;<?php _e('px','placid-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Visible height of the slider whent vertical orientation is selected. This height is excluding Slider title height','placid-slider'); ?>
	</div>
</span>
</td>
</tr>
<?php } ?>

<tr valign="top">
<th scope="row"><?php _e('Slide Item Width','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[iwidth]" id="placid_slider_iwidth" class="small-text" value="<?php echo $placid_slider_curr['iwidth']; ?>" />&nbsp;<?php _e('px','placid-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Either keep it blank for variable width items or Enter numeric value > 0','placid-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Slide Item Height','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[height]" id="placid_slider_height" class="small-text" value="<?php echo $placid_slider_curr['height']; ?>" />&nbsp;<?php _e('px','placid-slider'); ?><small style="color:#FF0000"><?php _e(' (IMP!! Enter numeric value > 0)','placid-slider'); ?></small></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Slide Background Color','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[bg_color]" id="color_value_1" value="<?php echo $placid_slider_curr['bg_color']; ?>" />&nbsp; <img id="color_picker_1" src="<?php echo placid_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="<?php _e('Pick the color of your choice','placid-slider'); ?>" /><div class="color-picker-wrap" id="colorbox_1"></div> <br /> 
<label for="placid_slider_bg"><input name="<?php echo $placid_slider_options;?>[bg]" type="checkbox" id="placid_slider_bg" value="1" <?php checked('1', $placid_slider_curr['bg']); ?>  /><?php _e(' Use Transparent Background','placid-slider'); ?></label> </td>
</tr>
 
<tr valign="top">
<th scope="row"><?php _e('Slide Border Thickness','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[border]" id="placid_slider_border" class="small-text" value="<?php echo $placid_slider_curr['border']; ?>" />&nbsp;<?php _e('px','placid-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Put 0 if no border is required','placid-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Slide Border Color','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[brcolor]" id="color_value_6" value="<?php echo $placid_slider_curr['brcolor']; ?>" />&nbsp; <img id="color_picker_6" src="<?php echo placid_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="<?php _e('Pick the color of your choice','placid-slider'); ?>" /><div class="color-picker-wrap" id="colorbox_6"></div>
</td>
</tr>

</table>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>

<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:10px 0">
<h2><?php _e('Miscellaneous','placid-slider'); ?></h2> 

<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Retain these html tags','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[allowable_tags]" class="regular-text code" value="<?php echo $placid_slider_curr['allowable_tags']; ?>" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Continue Reading Text','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[more]" class="regular-text code" value="<?php echo $placid_slider_curr['more']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Randomize Slides in Slider','placid-slider'); ?></th>
<td><input name="<?php echo $placid_slider_options;?>[rand]" type="checkbox" value="1" <?php checked('1', $placid_slider_curr['rand']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Check this if you want the slides added to appear in random order','placid-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Slide Link (\'a\' element) attributes  ','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[a_attr]" class="regular-text code" value="<?php echo htmlentities( $placid_slider_curr['a_attr'] , ENT_QUOTES); ?>" />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('eg. target="_blank" rel="external nofollow"','placid-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Use PrettyPhoto (Lightbox) for Slide Images','placid-slider'); ?></th>
<td><input name="<?php echo $placid_slider_options;?>[pphoto]" type="checkbox" value="1" <?php checked('1', $placid_slider_curr['pphoto']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('If checked, when user clicks the slide image, it will appear in a modal lightbox','placid-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Custom fields to display for post/pages','placid-slider'); ?></th>
<td><textarea name="<?php echo $placid_slider_options;?>[fields]"  rows="5" cols="44" class="regular-text code"><?php echo $placid_slider_curr['fields']; ?></textarea><span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Separate different fields using commas eg. description,customfield2','placid-slider'); ?>
	</div>
</span>
</td>
</tr>

<?php if(!isset($cntr) or empty($cntr)){?>
<tr valign="top">
<th scope="row"><?php _e('Minimum User Level to add Post to the Slider','placid-slider'); ?></th>
<td><select name="<?php echo $placid_slider_options;?>[user_level]" >
<option value="manage_options" <?php if ($placid_slider_curr['user_level'] == "manage_options"){ echo "selected";}?> ><?php _e('Administrator','placid-slider'); ?></option>
<option value="edit_others_posts" <?php if ($placid_slider_curr['user_level'] == "edit_others_posts"){ echo "selected";}?> ><?php _e('Editor and Admininstrator','placid-slider'); ?></option>
<option value="publish_posts" <?php if ($placid_slider_curr['user_level'] == "publish_posts"){ echo "selected";}?> ><?php _e('Author, Editor and Admininstrator','placid-slider'); ?></option>
<option value="edit_posts" <?php if ($placid_slider_curr['user_level'] == "edit_posts"){ echo "selected";}?> ><?php _e('Contributor, Author, Editor and Admininstrator','placid-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Text to display in the JavaScript disabled browser','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[noscript]" class="regular-text code" value="<?php echo $placid_slider_curr['noscript']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Add Shortcode Support','placid-slider'); ?></th>
<td><input name="<?php echo $placid_slider_options;?>[shortcode]" type="checkbox" value="1" <?php checked('1', $placid_slider_curr['shortcode']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Check this if you want to use Placid Slider Shortcode i.e [placidslider]','placid-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Placid Slider Styles to Use on Other than Post/Pages','placid-slider'); ?></th>
<td><select name="<?php echo $placid_slider_options;?>[stylesheet]" >
<?php 
$directory = PLACID_SLIDER_CSS_DIR;
if ($handle = opendir($directory)) {
    while (false !== ($file = readdir($handle))) { 
     if($file != '.' and $file != '..') { ?>
      <option value="<?php echo $file;?>" <?php if ($placid_slider_curr['stylesheet'] == $file){ echo "selected";}?> ><?php echo $file;?></option>
 <?php  } }
    closedir($handle);
}
?>
</select>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Stylesheet for index.php,category.php,archive.php etc; This value should not be changed unless you have a fine knowledge of CSS','placid-slider'); ?>
	</div>
</span>
</td>
</tr>
<?php } ?>

<?php if(!isset($cntr) or empty($cntr)){?>
<tr valign="top">
<th scope="row"><label for="placid_slider_multiple"><?php _e('Multiple Slider Feature','placid-slider'); ?></label></th>
<td> 
<input name="<?php echo $placid_slider_options;?>[multiple_sliders]" type="checkbox" id="placid_slider_multiple" value="1" <?php checked("1", $placid_slider_curr['multiple_sliders']); ?> /> 
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Enable Multiple Slider Function on Edit Post/Page and on Sliders Sub-menu','placid-slider'); ?>
	</div>
</span>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Create "SliderVilla Slides" Custom Post Type','placid-slider'); ?></th>
<td><select name="<?php echo $placid_slider_options;?>[custom_post]" >
<option value="0" <?php if ($placid_slider_curr['custom_post'] == "0"){ echo "selected";}?> ><?php _e('No','placid-slider'); ?></option>
<option value="1" <?php if ($placid_slider_curr['custom_post'] == "1"){ echo "selected";}?> ><?php _e('Yes','placid-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Remove Placid Slider Metabox on','placid-slider'); ?></th>
<td>
<select name="<?php echo $placid_slider_options;?>[remove_metabox][]" multiple="multiple" size="3" style="min-height:6em;">
<?php 
$args=array(
  'public'   => true
); 
$output = 'objects'; // names or objects, note names is the default
$post_types=get_post_types($args,$output); $remove_post_type_arr=$placid_slider_curr['remove_metabox'];
if(!isset($remove_post_type_arr) or !is_array($remove_post_type_arr) ) $remove_post_type_arr=array();
		foreach($post_types as $post_type) { ?>
                  <option value="<?php echo $post_type->name;?>" <?php if(in_array($post_type->name,$remove_post_type_arr)){echo 'selected';} ?>><?php echo $post_type->labels->name;?></option>
                <?php } ?>
</select>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('You can select single/multiple post types using Ctrl+Mouse Click. To deselect a single post type, use Ctrl+Mouse Click','placid-slider'); ?>
	</div>
</span>
</td>
</tr>

<?php } ?>

<tr valign="top">
<th scope="row"><?php _e('Enable FOUC','placid-slider'); ?></th>
<td><input name="<?php echo $placid_slider_options;?>[fouc]" type="checkbox" value="1" <?php checked('1', $placid_slider_curr['fouc']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('If checked the Slider will show Flash of Unstyled Content when the page is loaded, i.e. the slider content will appear before the javascripts are loaded.','placid-slider'); ?>
	</div>
</span>
</td>
</tr>

<?php if(!isset($cntr) or empty($cntr)){?>
<tr valign="top">
<th scope="row"><?php _e('Custom Styles','placid-slider'); ?></th>
<td><textarea name="<?php echo $placid_slider_options;?>[css]"  rows="5" cols="44" class="regular-text code"><?php echo $placid_slider_curr['css']; ?></textarea>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Custom css styles that you would want to be applied to the slider elements','placid-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Show Promotionals on Admin Page','placid-slider'); ?></th>
<td><select name="<?php echo $placid_slider_options;?>[support]" >
<option value="1" <?php if ($placid_slider_curr['support'] == "1"){ echo "selected";}?> ><?php _e('Yes','placid-slider'); ?></option>
<option value="0" <?php if ($placid_slider_curr['support'] == "0"){ echo "selected";}?> ><?php _e('No','placid-slider'); ?></option>
</select>
</td>
</tr>
<?php } ?>

</table>
</div>
</div> <!--Basic Tab Ends-->

<div id="slider_content">
<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:0;">
<h2><?php _e('Slider Title','placid-slider'); ?></h2> 
<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Default Title Text','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[title_text]" id="placid_slider_title_text" value="<?php echo $placid_slider_curr['title_text']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Pick Slider Title From','placid-slider'); ?></th>
<td><select name="<?php echo $placid_slider_options;?>[title_from]" >
<option value="0" <?php if ($placid_slider_curr['title_from'] == "0"){ echo "selected";}?> ><?php _e('Default Title Text','placid-slider'); ?></option>
<option value="1" <?php if ($placid_slider_curr['title_from'] == "1"){ echo "selected";}?> ><?php _e('Slider Name','placid-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font','placid-slider'); ?></th>
<td><select name="<?php echo $placid_slider_options;?>[title_font]" id="placid_slider_title_font" >
<option value="Arial,Helvetica,sans-serif" <?php if ($placid_slider_curr['title_font'] == "Arial,Helvetica,sans-serif"){ echo "selected";}?> >Arial,Helvetica,sans-serif</option>
<option value="Verdana,Geneva,sans-serif" <?php if ($placid_slider_curr['title_font'] == "Verdana,Geneva,sans-serif"){ echo "selected";}?> >Verdana,Geneva,sans-serif</option>
<option value="Tahoma,Geneva,sans-serif" <?php if ($placid_slider_curr['title_font'] == "Tahoma,Geneva,sans-serif"){ echo "selected";}?> >Tahoma,Geneva,sans-serif</option>
<option value="Trebuchet MS,sans-serif" <?php if ($placid_slider_curr['title_font'] == "Trebuchet MS,sans-serif"){ echo "selected";}?> >Trebuchet MS,sans-serif</option>
<option value="'Century Gothic','Avant Garde',sans-serif" <?php if ($placid_slider_curr['title_font'] == "'Century Gothic','Avant Garde',sans-serif"){ echo "selected";}?> >'Century Gothic','Avant Garde',sans-serif</option>
<option value="'Arial Narrow',sans-serif" <?php if ($placid_slider_curr['title_font'] == "'Arial Narrow',sans-serif"){ echo "selected";}?> >'Arial Narrow',sans-serif</option>
<option value="'Arial Black',sans-serif" <?php if ($placid_slider_curr['title_font'] == "'Arial Black',sans-serif"){ echo "selected";}?> >'Arial Black',sans-serif</option>
<option value="'Gills Sans MT','Gills Sans',sans-serif" <?php if ($placid_slider_curr['title_font'] == "'Gills Sans MT','Gills Sans',sans-serif"){ echo "selected";} ?> >'Gills Sans MT','Gills Sans',sans-serif</option>
<option value="'Times New Roman',Times,serif" <?php if ($placid_slider_curr['title_font'] == "'Times New Roman',Times,serif"){ echo "selected";}?> >'Times New Roman',Times,serif</option>
<option value="Georgia,serif" <?php if ($placid_slider_curr['title_font'] == "Georgia,serif"){ echo "selected";}?> >Georgia,serif</option>
<option value="Garamond,serif" <?php if ($placid_slider_curr['title_font'] == "Garamond,serif"){ echo "selected";}?> >Garamond,serif</option>
<option value="'Century Schoolbook','New Century Schoolbook',serif" <?php if ($placid_slider_curr['title_font'] == "'Century Schoolbook','New Century Schoolbook',serif"){ echo "selected";}?> >'Century Schoolbook','New Century Schoolbook',serif</option>
<option value="'Bookman Old Style',Bookman,serif" <?php if ($placid_slider_curr['title_font'] == "'Bookman Old Style',Bookman,serif"){ echo "selected";}?> >'Bookman Old Style',Bookman,serif</option>
<option value="'Comic Sans MS',cursive" <?php if ($placid_slider_curr['title_font'] == "'Comic Sans MS',cursive"){ echo "selected";}?> >'Comic Sans MS',cursive</option>
<option value="'Courier New',Courier,monospace" <?php if ($placid_slider_curr['title_font'] == "'Courier New',Courier,monospace"){ echo "selected";}?> >'Courier New',Courier,monospace</option>
<option value="'Copperplate Gothic Bold',Copperplate,fantasy" <?php if ($placid_slider_curr['title_font'] == "'Copperplate Gothic Bold',Copperplate,fantasy"){ echo "selected";}?> >'Copperplate Gothic Bold',Copperplate,fantasy</option>
<option value="Impact,fantasy" <?php if ($placid_slider_curr['title_font'] == "Impact,fantasy"){ echo "selected";}?> >Impact,fantasy</option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Color','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[title_fcolor]" id="color_value_2" value="<?php echo $placid_slider_curr['title_fcolor']; ?>" />&nbsp; <img id="color_picker_2" src="<?php echo placid_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="<?php _e('Pick the color of your choice','placid-slider'); ?>" /><div class="color-picker-wrap" id="colorbox_2"></div></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Size','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[title_fsize]" id="placid_slider_title_fsize" class="small-text" value="<?php echo $placid_slider_curr['title_fsize']; ?>" />&nbsp;<?php _e('px','placid-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Style','placid-slider'); ?></th>
<td><select name="<?php echo $placid_slider_options;?>[title_fstyle]" id="placid_slider_title_fstyle" >
<option value="bold" <?php if ($placid_slider_curr['title_fstyle'] == "bold"){ echo "selected";}?> ><?php _e('Bold','placid-slider'); ?></option>
<option value="bold italic" <?php if ($placid_slider_curr['title_fstyle'] == "bold italic"){ echo "selected";}?> ><?php _e('Bold Italic','placid-slider'); ?></option>
<option value="italic" <?php if ($placid_slider_curr['title_fstyle'] == "italic"){ echo "selected";}?> ><?php _e('Italic','placid-slider'); ?></option>
<option value="normal" <?php if ($placid_slider_curr['title_fstyle'] == "normal"){ echo "selected";}?> ><?php _e('Normal','placid-slider'); ?></option>
</select>
</td>
</tr>
</table>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>

<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:10px 0">
<h2><?php _e('Post Title','placid-slider'); ?></h2> 
<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Font','placid-slider'); ?></th>
<td><select name="<?php echo $placid_slider_options;?>[ptitle_font]" id="placid_slider_ptitle_font" >
<option value="Arial,Helvetica,sans-serif" <?php if ($placid_slider_curr['ptitle_font'] == "Arial,Helvetica,sans-serif"){ echo "selected";}?> >Arial,Helvetica,sans-serif</option>
<option value="Verdana,Geneva,sans-serif" <?php if ($placid_slider_curr['ptitle_font'] == "Verdana,Geneva,sans-serif"){ echo "selected";}?> >Verdana,Geneva,sans-serif</option>
<option value="Tahoma,Geneva,sans-serif" <?php if ($placid_slider_curr['ptitle_font'] == "Tahoma,Geneva,sans-serif"){ echo "selected";}?> >Tahoma,Geneva,sans-serif</option>
<option value="Trebuchet MS,sans-serif" <?php if ($placid_slider_curr['ptitle_font'] == "Trebuchet MS,sans-serif"){ echo "selected";}?> >Trebuchet MS,sans-serif</option>
<option value="'Century Gothic','Avant Garde',sans-serif" <?php if ($placid_slider_curr['ptitle_font'] == "'Century Gothic','Avant Garde',sans-serif"){ echo "selected";}?> >'Century Gothic','Avant Garde',sans-serif</option>
<option value="'Arial Narrow',sans-serif" <?php if ($placid_slider_curr['ptitle_font'] == "'Arial Narrow',sans-serif"){ echo "selected";}?> >'Arial Narrow',sans-serif</option>
<option value="'Arial Black',sans-serif" <?php if ($placid_slider_curr['ptitle_font'] == "'Arial Black',sans-serif"){ echo "selected";}?> >'Arial Black',sans-serif</option>
<option value="'Gills Sans MT','Gills Sans',sans-serif" <?php if ($placid_slider_curr['ptitle_font'] == "'Gills Sans MT','Gills Sans',sans-serif"){ echo "selected";} ?> >'Gills Sans MT','Gills Sans',sans-serif</option>
<option value="'Times New Roman',Times,serif" <?php if ($placid_slider_curr['ptitle_font'] == "'Times New Roman',Times,serif"){ echo "selected";}?> >'Times New Roman',Times,serif</option>
<option value="Georgia,serif" <?php if ($placid_slider_curr['ptitle_font'] == "Georgia,serif"){ echo "selected";}?> >Georgia,serif</option>
<option value="Garamond,serif" <?php if ($placid_slider_curr['ptitle_font'] == "Garamond,serif"){ echo "selected";}?> >Garamond,serif</option>
<option value="'Century Schoolbook','New Century Schoolbook',serif" <?php if ($placid_slider_curr['ptitle_font'] == "'Century Schoolbook','New Century Schoolbook',serif"){ echo "selected";}?> >'Century Schoolbook','New Century Schoolbook',serif</option>
<option value="'Bookman Old Style',Bookman,serif" <?php if ($placid_slider_curr['ptitle_font'] == "'Bookman Old Style',Bookman,serif"){ echo "selected";}?> >'Bookman Old Style',Bookman,serif</option>
<option value="'Comic Sans MS',cursive" <?php if ($placid_slider_curr['ptitle_font'] == "'Comic Sans MS',cursive"){ echo "selected";}?> >'Comic Sans MS',cursive</option>
<option value="'Courier New',Courier,monospace" <?php if ($placid_slider_curr['ptitle_font'] == "'Courier New',Courier,monospace"){ echo "selected";}?> >'Courier New',Courier,monospace</option>
<option value="'Copperplate Gothic Bold',Copperplate,fantasy" <?php if ($placid_slider_curr['ptitle_font'] == "'Copperplate Gothic Bold',Copperplate,fantasy"){ echo "selected";}?> >'Copperplate Gothic Bold',Copperplate,fantasy</option>
<option value="Impact,fantasy" <?php if ($placid_slider_curr['ptitle_font'] == "Impact,fantasy"){ echo "selected";}?> >Impact,fantasy</option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Color','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[ptitle_fcolor]" id="color_value_3" value="<?php echo $placid_slider_curr['ptitle_fcolor']; ?>" />&nbsp; <img id="color_picker_3" src="<?php echo placid_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="<?php _e('Pick the color of your choice','placid-slider'); ?>" /><div class="color-picker-wrap" id="colorbox_3"></div></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Size','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[ptitle_fsize]" id="placid_slider_ptitle_fsize" class="small-text" value="<?php echo $placid_slider_curr['ptitle_fsize']; ?>" />&nbsp;<?php _e('px','placid-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Style','placid-slider'); ?></th>
<td><select name="<?php echo $placid_slider_options;?>[ptitle_fstyle]" id="placid_slider_ptitle_fstyle" >
<option value="bold" <?php if ($placid_slider_curr['ptitle_fstyle'] == "bold"){ echo "selected";}?> ><?php _e('Bold','placid-slider'); ?></option>
<option value="bold italic" <?php if ($placid_slider_curr['ptitle_fstyle'] == "bold italic"){ echo "selected";}?> ><?php _e('Bold Italic','placid-slider'); ?></option>
<option value="italic" <?php if ($placid_slider_curr['ptitle_fstyle'] == "italic"){ echo "selected";}?> ><?php _e('Italic','placid-slider'); ?></option>
<option value="normal" <?php if ($placid_slider_curr['ptitle_fstyle'] == "normal"){ echo "selected";}?> ><?php _e('Normal','placid-slider'); ?></option>
</select>
</td>
</tr>
</table>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>

<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:10px 0">
<h2><?php _e('Thumbnail Image','placid-slider'); ?></h2>
<table class="form-table">
<tr valign="top"> 
<th scope="row"><?php _e('Image Pick Preferences','placid-slider'); ?> <small><?php _e('(The first one is having priority over second, the second having priority on third and so on)','placid-slider'); ?></small></th> 
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Image Pick Sequence','placid-slider'); ?> <small><?php _e('(The first one is having priority over second, the second having priority on third and so on)','placid-slider'); ?></small> </span></legend> 
<input name="<?php echo $placid_slider_options;?>[img_pick][0]" type="checkbox" value="1" <?php checked('1', $placid_slider_curr['img_pick'][0]); ?>  /> <?php _e('Use Custom Field/Key','placid-slider'); ?> &nbsp; &nbsp; 
<input type="text" name="<?php echo $placid_slider_options;?>[img_pick][1]" class="text" value="<?php echo $placid_slider_curr['img_pick'][1]; ?>" /> <?php _e('Name of the Custom Field/Key','placid-slider'); ?>
<br />
<input name="<?php echo $placid_slider_options;?>[img_pick][2]" type="checkbox" value="1" <?php checked('1', $placid_slider_curr['img_pick'][2]); ?>  /> <?php _e('Use Featured Post/Thumbnail (Wordpress 3.0 +  feature)','placid-slider'); ?>&nbsp; <br />
<input name="<?php echo $placid_slider_options;?>[img_pick][3]" type="checkbox" value="1" <?php checked('1', $placid_slider_curr['img_pick'][3]); ?>  /> <?php _e('Consider Images attached to the post','placid-slider'); ?> &nbsp; &nbsp; 
<input type="text" name="<?php echo $placid_slider_options;?>[img_pick][4]" class="small-text" value="<?php echo $placid_slider_curr['img_pick'][4]; ?>" /> <?php _e('Order of the Image attachment to pick','placid-slider'); ?> &nbsp; <br />
<input name="<?php echo $placid_slider_options;?>[img_pick][5]" type="checkbox" value="1" <?php checked('1', $placid_slider_curr['img_pick'][5]); ?>  /> <?php _e('Scan images from the post, in case there is no attached image to the post','placid-slider'); ?>&nbsp; 
</fieldset></td> 
</tr> 

<tr valign="top">
<th scope="row"><?php _e('Align to','placid-slider'); ?></th>
<td><select name="<?php echo $placid_slider_options;?>[img_align]" id="placid_slider_img_align" >
<option value="left" <?php if ($placid_slider_curr['img_align'] == "left"){ echo "selected";}?> ><?php _e('Left','placid-slider'); ?></option>
<option value="right" <?php if ($placid_slider_curr['img_align'] == "right"){ echo "selected";}?> ><?php _e('Right','placid-slider'); ?></option>
<option value="none" <?php if ($placid_slider_curr['img_align'] == "none"){ echo "selected";}?> ><?php _e('Center','placid-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Wordpress Image Extract Size','placid-slider'); ?>
</th>
<td><select name="<?php echo $placid_slider_options;?>[crop]" id="placid_slider_img_crop" >
<option value="0" <?php if ($placid_slider_curr['crop'] == "0"){ echo "selected";}?> ><?php _e('Full','placid-slider'); ?></option>
<option value="1" <?php if ($placid_slider_curr['crop'] == "1"){ echo "selected";}?> ><?php _e('Large','placid-slider'); ?></option>
<option value="2" <?php if ($placid_slider_curr['crop'] == "2"){ echo "selected";}?> ><?php _e('Medium','placid-slider'); ?></option>
<option value="3" <?php if ($placid_slider_curr['crop'] == "3"){ echo "selected";}?> ><?php _e('Thumbnail','placid-slider'); ?></option>
</select>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('This is for fast page load, in case you choose \'Custom Size\' setting from below, you would not like to extract \'full\' size image from the media library. In this case you can use, \'medium\' or \'thumbnail\' image. This is because, for every image upload to the media gallery WordPress creates four sizes of the same image. So you can choose which to load in the slider and then specify the actual size.','placid-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top"> 
<th scope="row"><?php _e('Image Size','placid-slider'); ?></th> 
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Image Size','placid-slider'); ?></span></legend> 
<label for="<?php echo $placid_slider_options;?>[img_width]"><?php _e('Width','placid-slider'); ?></label>
<input type="text" name="<?php echo $placid_slider_options;?>[img_width]" class="small-text" value="<?php echo $placid_slider_curr['img_width']; ?>" />&nbsp;<?php _e('px','placid-slider'); ?> &nbsp;&nbsp; 
<label for="<?php echo $placid_slider_options;?>[img_height]"><?php _e('Height','placid-slider'); ?></label>
<input type="text" name="<?php echo $placid_slider_options;?>[img_height]" class="small-text" value="<?php echo $placid_slider_curr['img_height']; ?>" />&nbsp;<?php _e('px','placid-slider'); ?> &nbsp;&nbsp; 
</fieldset></td> 
</tr>

<tr valign="top">
<th scope="row"><?php _e('Border Thickness','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[img_border]" id="placid_slider_img_border" class="small-text" value="<?php echo $placid_slider_curr['img_border']; ?>" />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Put 0 if no border is required','placid-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Border Color','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[img_brcolor]" id="color_value_4" value="<?php echo $placid_slider_curr['img_brcolor']; ?>" />&nbsp; <img id="color_picker_4" src="<?php echo placid_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="<?php _e('Pick the color of your choice','placid-slider'); ?>" /><div class="color-picker-wrap" id="colorbox_4"></div></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Disable Image Cropping (using timthumb)','placid-slider'); ?></th>
<td><select name="<?php echo $placid_slider_options;?>[timthumb]" >
<option value="0" <?php if ($placid_slider_curr['timthumb'] == "0"){ echo "selected";}?> ><?php _e('No','placid-slider'); ?></option>
<option value="1" <?php if ($placid_slider_curr['timthumb'] == "1"){ echo "selected";}?> ><?php _e('Yes','placid-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Make pure Image Slider','placid-slider'); ?></th>
<td><input name="<?php echo $placid_slider_options;?>[image_only]" type="checkbox" value="1" <?php checked('1', $placid_slider_curr['image_only']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Check this to convert Placid Slider to Image Slider with no content','placid-slider'); ?>
	</div>
</span>
</td>
</tr>
</table>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>

<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:10px 0">
<h2><?php _e('Slide Content','placid-slider'); ?></h2> 
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Show content in slides below title','placid-slider'); ?></th>
<td><input name="<?php echo $placid_slider_options;?>[show_content]" type="checkbox" value="1" <?php checked('1', $placid_slider_curr['show_content']); ?>  /></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Font','placid-slider'); ?></th>
<td><select name="<?php echo $placid_slider_options;?>[content_font]" id="placid_slider_content_font" >
<option value="Arial,Helvetica,sans-serif" <?php if ($placid_slider_curr['content_font'] == "Arial,Helvetica,sans-serif"){ echo "selected";}?> >Arial,Helvetica,sans-serif</option>
<option value="Verdana,Geneva,sans-serif" <?php if ($placid_slider_curr['content_font'] == "Verdana,Geneva,sans-serif"){ echo "selected";}?> >Verdana,Geneva,sans-serif</option>
<option value="Tahoma,Geneva,sans-serif" <?php if ($placid_slider_curr['content_font'] == "Tahoma,Geneva,sans-serif"){ echo "selected";}?> >Tahoma,Geneva,sans-serif</option>
<option value="Trebuchet MS,sans-serif" <?php if ($placid_slider_curr['content_font'] == "Trebuchet MS,sans-serif"){ echo "selected";}?> >Trebuchet MS,sans-serif</option>
<option value="'Century Gothic','Avant Garde',sans-serif" <?php if ($placid_slider_curr['content_font'] == "'Century Gothic','Avant Garde',sans-serif"){ echo "selected";}?> >'Century Gothic','Avant Garde',sans-serif</option>
<option value="'Arial Narrow',sans-serif" <?php if ($placid_slider_curr['content_font'] == "'Arial Narrow',sans-serif"){ echo "selected";}?> >'Arial Narrow',sans-serif</option>
<option value="'Arial Black',sans-serif" <?php if ($placid_slider_curr['content_font'] == "'Arial Black',sans-serif"){ echo "selected";}?> >'Arial Black',sans-serif</option>
<option value="'Gills Sans MT','Gills Sans',sans-serif" <?php if ($placid_slider_curr['content_font'] == "'Gills Sans MT','Gills Sans',sans-serif"){ echo "selected";} ?> >'Gills Sans MT','Gills Sans',sans-serif</option>
<option value="'Times New Roman',Times,serif" <?php if ($placid_slider_curr['content_font'] == "'Times New Roman',Times,serif"){ echo "selected";}?> >'Times New Roman',Times,serif</option>
<option value="Georgia,serif" <?php if ($placid_slider_curr['content_font'] == "Georgia,serif"){ echo "selected";}?> >Georgia,serif</option>
<option value="Garamond,serif" <?php if ($placid_slider_curr['content_font'] == "Garamond,serif"){ echo "selected";}?> >Garamond,serif</option>
<option value="'Century Schoolbook','New Century Schoolbook',serif" <?php if ($placid_slider_curr['content_font'] == "'Century Schoolbook','New Century Schoolbook',serif"){ echo "selected";}?> >'Century Schoolbook','New Century Schoolbook',serif</option>
<option value="'Bookman Old Style',Bookman,serif" <?php if ($placid_slider_curr['content_font'] == "'Bookman Old Style',Bookman,serif"){ echo "selected";}?> >'Bookman Old Style',Bookman,serif</option>
<option value="'Comic Sans MS',cursive" <?php if ($placid_slider_curr['content_font'] == "'Comic Sans MS',cursive"){ echo "selected";}?> >'Comic Sans MS',cursive</option>
<option value="'Courier New',Courier,monospace" <?php if ($placid_slider_curr['content_font'] == "'Courier New',Courier,monospace"){ echo "selected";}?> >'Courier New',Courier,monospace</option>
<option value="'Copperplate Gothic Bold',Copperplate,fantasy" <?php if ($placid_slider_curr['content_font'] == "'Copperplate Gothic Bold',Copperplate,fantasy"){ echo "selected";}?> >'Copperplate Gothic Bold',Copperplate,fantasy</option>
<option value="Impact,fantasy" <?php if ($placid_slider_curr['content_font'] == "Impact,fantasy"){ echo "selected";}?> >Impact,fantasy</option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Color','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[content_fcolor]" id="color_value_5" value="<?php echo $placid_slider_curr['content_fcolor']; ?>" />&nbsp; <img id="color_picker_5" src="<?php echo placid_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="Pick the color of your choice','placid-slider'); ?>" /><div class="color-picker-wrap" id="colorbox_5"></div></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Size','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[content_fsize]" id="placid_slider_content_fsize" class="small-text" value="<?php echo $placid_slider_curr['content_fsize']; ?>" />&nbsp;<?php _e('px','placid-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Style','placid-slider'); ?></th>
<td><select name="<?php echo $placid_slider_options;?>[content_fstyle]" id="placid_slider_content_fstyle" >
<option value="bold" <?php if ($placid_slider_curr['content_fstyle'] == "bold"){ echo "selected";}?> ><?php _e('Bold','placid-slider'); ?></option>
<option value="bold italic" <?php if ($placid_slider_curr['content_fstyle'] == "bold italic"){ echo "selected";}?> ><?php _e('Bold Italic','placid-slider'); ?></option>
<option value="italic" <?php if ($placid_slider_curr['content_fstyle'] == "italic"){ echo "selected";}?> ><?php _e('Italic','placid-slider'); ?></option>
<option value="normal" <?php if ($placid_slider_curr['content_fstyle'] == "normal"){ echo "selected";}?> ><?php _e('Normal','placid-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Pick content From','placid-slider'); ?></th>
<td><select name="<?php echo $placid_slider_options;?>[content_from]" id="placid_slider_content_from" >
<option value="slider_content" <?php if ($placid_slider_curr['content_from'] == "slider_content"){ echo "selected";}?> ><?php _e('Slider Content Custom field','placid-slider'); ?></option>
<option value="excerpt" <?php if ($placid_slider_curr['content_from'] == "excerpt"){ echo "selected";}?> ><?php _e('Post Excerpt','placid-slider'); ?></option>
<option value="content" <?php if ($placid_slider_curr['content_from'] == "content"){ echo "selected";}?> ><?php _e('From Content','placid-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Maximum content size (in characters)','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[content_chars]" id="placid_slider_content_chars" class="small-text" value="<?php echo $placid_slider_curr['content_chars']; ?>" />&nbsp;<?php _e('characters','placid-slider'); ?> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Maximum content size (in words)','placid-slider'); ?></th>
<td><input type="text" name="<?php echo $placid_slider_options;?>[content_limit]" id="placid_slider_content_limit" class="small-text" value="<?php echo $placid_slider_curr['content_limit']; ?>" />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('If specified will override the \'Maximum Content Size in Chracters\' setting above','placid-slider'); ?>
	</div>
</span>
</td>
</tr>

</table>

</div>
</div> <!-- slider_content tab ends-->

<div id="slider_nav">
<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:0;">
<h2><?php _e('Navigational Arrows','placid-slider'); ?></h2> 

<table class="form-table">
<tr valign="top"> 
<th scope="row"><label for="placid_slider_prev_next"><?php _e('Enable Prev/Next navigation arrows','placid-slider'); ?></label></th> 
<td> 
<input name="<?php echo $placid_slider_options;?>[prev_next]" type="checkbox" id="placid_slider_prev_next" value="1" <?php checked("1", $placid_slider_curr['prev_next']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('If enabled, will disable auto scrolling / sliding','placid-slider'); ?>
	</div>
</span>
</td>
</tr>
</table>
</div>
</div><!-- slider_nav tab ends-->

<div id="responsive">
<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:10px 0">
<h2><?php _e('Responsive Design Settings','placid-slider'); ?></h2> 

<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Enable Responsive Design','placid-slider'); ?></th>
<td><input name="<?php echo $placid_slider_options;?>[responsive]" type="checkbox" value="1" <?php checked('1', $placid_slider_curr['responsive']); ?>  />&nbsp;<?php _e('check this if you want to enable the responsive layout for Placid (you should be using Responsive/Fluid WordPress theme for this feature to work!) ','placid-slider'); ?></td>
</tr>
</table>
</div>

</div> <!--#responsive-->

<div id="preview">
<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:0;">
<h2><?php _e('Preview on Settings Panel','placid-slider'); ?></h2> 

<table class="form-table">

<tr valign="top"> 
<th scope="row"><label for="placid_slider_disable_preview"><?php _e('Disable Preview Section','placid-slider'); ?></label></th> 
<td> 
<input name="<?php echo $placid_slider_options;?>[disable_preview]" type="checkbox" id="placid_slider_disable_preview" value="1" <?php checked("1", $placid_slider_curr['disable_preview']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('If disabled, the \'Preview\' of Slider on this Settings page will be removed.','placid-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Placid Template Tag for Preview','placid-slider'); ?></th>
<td><select name="<?php echo $placid_slider_options;?>[preview]" >
<option value="2" <?php if ($placid_slider_curr['preview'] == "2"){ echo "selected";}?> ><?php _e('Recent Posts Slider','placid-slider'); ?></option>
<option value="1" <?php if ($placid_slider_curr['preview'] == "1"){ echo "selected";}?> ><?php _e('Category Slider','placid-slider'); ?></option>
<option value="0" <?php if ($placid_slider_curr['preview'] == "0"){ echo "selected";}?> ><?php _e('Custom Slider with Slider ID','placid-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top"> 
<th scope="row"><?php _e('Preview Slider Params','placid-slider'); ?></th> 
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Preview Slider Params','placid-slider'); ?></span></legend> 
<label for="<?php echo $placid_slider_options;?>[slider_id]"><?php _e('Slider ID in case of Custom Slider','placid-slider'); ?></label>
<input type="text" name="<?php echo $placid_slider_options;?>[slider_id]" class="small-text" value="<?php echo $placid_slider_curr['slider_id']; ?>" /> 
<br />  <br />
<label for="<?php echo $placid_slider_options;?>[catg_slug]"><?php _e('Category Slug in case of Category Slider','placid-slider'); ?></label>
<input type="text" name="<?php echo $placid_slider_options;?>[catg_slug]" class="regular-text code" style="width:100px;" value="<?php echo $placid_slider_curr['catg_slug']; ?>" /> 
</fieldset></td> 
</tr> 

</table>
</div>

</div><!-- preview tab ends-->

<div id="cssvalues">
<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:10px 0">
<h2><?php _e('CSS Generated thru these settings','placid-slider'); ?></h2> 
<p><?php _e('Save Changes for the settings first and then view this data. You can use this CSS in your \'custom\' stylesheets if you use other than \'default\' value for the Stylesheet folder.','placid-slider'); ?></p> 
<?php $placid_slider_css = placid_get_inline_css($cntr,$echo='1'); ?>
<div style="font-family:monospace;font-size:13px;background:#ddd;">
.placid_slider_set<?php echo $cntr;?>.placid_slider{<?php echo $placid_slider_css['placid_slider'];?>} <br />
.placid_slider_set<?php echo $cntr;?> .placid_slider_handle{<?php echo $placid_slider_css['placid_slider_handle'];?>} <br />
.placid_slider_set<?php echo $cntr;?> .sldr_title{<?php echo $placid_slider_css['sldr_title'];?>} <br />
.placid_slider_set<?php echo $cntr;?> .placid_slideri{<?php echo $placid_slider_css['placid_slideri'];?>} <br />
.placid_slider_set<?php echo $cntr;?> .placid_slider_thumbnail{<?php echo $placid_slider_css['placid_slider_thumbnail'];?>} <br />
.placid_slider_set<?php echo $cntr;?> .placid_slideri h2{<?php echo $placid_slider_css['placid_slider_h2'];?>} <br />
.placid_slider_set<?php echo $cntr;?> .placid_slideri h2 a{<?php echo $placid_slider_css['placid_slider_h2_a'];?>} <br />
.placid_slider_set<?php echo $cntr;?> .placid_slideri span{<?php echo $placid_slider_css['placid_slider_span'];?>} <br />
.placid_slider_set<?php echo $cntr;?> .placid_slideri p.more{<?php echo $placid_slider_css['placid_slider_p_more'];?>} <br />
.placid_slider_set<?php echo $cntr;?> .placid_text{<?php echo $placid_slider_css['placid_text'];?>} 
</div>
</div>
</div> <!--#cssvalues-->

<div class="svilla_cl"></div><div class="svilla_cr"></div>

</div> <!--end of tabs -->

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>

<!--Form to reset Settings set-->
<form action="" method="post">
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Reset Settings to','placid-slider'); ?></th>
<td><select name="placid_reset_settings" id="placid_slider_reset_settings" >
<option value="n" selected ><?php _e('None','placid-slider'); ?></option>
<option value="g" ><?php _e('Global Default','placid-slider'); ?></option>

<?php 
for($i=1;$i<=$scounter;$i++){
	if ($i==1){
	  echo '<option value="'.$i.'" >'.__('Default Settings Set','placid-slider').'</option>';
	}
	else {
	  if($settings_set=get_option('placid_slider_options'.$i)){
		echo '<option value="'.$i.'" >'.$settings_set['setname'].' (ID '.$i.')</option>';
	  }
	}
}
?>

</select>
</td>
</tr>
</table>

<p class="submit">
<input name="placid_reset_settings_submit" type="submit" class="button-primary" value="<?php _e('Reset Settings') ?>" />
</p>
</form>

</div> <!--end of float left -->

<div id="poststuff" class="metabox-holder has-right-sidebar" style="float:left;width:28%;max-width:350px;min-width:inherit;"> 
<form style="float:left;margin-right:10px;font-size:14px;margin-bottom:5px;" action="" method="post">
<input type="submit" class="button-primary" style="font-size:13px;" value="Create New Settings Set" name="create_set"  onclick="return confirmSettingsCreate()" />
</form>

<?php $url = placid_sslider_admin_url( array( 'page' => 'placid-slider-admin' ) );?>
<a href="<?php echo $url; ?>" title="<?php _e('Go to Sliders page where you can re-order the slide posts, delete the slides from the slider etc.','placid-slider'); ?>" class="button-primary" style="font-size:13px;margin-bottom:5px;"><?php _e('Go to Sliders Admin','placid-slider'); ?></a>
<div class="svilla_cl"></div>

<div class="postbox" style="margin:10px 0;"> 
			  <h3 class="hndle"><span></span><?php _e('Available Settings Sets','placid-slider'); ?></h3> 
			  <div class="inside">
<?php 
for($i=1;$i<=$scounter;$i++){
   if ($i==1){
      echo '<h4><a href="'.placid_sslider_admin_url( array( 'page' => 'placid-slider-settings' ) ).'" title="(Settings Set ID '.$i.')">Default Settings (ID '.$i.')</a></h4>';
   }
   else {
      if($settings_set=get_option('placid_slider_options'.$i)){
		echo '<h4><a href="'.placid_sslider_admin_url( array( 'page' => 'placid-slider-settings' ) ).'&scounter='.$i.'" title="(Settings Set ID '.$i.')">'.$settings_set['setname'].' (ID '.$i.')</a></h4>';
	  }
   }
}
?>
</div></div>

<div class="postbox"> 
<div style="background:#eee;line-height:200%"><a style="text-decoration:none;font-weight:bold;font-size:100%;color:#990000" href="http://guides.slidervilla.com/placid-slider/" title="Click here to read how to use the plugin and frequently asked questions about the plugin" target="_blank"> ==> Usage Guide and General FAQs</a></div>
</div>

<?php if ($placid_slider['support'] == "1"){ ?>
    
     		<div class="postbox"> 
			  <h3 class="hndle"><span></span><?php _e('Recommended Themes','placid-slider'); ?></h3> 
			  <div class="inside">
                     <div style="margin:10px 5px">
                        <a href="http://slidervilla.com/go/elegantthemes/" title="Recommended WordPress Themes" target="_blank"><img src="<?php echo placid_slider_plugin_url('images/elegantthemes.gif');?>" alt="Recommended WordPress Themes" style="width:100%;" /></a>
                        <p><a href="http://slidervilla.com/go/elegantthemes/" title="Recommended WordPress Themes" target="_blank">Elegant Themes</a> are attractive, compatible, affordable, SEO optimized WordPress Themes and have best support in community.</p>
                        <p><strong>Beautiful themes, Great support!</strong></p>
                        <p><a href="http://slidervilla.com/go/elegantthemes/" title="Recommended WordPress Themes" target="_blank">For more info visit ElegantThemes</a></p>
                     </div>
               </div></div>
          
			<div class="postbox"> 
			  <h3 class="hndle"><span><?php _e('About this Plugin:','placid-slider'); ?></span></h3> 
			  <div class="inside">
                <ul>
                <li><a href="http://slidervilla.com/placid/" title="<?php _e('Placid Slider Homepage','placid-slider'); ?>
" ><?php _e('Plugin Homepage','placid-slider'); ?></a></li>
				<li><a href="http://support.slidervilla.com/" title="<?php _e('Support Forum','placid-slider'); ?>
" ><?php _e('Support Forum','placid-slider'); ?></a></li>
				<li><a href="http://guides.slidervilla.com/placid-slider/" title="<?php _e('Usage Guide','placid-slider'); ?>
" ><?php _e('Usage Guide','placid-slider'); ?></a></li>
				<li><strong>Current Version: 1.1</strong></li>
                </ul> 
              </div> 
			</div> 
	<?php } ?>
                 
 </div> <!--end of poststuff --> 

<div style="clear:left;"></div>
<div style="clear:right;"></div>

</div> <!--end of float wrap -->
<?php	
}
function register_placid_settings() { // whitelist options
  $scounter=get_option('placid_slider_scounter');
  for($i=1;$i<=$scounter;$i++){
	   if ($i==1){
		  register_setting( 'placid-slider-group', 'placid_slider_options' );
	   }
	   else {
	      $group='placid-slider-group'.$i;
		  $options='placid_slider_options'.$i;
		  register_setting( $group, $options );
	   }
  }
}
?>