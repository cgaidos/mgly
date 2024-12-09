import moment from 'moment'
import Map from 'collections/map'
import List from 'collections/list'
import $ from 'jquery'
import Day from './Day'

export default class Calender {
    constructor ($start = null, $end = null, $options = {}) {
        this.options = Object.assign({}, {
            show_inputs: true
        }, $options);

        this.setDateRange($start, $end);
        this.days = new Map;
        this.body = {
            calender: $('<div class="calender">').attr('title', 'Click or shift + [mousemove]'),
            days: $('<ul class="days">'),
            head: $(`<ul class="days text-uppercase"><li>Mon</li><li>Tue</li><li>Wen</li><li>Thu</li><li>Fri</li><li>Sat</li><li>Sun</li></ul>`),
            inputs: $('<div class="calender-inputs">'),
        };

        // init
        this.generate();
        this.events();
    }

    /**
     * Assign events
     */
    events () {
        document.addEventListener('day-clicked', e => this.refreshRanges());
    }

    /**
     * Refresh events and others after generate
     */
    refresh () {
        this.days.forEach(day => day.refresh());
        this.events();
    }

    /**
     * @param $start
     * @param $end
     */
    setDateRange($start = null, $end = null) {
        var start = moment($start || new Date());
        var end = moment($end);

        // valid start
        if (start.isValid() === false) {
            throw 'The date of start id not valid'
        }

        // declare month
        var month = start.clone().startOf('month');

        // if end date is not defined generate calender for the entire month
        if (end.isValid() === false) {
            end = start.clone().endOf('month').weekday(7);
            start.startOf('month').weekday(1);
        }

        this.diff = end.diff(start, 'days');
        this.month = month;
        this.start = start;
        this.end = end;
    }

    /**
     * Get calender html
     */
    toString () {
        return this.body.calender.append(this.body.head).append(this.body.days).append(this.body.inputs);
    }

    /**
     * Refresh inputs with ranges
     */
    showInputs() {
        if (this.options.show_inputs !== true) return;

        this.body.inputs.empty().append(
            this.getInputs().toArray()
        );
    }

    /**
     * Get inputs
     *
     * @returns {List|*}
     */
    getInputs() {
        var inputs = new List;

        this.days.forEach(day => {
            day.getInput().forEach(input => {
                inputs.add(input)
            });
        });

        return inputs;
    }

    /**
     * Clear calender
     */
    clear () {
        this.body.inputs.empty();
        this.body.calender.empty();
        this.days.clear();
    }

    /**
     * Refresh range for all days
     */
    refreshRanges () {
        this.days.forEach(day => day.rangeUpdate());

        this.showInputs();
    }

    /**
     * Generate calender
     */
    generate () {
        this.clear();

        for (var i = 0; i <= this.diff; i++ ) {
            var date = this.start.clone().add(i, 'day');
            var options = {
                rangeable: date.isSame(this.month, 'month')
            };

            this.addDay(date, options);
        }

        setTimeout(() => this.refreshRanges(), 0);
    }

    /**
     * Add day to the calender
     *
     * @param $date
     * @param $options
     */
    addDay ($date, $options = {}) {
        // generate calender day
        var day = new Day($date, $options);

        // add the day to the list
        this.days.set(day.key(), day);

        // append day as html to the calender
        this.body.days.append(day.toString());
    }
}