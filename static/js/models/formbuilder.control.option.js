// models/formbuilder.control.option.js

wxApp = wxApp || {};

(function($){

	wxApp.FormBuilderControlOption = wxApp.FormBuilderControl.extend({
		defaults: function() {
			// This is annoying
			// https://github.com/documentcloud/backbone/issues/476
			var newDefaults = _.extend( this.constructor.__super__.defaults(), {
				control: 'option',
				innerText: 'Option'
			} );
			return newDefaults;
		},

		initialize: function( l ) {
			if ( l ) { this.set('innerText', l); }

			// So is this
			// http://documentcloud.github.com/backbone/#Model-extend
			wxApp.FormBuilderControl.prototype.initialize.apply( this );
			return this;
		}
	});

})(jQuery);
