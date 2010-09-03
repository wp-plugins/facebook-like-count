<?php
	include_once(ABSPATH . WPINC . '/pluggable.php');
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
?>

<table style="font-size:13px;">
	<tr>
		<th style="text-align:left;width:150px;padding:6px 0;"><?php _e('Likes','fblico');?></th>
		<td><?php echo $likecount;?></td>
	</tr>
	<?php
		if(get_option("fblico_spent".$curr_user)!='' xor get_option("fblico_spent".$curr_user)=='0'){
	?>
	<tr>
		<th style="text-align:left;width:150px;padding:6px 0;"><?php _e('Spent','fblico');?></th>
		<td><?php echo get_option("fblico_spent".$curr_user);?></td>
	</tr>
	<?php } 
		if($curr_user_level==10){
	?>
	<tr>
		<th style="text-align:left;width:150px;padding:6px 0;"><?php _e('Spent','fblico');?></th>
		<td><?php echo get_option("fblico_spent".$curr_user);?></td>
	</tr>
	<?php } ?>
</table>