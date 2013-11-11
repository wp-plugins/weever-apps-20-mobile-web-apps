/*
  Showoff 0.1.1
  Built by Nick Giancola: https://github.com/patbenatar
  Details and source: https://github.com/patbenatar/showoff
  Demo: https://patbenatar.github.com/showoff
*/

// Generated by CoffeeScript 1.3.3
(function() {
  var $, Showoff,
    __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

  $ = jQuery;

  $.fn.extend({
    showoff: function(options) {
      return $(this).each(function() {
        return new Showoff($(this), options);
      });
    }
  });

  Showoff = (function() {

    Showoff.prototype.settings = {
      destination: null,
      onNoBrowserSupport: null,
      filetypeMatcher: /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i,
      onInvalidFiletype: null,
      onFileReaderError: null,
      onDestinationUpdate: null
    };

    Showoff.prototype.$el = null;

    Showoff.prototype.fileReader = null;

    function Showoff($el, options) {
      this.$el = $el;
      if (options == null) {
        options = {};
      }
      this._onFileReaderLoad = __bind(this._onFileReaderLoad, this);

      this._onFileReaderError = __bind(this._onFileReaderError, this);

      this._onChange = __bind(this._onChange, this);

      this.options = $.extend({}, this.settings, options);
      if (!this.$el.is("input[type=file]")) {
        throw "You must initialize on a file upload field";
      }
      if (!((this.options.destination != null) && this.options.destination.is("img"))) {
        throw "Destination must be an img element";
      }
      if (!(window.File && window.FileReader && window.FileList && window.Blob)) {
        if (this.options.onNoBrowserSupport != null) {
          this.options.onNoBrowserSupport();
        }
        return;
      }
      if (typeof this.options.destination === "string") {
        this.options.destination = $(this.options.destination);
      }
      this.fileReader = new FileReader;
      this.fileReader.onload = this._onFileReaderLoad;
      this.$el.change(this._onChange);
    }

    Showoff.prototype._onChange = function(event) {
      var file;
      file = event.target.files[0];
      if (file != null) {
        return this._processFile(file);
      }
    };

    Showoff.prototype._processFile = function(file) {
      if ((this.options.filetypeMatcher != null) && !this.options.filetypeMatcher.test(file.type)) {
        if (this.options.onInvalidFiletype != null) {
          this.options.onInvalidFiletype(file.type);
        }
        return;
      }
      return this.fileReader.readAsDataURL(file);
    };

    Showoff.prototype._onFileReaderError = function(error) {
      if (this.options.onFileReaderError != null) {
        return this.options.onFileReaderError(error);
      }
    };

    Showoff.prototype._onFileReaderLoad = function(event) {
      return this._setDestinationSrc(event.target.result);
    };

    Showoff.prototype._setDestinationSrc = function(src) {
      this.options.destination.attr("src", src);
      if (this.options.onDestinationUpdate != null) {
        return this.options.onDestinationUpdate();
      }
    };

    return Showoff;

  })();

}).call(this);