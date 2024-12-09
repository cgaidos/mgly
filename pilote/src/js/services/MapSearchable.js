var $ = require('jquery');
var gmaps = require('gmaps');

/**
 * Send form via ajax
 */
export default class MapSearchable {
    constructor ($inputs, $place, $options = {}, $callback = null) {
        this.callback = $callback;
        this.map = {};
        this.inputs = $($inputs);
        this.place = $place;
        this.options = Object.assign({}, {
            event: 'change'
        }, $options);

        // init
        this.init();

        // events
        this.watch();
    }

    getAddress() {
        return this.inputs.map((index, obj) => {
            return String(obj.value).trim()
        }).get().join(' ');
    }

    update ($address) {
        gmaps.geocode({
            address: $address,
            callback: (results, status) => {
                if (status == 'OK') {

                    if (this.callback) this.callback(results);

                    var latlng = results[0].geometry.location;

                    this.map.setCenter(latlng.lat(), latlng.lng());
                    this.map.removeMarkers();
                    this.map.addMarker({
                        draggable: true,
                        lat: latlng.lat(),
                        lng: latlng.lng()
                    });
                }
            }
        });
    }

    watch () {
        this.inputs.bind(this.options.event, () => {
            this.update(this.getAddress());
        });
    }

    init () {
        this.map = new gmaps({
            el: this.place,
            lat: 0,
            lng: 0
        });
    }
}

