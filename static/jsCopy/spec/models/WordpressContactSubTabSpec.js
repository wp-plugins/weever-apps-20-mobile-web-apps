describe('WordpressContact SubTab', function() {
    beforeEach(function() {
        var model = new wxApp.WordpressContactsSubTab();
        this.model = model;
        wx.log( JSON.stringify( this.model.getConfig() ) );
    });

    afterEach(function() {
        this.model = null;
    });

	it('should have default title of Forms', function() {
		expect( this.model.get('title') ).toEqual('Contact');
	});

    it('should have default content of contact', function() {
        expect( this.model.get('content') ).toEqual('contact');
    });

    it('should have default layout of panel', function() {
        expect( this.model.get('layout') ).toEqual('panel');
    });

    it('should be published by default', function() {
        expect( this.model.get('published') ).toEqual(1);
    });

    it('should have default icon id', function() {
        expect( this.model.get('icon_id') ).toEqual( this.model.default_icon_id );
    });

});