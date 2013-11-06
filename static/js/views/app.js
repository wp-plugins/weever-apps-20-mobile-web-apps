
wxApp = wxApp || {};

(function($){

	var escapeJSON = function( key, val ) {
		if ( typeof val !== 'string' ) {
            return val;
        }

		var replaced = encodeURIComponent( val );
		return replaced;
	};

	var toJSONrecursive = function() {
		return JSON.parse(JSON.stringify(this.attributes, escapeJSON));
	}

	var collectionToJSONrecursive = function() {
		var coll = [];
		this.models.forEach( function( model ) {
			coll.push( model.toJSONrecursive() );
		} );
		return JSON.parse(JSON.stringify(coll));
	}

//	Backbone.Model.prototype.toJSON = toJSONrecursive;
//	Backbone.Collection.prototype.toJSON = collectionToJSONrecursive;

	Backbone.Model.prototype.toJSONrecursive = toJSONrecursive;
	Backbone.Collection.prototype.toJSONrecursive = collectionToJSONrecursive;

    wxApp.App = Backbone.View.extend({
        el: '#toptabs',

        initialize: function() {
            this.allowAddContentButtonsToBeDragged();
            this.allowDroppingOnAddArea();
            // Backbone.Events.on( 'api:success', this.highlightAppPreviewRefresh, this );
            Backbone.Events.on( 'subtab:dragstart', this.showDropTab, this );
            Backbone.Events.on( 'subtab:dragstop', this.hideDropTab, this );
            Backbone.Events.on( 'tab:dropped', this.clearBodyStyles, this );
            Backbone.Events.on( 'tab:dropped', this.hideDropTab, this );
        },

        events: {
            'click #preview-refresh': 'refreshAppPreview'
        },

        allowAddContentButtonsToBeDragged: function() {
            if ( undefined != this.$('list-add-content-items li').draggable ) {
                this.$('.list-add-content-items li').draggable({
                    cursor: "move",
                    cursorAt: { top: 10, left: 10 },
                    helper: function( event ) {
                        return $( "<div class='ui-widget-draggable'>" + $(event.delegateTarget).find('span')[0].innerHTML + "</div>" );
                    },
                    revert: true
                });
            }
        },

        allowDroppingOnAddArea: function() {
            if ( undefined !== this.$('#addFeatureID').droppable ) {
                this.$('#addFeatureID').droppable( {
                    accept: ".list-add-content-items li",
                    hoverClass: "ui-state-hover",
                    drop: this.onDropOnAddArea
                } );
            }
        },

        onDropOnAddArea: function(event, ui) {
            // Using global wxApp.appView since this is the dropped on li
            wxApp.appView.createFeatureView($(ui.draggable).attr('id').replace('add-', ''));
        },

        createFeatureView: function(id, parentId) {
            if ( undefined !== wxApp[id + 'SubTab'] && undefined !== wxApp[id + 'SubTabEditView'] ) {
                var tab = new wxApp[id + 'SubTab']();
                if ( undefined != parentId && parentId )
                    tab.set( 'parent_id', parseInt( parentId ) );
                
                //tab.set( 'feature_name', id );
                var view = new wxApp[id + 'SubTabEditView']({ model: tab, el: '#wx-edit-area-' + id });
            } else {
                throw new Error('Invalid type ' + id);
            }
        },

        refreshAppPreview: function() {
            console.log('Refreshing...')
            wx.refreshAppPreview();
        },

        showDropTab: function() {
            $('#dropTab').show();
            $('.wx-layout-tablist .wx-tab').addClass('dragging');
        },

        hideDropTab: function() {
            $('#dropTab').hide();
            $('.wx-layout-tablist .wx-tab.dragging').removeClass('dragging');
        },

        clearBodyStyles: function() {
            $('body').attr('style', '');
        },

        changeFont: function(font) {
            f = font;
            wxApp.currentFont = font;

            var css = "@font-face { " +
            "    font-family: 'wxFont-1'; " +
            "    src: " +
            "        url('data:image/svg+xml;base64," + font.get('svg') + "') format('svg'), " +
            "        url('data:application/font-woff;charset=utf-8;base64," + font.get('woff') + "') format('woff'), " +
            "        url('data:application/x-font-ttf;charset=utf-8;base64," + font.get('ttf') + "') format('truetype'); " +
            "} " +
            ".wxFont-1:before { font-family: 'wxFont-1'; } ";

            $('#fontstyle').html( css );
        }
    });

    // Start app
    wxApp.appView = new wxApp.App();

    // Load the current icon font
    wx.makeApiCall('design/get_font_id', {}, function(data) {
        if ( typeof data.font_id !== 'undefined' ) {
            var font = new wxApp.IconFont();
            font.fetch( data.font_id, function() {
                // Put the font on the page.
                $('<style id="fontstyle" type="text/css"></style>').appendTo('head');
                wxApp.appView.changeFont( font );
            } );
        }
    });
})(jQuery);