// require images not used in css
let requireImages = require.context("./../images", true, /.*\.(jpg|png|svg)$/);
requireImages.keys().map(requireImages);

let requireDevImages = require.context("./../img/dev", true, /.*\.(jpg|png|svg)$/);
requireDevImages.keys().map(requireDevImages);

// require js
window.$ = require("jquery");
require("jquery-ui/ui/widget");
require("jquery-ui/ui/widgets/mouse");

require("jquery-ui/ui/widgets/slider");
require("jquery-ui/themes/base/slider.css");
require("bootstrap-sass/assets/javascripts/bootstrap");

window.Bloodhound = require('bloodhound-js');

// declare locators
window.functions = {};
window.libs = {};
window.container = {};
window.resources = {};

// libs
window.libs.parentable = require('./libs/Parentable');
window.libs.checkedMarker = require('./libs/CheckedMarker');
window.libs.footerBottom = require('./libs/FotterBottom');
window.libs.activitesManager = require('./libs/ActvitesManager');
window.libs.circeChar = require('./libs/CirceChart');
window.libs.circleRangeChart = require('./libs/CircleRangeChart');
window.libs.placeTips = require('./services/PlaceTips').default;
window.libs.tags = require('./services/Tags').default;
window.libs.list = require('./services/List').default;
window.libs.mapSearchable = require('./services/MapSearchable').default;
window.libs.options = require('./services/Options').default;
window.libs.listLoader = require('./services/ListLoader').default;
window.libs.inputCalendar = require('./services/CalednarInput').default;
window.libs.typeahead = require('./services/typeahead');
window.libs.cropp = require('./services/Cropp').default;

window.libs.calender = require('./services/calender').default;
window.libs.calenderNav = require('./services/calender').nav;

window.libs.multiselect = require('./services/Multiselect');

// controllers
window.fileController = require('./controllers/FileController').default;
window.sliderController = require('./controllers/SliderController').default;
window.controllerAjaxForm = require('./services/AjaxForm').controllerAjaxForm;
window.certificateController = require('./controllers/CertificateController');

/**
 * Styles
 */
require('sass/main.scss');


