import $ from 'jquery'

require('typeahead.js');
require('../../../../../bower_components/typeahead.js/');

/**
 * Tips class
 */
export default class Typeahead {
    constructor($input, $source, $options) {
        this.input = $($input);
        this.source = $source;
        this.th = {};
        this.options = Object.assign({}, {
            displayKey: '',
        }, $options);

        this.create();
    }

    create () {
        this.th = this.input.typeahead(null, Object.assign({}, {
            source: this.source
        }, this.options));
    }
}
