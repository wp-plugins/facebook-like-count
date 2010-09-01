<?php
	$curr_user = get_current_user_id();
	
	
	$posts = get_posts('showposts=-1&post_type=any');
	if ($posts) {
		$cat_post_ids = array();
		foreach($posts as $post) {
			$post_ids[] = $post->ID;
			$permalinks[] = '"'.get_permalink( $post->ID ).'"';
		}
	}

	$likes_array = array();
	
	foreach($post_ids as $post) {
		
		$post_like_count = get_post_meta($post, 'like_count', 'true'); //Number of likes of the post
		
		$post_data = get_post($post);
		$post_author = $post_data->post_author; //Author of the post
		
		if($post_author==$curr_user){
			$likecount += $post_like_count;
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
	<tr>
		<th style="text-align:left;width:150px;padding:6px 0;"><?php _e('Spent','fblico');?></th>
		<td><?php echo get_option("fblico_spent".$curr_user);?></td>
	</tr>
</table>