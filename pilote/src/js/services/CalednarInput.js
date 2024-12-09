import $ from 'jquery'
import moment from 'moment'

require('bootstrap-datepicker');
require('bootstrap-datepicker/dist/css/bootstrap-datepicker3.standalone.css');
require('bootstrap-datepicker/dist/css/bootstrap-datepicker3.css');

/**
 *
 */
export default class CalednerInput {

    /**
     * @param $input
     * @param $calOptions
     */
    constructor($input, $calOptions) {
        this.input = $($input);

        this.calOptions = Object.assign({}, {
            format: 'yyyy-mm-dd',
            orientation: 'top',
        }, $calOptions);
    }

    /**
     *
     */
    init() {
        this.input.datepicker(this.calOptions);
    }

    setEnd ($date) {
        this.input.datepicker('setEndDate', $date);
    }

    setStart ($date) {
        this.input.datepicker('setStartDate', $date);
    }

    close () {
        this.input.datepicker('hide');
    }

    open () {
        this.input.datepicker('show');
    }
}

/**
 * @param $from
 * @param $to
 */
export function daterange($from, $to) {
    let from = new CalednerInput($from);
    let to = new CalednerInput($to);

    $($from).parents('.input-daterange').datepicker({
        format: 'yyyy-mm-dd',
        startDate: moment().toDate(),
        orientation: 'bottom',
    });

    from.input.on('changeDate', () => {
        from.close();
    });
}

daterange('.date_from', '.date_to');