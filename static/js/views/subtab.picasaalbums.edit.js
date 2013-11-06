
wxApp = wxApp || {};

(function($){
    wxApp.PicasaAlbumsSubTabEditView = wxApp.SubTabEditView.extend({
        subTabEditTplSelector: '#picasaalbums-subtab-edit-template',

        setModelFromView: function(model) {
            if ( this.$('.wx-edit-input') ) {
                model.setConfig('user_id', this.$('.wx-edit-input').val());
                model.setConfig('email', this.$('.wx-edit-input').val());
            }
            return model;
        }
    });
})(jQuery);
