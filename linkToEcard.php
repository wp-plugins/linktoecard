<?php
/*
Plugin Name: Link to ecard
Plugin URI: http://clickTocard.com/wordpress-plugin
Description: Every image from you blog can be sent as an ecard
Version: 1.4
Author: Mazgalici
*/


add_action('the_content', 'linkToEcardTheContent',97);
add_action('the_content', 'linkToEcardTheContentPre',3);

register_activation_hook(__FILE__,'linkToEcardInstall');
add_action('admin_menu', 'linkToEcardSettings');



function linkToEcardSettings(){
	add_submenu_page('options-general.php','Link To Ecard', 'Link To Ecard', 10, __FILE__,'linkToEcardAdmin');
}


function linkToEcardAdmin(){
	include('_config.php');


	if (isset($_POST['sent'])){
		//print_r($_POST['onlyOnInteriorPages']);

		update_option('linkToEcardTextLink',trim($_POST['textLink']));
		update_option('linkToEcardTextEmail',trim($_POST['textEmail']));
		update_option('linkToEcardLang',trim($_POST['lang']));
		update_option('linkToEcardCategories',@implode(',',$_POST['categs']));
		update_option('linkToEcardTags',strtolower(trim(str_replace('  ',' ',$_POST['tags']))));
		update_option('linkToEcardPosts',strtolower(trim(str_replace('  ',' ',$_POST['posts']))));

		if ($_POST['onlyOnInteriorPages']=='on'){
			$_POST['onlyOnInteriorPages']=1;
		}
		else {
			$_POST['onlyOnInteriorPages']=0;
		}

		
		update_option('linkToEcardOnlyOnInteriorPages',$_POST['onlyOnInteriorPages']);

	}



	$out='<div class="wrap"><h2>Link to ecard settings</h2></div>';
	
	$out.='<br><form action="" method="POST">';
	$out.='<table>';
	$out.='<tr><td>HTML on your blog</td>';
	$out.='<td><textarea name="textLink" style="width:450px">'.stripslashes(get_option('linkToEcardTextLink')).'</textarea></td></tr>';
	$out.='<tr><td>HTML on the emails sent <br>
		<i><small>A good place to advertise your site</small></i>
	</td>';
	$out.='<td><textarea name="textEmail" style="width:450px">'.stripslashes(get_option('linkToEcardTextEmail')).'</textarea></td></tr>';

	$categories = get_categories(array('hide_empty'=>'false'));

	$out.='<tr><td>Enable plugin for this categories<br>
		<i><small>CTRL+Click for multiple selection</small></i>
	</td>';
	$out.='<td valign="middle"><select name="categs[]" size="10" style="height:100px" multiple="multiple">';
	$selectedCategs=explode(',',get_option('linkToEcardCategories'));

	$selected='';
	if (in_array('0',$selectedCategs)){
		$selected='selected';
	}
	$out.='<option '.$selected.' value="0">All categs</option>';



	for ($i=0;$i<sizeof($categories);$i++){
		if (strlen($categories[$i]->cat_ID)<1){
			continue;
		}
		$selected='';

		if (in_array($categories[$i]->cat_ID,$selectedCategs)){
			$selected='selected';
		}

		$out.='<option '.$selected.' value="'.$categories[$i]->cat_ID.'">'.$categories[$i]->cat_name.'</option>';
	}
	$out.='</select> <i><small>Categories with 0 posts aren\'t displayed here</small></i></td></tr>';
	//print_r($categories);

	$out.='<tr><td> Enable plugin for this tags <br>
	<i><small>Ex: tag1,tag2,tag3</small></i>
	</td><td><input type="text" name="tags" value="'.get_option('linkToEcardTags').'" style="width:450px" ></td></tr>';

	$out.='<tr><td> Enable plugin for this posts ids<br>
	<i><small>Ex: 11,23,45</small></i>
	</td><td><input type="text" name="posts" value="'.get_option('linkToEcardPosts').'" style="width:450px" ></td></tr>';


	$out.='<tr><td>Enable only for single post pages <br>

	
	</td><td><input type="checkbox" name="onlyOnInteriorPages" ';
	if (get_option('linkToEcardOnlyOnInteriorPages')==1){
		$out.=' checked ';
	}
	$out.='> </td></tr>';

	$out.='<tr><td>Ecards form language <br>
		<i><small>More languages on request<small></i>
	</td>';
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


	$out.="<tr><td  colspan='2' align='right'><input type='submit' value='  Update  '></td>";
	$out.='</table>';
	$out.='<input type="hidden" name="sent" value="1"> </form>';

	$out.='<br><small><i>Contact for bugs and curses <a href="mailto:fcmazgalici@yahoo.com">fcmazgalici@yahoo.com</a></small></i>';
	echo $out;

}

function linkToEcardInstall(){


	$linkToEcardDefaultText='<center><table><tr><td valign="middle"><img src="http://messengerinvisible.com/ecard.gif" border="0"></td><td valign="middle">Send this picture as an ecard</td></table></center>';
	$linkToEcardTextEmail='Check this site [url]!';
	$linkToEcardLang='en';
	$linkToEcardCategories=0;
	$linkToEcardTags='';
	$linkToEcardPosts='';
	$linkToEcardOnlyOnInteriorPages='0';

	add_option('linkToEcardTextLink',$linkToEcardDefaultText);
	add_option('linkToEcardTextEmail',$linkToEcardTextEmail);
	add_option('linkToEcardLang',$linkToEcardLang);
	add_option('linkToEcardCategories',$linkToEcardCategories);
	add_option('linkToEcardTags',$linkToEcardTags);
	add_option('linkToEcardPosts',$linkToEcardPosts);
	add_option('linkToEcardOnlyOnInteriorPages',$linkToEcardOnlyOnInteriorPages);

}

function linkToEcardTheContentPre($content){
	global $post;
	$content=preg_replace('|<img|','<!-- --><img',$content);
	return  $content;
}


function linkToEcardTheContent($content){
	global $post;

//echo is_single();
	if (get_option('linkToEcardOnlyOnInteriorPages')==1 && is_single()!=1){
		return $content;
	}
	
	$categories=get_the_category();
	$tags=get_the_tags();
	$enabledCategories=explode(',',get_option('linkToEcardCategories'));



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

	if (!$enabled && sizeof($tags)>1){
		$linkToEcardTags=explode(',',get_option('linkToEcardTags'));
		if (sizeof($linkToEcardTags)>0){
			for ($i=0;$i<sizeof($linkToEcardTags);$i++){
				foreach ($tags as $tag){
					if ($linkToEcardTags[$i]==strtolower(trim($tag->name))){
						$enabled=true;
						break;
					}
				}

			}
		}
	}

	if (!$enabled){
		$linkToEcardPosts=explode(',',get_option('linkToEcardPosts'));
		for ($i=0;$i<sizeof($linkToEcardPosts);$i++){
			if ($linkToEcardPosts[$i]==$post->ID){
				$enabled=true;
				break;
			}
		}
	}


	if ($enabled){
		$content=preg_replace('|(<!-- --><img.+?src="([^"]+)".+?>)|','$0</a><br><a target="_blank" href="http://clickToEcard.com/send/?u=$2&l='.get_option('linkToEcardLang').'&t='.urlencode(get_option('linkToEcardTextEmail')).'&f=w" style="text-decoration:none;border:none">'.stripslashes(get_option('linkToEcardTextLink')).'</a>',$content);
	}

	return $content;
}


?>