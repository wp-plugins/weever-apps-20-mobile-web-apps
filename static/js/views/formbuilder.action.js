
wxApp = wxApp || {};

(function($){
	wxApp.FormBuilderActionView = Backbone.View.extend({
		tagName: 'section',
		className: 'wx-form-builder-row',
		tplPostSelector: '#form-builder-action-post',
		tplEmailSelector: '#form-builder-action-email',
		tplDocusignSelector: '#form-builder-action-docusign',

		events: {
			'blur .wx-form-builder-action': 'updateAction',
			'click .wx-form-builder-delete': 'deleteControl',
			'blur .wx-form-builder-docusign-username': 'updateUsername',
			'blur .wx-form-builder-docusign-password': 'updatePassword',
			'blur .wx-form-builder-docusign-returnUrl': 'updateReturnUrl',
			'blur .wx-form-builder-pdfheader-title': 'updatePdfHeader',
			'blur .wx-form-builder-pdfheader-line1': 'updatePdfHeader',
			'blur .wx-form-builder-pdfheader-line2': 'updatePdfHeader',
			'blur .wx-form-builder-pdfheader-line3': 'updatePdfHeader'
		},

		initialize: function() {
			var tplSelector = '';
			switch( this.model.get( 'method' ) ) {
				case 'post':
					tplSelector = this.tplPostSelector;
					break;
				case 'email':
					tplSelector = this.tplEmailSelector;
					break;
				case 'docusign':
					tplSelector = this.tplDocusignSelector;
					break;
			}
			var $template = $( tplSelector );
			this.tpl = _.template( $template.html() );
//			this.model.bind('change', this.render, this);
		},

		render: function() {
			this.$el.html( this.tpl( this.model.toJSON() ) );
			return this;
		},

		updateUsername: function( ev ) {
			this.model.set( 'username', $( ev.currentTarget ).val() );
		},

		updatePassword: function( ev ) {
			this.model.set( 'password', $( ev.currentTarget ).val() );
		},

		updateReturnUrl: function( ev ) {
			this.model.set( 'returnUrl', $( ev.currentTarget ).val() );
		},

		updatePdfHeader: function( ev ) {
			console.log( 'updatePdfHeader' );
			var $me = $( ev.currentTarget );

			if ( $me.hasClass( 'wx-form-builder-pdfheader-title' ) )
				this.model.get( 'pdfHeader' ).title = $me.val();
			if ( $me.hasClass( 'wx-form-builder-pdfheader-line1' ) )
				this.model.get( 'pdfHeader' ).line1 = $me.val();
			if ( $me.hasClass( 'wx-form-builder-pdfheader-line2' ) )
				this.model.get( 'pdfHeader' ).line2 = $me.val();
			if ( $me.hasClass( 'wx-form-builder-pdfheader-line3' ) )
				this.model.get( 'pdfHeader' ).line3 = $me.val();

			console.log( this.model );
		},

		updateAction: function( ev ) {
			console.log( 'updateAction' );
			ev.preventDefault();
			var $me = $( ev.currentTarget );
			this.model.set( 'value', $me.val() );
			console.log( this.model );
		},

		deleteControl: function() {
			console.log( 'deleteControl' );
			this.remove();
			this.model.destroy();
		}

	});
})(jQuery);