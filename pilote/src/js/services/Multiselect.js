import $ from 'jquery'
import sumoselect from 'sumoselect'

// require('sumoselect/sumoselect.css');

/**
 *
 */
export default class Multiselect {
    constructor($select) {
        this.select = $($select);
        this.preparedSelect = {};

        if (this.select.length > 0 && this.select.prop('tagName') !== 'SELECT') {
            return console.error('The select do not exists or is invalid');
        }

        if (this.select.length > 0) this.init();
    }

    init () {
        this.preparedSelect = this.select.SumoSelect({
            floatWidth: 'auto'
        });
    }
}

/**
 * Bootstrap
 */
$(() => new Multiselect('select[multiple]'));

