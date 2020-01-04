jQuery(document).ready(function() {	
	// Adds new fields for new reaction
	var wrapper = jQuery('#vhm-emoji-reaction-list');
	var add_button = jQuery('#add_vhm_emoji_reaction');
	
	jQuery(add_button).bind('click', (function(e){
		var i = (jQuery('#vhm-emoji-reaction-list tr').length > 0) ? parseInt(jQuery("input[name='reaction_id']").last().val()) + 1 : 0 ;
		
		jQuery(wrapper).append('<tr><td class="column-cb"><input type="hidden" name="reaction_id" value="' + i +'"><input type="text" name="vhmEmojionePostReactionsOptions[reactions][' + i + '][label]"></td><td class="column-label"><input type="text" name="vhmEmojionePostReactionsOptions[reactions][' + i + '][code]"> <a class="button remove_reaction" href="javascript:;">Remove reaction</a></td></tr>');
	}));
	
	jQuery(wrapper).on("click", ".remove_reaction", function(e){
		jQuery(this).closest('tr').remove();
	});
	
});
