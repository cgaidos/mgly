module.exports = (function ($) {
    /**
     * @param options
     * @param parent
     * @constructor
     */
    function Parentable(options, parent) {
        this.options = options;
        this.parent = parent;
    }

    /**
     *
     */
    Parentable.prototype.refresh = function () {
        var index, self = this;

        for (index in this.options) {
            var selector = this.options[index].selector;
            var classes = this.options[index].class;

            this._elements(selector).each(function () {
                self._append(this, classes);
            });
        }
    };

    /**
     * @private
     */
    Parentable.prototype._append = function (element, classes) {
        var parent = this._parent(classes);
        element = $(element);

        if (this._exists(element, classes) === false) {
            element.after(parent);
            parent.append(element);
        }
    };

    /**
     * Check whether exists
     *
     * @returns {*|HTMLElement}
     * @private
     */
    Parentable.prototype._exists = function (element, selector) {
        return element.parents(
                this._prepareSelectors(selector)
            ).length > 0;
    };

    /**
     * @returns {*|HTMLElement}
     * @private
     */
    Parentable.prototype._elements = function (selectors) {
        return $(selectors);
    };

    /**
     * @param selectors
     * @returns {string}
     * @private
     */
    Parentable.prototype._prepareSelectors = function (selectors) {
        return '.' + selectors.replace('.', '').split(' ').join(' .');
    };

    /**
     * @returns {*|HTMLElement}
     * @private
     */
    Parentable.prototype._parent = function (classes) {
        return $(this.parent).addClass(classes);
    };

    return Parentable;
})(jQuery);
