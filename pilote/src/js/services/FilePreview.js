var $ = require('jquery');

export default class FilePreview {
    /**
     * @param $file
     * @param $callback
     * @param $options
     */
    constructor ($file, $callback, $options = {}) {
        this.oprions = Object.assign({}, {
            engine: 'objectUrl'
        }, $options);

        this.data = '';
        this.file = $file;
        this.callback = (typeof $callback === 'function' ? $callback : () => {});

        if (typeof this[this.oprions.engine] === "function") {
            this[this.oprions.engine]()
        }
    }

    isImage () {
        return this.file.type.indexOf('image') !== -1;
    }

    objectUrl () {
        this.data = window.URL.createObjectURL(this.file);

        this.callback(this.data);
    }

    fileReader () {
        if (this.isImage() === false) {
            return this.callback('');
        }

        var reader = new FileReader();

        reader.onload = (e) => {
            this.data = e.target.result;
            this.callback(this.data);
        };

        reader.readAsDataURL(this.file);
    }
}
