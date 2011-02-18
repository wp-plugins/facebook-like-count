<?php
	function fblico_admin() { //Checks if user is allowed to change options

		if (!current_user_can('manage_options'))  { //Errormessage if he is not
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
?>
<div class="wrap">
	<h2><?php _e('Facebook Like Count for Wordpress - Admin','fblico');?></h2>
	
	<form action="options.php" method="post">
		<?php wp_nonce_field('update-options');?>
		<div id="poststuff" class="postbox">
			<h3 style="cursor:default"><?php _e('App ID','fbcomcon');?></h3>
			<div class="inside">
				<table class="form-table" style="width:auto;">
					<tr>
						<td><?php _e('Facebook App ID','fblico');?></td>
						<td><input type="text" value="<?php echo get_option("fblico_appid");?>" name="fblico_appid" /></td>
						<td>
							<input type="hidden" name="action" value="update" />
							<input type="hidden" name="page_options" value="fblico_appid" />
							<p class="submit">
								<input type="submit" value="<?php _e('save','fblico');?>" class="button-secondary"/>
							</p>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</form>

	<?php
		if (get_option("fblico_appid")){
		
		
		$posts = get_posts('showposts=-1&post_type=any');
		if ($posts) {
			$cat_post_ids = array();
			foreach($posts as $post) {
				$post_ids[] = $post->ID;
				$permalinks[] = '"'.get_permalink( $post->ID ).'"';
			}
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

	<div id="update_form_wrap" style="margin:0;padding:0;">
		<a id="login" href="#" style="display:none;" onClick="login()">
			<?php _e('Connect with Facebook','fblico');?>
		</a>
		<div id="fbcount" style="display:none;"></div>
	</div>

	<div id="poststuff" class="postbox">
		<h3 style="cursor:default"><?php _e('Like Chart','fbcomcon');echo ' - ';_e('Authors','fbcomcon');?></h3>
		<div class="inside">
			<form action="options.php" method="post">
				<?php wp_nonce_field('update-options');?>
		
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

						FB.getLoginStatus(
							function(response) {
								if (response.session) {
									showStats();
									document.getElementById('fbcount').style.display = 'block';
								}
								else {
									document.getElementById('login').style.display = 'block';
								}
							}
						); 
					   
					};
					
					function login() {
						FB.login(
							function(response){
								window.location.reload();
							}
						);
					}	
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
				
				<table class="form-table" style="width:auto;">
					<tr valign="top">
						<th style="font-weight:bold;"><?php _e('Author Name','fblico');?></th>
						<th style="font-weight:bold;"><?php _e('Likes','fblico');?></th>
						<th style="font-weight:bold;"><?php _e('Spent','fblico');?></th>
					</tr>
					<?php

						$likes_array = array();
						
						foreach($post_ids as $post) {
							
							$post_like_count = get_post_meta($post, 'like_count', 'true'); //Number of likes of the post
							
							$post_data = get_post($post);
							$post_author = $post_data->post_author; //Author of the post
							
							$author_is_there = 0;
							foreach($likes_array as $key => $row){ //Tests if author is already in the array
								if($key == $post_author) {
									$author_is_there = 1;
								}
							}
							
							if($author_is_there == 0){
								$likes_array[$post_author] = $post_like_count;
							}
							else{
								$likes_array[$post_author] += $post_like_count;
							}
						}
						
						foreach($likes_array as $userID => $likecount){
							if(get_option("fblico_spent".$userID)){
								$likes_array[$userID] = $likecount - get_option("fblico_spent".$userID);
							}
						}
						
						arsort($likes_array);
						
						foreach($likes_array as $userID => $likecount){
							
							echo'
							<tr>
								<td>'.get_the_author_meta( 'first_name', $userID ).' '.get_the_author_meta( 'last_name', $userID ).'</td>
								<td>'.$likecount.'</td>
								<td><input type="text" value="'.@get_option("fblico_spent".$userID).'" name="fblico_spent'.$userID.'" /></td>
							</tr>';
							$page_options[] = 'fblico_spent'.$userID;
						}
					?>
				</table>
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="<?php echo implode(',',$page_options);?>" />
				<p class="submit">
					<input type="submit" value="<?php _e('save','fblico');?>" class="button-primary"/>
				</p>
			</form>
		</div>
	</div>

	<div id="poststuff" class="postbox">
		<h3 style="cursor:default"><?php _e('Like Chart','fbcomcon'); echo ' - ';_e('Posts','fbcomcon');?></h3>
		<div class="inside">

			<table class="form-table" style="width:auto;">
				<tr valign="top">
					<th style="font-weight:bold;width:auto;"><?php _e('Maximum number of posts to show','fblico');?></th>
					<th style="font-weight:bold;">
						<form action="options.php" method="post">
							<?php wp_nonce_field('update-options');?>	
							<input type="text" value="<?php echo get_option("fblico_number_posts");?>" name="fblico_number_posts" style="width:30px;"/>
							<input type="hidden" name="action" value="update" />
							<input type="hidden" name="page_options" value="fblico_number_posts" />
							<input type="submit" value="<?php _e('save','fblico');?>" class="button-secondary" />
						</form>
					</th>
				</tr>	
				<tr valign="top">
					<th style="font-weight:bold;width:auto;"><?php _e('Title','fblico');?></th>
					<th style="font-weight:bold;"><?php _e('Likes','fblico');?></th>
				</tr>	
		
				<?php

					foreach($post_ids as $post) {
						$post_like_count = get_post_meta($post, 'like_count', 'true'); //Number of likes of the post
						$post_likes_array[$post] = $post_like_count;
					}
					
					arsort($post_likes_array);
					
					foreach($post_likes_array as $postID => $post_likecount){
						
						$post_title = strip_tags(get_the_title($postID));
						$post_permalink = get_permalink($postID);
						
						if($post_likecount!='' xor $post_likecount=='0'){
							echo'
							<tr>
								<td><a href="'. $post_permalink.'" title="'. __("Link to ","fblico") .$post_title.'" target="_blank">'.$post_title.'</a></td>
								<td>'.$post_likecount.'</td>
							</tr>';
						}
						
						$paging++;
						if(get_option("fblico_number_posts")==$paging && (get_option("fblico_number_posts") != '0' || get_option("fblico_number_posts")=='')){
							break;
						}
					}
				?>
		
			</table>
		</div>
	</div>
	
	<!-- PayPal Link -->
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="N6E42VW26QD66">
		<input type="image" src="<?php echo get_option( "siteurl" ).'/'.PLUGINDIR.'/facebook-comment-control/img/donate.jpg';?>" border="0" name="submit" alt="Buy me a beer">
		<img alt="" border="0" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1">
	</form>
	<?php _e('Plugin Homepage','fblico');?>: <a href="http://fblico.mafact.de/">Facebook Like Count</a><br/>
	<?php _e('Plugin Author','fblico');?>: <a href="http://www.facebook.com/ms.fb.ger">Marco Scheffel</a>
</div>
<?php }} ?>