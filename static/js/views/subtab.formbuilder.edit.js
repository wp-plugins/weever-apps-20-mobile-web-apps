
wxApp = wxApp || {};

(function($){
    wxApp.FormBuilderSubTabEditView = wxApp.SubTabEditView.extend({
		previewPaneSelector: '.wx-form-builder-preview',
        subTabEditTplSelector: '#form-builder-subtab-edit-template',
	    hasCalledFinish: false,
	    finishView: null,

        initializeEvents: function() {
            this.events = _.extend({}, this.genericEvents, this.events);
//			this.controls = new wxApp.FormBuilderCollection();
        },

		initialize: function() {
			// Call parent's initialize() function
			this.constructor.__super__.initialize.apply( this, arguments );
			console.log('FormBuilderSubTabEditView initialize');

			if ( typeof this.model.get( 'config' ).formElements == 'undefined' ) {
				this.model.get( 'config' ).formElements = new wxApp.FormBuilderCollection();
			}
			else {
				// Load currently existing form elements.
				console.log( this.model.get( 'config' ).formElements );
				var elementsJson = JSON.parse( this.model.get( 'config' ).formElements );

				this.model.get('config').formElements = new wxApp.FormBuilderCollection();

				for ( var i = 0; i < elementsJson.length; i++ ) {

					if ( elementsJson[i].control === 'div' ) {

						this.addInfoWithProperties( elementsJson[i] );

					} else if ( elementsJson[i].control === 'textarea' ) {

						this.addTextareaWithProperties( elementsJson[i] );

					} else if ( elementsJson[i].control === 'radiofieldset' ) {

						this.addRadioGroupWithProperties( elementsJson[i] );

					} else if ( elementsJson[i].control === 'checkboxfieldset' ) {

						this.addCheckboxGroupWithProperties( elementsJson[i] );

					} else if ( elementsJson[i].control === 'select' ) {

						this.addSelectWithProperties( elementsJson[i] );

					} else {

						this.addInput( elementsJson[i] );

					}
				}
			}

			if ( typeof this.model.get( 'config' ).formActions == 'undefined' ) {
				this.model.get( 'config' ).formActions = new Backbone.Collection();
			}
			else {
				// Load currently existing form actions.
				console.log( this.model.get( 'config' ).formActions );
				var actionsJson = JSON.parse( this.model.get( 'config' ).formActions );
				this.model.get( 'config' ).formActions = new Backbone.Collection();

				for ( var i = 0; i < actionsJson.length; i++ ) {
					if ( actionsJson[i].method == 'docusign' ) {
						this.addDocusignAction( null, actionsJson[i] );
					}
					else if ( actionsJson[i].method == 'post' ) {
						this.addPostAction( null, actionsJson[i] );
					}
					else if ( actionsJson[i].method == 'email' ) {
						this.addEmailAction( null, actionsJson[i] );
					}
				}
			}

			if ( typeof this.model.get( 'config' ).onUpload == 'string' ) {
				this.model.get( 'config' ).onUpload = JSON.parse( this.model.get( 'config' ).onUpload );
			}

		},

		setModelFromView: function( model ) {
//			console.log( 'setModelFromView' );
//			console.log( model );
//			model = model.toJSONrecursive();
//			console.log( model );
			return model;
		},

        events: {
            'click .wx-form-builder-add-text-input': 'addTextInput',
			'click .wx-form-builder-add-password-input': 'addPasswordInput',
			'click .wx-form-builder-add-date-input': 'addDateInput',
			'click .wx-form-builder-add-datetime-local-input': 'addDateTimeLocalInput',
			'click .wx-form-builder-add-email-input': 'addEmailInput',
			'click .wx-form-builder-add-file-input': 'addFileInput',
			'click .wx-form-builder-add-month-input': 'addMonthInput',
			'click .wx-form-builder-add-number-input': 'addNumberInput',
			'click .wx-form-builder-add-tel-input': 'addTelInput',
			'click .wx-form-builder-add-time-input': 'addTimeInput',
			'click .wx-form-builder-add-url-input': 'addUrlInput',
			'click .wx-form-builder-add-radio-group': 'addRadioGroup',
			'click .wx-form-builder-add-checkbox-group': 'addCheckboxGroup',
			'click .wx-form-builder-add-textarea': 'addTextarea',
			'click .wx-form-builder-add-range-input': 'addRangeInput',
			'click .wx-form-builder-add-select': 'addSelect',
			'click .wx-form-builder-add-info': 'addInfo',
			'click .wx-form-builder-add-docusign-action': 'addDocusignAction',
			'click .wx-form-builder-add-post-action': 'addPostAction',
			'click .wx-form-builder-add-email-action': 'addEmailAction',
	        'sortable-update': 'sortableUpdate'
		},

	    sortableUpdate: function( event, model, position ) {
		    console.log( 'sortableUpdate' );
		    var formElements = this.model.get( 'config' ).formElements;

		    formElements.remove( model );

		    formElements.each( function( model, index ) {
			    var ordinal = index;
			    if ( index >= position ) {
				    ordinal += 1;
			    }
			    model.set( 'ordinal', ordinal );
		    });

		    model.set( 'ordinal', position );
		    formElements.add( model, {at: position} );
	    },

	    /**
	     * Override __super__.finish()
	     */
	    finish: function() {
		    console.log( 'subtab.formbuilder.edit.finish()' );
		    console.log( this );

		    var hasUpload = false,
			    formElements = this.model.get( 'config' ).formElements,
			    formActions = this.model.get( 'config' ).formActions,

			    /**
			     * Should be called using .call( this ) or .apply( this ) so that the scope remains the same
			     */
		        addFinishView = function() {
				    this.finishView = new wxApp.FormBuilderFinishView({
					    model: this.model
				    });
				    this.$( this.previewPaneSelector ).append( this.finishView.render().el );
	//			    $( 'body' ).append( finishView.render().el );
			    };

		    console.log( formElements );
		    console.log( formActions );

		    // Check for an upload element
		    var model = {};
		    for ( var i = 0; i < formElements.length; i++ ) {
			    model = formElements.at( i );
			    if ( 'input' == model.get( 'control' ) && 'file' == model.get( 'attributes' ).get( 'type' ) ) {
				    hasUpload = true;
				    break;
			    }
		    }

		    // Call super and exit if an index has already been identified
		    if ( ! hasUpload || typeof this.model.get( 'config' ).idFieldIndex == 'number' ) {
			    this.constructor.__super__.finish.apply( this );
			    return;
		    }

		    // Select index
		    if ( typeof this.model.get( 'config' ).idFieldIndex != 'number' && ! this.hasCalledFinish ) {
			    addFinishView.apply( this );
			    this.hasCalledFinish = true;
			    return;
		    }

		    // Re-add finish view in case elements have changed
		    if ( this.hasCalledFinish ) {
			    this.finishView.remove();
			    addFinishView.apply( this );
		    }

	    },

	    addDocusignAction: function( event, properties ) {
		    console.log( 'addDocusignAction' );

		    var action;
		    if ( typeof properties != 'undefined' ) {
			    action = this.addCustomAction( properties );
		    }
		    else {
			    action = this.addCustomAction( { method : 'docusign' } );
		    }

		    return action;
	    },

	    addEmailAction: function( event, properties ) {
			console.log( 'addEmailAction' );

		    var action;
		    if ( typeof properties != 'undefined' ) {
			    action = this.addCustomAction( properties );
		    }
		    else {
			    action = this.addCustomAction( { method : 'email' } );
		    }

			return action;
		},

	    addPostAction: function( event, properties ) {
		    console.log( 'addPostAction' );

		    var action;
		    if ( typeof properties != 'undefined' ) {
			    action = this.addCustomAction( properties );
		    }
		    else {
			    action = this.addCustomAction( { method : 'post' } );
		    }

		    return action;
	    },

		addCustomAction: function( customAction ) {
			console.log( 'addCustomAction' );
			var action = new wxApp.FormBuilderAction();
			if ( typeof customAction == 'object' ) {
				action.set( customAction );
			}

			var actionView = new wxApp.FormBuilderActionView({
				model: action
			});
			this.$( this.previewPaneSelector ).append( actionView.render().el );

//			this.model.get( 'config' ).formElements.push( action );
			this.model.get( 'config' ).formActions.push( action );
			return action;
		},

		addInput: function( properties ) {
			var mainProperties = {};
			var attributes = {};
			for ( var propKey in properties ) {
				if ( propKey != 'attributes' ) {
					mainProperties[propKey] = properties[propKey];
				}
				else {
					for ( var attrKey in properties[propKey] ) {
						attributes[attrKey] = properties[propKey][attrKey];
					}
				}
			}

			var input = new wxApp.FormBuilderControlInput( mainProperties );
			input.get( 'attributes' ).set( attributes );

			var inputView = new wxApp.FormBuilderControlInputView({
				model: input
			});
			this.$( this.previewPaneSelector ).append( inputView.render().el );

//			this.model.get( 'controls' ).push( input );
			this.model.get( 'config' ).formElements.push( input );
			return input;
		},

		addDateInput: function() {
			this.addInput({
				label: 'Date',
//				minClass: '',
//				maxClass: '',
//				valueClass: '',
//				minType: 'date',
//				maxType: 'date',
//				valueType: 'date',
				attributes: {
					type: 'date'
				}
			});
		},

		addDateTimeLocalInput: function() {
			this.addInput({
				label: 'Date/Time',
//				minClass: '',
//				maxClass: '',
//				stepClass: '',
//				valueClass: '',
//				minType: 'date',
//				maxType: 'date',
//				valueType: 'date',
				attributes: {
					type: 'datetime-local'
				}
			});
		},

		addEmailInput: function() {
			this.addInput({
				label: 'Email',
				showPlaceholder: true,
				multiClass: '',
				attributes: {
					type: 'email'
				}
			});
		},

		addFileInput: function() {
			this.addInput({
				label: 'File',
				multiClass: '',
				autocompleteClass: 'hide',
				attributes: {
					type: 'file',
					accept: 'image/*'
				}
			});
		},

		addMonthInput: function() {
			this.addInput({
				label: 'Month',
				attributes: {
					type: 'month'
				}
			});
		},

		addNumberInput: function() {
			this.addInput({
				label: 'Number',
				minClass: '',
				maxClass: '',
				stepClass: '',
				valueClass: '',
				attributes: {
					type: 'number'
				}
			});
		},

		addPasswordInput: function() {
			this.addInput({
				label: 'Password',
				attributes: {
					type: 'password'
				}
			});
		},

		addRangeInput: function() {
			this.addInput({
				label: 'Range',
				minClass: '',
				maxClass: '',
				stepClass: '',
				valueClass: '',
				attributes: {
					type: 'range'
				}
			});
		},

		addTelInput: function() {
			this.addInput({
				label: 'Telephone',
				showPlaceholder: true,
				attributes: {
					type: 'tel'
				}
			});
		},

		addTextInput: function() {
			this.addInput({
				label: 'Text',
				type: 'text',
				showPlaceholder: true,
				valueType: 'text',
				valueClass: '',
				attributes: {
					type: 'text'
				}
			});
		},

		addTimeInput: function() {
			this.addInput({
				label: 'Time',
				attributes: {
					type: 'time'
				}
			});
		},

		addUrlInput: function() {
			this.addInput({
				label: 'URL',
				showPlaceholder: true,
				attributes: {
					type: 'url'
				}
			});
		},

		addInfo: function( event ) {
			this.addInfoWithProperties( {} );
		},

		addInfoWithProperties: function( properties ) {

			var info = new wxApp.FormBuilderControlInfo( properties );
			var infoView = new wxApp.FormBuilderControlInfoView({
				model: info
			});
			this.$( this.previewPaneSelector ).append( infoView.render().el );
			this.model.get( 'config' ).formElements.push( info );

		},

		addTextarea: function() {
			this.addTextareaWithProperties( {} );
		},

		addTextareaWithProperties: function( properties ) {

			var textArea = new wxApp.FormBuilderControlTextarea( properties );
			var textAreaView = new wxApp.FormBuilderControlTextareaView({
				model: textArea
			});
			this.$( this.previewPaneSelector ).append( textAreaView.render().el );
//			this.model.get( 'controls' ).push( textArea );
			this.model.get( 'config' ).formElements.push( textArea );
		},

		addRadioGroup: function() {
			this.addRadioGroupWithProperties( {} );
		},

	    addRadioGroupWithProperties: function( properties ) {
			var radioFieldset = new wxApp.FormBuilderControlRadioFieldset( properties );
		    var radioFieldsetView = new wxApp.FormBuilderControlRadioFieldsetView({
				model: radioFieldset
			});

			this.$( this.previewPaneSelector ).append( radioFieldsetView.render().el );

			var radioGroupView = new wxApp.FormBuilderControlRadioGroupView({
				collection: radioFieldset.get( 'radioGroup' )
			});

			radioFieldsetView.$( '.wx-form-builder-radio-fieldset' ).append( radioGroupView.render().el );

			if ( properties.radioGroup == undefined || properties.radioGroup.length == 0 ) {
				radioFieldset.get( 'radioGroup' ).add( new wxApp.FormBuilderControlRadio() );
			} else {
				for ( var i = 0; i < properties.radioGroup.length; i++ ) {
					var option = new wxApp.FormBuilderControlRadio( properties.radioGroup[i] );
					radioFieldset.get( 'radioGroup' ).add( option );
				};
			}

			this.model.get( 'config' ).formElements.push( radioFieldset );
		},

		addCheckboxGroup: function() {
			this.addCheckboxGroupWithProperties( {} );
		},

		addCheckboxGroupWithProperties: function( properties ) {
			var checkboxFieldset = new wxApp.FormBuilderControlCheckboxFieldset( properties );
			var checkboxFieldsetView = new wxApp.FormBuilderControlCheckboxFieldsetView({
				model: checkboxFieldset
			});

			this.$( this.previewPaneSelector ).append( checkboxFieldsetView.render().el );

			var checkboxGroupView = new wxApp.FormBuilderControlCheckboxGroupView({
				collection: checkboxFieldset.get( 'checkboxGroup' )
			});

			checkboxFieldsetView.$( '.wx-form-builder-checkbox-fieldset' ).append( checkboxGroupView.render().el );

			if ( properties.checkboxGroup == undefined || properties.checkboxGroup.length == 0 ) {
				checkboxFieldset.get( 'checkboxGroup' ).add( new wxApp.FormBuilderControlCheckbox() );
			} else {
				for ( var i = 0; i < properties.checkboxGroup.length; i++ ) {
					var option = new wxApp.FormBuilderControlCheckbox( properties.checkboxGroup[i] );
					checkboxFieldset.get( 'checkboxGroup' ).add( option );
				};
			}

			this.model.get( 'config' ).formElements.push( checkboxFieldset );
		},

		/**
		 * Structure:
		 * Select Model
		 *     allowMultipleSelections
		 *     optionCollection (current Select Group)
		 *         model: Option
		 *
		 *         Option Model
		 *             (current Select Model)
		 */
		addSelect: function() {
			this.addSelectWithProperties( {} );
		},

		addSelectWithProperties: function( properties ) {
			var select = new wxApp.FormBuilderControlSelect( properties );
			var selectView = new wxApp.FormBuilderControlSelectView({
				model: select
			});

			// Add Select to preview pane
			this.$( this.previewPaneSelector ).append( selectView.render().el );

			var optionGroupView = new wxApp.FormBuilderControlOptionGroupView({
				collection: select.get('optionGroup')
			});

			// Add Option Group to Select
			selectView.$( '.wx-form-builder-select' ).append( optionGroupView.render().el );

			// Add an Option to the Option Group
			console.log( properties );
			if ( properties.optionGroup == undefined || properties.optionGroup.length == 0 ) {
				select.get('optionGroup').add( new wxApp.FormBuilderControlOption() );
			} else {
				for ( var i = 0; i < properties.optionGroup.length; i++ ) {
					var option = new wxApp.FormBuilderControlOption( properties.optionGroup[i] );
					select.get('optionGroup').add( option );
				};
			}

			// Add Select to control collection
			this.model.get( 'config' ).formElements.push( select );
		}

	});
})(jQuery);