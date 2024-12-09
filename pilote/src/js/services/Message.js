import $ from 'jquery'

/**
 * Send form via ajax
 */
export default class Message {
    constructor (messagePlace) {
        this.messagePlace = $(messagePlace);
    }

    template(type, message, close = true) {
        let closeButton = close === true
            ? '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
            : '';

        return `<div class="alert alert-`+type+` alert-dismissible" role="alert">
                    `+closeButton+`
                    `+message+`
                </div>`;
    }

    clear () {
        this.messagePlace.empty().fadeOut('fast');
    }

    message (type, message, close = true) {
        let template = this.template(type, message, close);
        this.messagePlace.append(template);
    }

    show () {
        setTimeout(() => this.messagePlace.fadeIn('fast'), 0);
    }

    success (message, close = true) {
        this.message('success', message, close);
    }

    error (message, close = true) {
        this.message('danger', message, close);
    }
}

