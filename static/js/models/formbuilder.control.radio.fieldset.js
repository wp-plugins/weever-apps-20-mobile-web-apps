// models/formbuilder.control.radio.fieldset.js

wxApp = wxApp || {};

(function($){

	wxApp.FormBuilderControlRadioFieldset = Backbone.Model.extend({

		defaults: {
			control: 'radiofieldset',
			title: 'Radio Fieldset',
			allowAdditional: '',
			allowAdditionalClass: '',
			name: ''
		},

		initialize: function() {
			this.set( 'attributes', new wxApp.FormBuilderControlAttributes() );
			this.set( 'radioGroup', new wxApp.FormBuilderControlRadioGroup() );
			this.get( 'radioGroup' ).on( 'add', this.onRadioGroupAdd, this );
			//console.log(this);
			return this;
		},

		onRadioGroupAdd: function( checkbox ) {
			console.log( 'onRadioGroupAdd' );
			checkbox.get( 'attributes' ).set( 'name', this.get( 'name' ) );
		}


	});

})(jQuery);