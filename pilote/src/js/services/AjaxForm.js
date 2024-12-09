import Map from 'collections/map'
import $ from 'jquery'

/**
 * Send form via ajax
 */
export default class AjaxForm {
    constructor ($form) {
        this.form = $form;
        this.formData = new FormData(this.form);
    }

    /**
     * Prepare form data
     *
     * @returns {*}
     * @private
     */
    _formData () {
        this._appendFiles();

        return this.formData;
    }

    _appendFiles () {
        let fileInputs = this.form.files || false;

        if (fileInputs && fileInputs instanceof Map) {
            // get inputs
            fileInputs.forEach(files => {

                //get files from inputs
                files.forEach((file, name) => {

                    // valid the file
                    if (file instanceof File && typeof name === 'string') {

                        // append to form data
                        this.formData.append(name, file);
                    }
                });
            });
        }
    }

    /**
     * Get send url
     *
     * @returns {*}
     * @private
     */
    _url () {
        return this.form.action;
    }

    /**
     * Get send url
     *
     * @returns {*}
     * @private
     */
    _method () {
        return this.form.method || 'post';
    }

    /**
     * Send form via ajax request
     *
     * @returns {*}
     */
    send (extend) {
        let options = Object.assign({}, {
            method: this._method(),
            data: this._formData(),
            contentType: false,
            cache: false,
            processData:false,
        }, extend);

        return $.ajax(this._url(), options)
    }
}

/**
 * Helper for ajax form send
 *
 * @param $form
 * @param $responseSelector
 */
import Message from './Message'
import Spinner from './Spinner'

export function controllerAjaxForm(event, $form, $responseSelector, $callback = null) {
   event.preventDefault();

   let ajax = new AjaxForm($form);
   let message = new Message($responseSelector);
   let spinner = new Spinner('body');

   ajax.send({beforeSend: () => {
       message.clear();
       spinner.clearAndRun()
   }, statusCode: {
       500: () => message.error('Something went wrong. Pleas try again and check your form data.')
   }}).always((data, status, xhr) => {

       // make a call if the callback exists
       $callback && setTimeout(()=> $callback(xhr, message), 0);

       // json
       let response = jQuery.parseJSON(data);

       // redirect
       if (!jQuery.isEmptyObject(response.targetUrl)){
           window.location.replace(response.targetUrl);
       }

       if(response.validation !== 'success'){
           message.error(response.message);
       }

       message.show();
       spinner.clear();
   });
}