<?php
	include_once(ABSPATH . WPINC . '/pluggable.php');
	global $current_user;
	get_currentuserinfo();	

	$curr_user = $current_user->ID;
	
	//get FB uid from cookie
	$cookie_name = 'fbs_'.get_option('fblico_appid');
	$cookie_content = $_COOKIE[$cookie_name];
	
	$posts = get_posts('showposts=-1&post_type=any');
	if ($posts) {
		foreach($posts as $post) {
			$post_ids[] = $post->ID;
			$permalinks[] = '"'.get_permalink( $post->ID ).'"';
		}
	}
		
	//if "update data" form got submitted
	if($_POST['field_post_ids']){ 
		
		$field_likecount	=	trim($_POST['field_likecount'],',');
		
		$arr_like_count = explode(',',$field_likecount);
		$arr_post_ids = explode(',',$_POST["field_post_ids"]);
		
		$i = 0;
		
		foreach($arr_like_count as $likecount_upd) {
			
			add_post_meta($arr_post_ids[$i], 'like_count', $likecount_upd, 'true') or
				update_post_meta($arr_post_ids[$i], 'like_count', $likecount_upd);
			
			$i++;
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
?>

<table style="font-size:13px;">
	<tr>
		<th style="text-align:left;width:150px;padding:6px 0;"><?php _e('Likes','fblico');?></th>
		<td><?php echo $likecount;?></td>
	</tr>
	<?php
		if(get_option("fblico_spent".$curr_user)!=''){
	?>
	<tr>
		<th style="text-align:left;width:150px;padding:6px 0;"><?php _e('Spent','fblico');?></th>
		<td><?php echo get_option("fblico_spent".$curr_user);?></td>
	</tr>
	<?php } 
		if($cookie_content != ''){
	?>
	<tr>
		<td colspan="2" style="text-align:left;padding:6px 0;">
			<div id="fbcount" style="display:none;"></div>
		</td>
	</tr>
	<?php } ?>
	
</table>
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
		var e = document.createElement('script'); e.async = true;
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
				'<input type="submit" value="<?php _e('update data','fblico');?>" class="button-primary"/>' +
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