let $ = require('jquery');

/**
 *
 * @param $selector
 * @param $options
 */
export default class SliderController {
    constructor ($selector, $options, $callback) {
        this.temp1 = this._template();
        this.temp2 = this._template();
        this.callback = $callback;

        this.options = Object.assign({}, {
            create: ( event, ui ) => {
                setTimeout(() => {
                    this.showPreview();
                    this.refreshPreview();
                }, 0)
            },
            slide: ( event, ui ) => {
                setTimeout(() => {
                    this.refreshPreview();
                }, 0)
            }
        }, $options);

        this.slider = $($selector).slider(this.options).slider( "instance" );
        this.range = this.slider.option('range');
    }

    /**
     * @returns {*|jQuery}
     */
    _template () {
        return $('<span>').css({
            position: 'absolute',
            width: '50px',
            textAlign: 'center',
            top: '20px',
            left: '-18px',
            color: '#1e89db'
        });
    }

    showPreview () {
        let slideHandlers = this.slider.element.find('.ui-slider-handle');

        slideHandlers.eq(0).append(this.temp1);
        this.range && slideHandlers.eq(1).append(this.temp2);
    }

    refreshPreview () {
        let value;

        // for range slider
        if (this.range) {
            value = this.slider.values();

            this.temp1.text(value[0]);
            this.temp2.text(value[1]);
        }
        // load value when not range
        else {
            value = this.slider.value();

            this.temp1.text(value);
        }

        // callback
        if(this.callback) this.callback(value)
    }
}