import FileController from './FileController'
import List from 'collections/list'
import CertificatesResource from './../resources/Certificates'
import fileController from './../controllers/FileController'

export default function (event, context) {}

export function list($callbackList, $callbackPreview) {
    var list = $('<ul class="list-inline list-gutter-40">');
    var item = $('<li class="item permanent-file-input">');
    var checkmark = $('<i class="checkmark green">');
    var icoPrefix = 'cert-80-';

    item.append(checkmark);

    CertificatesResource.get({}, certs => {
        (new List(certs)).forEach(cert => {
            var certTemplate = item.clone().addClass(icoPrefix + cert.cer_code);
            var input = $('<input type="file">');
            var button = $('<button type="button" class="remove">');

            certTemplate['button'] = button;

            var filePreview = (urlData, formFile) => {
                if (typeof $callbackPreview === 'function') {
                    $callbackPreview(urlData, formFile, certTemplate)
                }
            };

            input.bind('change', e => {
                fileController(e, e.target, filePreview, {name: 'certificates['+cert.cer_code+']'})
            });

            // append
            certTemplate.append(input).append(button);
            list.append(certTemplate);
        });

        if (typeof $callbackList === 'function') $callbackList(list);
    });
}