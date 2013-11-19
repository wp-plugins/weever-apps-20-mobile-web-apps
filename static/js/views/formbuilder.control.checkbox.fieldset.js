// views/formbuilder.control.checkbox.fieldset.js

wxApp = wxApp || {};

(function($){
	wxApp.FormBuilderControlCheckboxFieldsetView = wxApp.FormBuilderControlBaseFieldsetView.extend({
		tplSelector: '#form-builder-checkbox-fieldset',

		getPreview: function() {
			if ( this.preview === null ) {
				this.preview = new wxApp.FormBuilderControlCheckboxFieldsetPreview({ model: this.model });
			}
			return this.preview;
		},

		addOption: function() {
			this.model.get( 'checkboxGroup' ).add( new wxApp.FormBuilderControlCheckbox() );
		}

	});

	wxApp.FormBuilderControlCheckboxFieldsetPreview = wxApp.FormBuilderControlBaseFieldsetPreview.extend({
		selector: '#form-builder-checkbox-fieldset-preview'
	});
})(jQuery);
