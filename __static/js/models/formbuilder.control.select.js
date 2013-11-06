// models/formbuilder.control.select.js

wxApp = wxApp || {};

(function($){

	wxApp.FormBuilderControlSelect = Backbone.Model.extend({

		defaults: {
			control: 'select',
			title: 'Dropdown',
			allowAdditional: '',
			allowAdditionalClass: '',
			requiredClass: '',
			name: ''
		},

		initialize: function() {
			this.set( 'attributes', new wxApp.FormBuilderControlAttributes() );
			this.set( 'optionGroup', new wxApp.FormBuilderControlOptionGroup() );
			console.log(this);
			return this;
		}

	});

})(jQuery);
