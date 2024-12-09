import moment from 'moment'
import $ from 'jquery'
import List from 'collections/list'
import Map from 'collections/map'
import Set from 'collections/set'

/**
 * Day statuses
 *
 * @type {{available: string, unavailable: string}}
 */
var statuses = {
    available: 'available',
    unavailable: 'unavailable',
};
var Statuses = new List(statuses);

/**
 * Ranges
 *
 * @type {{from: string, to: string}}
 */
var ranges = {
    from: 'from',
    to: 'to',
};
var Ranges = new List(ranges);

/**
 * Day class
 */
export default class Day {
    constructor ($date, $attr) {
        this.attr = Object.assign({}, {
            rangeable: true,
            status: statuses.unavailable,
            inputName: 'availability'
        }, $attr);

        this.range = new Set;
        this.date = moment($date);
        this.input = new List;
        this.inputName = this.attr.inputName;
        this.template = $('<li>').text(this.date.format('D')).prop('calender_day', this);

        // validation
        if (this.date.isValid() === false) throw 'The date is not valid';

        // set default status
        this._setStatus(this.attr.status);
        
        this.init();
    }
    
    init() {
        // events
        if(this.attr.rangeable) {
            // remove old events
            this.template.unbind('click mouseenter');

            // refresh events
            this.template.bind('click', e => {
                this._toggleStatus();
                setTimeout(() => document.dispatchEvent(new Event('day-clicked')), 0);
            });
            this.template.bind('mouseenter', e => {if (e.shiftKey) this.template.click()});
        }  
    }
    
    refresh() {
        this.init();
    }

    key () {
        return String(this.date.format('DD')) + String(this.date.month()) + String(this.date.year());
    }

    unavailable () {
        this._setStatus(statuses.unavailable);
    }

    available () {
        this._setStatus(statuses.available);
    }

    isAvailable () {
        return this.checkStatus(statuses.available);
    }

    isUnavailable () {
        return this.checkStatus(statuses.unavailable);
    }

    prev () {
        return this.template.prev().prop('calender_day');
    }

    next () {
        return this.template.next().prop('calender_day');
    }

    rangeUpdate () {
        // check whether the status of siblings is the same
        var prevStatus = this.prev() ? this.prev().checkStatus(this.status) : null;
        var nextStatus = this.next() ? this.next().checkStatus(this.status) : null;

        // remove old range
        this.rangeRemove();

        // for one day
        if (prevStatus === false && nextStatus === false) {
            this.rangeFrom();
            this.rangeTo();
        }

        // for more days
        if (nextStatus === false) this.rangeTo();
        if (prevStatus === false) this.rangeFrom();

        setTimeout(() => document.dispatchEvent(new Event('day-range-updated')), 0);
    }

    rangeRemove () {
        this.range.clear();
        Ranges.forEach(range => this.template.removeClass(range));
    }

    rangeTo () {
        var range = Ranges.get('to');

        this.range.add(range);
        this.template.addClass(range);
    }

    rangeFrom () {
        var range = Ranges.get('from');

        this.range.add(range);
        this.template.addClass(range);
    }

    getInput () {
        // remove old inputs
        this.input.clear();

        // break if no avabilites
        if (this.checkStatus(statuses.available) === false) {
            return this.input;
        }

        var date = this.date.format('YYYY-MM-DD');
        var rangeFrom = Ranges.get('from');
        var rangeTo = Ranges.get('to');
        var nameFrom =  this.inputName + '[][' +rangeFrom+ ']';
        var nameTo =  this.inputName + '[][' +rangeTo+ ']';

        if (this.range.has(rangeFrom)) {
            this.input.add($('<input type="hidden">').attr('name', nameFrom).val(date));
        }

        if (this.range.has(rangeTo)) {
            this.input.add($('<input type="hidden">').attr('name', nameTo).val(date));
        }

        return this.input;
    }

    toString () {
        return this.template;
    }

    _toggleStatus() {
        if (this.isUnavailable()) {
            this.available()
        }
        else if (this.isAvailable()) {
            this.unavailable()
        }

        setTimeout(() => document.dispatchEvent(new Event('day-status-toggle')), 0);
    }

    _setStatus ($status) {
        this.status = $status;
        this.rangeUpdate();

        Statuses.forEach(status => this.template.toggleClass(status, this.checkStatus(status)));
    }

    checkStatus ($status) {
        return this.status === $status;
    }
}