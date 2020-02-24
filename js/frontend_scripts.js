jQuery(document).ready(function() {
	
	var reaction_box;
	var item;
	var item_voted;
	
	// Vote action
	jQuery('.vhm-emoji-post-reactions-box ol.vote li').live('click', function(e){
		item = jQuery(this);
		post_id = item.parent().parent().data('post');
		item_voted = item.data('vhm_emoji_vote_id');
		// Sending reaction text
		reaction_box = item.closest('.vhm-emoji-post-reactions-box');
		reaction_box.html(vhm_emoji_var.sending_text);
		
		if (post_id)
		{
			// send "vote" information
			jQuery.ajax({
				type: "post",
				dataType: "html",
				url: vhm_emoji_var.ajax_url,
				data: "action=vote&item_voted="+item_voted+"&post_id="+post_id+"&nonce="+vhm_emoji_var.nonce,
				success: function(output){
					// Reload the reactions box
					reaction_box.html(output)
				},
				error: function(json)
				{
					console.log(json);
				}
			});
		}
	});
});
