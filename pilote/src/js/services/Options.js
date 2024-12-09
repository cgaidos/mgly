var $ = require('jquery');
var tmpl = require('riot-tmpl').tmpl;

export default class Options {
    constructor ($input, $template, $place) {
        this.input = $($input);
        this.template = $($template);
        this.place = $($place);
        this.data = this.template.data('value');
        this.options = [];

        for (var index in this.data) {
            this.add(Object.assign({}, this.data[index], {index: this._prepareIndex()}));
        }

        this._tryRemove();
        this._watch();
    }

    _inputNumber () {
        return parseInt(this.input.val());
    }

    _currentNumber () {
        return Object.keys(this.options).length;
    }

    _prepareIndex () {
        return Math.round(new Date().getTime() + (Math.random() * 100));
    }

    _shouldAdd () {
        return this._inputNumber() > this._currentNumber();
    }

    _watch () {
        this.input.bind('change', () => {
            this._tryRemove();

            for (var i = 0; i < this._inputNumber(); i++) {
                if (this._shouldAdd()) this.add({index: this._prepareIndex()})
            }
        });
    }

    _tryRemove () {
        for (var optionIndex in this.options) {
            if (this._currentNumber() > this._inputNumber()) {
                this.options[optionIndex].remove();
                delete this.options[optionIndex];
            }
        }
    }

    add (data) {
        var option = $(tmpl(this.template.html(), data));

        this.options.push(option);
        this.place.append(option);
    }
}
