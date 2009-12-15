<?php
/*
Plugin Name: LinkToEcard
Plugin URI: http://LinkToEcard.com/wordpress-plugin
Description: Mazgalici
Version: 1.02
Author: Mazgalici
*/


add_filter('the_content', 'linkToEcardTheContent');
register_activation_hook(__FILE__,'linkToEcardInstall');
add_action('admin_menu', 'linkToEcardSettings');



function linkToEcardSettings(){
	add_submenu_page('options-general.php','Link To Ecard', 'Link To Ecard', 10, __FILE__,'linkToEcardAdmin');
}


function linkToEcardAdmin(){
	include('_config.php');


	if (isset($_POST['sent'])){

		update_option('linkToEcardTextLink',trim($_POST['textLink']));
		update_option('linkToEcardTextEmail',trim($_POST['textEmail']));
		update_option('linkToEcardLang',trim($_POST['lang']));

	}

	if (isset($_POST['reset'])){

		update_option('linkToEcardTextLink',$linkToEcardDefaultText);
		update_option('linkToEcardTextEmail',$linkToEcardTextEmail);
		update_option('linkToEcardLang',$linkToEcardLang);

	}


	$out='<div class="wrap">
	<h2>linkToEcard settings</h2>
</div>';
	$out.='<br><br><form action="" method="POST">';
	$out.='<table>';
	$out.='<tr><td>Text on your blog</td>';
	$out.='<td><textarea name="textLink" style="width:450px">'.stripslashes(get_option('linkToEcardTextLink')).'</textarea></td></tr>';
	$out.='<tr><td>Text on the emails</td>';
	$out.='<td><textarea name="textEmail" style="width:450px">'.stripslashes(get_option('linkToEcardTextEmail')).'</textarea></td></tr>';
	$out.='<tr><td>Ecards form language</td>';
	$out.='<td><select name="lang">';

	foreach ($linkToEcardLanguages as $linkToEcardKey=>$linkToEcardVal){
		$outLang.='<option value="'.$linkToEcardVal.'" ';
		if (get_option('linkToEcardLang')==$linkToEcardVal){
			$outLang.=' selected ';
		}
		$outLang.='>'.$linkToEcardKey.'</option>';
	}

	$out.=$outLang;
	$out.='</select></td></tr>';
	$out.="<tr><td></td><td align='right'><input type='submit' value='Update'></td>";
	$out.='</table>';
	$out.='<input type="hidden" name="sent" value="1"> </form>';

	$out.='<br><form action="" method="POST"><input type="submit" value="Reset to default values"><input type="hidden" name="reset" value="1"></form>';
	echo $out;

}

function linkToEcardInstall(){
	include('_config.php');
	global $linkToEcardDefaultText,$linkToEcardTextEmail,$linkToEcardLang;

	add_option('linkToEcardTextLink',$linkToEcardDefaultText);
	add_option('linkToEcardTextEmail',$linkToEcardTextEmail);
	add_option('linkToEcardLang',$linkToEcardLang);
	

}

function linkToEcardTheContent($post){

	$post=preg_replace('|(<img.+?src="([^ ]+)".+?>)|','$0</a><br><a target="_blank" href="http://linkToEcard.com/?u=$2&l='.get_option('linkToEcardLang').'&t='.urlencode(get_option('linkToEcardTextEmail')).'" style="text-decoration:none;border:none">'.stripslashes(get_option('linkToEcardTextLink')).'</a>',$post);


	return $post;
}



?>