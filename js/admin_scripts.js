jQuery(document).ready(function() {	
	// Adds new fields for new reaction
	var wrapper = jQuery('#vhm-emoji-reaction-list');
	var add_button = jQuery('#add_vhm_emoji_reaction');
	
	jQuery(add_button).bind('click', (function(e){
		var i = (jQuery('#vhm-emoji-reaction-list tr').length > 0) ? parseInt(jQuery("input[name='reaction_id']").last().val()) + 1 : 0 ;
		
		jQuery(wrapper).append('<tr><td class="column-cb"><input type="hidden" name="reaction_id" value="' + i +'"><input type="text" name="vhmEmojiPostReactionsOptions[reactions][' + i + '][label]"></td><td class="column-label"><input type="text" class="emoji_url" name="vhmEmojiPostReactionsOptions[reactions][' + i + '][code]" readonly> <input type="button" class="upload_emoji button-primary" value="Select" /> <a class="button remove_reaction" href="javascript:;">Remove</a></td></tr>');
	}));
	
	jQuery(wrapper).on("click", ".remove_reaction", function(e){
		jQuery(this).closest('tr').remove();
	});
	
	var mediaUploader;
	var _input_emoji;
	jQuery('.upload_emoji').live('click', (function(e) {
		_input_emoji = jQuery(this).prev(".emoji_url");
		
		e.preventDefault();
		if (mediaUploader) {
			mediaUploader.open();
			return;
		}
		mediaUploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image',
			button: {
				text: 'Choose Image'
			}, multiple: false });
		mediaUploader.on('select', function() {
			var attachment = mediaUploader.state().get('selection').first().toJSON();
			_input_emoji.val(attachment.url);
		});
		mediaUploader.open();
	}));
});
