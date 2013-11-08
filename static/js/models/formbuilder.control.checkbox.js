
wxApp = wxApp || {};

(function($){

	wxApp.FormBuilderControlCheckbox = wxApp.FormBuilderControlInput.extend({
		defaults: function() {
			// This is annoying
			// https://github.com/documentcloud/backbone/issues/476
			var newDefaults = _.extend( this.constructor.__super__.defaults(), {
				label: 'Checkbox',
				autocompleteClass: 'hide'
			} );
			return newDefaults;
		},

		initialize: function() {
			// So is this
			// http://documentcloud.github.com/backbone/#Model-extend
			wxApp.FormBuilderControl.prototype.initialize.apply( this );

			this.get( 'attributes' ).set( 'type', 'checkbox' );
		}
	});

})(jQuery);
