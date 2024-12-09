module.exports = (function($){

    /**
     * @param body
     * @param footer
     * @constructor
     */
    function FooterBottom(body, footer) {
        this.body = $(body);
        this.footer = $(footer);
    }

    /**
     * init
     */
    FooterBottom.prototype.refresh = function () {
        this._setPosition();
        this._events();
    };

    /**
     * @private
     */
    FooterBottom.prototype._events = function () {
        var self = this;

        var handler =  function () {
            self._setPosition();
        };

        $(window).unbind('resize', handler).bind('resize', handler);
    };

    /**
     * @private
     */
    FooterBottom.prototype._setPosition = function () {
        if (this._needed()) {
            this.body.css({
                minHeight: "calc(100vh - "+this.footer.outerHeight(true)+'px)'
            });
        } else {
            this.body.css({
                minHeight: ''
            });
        }
    };

    /**
     * @returns {boolean}
     * @private
     */
    FooterBottom.prototype._needed = function () {
        return window.innerHeight > this.body.outerHeight(false)
    };

    return FooterBottom;
})(jQuery);

