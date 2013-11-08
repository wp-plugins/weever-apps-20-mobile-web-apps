
wxApp = wxApp || {};

(function($){
    wxApp.FoursquarePhotosSubTabEditView = wxApp.SubTabEditView.extend({
        subTabEditTplSelector: '#foursquarephotos-subtab-edit-template',

        setModelFromView: function(model) {
            if ( this.$('.wx-edit-input') )
                model.setConfig('venue_id', this.$('.wx-edit-input').val());
            return model;
        }
    });
})(jQuery);
