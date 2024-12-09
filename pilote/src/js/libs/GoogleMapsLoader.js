var GoogleMapsLoader = require('google-maps');

GoogleMapsLoader.KEY = 'AIzaSyCcMENLVUSmjZruzuTe3Ls3C_dZVrUprIw';
GoogleMapsLoader.LIBRARIES = ['geometry', 'places'];
GoogleMapsLoader.LANGUAGE = 'en';

GoogleMapsLoader.onLoad(function (google) {
    window.google = google;
});

export default GoogleMapsLoader;