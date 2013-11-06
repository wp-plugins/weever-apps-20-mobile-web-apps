
wxApp = wxApp || {};

(function($){
    wxApp.SubTabsView = Backbone.View.extend({
        tagName: 'ul',
        className: 'list-items-sortable list-items list-sub-items',

        attributes: function() {
            return {
                id: 'listItemsSortable' + this.model.get('id')
            }
        },

        initialize: function() {
            var me = this;
            // this.subTabTpl = _.template( $('#subtab-template').html() );
            this.model.get('subTabs').bind( 'add', me.addSubTab, me );
            Backbone.Events.on( 'tab:dropped', this.cancelSort, this );
        },

        events: {
            // TODO: Handle re-order event?
        },

        render: function() {
            this.$el.html('');
            this.startSortable();

            if ( this.model.getSubTabs().length ) {
                var me = this;
                this.model.getSubTabs().forEach(function(subTab) {
                    me.addSubTab(subTab);
                });
            }
            return this;
        },

        addSubTab: function(subTab) {
            var me = this;
            var view = new wxApp.SubTabView({ model: subTab });
            this.$el.append( view.render().el );
            subTab.on('destroy', function() {
                me.deleteSubTab(this);
            });
            this.refreshSortable();
        },

        startSortable: function() {
            var me = this;
            if ( undefined != this.$el.sortable ) {
                this.$el.sortable({
                    start: function(event, ui) {
                        console.log('trigger dragstart');
                        Backbone.Events.trigger( 'subtab:dragstart' );
                    },
                    stop: function(event, ui) {
                        console.log('trigger dragstop');
                        Backbone.Events.trigger( 'subtab:dragstop' );
                    },
                    update: function(event, ui) {
                        console.log('update');
                        var order = String( $(this).sortable('toArray').map( function(element) {
                            return element.replace('SubtabID', '');
                        }) );
                        wx.makeApiCall( 'tabs/sort_tabs', { order: order }, function() {
                            me.setSubTabCollectionOrder( order.split(',') );
                            var firstTabId = order.split(',')[0];
                            var mainTabId = $(ui.item).data('backbone-view').model.get('parent_id');
                            if ( mainTabId != firstTabId )
                                Backbone.Events.trigger( 'tab:id-update', mainTabId, firstTabId );
                        });
                    },
                    helper: 'clone',
                    cursor: 'move',
                    handle: '.wx-subtab-movehandle',
                    cursorAt: { top: 0, left: 0 }
                });
            }
        },

        setSubTabCollectionOrder: function( order ) {
            this.model.get('subTabs').comparator = function(subTab) {
                return order.indexOf( String( subTab.get('id') ) );
            }
            this.model.get('subTabs').sort();
        },

        refreshSortable: function() {
            if ( undefined != this.$el.sortable )
                this.$el.sortable( 'refresh' );
        },

        cancelSort: function( tabId ) {
            var me = this;
            if ( undefined != this.$el.sortable && tabId == this.model.get('id') ) {
                me.$el.sortable( 'cancel' );
            }
        },

        deleteSubTab: function(subTab) {
            this.model.deleteSubTab(subTab);
        }
    });
})(jQuery);