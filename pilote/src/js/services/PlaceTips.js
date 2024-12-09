import GoogleMapsLoader from './../libs/GoogleMapsLoader'
import $ from 'jquery'

/**
 * Send form via ajax
 */
export default class PlaceTips {
    constructor($input, $options, $callback = null) {
        this.input = $($input);
        this.options = $options;
        this.callback = $callback;

        this.input.each((index, input) => this.init(input))
    }

    init($input) {
        let autocomplete = new google.maps.places.Autocomplete($input, this.options);

        google.maps.event.addListener(autocomplete, 'place_changed', () => {
            let place = autocomplete.getPlace();
            let value = place.address_components[0] && place.address_components[0].short_name || '';

            if (typeof this.callback == 'function') {
                this.callback(this.input, place, value);
            }

            this.input.trigger('place_changed');
        })
    }
}

$(() => {
    GoogleMapsLoader.load((google) => {
        window.google = google;

        window.reloadGeolocale = () => {
            new PlaceTips(
                '.address_full',
                {types: ['(cities)'], event: 'place_changed'}
            );

            new PlaceTips(
                '.address_city, [name="address[city]"]',
                {types: ['(cities)'], event: 'place_changed'},
                ($input, $place, $value) => $input.val($value)
            );

            new PlaceTips('.address_country, [name="address[country]"]', {types: ['(regions)'], event: 'place_changed'}, ($input, $place, $value) => {
                let value = $place.address_components && $place.address_components[0]
                    ? $place.address_components.slice(-1)[0].long_name
                    : '';

                $input.val(value);
            });

            /**
             * Maps
             */
            $('a[href="#bh-housing"]').on('shown.bs.tab', function (e) {
                new libs.mapSearchable('#become-host-form [name*="address"]', '#become-host-map', {event: 'change place_changed'}, results => {
                    if (results && results[0]) {
                        let latlng = results[0].geometry.location;

                        $('#addres-longitude').val(latlng.lng());
                        $('#addres-latitude').val(latlng.lat());
                    }
                });
            });
        };

        $(document).on('reload-geolocale', e => reloadGeolocale()).trigger('reload-geolocale');
    });
});