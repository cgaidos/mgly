import Map from 'collections/map'
import shortid from 'shortid'

/**
 * Create file from base64 saved as dataurl
 * @param $dataURL
 * @param $name
 * @returns {File}
 */
const fileFromDataUrl = ($dataURL, $name) => {
    let arr = $dataURL.split(','), mime = arr[0].match(/:(.*?);/)[1];
    let bstr = atob(arr[1]);
    let n = bstr.length;
    let u8arr = new Uint8Array(n);

    while(n--) u8arr[n] = bstr.charCodeAt(n);

    return new File([u8arr], $name, {type:mime});
};

/**
 *
 */
export class FormFile {
    /**
     * @param $form
     * @param $file
     * @param $options
     */
    constructor ($form, $file, $options) {
        this.options = Object.assign({}, {
            aggregator: 'files',
            name: 'file',
            multiple: false
        }, $options);

        this.form = $form;
        this.form.files = this.form.files || new Map;
        this.id = shortid.generate();
        this.name = this._prepareName();
        this.aggregator = this._prepareAggregator();

        this.set($file);
    }

    set ($file) {
        this.file = $file;
        this.aggregator.set(this.name, this.file);
    }

    matchType (type) {
        return this.file.type.indexOf(type) !== -1;
    }

    files () {
        return this.aggregator;
    }

    remove () {
        if (this.exists()) {
            this.aggregator.delete(this.name);
        }
    }

    replace($data, $filename = 'image.jpg') {
        let file = '';

        if (typeof $data == 'string' && $data.indexOf('data:image') !== -1) {
            file = fileFromDataUrl($data, $filename);
        } else if ($data instanceof Blob) {
            file = $data;
        } else {
            throw new Error('The image data type is not known');
        }

        this.remove();
        this.set(file);
    }

    exists () {
        return this.aggregator.has(this.name);
    }

    _prepareAggregator () {
        var aggregatorIndex = this.options.aggregator;

        // get aggregator
        var aggregator = this.form.files.get(aggregatorIndex) || new Map();

        // update aggregator
        this.form.files.set(aggregatorIndex, aggregator);

        // clear if not multiple file input
        if (this.options.multiple === false) {
            aggregator.clear();
        }

        return aggregator;
    }

    /**
     * Prepare input name
     *
     * @returns {string}
     * @private
     */
    _prepareName () {
        if (this.options.multiple === true) {
            return  this.options.name + "["+ this.id +"]"
        }

        return this.options.name;
    }
}

export class FormFileList {
    /**
     * @param $form
     */
    constructor ($form) {
        this.form = $form;
        this._prepareAggregator();
    }

    _prepareAggregator () {
        if ((this.form.files instanceof Map) === false) {
            this.form.files = new Map();
        }
    }

    get () {
        return this.form.files;
    }
}
