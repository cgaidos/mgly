import $ from 'jquery'
var tmpl = require('riot-tmpl').tmpl;

export default class Tags {
    constructor ($input, $place, $name, $editable = true) {
        this.input = $($input);
        this.place = $($place);
        this.name = $name;
        this.editable = $editable;
        this.tags = [];

        this.init();
    }

    init () {
        this.input.bind('change', ($event) => {
            this.fromInput($event.target);
            this.resetInput($event.target);
        });
    }

    refresh () {
        this.init();
    }

    _prepareIndex () {
        return Math.round(new Date().getTime() + (Math.random() * 100));
    }

    resetInput ($input) {
        $($input).val(null);
    }

    fromInput ($input) {
        var input = $($input);
        var name = input.val();
        var key = input.val();

        if (input.prop('tagName') === 'SELECT') {
            name = $($input).find(':selected').text();
        }

        this.add(name, key);
    }

    template($name, $key) {
        var tag = $('<span>').text($name).addClass('tag');
        var removeButton = $('<button type="button" class="remove">');
        var input = $('<input>').val($key).attr({
            type: 'hidden',
            name: this.name + '['+ this._prepareIndex() +']',
        });

        console.log($name, $key);

        if (this.editable === true) {
            removeButton.bind('click', e => this.remove($name));
            tag.append(input).append(removeButton)
        }

        return tag;
    }

    remove ($name) {
        if (this.tags[$name]) {
            this.tags[$name].remove();
            delete this.tags[$name];
        }
    }

    add($name, $key) {
        if ($name && !this.tags[$name]) {
            var temp = this.template($name, $key);
            this.place.append(temp);
            this.tags[$name] = temp;
        }
    }
}
