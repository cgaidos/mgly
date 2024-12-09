var tmpl = require('riot-tmpl').tmpl;
var $ = require('jquery');
var shortid = require('shortid');
var Collection = require("collections/map");

export default class List {
    constructor ($input, $template, $place, $editable = false) {
        this.input = $($input);
        this.editable = $editable;
        this.template = $template;
        this.place = $($place);
        this.list = new Collection;

        this.input.bind('change', () => this.fromInput(this.input));
    }

    _template ($name, $key) {
        var parameters = {
            editable: this.editable,
            index: shortid.generate(),
            name: $name,
            key: $key
        };

        if (typeof this.template == 'function') {
            return this.template(parameters, this);
        }

        return tmpl($(this.template).html(), parameters);
    }

    fromInput ($input) {
        // helper functions
        var select = $input => {
            var option = $input.find(':selected');
            this.add(option.text(), option.val());
        };

        var input = $input => {
            this.add($input.text(), $input.val());
        };

        // check tag
        if ($input.prop('tagName') === 'SELECT') select($input);
        if ($input.prop('tagName') === 'INPUT') input($input);

        // reset input
        $input.val(null);
    }

    remove ($name) {
        if  (this.list.has($name)) {
            this.list.get($name).remove();
            this.list.delete($name);
        }
    }

    add($name, $key) {
        if (this.list.has($name) === false) {
            var template = this._template($name, $key);

            this.list.add(template, $name);
            this.place.append(template);
        }
    }
}
