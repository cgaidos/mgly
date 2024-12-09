import Calender from './Calender'
import moment from 'moment'
import List from 'collections/map'
import $ from 'jquery'
import Day from './Day'

export default class Navigation {
    constructor ($currentMonth, $options = {} ) {
        this.options = Object.assign({}, {
            type: 'full',
            show_inputs: true,
        }, $options);

        this.currentMonth = moment($currentMonth || new Date());
        this.currentCalenderBody = $('<div>');
        this.type = this.options.type;
        this.months = new List;
        this.template = {
            body: $('<div class="calender-nav text-uppercase">').append(''),
            prev: $('<button type="button" class="prev">'),
            current: $('<div class="col-xs-6 text-center current">'),
            next: $('<button type="button" class="next">'),
            inputs: $('<div class="calender-inputs-nav">'),
        };

        // date validation
        if (this.currentMonth.isValid() === false) throw 'The date is not valid';

        // events
        this.template.prev.click(e => this.prev());
        this.template.next.click(e => this.next());
        document.addEventListener('day-range-updated', e => this.refreshInputs());

        // init
        this.refreshCalenderBody();
        this.refreshNavBody();
        this.refreshInputs();
    }

    getNavigationBody () {
        if (this.type === 'short') this.template.body.addClass('short');

        var col1 = $('<div class="col-xs-3 text-left">').append(this.template.prev);
        var col2 = this.template.current;
        var col3 = $('<div class="col-xs-3 text-right">').append(this.template.next);

        var row = $('<div class="row">').append(col1, col2, col3);

        return this.template.body.html('').append(row).append(this.template.inputs);
    }

    prev () {
        this.currentMonth.subtract(1, 'month');
        this.refreshCalenderBody();
        this.refreshNavBody();
    }

    next () {
        this.currentMonth.add(1, 'month');
        this.refreshCalenderBody();
        this.refreshNavBody();
    }

    getCalenderBody() {
        return this.currentCalenderBody;
    }

    refreshInputs () {
        if (this.options.show_inputs !== true) return null;

        // remove inputs
        this.template.inputs.html('');

        this.months.forEach(month => {
            this.template.inputs.append(
                month.getInputs().toArray()
            );
        })
    }

    refreshNavBody () {
        var current = this.currentMonth;
        var fakePrev = this.currentMonth.clone().subtract(1, 'month');
        var fakeNext = this.currentMonth.clone().add(1, 'month');

        this.template.prev.text(fakePrev.format('MMMM'));
        this.template.current.text(current.format('MMMM YYYY'));
        this.template.next.text(fakeNext.format('MMMM'));
    }

    refreshCalenderBody () {
        var key = this.monthKey();
        var calender = {};

        // get from cache
        if (this.months.has(key)) {
            calender = this.months.get(key);
            calender.refresh();
        }
        // create new and save to the cache
        else {
            calender = new Calender(this.currentMonth, null, {
                show_inputs: false
            });

            this.months.set(key, calender);
        }

        this.currentCalenderBody.html(calender.toString());
    }

    monthKey () {
        return this.currentMonth.year().toString() + this.currentMonth.month().toString()
    }
}

