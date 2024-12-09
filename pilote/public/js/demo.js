ScrollRate = 100;
var DivElmnt;
var ScrollInterval;

function scrollDiv_init() {
	DivElmnt = document.getElementById('missiondiv');
	ReachedMaxScroll = false;

	DivElmnt.scrollTop = 0;
	PreviousScrollTop  = 0;

	ScrollInterval = setInterval('scrollDiv()', ScrollRate);
}

function scrollDiv() {

	if (!ReachedMaxScroll) {
		DivElmnt.scrollTop = PreviousScrollTop;
		PreviousScrollTop++;

		ReachedMaxScroll = DivElmnt.scrollTop >= (DivElmnt.scrollHeight - DivElmnt.offsetHeight);
	}
	else {
        ReachedMaxScroll = false;
		DivElmnt.scrollTop = 0;
		PreviousScrollTop  = 0;
	}
}

function pauseDiv() {
	clearInterval(ScrollInterval);
}

function resumeDiv() {
	PreviousScrollTop = DivElmnt.scrollTop;
	ScrollInterval    = setInterval('scrollDiv()', ScrollRate);
}

$(document).ready(function() {
    // Search sidebar activity
    $( "#slider" ).slider({
        min: 0,
        max: 100,
        value:0
    });

    $( "#slider" ).on( "slidechange", function( event, ui ) {} );

    $( "#slider" ).slider({
        slide: function( event, ui )
        {
            $('#search-form-sidebar [name="radius"]').val($('#slider').slider( "option", "value" ));
        }
    });

    $( ".demo-sidebar-activity #slider" ).slider({
        change: function( event, ui )
        {
            if ($('#slider').slider( "option", "value" ) >= 40) {
                $('.act03').removeClass('hide');
                $('.act04').removeClass('hide');
            } else {
                $('.act03').addClass('hide');
                $('.act04').addClass('hide');
            }
        }
    });

    // Search sidebar activity
    $('input[type=radio][name=adult]').change(function() {
        if (this.value == 'yes') {
            $('.stay004').addClass('hide');
        }
        else if (this.value == 'no') {
            $('.stay004').removeClass('hide');
        }
    });

    $('select').on('change', function() {
        $('.stay04').addClass('hide');
    })

    // Create activity
    $('.schedule input[type=radio][name=timetable]').on('change', function() {
       $('.schedule').find('input[type=text][name=place]').val('');
    })

    $('.periodic input[type=radio][name=periodicity]').on('change', function() {
        if (this.value == 'repeat') {
            $('.weeks').removeClass('hide');
        } else {
            $('.weeks').addClass('hide');
        }
    })
    // Scroll mission
   	DivElmnt = document.getElementById('missiondiv');
   	if (DivElmnt != null) {
   	    scrollDiv_init();
   	}
});
