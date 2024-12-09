let resources = require('./../resources');

let List = require('collections/list');
let $ = require('jquery');

export default class ListLoader {
    constructor ($list) {
        this.list = $($list);
        this.resource = this.resolveResource(this.list.data('ll-resource'));

        var filter = this.list.data('ll-filter');

        this.filter = filter ? new List(filter) : new List();

        this.text = this.list.data('ll-text');
        this.key = this.list.data('ll-key');
        this.placeholder = this.list.attr('placeholder');

        this.resource.get({}, data => {
            if (typeof data === 'object') {
                this.prepareOptions(new List(data))
            } else {
                console.error('Response not is a object');
            }
        })
    }

    resolveResource ($resourceName) {
        try {return resources[$resourceName];} catch (error) {console.error(error);}
    }

    optionTemplate($text, $value = '') {
        return $('<option>').val($value).text($text);
    }

    prepareOptions($options) {
        this.list.empty();
        this.addOption(this.placeholder, '');

        // filter options
        this.filter.forEach(filter => {
            for (var key in filter) {
                $options = $options.filter(option => {
                    return option[key] == filter[key];
                });
            }
        });

        // add options
        $options.forEach(option => {
            this.addOption(option[this.text], option[this.key])
        });
    }

    addOption ($text, $key) {
        var template = this.optionTemplate($text, $key);
        this.list.append(template);
    }
}
