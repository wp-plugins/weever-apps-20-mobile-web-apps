
wxApp = wxApp || {};

(function($){
	wxApp.FormBuilderControlView = Backbone.View.extend({
		className: 'wx-form-builder-row',

		events: {
			'click .wx-form-builder-edit-label': 'editLabel',
			'blur .wx-form-builder-label-input': 'updateLabel',
			'blur .wx-form-builder-placeholder-input': 'updatePlaceholder',
			'blur .wx-form-builder-min-input': 'setMin',
			'blur .wx-form-builder-max-input': 'setMax',
			'blur .wx-form-builder-value-input': 'setValue',
			'blur .wx-form-builder-step-input': 'setStep',
			'blur .wx-form-builder-name-input': 'setName',
			'click .wx-form-builder-autocomplete': 'setAutocomplete',
			'click .wx-form-builder-control-checked': 'setChecked',
			'click .wx-form-builder-control-selected': 'setSelected',
			'click .wx-form-builder-allow-multiple': 'setMultiple',
			'click .wx-form-builder-allow-additional': 'setAllowAdditional',
			'click .wx-form-builder-required': 'setRequired',
			'click .wx-form-builder-delete': 'deleteControl',
			'sortable-drop': 'sortableDrop'
		},

		sortableDrop: function( event, index ) {
			console.log( 'sortableDrop' );
			this.$el.trigger( 'sortable-update', [this.model, index] );
		},

		deleteControl: function() {
			console.log( 'deleteControl' );
			this.remove();
			this.model.destroy();
		},

		editLabel: function( ev ) {
			console.log('editLabel');
			ev.preventDefault();
			this.$label = $( ev.currentTarget );
			this.$( '.wx-form-builder-label-input' ).val( this.$label.text() ).show().select();
			this.$label.hide();
		},

		updateLabel: function( ev ) {
			console.log('updateLabel');
			var $me = $( ev.currentTarget );
			this.$label.text( $me.val() ).show();
			$me.hide();

			this.model.set( 'label', $me.val() );
		},

		updatePlaceholder: function(ev) {
			console.log('updatePlaceholder');
			var $me = $( ev.currentTarget );
			if ( $me.val() !== '' )
				this.model.get( 'attributes' ).set( 'placeholder', $me.val() );
			
			this.getInput().attr( 'placeholder', $me.val() );
		},

		setMin: function( ev ) {
			this.model.get( 'attributes' ).set( 'min', $( ev.currentTarget ).val() );
			this.getInput().attr( 'min', $( ev.currentTarget ).val() );
		},

		setMax: function ( ev ) {
			this.model.get( 'attributes' ).set( 'max', $( ev.currentTarget ).val() );
			this.getInput().attr( 'max', $( ev.currentTarget ).val() );
		},

		setValue: function ( ev ) {
			this.model.get( 'attributes' ).set( 'value', $( ev.currentTarget ).val() );
			this.getInput().val( $( ev.currentTarget ).val() );
		},

		setStep: function ( ev ) {
			this.model.get( 'attributes' ).set( 'step', $( ev.currentTarget ).val() );
			this.getInput().attr( 'step', $( ev.currentTarget ).val() );
		},

		setName: function( ev ) {
			var $me = $( ev.currentTarget );
			if ( $me.val() !== '' )
				this.model.get( 'attributes' ).set( 'name', $me.val() );

			this.getInput().attr( 'name', $me.val() );
		},

		setAutocomplete: function( ev ) {
			this.model.get( 'attributes' ).set( 'autocomplete', $( ev.currentTarget ).val() );
		},

		setChecked: function( ev ) {
			console.log('setChecked');
			this.model.collection.models.forEach( function( control ) {
				control.get( 'attributes' ).unset( 'checked' );
			});
			this.model.get( 'attributes' ).set( 'checked', 'checked' );
		},

		setSelected: function( ev ) {
			console.log('setSelected');
			this.model.collection.models.forEach( function( control ) {
				control.get( 'attributes' ).unset( 'selected' );
			});
			this.model.get( 'attributes' ).set( 'selected', 'checked' );
		},

		setMultiple: function( ev ) {
			console.log('setMultiple');
			var $me = $( ev.currentTarget );
			if ( $me.is( ':checked' ) ) {
				this.model.get( 'attributes' ).set( 'multiple', 'checked' );
			}
			else {
				this.model.get( 'attributes' ).unset( 'multiple' );
			}
		},

		setAllowAdditional: function( ev ) {
			console.log('setAllowAdditional');
			var $me = $( ev.currentTarget );
			if ( $me.is( ':checked' ) ) {
				console.log('checked');
				this.model.set( 'allowAdditional', 'checked' );
			}
			else {
				this.model.set( 'allowAdditional', '' );
			}
		},

		setRequired: function( ev ) {
			console.log('setRequired');
			var $me = $( ev.currentTarget );
			if ( $me.is( ':checked' ) ) {
				this.model.get( 'attributes' ).set( 'required', 'checked' );
				$('span.required').show();
			}
			else {
				this.model.get( 'attributes' ).unset( 'required' );
				$('span.required').hide();
			}
		},

		getInput: function() {
			var $input = this.$('.wx-form-builder-' + this.model.get('attributes').get('type') + '-input');
			if ( $input.length == 0 ) {
				// Must be a text area.
				$input = this.$('.wx-form-builder-textarea');
			}
			return $input;
		}

	});
})(jQuery);