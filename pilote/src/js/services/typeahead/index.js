import Typeahead from './Typeahead'
import * as resources from './../../resources'
import Bloodhound from 'bloodhound-js'

/**
 * Tips controller for activities
 *
 * @param $input
 * @constructor
 */
export function TypeaheadActivity($input) {
    let source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('act_en'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: resources.activities.endpoint,
    });

    let th = new Typeahead($input, source, {displayKey: 'act_en'});

    th.input.parents('.twitter-typeahead').addClass('width-full');
}