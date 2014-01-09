<?php
/*
Plugin Name: PDFjs Viewer
Plugin URI: http://tphsfalconer.com
Description: View PDFs with pdf.js
Version: 1.1
Author: Ben & Josh
Author URI: http://tphsfalconer.com
License: GPLv2
*/
//tell wordpress to register the demolistposts shortcode
add_shortcode("pdfjs-viewer", "pdfjs_handler");

add_action('media_buttons', 'pdfjs_shortcodes', 11);

function pdfjs_shortcodes() {

	global $post;
    ?>

<div id="pdfjs_window" title="PDFJS Shortcode Dialog" style="display: none;">
	<label for="idselect"><span id="filegetir" tur="application" style="cursor:pointer;font-weight:bold;">Select File </span></label> 
	<select id="sfile">
<?php
	$args = array(
    'post_type' => 'attachment',
    'post_mime_type' => 'application/pdf',
    'numberposts' => -1,
    'post_status' => null,
    'post_parent' => null, // any parent
    ); 
	$file = get_posts($args);
	$c = count($file);
	if ($c > 0) {
		foreach ($file as $f) {
			$replaceme = array("http://".$_SERVER['SERVER_ADDR'], "http://".$_SERVER['SERVER_NAME'], "https://".$_SERVER['SERVER_ADDR'], "https://".$_SERVER['SERVER_NAME']);
			$value = str_replace($replaceme, "", $f->guid);
			echo "<option value=".$value.">".$f->post_name."</option>";
			}
	} else {
?>
    <option value='none'>No File</option>
<?php }?>
    </select>
	<div class="clear"> </div>
	<a class="button-primary" id="addFile" href="#" title="Submit File" style="color:#FFF;"><span>Submit</span></a>
</div>
<a id="pdfjs_button" title="PDFJS Shortcode" class="button-secondary" href="#" style="cursor:pointer;">
        <img src="<?php echo plugins_url('/pdfjs-viewer-shortcode'); ?>/custom/pdf_button.png" alt="PDFJS Shortcode" style="margin-top:-2px;"/> Add Pdf
    </a>   
<?php  } 

add_action('admin_head-post-new.php', 'button_js');
add_action('admin_head-post.php', 'button_js');
function button_js() {
	wp_enqueue_style('modali', plugins_url().'/pdfjs-viewer-shortcode/custom/modal.css', __FILE__);

	echo "<script type='text/javascript'>
	jQuery(function ($) {
		//$('#pdfjs_window select').css('width','110px','max-width','110px');
		$('#pdfjs_window').dialog({
			autoOpen: false,
			width: '600',
			height: '200',
			modal: true,
			draggable: false,
			resizable: false,
			closeOnEscape: true
		});
		$('#pdfjs_button').click(function(){
			$('#pdfjs_window').dialog('open');
		});
		$('#addFile').live('click', function () {
			var pdf = $('#sfile').val();
			if (pdf !='none'){
				//$('#content').val('[pdfjs-viewer url=' + pdf + ' viewer_height=700px]');
				var s = '[pdfjs-viewer url=' + pdf + ' viewer_height=700px]';
				tinyMCE.activeEditor.setContent(s);
			    //alert(pdf);
			}
			$('#pdfjs_window').dialog('close');
		});
	});
//--><!]]></script>";
}

function pdfjs_handler($incomingfrompost) {
  //set defaults 
  $incomingfrompost=shortcode_atts(array(
    'url' => 'bad-url.pdf',  
    'viewer_height' => '1360px',
    'viewer_width' => '100%',
    'fullscreen' => 'true',
    'download' => 'true',
    'print' => 'true'
  ), $incomingfrompost);
  //run function that actually does the work of the plugin
  $pdfjs_output = pdfjs_function($incomingfrompost);
  //send back text to replace shortcode in post
  return $pdfjs_output;
}

function pdfjs_function($incomingfromhandler) {
  $viewer_base_url= "/knowledge/wp-content/plugins/pdfjs-viewer-shortcode/web/viewer.php";
  
  $file_name = $incomingfromhandler["url"];
  $viewer_height = $incomingfromhandler["viewer_height"];
  $viewer_width = $incomingfromhandler["viewer_width"];
  $fullscreen = $incomingfromhandler["fullscreen"];
  $download = $incomingfromhandler["download"];
  $print = $incomingfromhandler["print"];
  
  if ($download != 'true') {
      $download = 'false';
  }
  
  if ($print != 'true') {
      $print = 'false';
  }
  $final_url = $viewer_base_url."?file=".$file_name."&download=".$download."&print=".$print;
  $fullscreen_link = '';
  if($fullscreen == 'true'){
       $fullscreen_link = '<a target="_blank" href="'.$final_url.'">View Fullscreen</a><br>';
  }
  $iframe_code = '<iframe width="'.$viewer_width.';" height="'.$viewer_height.';" src="'.$final_url.'"></iframe> ';
  
  return $fullscreen_link.$iframe_code;
}
?>
