var GoogleLocations = require('google-locations');

var locations = new GoogleLocations('AIzaSyCPRU663hWHfUNr23tQhpssVATCg1xJ86s', {});

export default locations;

export function searchCoordinates(text, cb) {
    locations.autocomplete({input: this.fullAddress, types: "(cities)"}, (error, response) => {
        if (error !== null && !response.predictions[0].place_id) {
            cb(null);
        } else {
            locations.details({placeid: response.predictions[0].place_id}, function(err, response) {
                cb(response.result.geometry.location);
            });
        }
    });
}