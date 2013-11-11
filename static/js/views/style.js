wxApp = wxApp || {};

(function($){

    wxApp.StyleBase = Backbone.View.extend({

        showLoadingGif: function( id ) {
            var loading_id = '#' + id + '_loading';
            $(loading_id).show();
            return loading_id;
        },

        hideLoadingGif: function(id, loading_id) {
            // Convenience method.
            this.hideLoadGif(id, loading_id);
        },

        hideLoadGif: function(id, loading_id) {
            // Create a span that tells you the element was saved.
            // Add it to the page for two seconds, then fade it out, then remove it from the page.
            // Hide the loading gif
            var post_load = id + "_done";
            var span = "<label id=\"" + post_load + "\" class=\"left inline\">Saved.</label>";
            $(loading_id).before( span );
            $( '#' + post_load ).delay( 2000 ).fadeOut( 1000 ).queue( function() { $(this).remove();} );
            $(loading_id).hide();
        }
        
    });
})(jQuery);