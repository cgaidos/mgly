/**
 * Parentable
 */
window.container.parentable = new window.libs.parentable([{
    selector: 'input[type="checkbox"]',
    class: 'checkbox'
}, {
    selector: 'input[type="radio"]',
    class: 'radio'
}, {
    selector: 'select',
    class: 'select'
}], '<span>');
window.container.parentable.refresh();

/**
 * Select Marker
 */
window.container.selectmarker = new window.libs.checkedMarker(
    'input[type="checkbox"], input[type="radio"]',
    'span',
    'active',
    'inactive'
);

/**
 * Footer to bottom
 */
window.container.footerBottom = new window.libs.footerBottom('#page', '#footer');
window.container.footerBottom.refresh();

/**
 * Acitivites manager
 */
window.container.activitesManager = new window.libs.activitesManager('.activity');
$(function () {
    window.container.activitesManager.init();
});


$( function() {

    /**
     * Options manager
     */
    new libs.options(
        '#become-host-form #parents',
        '#become-host-form #parent-template',
        '#become-host-form #parents-options-placement'
    );

    new libs.options(
        '#become-host-form #kids',
        '#become-host-form #kid-template',
        '#become-host-form #kids-options-placement'
    );

    /**
     * Tooltip
     */
    $('[data-toggle="tooltip"]').tooltip({
        placement: 'top'
    });

    window.container.selectmarker.refresh();
} );


/**
 * Chars
 */
window.functions.loadChars = function () {
    $('.chart-circle').each(function(){
        var char = new window.libs.circeChar(this);
        char.create();
    });

    $('.chart-circle-range').each(function(){
        var char = new window.libs.circleRangeChart(this);
        char.create();
    });
};
window.onload = function() {
    window.functions.loadChars();
};

// resources

window.bind('load', () => {
    $('[data-ll-resource]').each((event, context) => {
        new libs.listLoader(context)
    });
});




