jQuery(document).ready(function() {
		
	var reaction_box = jQuery('#vhm-emoji-post-reactions-box');
	var original;
	var converted;
	function load_reactions_box()
	{
		jQuery.ajax({
			type: "post",
			dataType: "html",
			url: vhm_emoji_var.ajax_url,
			data: "action=load_reactions_box&post_id="+vhm_emoji_var.post_id+"&nonce="+vhm_emoji_var.nonce,
			success: function(output){
				reaction_box.html(output);
			},
			error: function(result)
			{
				console.log(result);
			}
		}).done(function(){
			jQuery('.vhm-emoji-post-reactions-code').each(function() {
				original = jQuery(this).html();
				converted = emoji.shortnameToImage(original);
				jQuery(this).html(converted);
			});
		});
	}
	load_reactions_box();
	
	// Vote action
	jQuery('#vhm-emoji-post-reactions-box ol.vote li').live('click', function(e){
		item = jQuery(this);
		item_voted = item.data('vhm_emoji_vote_id');
		reaction_box.html(vhm_emoji_var.sending_text);
		
		if (vhm_emoji_var.post_id)
		{
			// realiza la carga
			jQuery.ajax({
				type: "post",
				dataType: "json",
				url: vhm_emoji_var.ajax_url,
				data: "action=vote&item_voted="+item_voted+"&post_id="+vhm_emoji_var.post_id+"&nonce="+vhm_emoji_var.nonce,
				success: function(json){
					load_reactions_box();
				},
				error: function(json)
				{
					console.log(json);
				}
			});
		}
	});
	
});
