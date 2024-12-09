import FilePreview from '../services/FilePreview'
import {FormFile} from '../services/FormFileList'
import List from 'collections/list'

/**
 * @param event
 * @param $input
 * @param $callback
 * @param $options
 */
export default function (event, $input, $callback, $options) {
    var options = Object.assign({}, {
        name: 'file',
        multiple: false
    }, $options);
    var form = $input.form;
    var files = new List($input.files);

    // only files
    files.filter((file) => {return file instanceof File});

    files.forEach((file) => {
        // assing file to form object
        var formFile = new FormFile(form, file, options);

        // generate preview and call callback
        new FilePreview(file, (imgData) => $callback(imgData, formFile));
    });

    // reset input
    $input.value = '';
}