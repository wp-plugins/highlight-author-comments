<?php
/*
Plugin Name: Highlight Author Comments
Plugin URI: http://rmarsh.com/plugins/highlight-comments/
Description: Automatically applies a distinctive style to comments by the post's author.
Author: Rob Marsh, SJ
Version: 1.0.0 
Author URI: http://rmarsh.com/
*/ 

/*
Copyright 2007  Rob Marsh, SJ  (http://rmarsh.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

function hac_highlight_comment($content){
	global $comment;
	if ($comment->comment_author_email !== get_the_author_email()) 
		return $content;
	else {
		$options = get_option('hac_highlight_author_comments');
		return '<div style="'.$options['highlight_style'].'">'.$content.'</div>';
	}	
}

add_filter('comment_text', 'hac_highlight_comment');

function hac_option_menu() {
	if (function_exists('current_user_can')) {
		if (!current_user_can('manage_options')) return;
	} else {
		global $user_level;
		get_currentuserinfo();
		if ($user_level < 8) return;
	}
	if (function_exists('add_options_page')) {
		add_options_page(__('Highlight Comments'), __('Highlight Comments'), 1, __FILE__, 'hac_options_page');
	}
}

// Install the options page
add_action('admin_menu', 'hac_option_menu');

// Prepare the default set of options
$default_options['highlight_style'] = 'padding: 1em';
add_option('hac_highlight_author_comments', $default_options);

function hac_options_page(){
	global $wpdb;
	if (isset($_POST['update_options'])) {
		$options['highlight_style'] = trim($_POST['highlight_style'],'{}');
		update_option('hac_highlight_author_comments', $options);
		// Show a message to say we've done something
		echo '<div class="updated"><p>' . __('Options saved') . '</p></div>';
	} else {
		// If we are just displaying the page we first load up the options array
		$options = get_option('hac_highlight_author_comments');
	}
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php echo __('Highlight Author Comments Options'); ?></h2>
		<form method="post" action="">
		<fieldset class="options">
		<table class="optiontable">
			<tr valign="top">
				<th scope="row"><?php _e('CSS Styles for Author Comments:') ?></th>
				<td><input name="highlight_style" type="text" id="highlight_style" value="<?php echo $options['highlight_style']; ?>" size="60" /></td>
			</tr>
		</table>
		</fieldset>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Update') ?>"  style="font-weight:bold;" /></div>
		</form>    		
	</div>
	<?php	
}

?>