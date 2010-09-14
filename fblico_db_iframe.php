<?php

	$server_root = $_SERVER['DOCUMENT_ROOT'];
	$os_test = substr($server_root, -1);
	if($os_test != '/'){
		$server_root = $server_root.'/';
	}
	// Requires
	require_once($server_root . 'wp-load.php');
	require_once($server_root . 'wp-includes/post.php');
	require_once($server_root . 'wp-includes/functions.php');
	require_once($server_root . 'wp-includes/link-template.php');
	require_once($server_root . 'wp-includes/pluggable.php');

	global $user_level;
    get_currentuserinfo();

	if(!empty($user_level)){
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="de-DE">
<head>
<style type="text/css">
<!--
body{font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;font-size:13px;}
-->
</style>
</head>
<body>
<?php
	global $current_user;
	get_currentuserinfo();	

	$curr_user = $current_user->ID;
	$curr_user_level = $current_user->user_level;
	
	$posts = get_posts('showposts=-1&post_type=any');
	if ($posts) {
		foreach($posts as $post) {
			$post_ids[] = $post->ID;
			$permalinks[] = '"'.get_permalink( $post->ID ).'"';
		}
	}
	
	foreach($post_ids as $post) {
		
		$post_like_count = get_post_meta($post, 'like_count', 'true'); //Number of likes of the post
		
		$post_data = get_post($post);
		$post_author = $post_data->post_author; //Author of the post
		
		if($post_author==$curr_user){
			$likecount += $post_like_count;
			$user_has_posts = 1;
		}
	}
	
	if(get_option("fblico_spent".$curr_user)){
			$likecount = $likecount - get_option("fblico_spent".$curr_user);
	}
		
	if($_POST['field_likecount']){ //if "update data" form got submitted
		
		$field_likecount	=	trim($_POST['field_likecount'],',');
		
		$arr_like_count = explode(',',$field_likecount);
		$arr_post_ids = explode(',',$_POST["field_post_ids"]);
		
		$i = 0;
		
		foreach($arr_like_count as $likecount) {
			
			add_post_meta($arr_post_ids[$i], 'like_count', $likecount, 'true') or
				update_post_meta($arr_post_ids[$i], 'like_count', $likecount);
			
			$i++;
		}
	}
?>

	<div id="fb-root"></div>

<script type="text/javascript">
	window.fbAsyncInit = function() {
		FB.init(
			{
				appId: '<?php echo get_option("fblico_appid");?>',
				status: true,
				cookie: true,
				xfbml: true
			}
		);
		
		showStats();
		document.getElementById('fbcount').style.display = 'block';
	};

	(function() {
		var e = document.createElement('script'); e.async = false;
		e.src = document.location.protocol +
		'//connect.facebook.net/en_US/all.js';
		document.getElementById('fb-root').appendChild(e);
	}());

function getStats() {
	var likes = new Array();
	FB.api(
		{
			method: 'fql.query',
			query: 'SELECT total_count FROM link_stat WHERE url IN (<?php echo implode(',',$permalinks);?>)'
		},
		function(response) {

			var count_form = '';
		
			if (response.length == 0 || typeof response.length === 'undefined') {
				count_form = '<h4 style="height:18px;"><?php _e('No likes','fblico');?></h4>';
			}
		
			var like_data = '';
		
			for (i=0;i<response.length;i++) {
				// Put likes into data var
				like_data	+= response[i].total_count + ',';
			}
	
			var count_form = '<form action="" method="post" name="update_form" id="update_form">' +
			'<input type="hidden" value="<?php echo implode(',',$post_ids);?>" name="field_post_ids"/>' +
			'<input type="hidden" value="' + like_data + '" name="field_likecount"/>' +
			'<p class="submit" style="margin:0;padding:0;">' +
			'<input type="submit" value="<?php _e('update data','fblico');?>" style="background:url(/wp-admin/images/white-grad-active.png) repeat-x scroll left top #EEEEEE;border-color: #BBBBBB;text-shadow:0 1px 0 #FFFFFF;color: #464646;-moz-border-radius:11px;-moz-box-sizing:content-box;-webkit-box-sizing: content-box;border-radius: 11px 11px;border-style: solid;border-width: 1px;cursor: pointer;font-size: 11px !important;line-height: 13px;padding: 3px 8px;text-decoration: none;"/>' +
			'<p>' +
			'</form>';
			
			document.getElementById('fbcount').innerHTML = count_form;

		}
	);
}

function showStats() {
	document.getElementById('fbcount').innerHTML = '<img src="http://static.ak.fbcdn.net/rsrc.php/z5R48/hash/ejut8v2y.gif" title="<?php _e('Loading...','fbcomcon');?>" alt="<?php _e('Loading...','fblico');?>" style="width:32px; height:32px;margin:10px;">';
	getStats();
}
</script>

<table style="font-size:13px;width:195px;">
	<tr>
		<th style="text-align:left;width:100px;padding:6px 0;"><?php _e('Likes','fblico');?></th>
		<td><?php echo $likecount;?></td>
	</tr>
	<?php
		if(get_option("fblico_spent".$curr_user)!='' xor get_option("fblico_spent".$curr_user)=='0'){
	?>
	<tr>
		<th style="text-align:left;width:100px;padding:6px 0;"><?php _e('Spent','fblico');?></th>
		<td><?php echo get_option("fblico_spent".$curr_user);?></td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="2"><div id="fbcount" style="display:none;"></div></td>
	</tr>
</table>
</body>
</html>
<?php } ?>