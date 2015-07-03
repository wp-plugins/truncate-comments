<?php
/*
Plugin Name: Truncate Comments
Plugin URI: https://wordpress.org/plugins/truncate-comments/
Description: The plugin uses Javascript to hide long comments (Amazon-style comments).
Version: 1.01
Author: Flector
Author URI: https://profiles.wordpress.org/flector#content-plugins
*/ 

function tc_init() {
    $tc_options = array(); tc_setup();
    $tc_options['cutby'] = "words";
    $tc_options['length'] = "40";
    $tc_options['ellipsis'] = "…";
    $tc_options['showText'] = __('Read more','truncate-comments');
    $tc_options['speed'] = "1500";
    $tc_options['dont_load_jquery'] = "0";
    $tc_options['only_single'] = "1";
    add_option('tc_options', $tc_options);
}
add_action('activate_truncate-comments/truncate-comments.php', 'tc_init');

function tc_setup(){
    load_plugin_textdomain('truncate-comments', null, dirname(plugin_basename(__FILE__)) . '/lang');
}
add_action('init', 'tc_setup');

function tc_actions($links) {
	return array_merge(array('settings' => '<a href="options-general.php?page=truncate-comments.php">' . __('Settings', 'truncate-comments') . '</a>'), $links);
}
add_filter('plugin_action_links_' . plugin_basename( __FILE__ ),'tc_actions');

function tc_add_jquery() {
    $tc_options = get_option('tc_options');
    if(!wp_script_is('jquery') & $tc_options['dont_load_jquery'] != '1'){wp_enqueue_script('jquery');}
}
add_action('wp_enqueue_scripts', 'tc_add_jquery');

function tc_add_collapser_script() {
    $tc_options = get_option('tc_options');
    $purl = plugins_url();
    if ($tc_options['only_single'] == '1' & is_single()) {
        wp_register_script('jcollapser', $purl.'/truncate-comments/inc/jquery.collapser.min.js');  
        wp_enqueue_script('jcollapser');
    }
    if ($tc_options['only_single'] != '1') {
        wp_register_script('jcollapser', $purl.'/truncate-comments/inc/jquery.collapser.min.js');  
        wp_enqueue_script('jcollapser');
    }
}
add_action('wp_enqueue_scripts', 'tc_add_collapser_script');

function tc_collapser_comment($content) {
    return "<div class=\"tc-collapser-comment\">". $content ."</div>";
}
add_filter('comment_text', 'tc_collapser_comment', 999);

function tc_print_script() { 
$tc_options = get_option('tc_options'); ?>
<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery('.tc-collapser-comment').collapser({
        mode: '<?php echo $tc_options['cutby']; ?>',
        truncate: <?php echo $tc_options['length']; ?>,
        ellipsis: '<?php echo $tc_options['ellipsis']; ?>',
        showText: '<?php echo $tc_options['showText']; ?>',
        speed: <?php echo $tc_options['speed']; ?>,
        controlBtn: 'commentMoreLink',
        lockHide: true
    });
});     
</script>
<?php }

function tc_collapser() {
    $tc_options = get_option('tc_options');
    if ($tc_options['only_single'] == '1' & is_single()) {tc_print_script();}
    if ($tc_options['only_single'] != '1') {tc_print_script();}
}
add_action('wp_footer', 'tc_collapser');

function tc_options_page() {

if (isset($_POST['submit'])) {
    $tc_options['cutby'] = htmlspecialchars($_POST['cutby']);
    $tc_options['length'] = htmlspecialchars($_POST['length']);
    $tc_options['ellipsis'] = htmlspecialchars($_POST['ellipsis']);
    $tc_options['showText'] = htmlspecialchars($_POST['showText']);
    $tc_options['speed'] = htmlspecialchars($_POST['speed']);
    $tc_options['dont_load_jquery'] = htmlspecialchars($_POST['dont_load_jquery']);
    $tc_options['only_single'] = htmlspecialchars($_POST['only_single']);
    update_option('tc_options', $tc_options);
}
$tc_options = get_option('tc_options');
?>
<?php if (!empty($_POST)) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.', "truncate-comments") ?></strong></p></div>
<?php endif; ?>
<div class="wrap">
<h2><?php _e('Truncate Comments Settings', 'truncate-comments'); ?></h2>

<div class="metabox-holder" id="poststuff">
<div class="meta-box-sortables">
<script>
jQuery(document).ready(function($) {
	$('.postbox').children('.handlediv').click(function(){ $(this).siblings('.inside').toggle();});
});
</script>
<div class="postbox">

   <div title="<?php _e("Click to open/close", "truncate-comments"); ?>" class="handlediv">
      <br>
    </div>
    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span><?php _e("Do you like this plugin ?", "truncate-comments"); ?></span></h3>
    <div class="inside" style="display: block;">
        <img src="<?php echo WP_PLUGIN_URL. '/truncate-comments/img/icon_coffee.png'; ?>" title="<?php _e("buy me a coffee", "truncate-comments"); ?>" style=" margin: 5px; float:left;" />
		
        <p><?php _e("Hi! I'm <strong>Flector</strong>, developer of this plugin.", "truncate-comments"); ?></p>
        <p><?php _e("I've been spending many hours to develop this plugin.", "truncate-comments"); ?> <br />
		<?php _e("If you like and use this plugin, you can <strong>buy me a cup of coffee</strong>.", "truncate-comments"); ?></p>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
            <input type="image" src="<?php echo WP_PLUGIN_URL. '/truncate-comments/img/donate.gif'; ?>" border="0" name="submit" title="<?php _e("Donate with PayPal", "truncate-comments"); ?>">
        </form>
        <div style="clear:both;"></div>
    </div>
</div>

<form action="" method="post">


<div class="postbox">

    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span><?php _e("General Options", "truncate-comments"); ?></span></h3>
    <div class="inside" style="display: block;">

        <table class="form-table">
        
            <tr>
                <th><?php _e("Collapse comments:", "truncate-comments") ?></th>
                <td>
                    <select name="cutby">
                        <option value="chars" <?php if ($tc_options['cutby'] == 'chars') echo "selected='selected'" ?>><?php _e("Characters", "truncate-comments"); ?></option>
                        <option value="words" <?php if ($tc_options['cutby'] == 'words') echo "selected='selected'" ?>><?php _e("Words", "truncate-comments"); ?></option>
                        <option value="lines" <?php if ($tc_options['cutby'] == 'lines') echo "selected='selected'" ?>><?php _e("Lines", "truncate-comments"); ?></option>
                    </select>
<small>
<ul style="margin-bottom:0px;">
<li><?php _e("<strong>Characters</strong>: to truncate characters.", "truncate-comments"); ?></li>
<li><?php _e("<strong>Words</strong>: to truncate words.", "truncate-comments"); ?></li>
<li style="margin-bottom:0px;"><?php _e("<strong>Lines</strong>: to truncate lines.", "truncate-comments"); ?></li>
</ul>
</small>
                </td>
            </tr>
        
            <tr>
                <th><?php _e("Comment length:", "truncate-comments") ?></th>
                <td>
                    <input type="text" name="length" size="3" value="<?php echo stripslashes($tc_options['length']); ?>" /><br /><small><?php _e("Length in characters, words or lines.", "truncate-comments"); ?> </small>
                </td>
            </tr>
            
            <tr>
                <th><?php _e("Ellipsis:", "truncate-comments") ?></th>
                <td>
                    <input type="text" name="ellipsis" size="20" value="<?php echo stripslashes($tc_options['ellipsis']); ?>" />  <br /><small><?php _e("The text displayed next to the hidden comment to indicate the presence of more content.", "truncate-comments"); ?> </small>
                </td>
            </tr>
            
            <tr>
                <th><?php _e("\"Read more\" text:", "truncate-comments") ?></th>
                <td>
                    <input type="text" name="showText" size="20" value="<?php echo stripslashes($tc_options['showText']); ?>" />  <br /><small><?php _e("The link that expands a collapsed comment.", "truncate-comments"); ?></small>
                </td>
            </tr>
           
           <tr>
                <th><?php _e("Speed:", "truncate-comments") ?></th>
                <td>
                    <input type="text" name="speed" size="3" value="<?php echo stripslashes($tc_options['speed']); ?>" /><br /><small><?php _e("The speed (duration) of a comment's vertical collapse (in milliseconds).", "truncate-comments"); ?></small>
                </td>
            </tr>
           

            <tr>
                <th></th>
                <td>
                    <input type="submit" name="submit" class="button button-primary" value="<?php _e('Update options &raquo;', "truncate-comments"); ?>" />
                </td>
            </tr>
            
            
        </table>

    </div>
</div>

<div class="postbox">

    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span><?php _e('Advanced Options', 'truncate-comments'); ?></span></h3>
	  <div class="inside" style="display: block;">
      
         <table class="form-table">   
         

          <tr>
                <td><input type="checkbox" value="1" <?php if ($tc_options['dont_load_jquery'] == '1') echo "checked='checked'"; ?> name="dont_load_jquery"  id="dont_load_jquery" /> <label for="dont_load_jquery"><?php _e("Don't load jQuery", "truncate-comments"); ?></label><br /><small><?php _e("Don't load jQuery if it's included in your theme.", "truncate-comments"); ?> </small></td>
            </tr>
            
          <tr>
                <td><input type="checkbox" value="1" <?php if ($tc_options['only_single'] == '1') echo "checked='checked'"; ?> name="only_single" id="only_single" /> <label for="only_single"><?php _e("Load the plugin script on single pages only", "truncate-comments"); ?></label><br /><small><?php _e("Load the plugin script only on single pages (to avoid loading the script on pages with no comments on them).", "truncate-comments"); ?></small></td>
            </tr>  
          <tr>
                <td>
                    <input type="submit" name="submit" class="button button-primary" value="<?php _e('Update options &raquo;', "truncate-comments"); ?>" />
                </td>
            </tr>  
            
        </table>
    
    </div>
</div>


<div class="postbox">

    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span><?php _e('About', 'truncate-comments'); ?></span></h3>
	  <div class="inside" style="display: block;"><p>
	 
	  <?php _e('The <strong>Truncate Comments</strong> plugin uses <a target="_blank" href="http://www.aakashweb.com/jquery-plugins/collapser/">jQuery Collapser</a> by Aakash Chakravarthy.', 'truncate-comments'); ?>
    </p>
    
    </div>
</div>
</form>
</div>
</div>
<?php 
}

function tc_menu() {
	add_options_page('Truncate Comments', 'Truncate Comments', 'manage_options', 'truncate-comments.php', 'tc_options_page');
}
add_action('admin_menu', 'tc_menu');


?>