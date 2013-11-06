
wxApp = wxApp || {};

(function($){
    wxApp.RSSSubTab = wxApp.SubTab.extend({
        default_icon_id: 6,
        typeDescription: 'RSS Feed',
        validateFeed: false,
        allowedLayouts: ['list'],

        defaults: _.extend( {}, wxApp.SubTab.prototype.defaults,
            {
                title: 'RSS Feed',
                icon_id: 6,
                type: 'rss',
                content: 'html',
                layout: 'list',
                config: { url: 'http://yourwebsite.com/' }
            }
        )
    });

})(jQuery);