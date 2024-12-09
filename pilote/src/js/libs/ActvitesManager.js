var ActvitesPopover = require('./ActvitesPopover');

module.exports = (function($, ActvitesPopover){
    /**
     * @param selector
     * @constructor
     */
    function ActvitesManager(selector) {
        this.selector = selector;
    }

    /**
     * Init
     */
    ActvitesManager.prototype.init = function () {
        var self = this;

        this.get().each(function() {
            new ActvitesPopover(this);
        });
    };

    /**
     * Get activites managers
     *
     * @returns {*|HTMLElement}
     */
    ActvitesManager.prototype.get = function () {
        return $(this.selector);
    };

    return ActvitesManager;

})(jQuery, ActvitesPopover);

