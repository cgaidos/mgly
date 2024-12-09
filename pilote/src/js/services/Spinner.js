import $ from 'jquery'

/**
 * Send form via ajax
 */
export default class Spinner {
    constructor (context) {
        this.context = $(context);
        this.spinnerId = 'ajaxSpinner';
        this.spinnerIdSelector = '#'+this.spinnerId;
    }

    spinner () {
        if (this.context.css('position') == 'static') {
            this.context.css('position', 'relative');
        }

        let content = $('<div>').attr({
            id: this.spinnerId
        }).css({
            background: 'rgba(0, 0, 0, 0.5)',
            position: 'absolute',
            top: '0',
            left: '0',
            width: '100%',
            height: '100%'
        });

        let spinner = $('<i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>').css({
            position: 'absolute',
            top: 'calc(50% - 36px)',
            left: 'calc(50% - 36px)'
        });

        return content.append(spinner);
    }

    run () {
        this.spinner().appendTo(this.context);
    }

    clear () {
        this.context.find(this.spinnerIdSelector).remove();
    }

    clearAndRun () {
        this.clear();
        this.run();
    }
}

