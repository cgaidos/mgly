module.exports = (function($){

    /**
     * @param object
     * @constructor
     */
    function ActvitesPopover(object) {
        this.object = $(object);

        $(this.object).popover({
            content: this.toEdit() ? this.editTemplate() : this.showTemplate(),
            html: true,
            placement: 'bottom',
            viewport: { selector: "body", padding: 0 },
            trigger: 'hover click'
        });
    }

    /**
     *
     */
    ActvitesPopover.prototype.editTemplate = function () {
        return '<div class="tags text-center">' +
            '<div class="tag">Tenis <button class="remove"></div>' +
            '<div class="tag">Cinema <button class="remove"></button></div>' +
            '<div class="tag">Footbal <button class="remove"></div>' +
            '<div class="mt-25 text-center"><button class="btn btn-primary">Add an activity</button></div>' +
            '<div class="separator-horizontal dark mt-20 mb-20"></div>' +
            '<div class="text-center"><button class="btn btn-primary">Add a category</button></div>' +
            '</div>';
    };

    /**
     *
     */
    ActvitesPopover.prototype.showTemplate = function () {
        return '<div class="tags no-remove text-center">' +
            '<div class="tag">Tenis</div>' +
            '<div class="tag">Cinema</div>' +
            '<div class="tag">Footbal</div>' +
            '</div>';
    };

    ActvitesPopover.prototype.toEdit = function () {
        return this.object.data('edit');
    };

    return ActvitesPopover;
})(jQuery);

