<div class="wrap">
    <h1>VHM EMOJI Post Reactions</h1>
	<p><?php _e('Let users to show their reactions on your pages or posts.', TEXTDOMAIN ); ?></p>
	
	<?php if ($_REQUEST['message'] == 'created'): ?>

		<div id="message" class="updated"><p><?php echo __('Shortcode was successfully CREATED.', TEXTDOMAIN); ?></p></div>

    <?php elseif ($_REQUEST['message'] == 'not-created'): ?>

		<div id="notice" class="error"><p><?php echo __('Shortcode was NOT CREATED.', TEXTDOMAIN) ?></p></div>

	<?php elseif ($_REQUEST['message'] == 'deleted'):?>

		<div id="message" class="updated"><p><?php echo __('Shortcode was successfully DELETED.', TEXTDOMAIN); ?></p></div>

    <?php endif;?>

	<form method="post" action="options.php">
		<?php @settings_fields('vhmEmojiPostReactionsGroup'); ?>

		<div id="reactions">
			<h2 class="title"><?php _e('Configuration', TEXTDOMAIN); ?></h2 class="title">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="item_title"><?php _e('Title', TEXTDOMAIN)?></label></th>
					<td>
						<p><input id="item_title" class="regular-text" type="text" name="vhmEmojiPostReactionsOptions[title]" value="<?php echo $this->options['title']?>" /></p>
						<p class="description"><?php _e('HTML allowed', TEXTDOMAIN); ?></p>
					</td>
				</tr>
			</table>
			
			<h2 class="title"><?php _e('Reactions', TEXTDOMAIN); ?></h2 class="title">
			
			<ol>
				<li><?php printf(__('Click the %s button.', TEXTDOMAIN), '<code>Add new reaction</code>' );?></li>
				<li><?php printf(__('Label your EMOJI into the %s (eg.: %s).', TEXTDOMAIN), '<code>Label field text</code>', '<code>Loved it!</code>');?></li>
				<li><?php _e('Select or upload the EMOJI you want to use.', TEXTDOMAIN); ?></li>
				<li><?php printf(__('Paste it into the %s', TEXTDOMAIN), '<code>Emoji field text</code>');?></li>
				<li><?php _e('Save changes.', TEXTDOMAIN);?></li>
			</ol>
			
			<table class="wp-list-table widefat fixed tags">
				<thead>
					<tr>
						<th scope="col" class="column-label desc" style=""><?php _e('Label', TEXTDOMAIN)?></th>
						<th scope="col" class="column-cb desc" style=""><?php _e( 'Emoji', TEXTDOMAIN ); ?></th>
					</tr>
				</thead>
				<tbody id="vhm-emoji-reaction-list">
					<?php if (isset($this->options['reactions'])) { $i = 0; foreach($this->options['reactions'] as $k => $reaction) { ?>
						<tr>
							<td class="column-cb">
								<input type="hidden" name="reaction_id" value="<?=$k; ?>">
								<input type="text" name="vhmEmojiPostReactionsOptions[reactions][<?=$k?>][label]" value="<?=$reaction['label']?>">
							</td>
							<td class="column-label">
								<input type="text" class="emoji_url" name="vhmEmojiPostReactionsOptions[reactions][<?=$k?>][code]" value="<?=$reaction['code']?>" readonly> 
								<input type="button" class="upload_emoji button-primary" value="Select" />
								<a class="button remove_reaction" href="javascript:;"><?php _e('Remove', TEXTDOMAIN)?></a>
							</td>
						</tr>
					<?php $i++; } } ?>
				</tbody>
			</table>
			<p><a id="add_vhm_emoji_reaction" class="button-primary" href="javascript:;"><?php _e('Add new reaction', TEXTDOMAIN);?></a></p>
		</div>
		
		<div id="configuration">
			<h2 class="title"><?php _e('Reset configuration', TEXTDOMAIN)?></h2 class="title">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="reset"><?php _e('Reset', TEXTDOMAIN)?></label></th>
					<td>
						<p><input id="reset" class="regular-text" type="text" name="reset" /></p>
						<ol>
							<li>Write <b>REACTIONS</b> to reset ALL the reactions from ALL entries</li>
							<li>Write <b>VOTERS</b> to reset ALL the voters from ALL entries</li>
							<li>Write <b>ALL</b> to reset plugin to factory settings</li>
						</ol>
					</td>
				</tr>
			</table>
		</div>
		
		<div id="about">

			<h2 class="title"><?php _e('About', TEXTDOMAIN); ?></h2 class="title">
			<p><strong><?php _e('Plugin developed by graphic and web designer', TEXTDOMAIN); ?> <a href="http://viktormorales.com/">viktormorales.com</a></p> 
			<p><strong><?php _e('Need support? Don\'t waste your precious time, HIRE ME!', TEXTDOMAIN); ?></strong></p>
			<p><strong><?php _e('Feeling generous? Buy me a beer (is good for motivation).', TEXTDOMAIN)?></strong></p>
			
			<p><?php _e('This is me around the cloud', TEXTDOMAIN); ?></p>
			<ul>
				<li>Instagram: <a href="http://instagram.com/vhmfreelance">instagram.com/vhmfreelance</a></li>
				<li>Facebook: <a href="http://facebook.com/vhmfreelance">facebook.com/vhmfreelance</a></li>
				<li>Twitter: <a href="http://twitter.com/viktormorales">twitter.com/viktormorales</a></li>
			</ul>
		</div>
	
	<?php @submit_button(); ?>

	</form>

</div>