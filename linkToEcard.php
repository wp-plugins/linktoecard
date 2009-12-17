<?php
/*
Plugin Name: LinkToEcard
Plugin URI: http://LinkToEcard.com/wordpress-plugin
Description: Mazgalici
Version: 1.1.1
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
		//print_r($_POST['categs']);
		update_option('linkToEcardTextLink',trim($_POST['textLink']));
		update_option('linkToEcardTextEmail',trim($_POST['textEmail']));
		update_option('linkToEcardLang',trim($_POST['lang']));
		
		update_option('linkToEcardCategories',@implode(',',$_POST['categs']));

	}

	if (isset($_POST['reset'])){

		update_option('linkToEcardTextLink',$linkToEcardDefaultText);
		update_option('linkToEcardTextEmail',$linkToEcardTextEmail);
		update_option('linkToEcardLang',$linkToEcardLang);
		update_option('linkToEcardCategories',$linkToEcardCategories);

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

	$categories = get_categories();

	$out.='<tr><td>Enable plugin for</td>';
	$out.='<td><select name="categs[]" size="10" style="height:100px" multiple="multiple">';
	$selectedCategs=explode(',',get_option('linkToEcardCategories'));
	
	$selected='';
	//print_r($selectedCategs);
			if (in_array('0',$selectedCategs)){
			$selected=' selected="selected" ';
		}
	$out.='<option '.$selected.' value="0">All categs</option>';

	

	for ($i=0;$i<sizeof($categories);$i++){
		if (strlen($categories[$i]->cat_ID)<1) continue;
		$selected='';

		if (in_array($categories[$i]->cat_ID,$selectedCategs)){
			$selected=' selected="selected" ';
		}

		$out.='<option '.$selected.' value="'.$categories[$i]->cat_ID.'">'.$categories[$i]->cat_name.'</option>';
	}
	$out.='</select></td></tr>';
	//print_r($categories);

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


	$linkToEcardDefaultText='<center><table><tr><td valign="middle"><img src="http://messengerinvisible.com/ecard.gif" border="0"></td><td valign="middle">Send this picture as an ecard</td></table></center>';
	$linkToEcardTextEmail='Check this site [url]!';
	$linkToEcardLang='en';
	$linkToEcardCategories='0,';

	add_option('linkToEcardTextLink',$linkToEcardDefaultText);
	add_option('linkToEcardTextEmail',$linkToEcardTextEmail);
	add_option('linkToEcardLang',$linkToEcardLang);
	add_option('linkToEcardCategories',$linkToEcardCategories);

}

function linkToEcardTheContent($content){

	global $post;

	$categories=get_the_category();
	$enabledCategories=explode(',',get_option('linkToEcardCategories'));
	//print_r($enabledCategories);

	//echo "dsdsdsds".print_r($enabledCategories);
	if (in_array('0',$enabledCategories)){
		$enabled=true;
	}
	else {
		$enabled=false;
	}

	if (!$enabled){
		foreach ($categories as $categ){
			if (in_array($categ->cat_ID,$enabledCategories)){
				$enabled=true;
				break;
			}
		}
	}

	if ($enabled){
		$content=preg_replace('|(<img.+?src="([^ ]+)".+?>)|','$0</a><br><a target="_blank" href="http://linkToEcard.com/?u=$2&l='.get_option('linkToEcardLang').'&t='.urlencode(get_option('linkToEcardTextEmail')).'" style="text-decoration:none;border:none">'.stripslashes(get_option('linkToEcardTextLink')).'</a>',$content);
	}

	return $content;
}



?>