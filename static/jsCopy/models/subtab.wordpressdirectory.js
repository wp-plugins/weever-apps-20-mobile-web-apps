
wxApp = wxApp || {};

(function($){
    wxApp.WordpressDirectorySubTab = wxApp.SubTab.extend({
        default_icon_id: 11,
        allowedLayouts: ['list'],
        typeDescription: 'Content: Directory',
        validateFeed: false,

        defaults: _.extend( {}, wxApp.SubTab.prototype.defaults,
            {
                title: 'Directory',
                icon: 'e800',
                icon_id: 11,
                type: 'WordpressDirectory',
                content: 'html',
                layout: 'list',
                config: { subtab_name: 'WordpressDirectorySubTab' }
            }
        )
    });

})(jQuery);