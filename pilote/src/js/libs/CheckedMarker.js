module.exports = (function($){
    /**
     * @param selectors
     * @constructor
     */
    function SelectMarker(watch, parent, activeClass, inactiveClass) {
        this.watch = watch;
        this.parent = parent;
        this.activeClass = activeClass;
        this.inactiveClass = inactiveClass || '';
    }

    /**
     *
     */
    SelectMarker.prototype.refresh = function () {
        $(document).off('change', this.watch, $.proxy(this._handler, this));
        $(document).on('change', this.watch, $.proxy(this._handler, this));

        $(this.watch).trigger('change');
    };

    /**
     * @param event
     * @private
     */
    SelectMarker.prototype._handler = function (event) {
        var element = $(event.target);

        this._inactiveSiblings(element);
        this._setClasses(element);
    };

    /**
     * @param element
     * @private
     */
    SelectMarker.prototype._inactiveSiblings = function (element) {
        var self = this;
        var name = element.prop('name');

        if (name) {
            $('[name="'+name+'"]').not(element).each(function () {
                self._setClasses($(this));
            })
        }
    };

    /**
     * @param element
     * @private
     */
    SelectMarker.prototype._setClasses = function (element) {
        var selected = element.is(':selected') || element.is(':checked');

        $(element).toggleClass(this.activeClass, selected === true);
        $(element).parents(this.parent).toggleClass(this.activeClass, selected === true);

        $(element).toggleClass(this.inactiveClass, selected === false);
        $(element).parents(this.parent).toggleClass(this.inactiveClass, selected === false);
    };

    return SelectMarker;
})(jQuery);

