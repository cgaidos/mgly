import $ from 'jquery'

// require('cropper');
require('./cropper/dist/cropper.min.css');

/**
 * Send form via ajax
 */
export default class Cropp {
    /**
     * @param $src
     * @param $options
     */
    constructor ($src, $options = {}) {
        this.modal = this._modal();
        this.modal.body.appendTo('body');

        this.options = Object.assign({}, {
            viewMode: 2,
            aspectRatio: 1,
            preview: this.modal.preview
        }, $options);

        // init
        this.setSrc($src);

        // events
        this.modal.body.on('shown.bs.modal', e => this.create());
        this.modal.buttonRotateLeft.on('click', e => this.adapter().cropper('rotate', -90));
        this.modal.buttonRotateRight.on('click', e => this.adapter().cropper('rotate', 90));
        this.onSave(() => this.close());
    }

    /**
     * Open modal
     */
    open () {
        this.modal.body.modal('show');
    }

    /**
     * Close modal
     */
    close () {
        this.modal.body.modal('hide')
    }

    /**
     * Close modal
     */
    remove () {
        this.destroy();
        this.modal.body.remove();
    }

    /**
     * Refresh cropper
     */
    refresh () {
        this.destroy();
        this.create();
    }

    create () {
        this.modal.img.cropper(this.options);
    }

    /**
     * Destroy cropper
     */
    destroy () {
        this.modal.img.cropper('destroy');
    }

    /**
     * @param $callback
     */
    onSave($callback) {
        if (typeof $callback == 'function') {
            this.modal.buttonSave.on('click', e => $callback(this));
        }
    }

    /**
     * Set img src
     *
     * @param $src
     */
    setSrc ($src) {
        this.modal.img.attr('src', $src);
    }

    /**
     * @returns {jQuery}
     */
    adapter() {
       return this.modal.img;
    }

    /**
     * @returns {{body: jQuery, img: jQuery, preview: jQuery}}
     * @private
     */
    _modal () {
        let modal = $(`
            <div class="modal fade model-cropper" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-body text-center">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div class="clearfix"></div>

                    <div class="form-group">Preview</div>
                    <div class="preview-body display-inline-block">
                        <div class="preview"></div>
                    </div>

                    <div class="form-group mt-30">Source image</div>
                    <img class="img-responsive cropper-image" src="">

                  </div>
                  <div class="modal-footer btn-group-sm">
                    <button id="btn-rotate-left" type="button" class="btn btn-info width-auto">&nbsp;<i class="fa fa-undo" aria-hidden="true"></i>&nbsp;</button>
                    <button id="btn-rotate-right" type="button" class="btn btn-info width-auto">&nbsp;<i class="fa fa-repeat" aria-hidden="true"></i>&nbsp;</button>
                    <button id="btn-save" type="button" class="btn btn-success width-auto">Save</button>
                    <button id="btn-close" type="button" class="btn btn-danger width-auto" data-dismiss="modal" aria-label="Close">Close</button>
                  </div>
                </div>
              </div>
            </div>
        `).modal({
            show: false
        });

        return {
            body: modal,
            img: modal.find('img'),

            preview: modal.find('.preview'),
            previewBody: modal.find('.preview-body'),

            buttonSave: modal.find('#btn-save'),
            buttonClose: modal.find('#btn-close'),

            buttonRotateLeft: modal.find('#btn-rotate-left'),
            buttonRotateRight: modal.find('#btn-rotate-right'),
        };
    }

}