$(document).ready(function(){

    // Shows the table with fadeIn animation
    $('#contentFadeIn').fadeIn(700);

    // Once the table has loaded up, gets the scrollable size in pixels for the whole document and determines if the arrowDown icon must be showed up or not    
    $('#contentFadeIn').ready(function(){
        // Declares two vars which we'll use later on in order to improve code's legibility and overall smoothness
        var scrollAvail = $(document).height();
        var scrollPosition = $(window).height() + $(window).scrollTop();

        // Updates scrollPosition without delay (throttle) on scroll
        $(window).on('scroll', function(){
            scrollPosition = $(window).height() + $(window).scrollTop();
        });

        // On documents load, if scroll available is at least twice the height of the window, shows the goBottom arrow
        if(scrollAvail >= ($(window).height() * 2)){
            $(arrowDown).fadeIn(1000, function(){
                // On documents load, is current scroll position is nearly at bottom, shows the goTop arrow but on but only if the goBottom arrow was ever showed once
                if(scrollPosition >= (scrollAvail - (scrollAvail / 5))) {
                    $(arrowUp).fadeIn(1000);
                }
            });

            // On window's scroll, check if scroll position is nearly at bottom and if it does, shows the goTop arrow. Otherwise, hides it
            $(document).on('scroll', _.throttle(function() {
                if(scrollPosition > (scrollAvail - ($(window).height() / 4))) {
                    $(arrowUp).fadeIn(1000);
                }
                else{
                    $(arrowUp).fadeOut(100);
                }
            }, 350));
        }

        /* Throttle added because otherwise, hundreds of events are triggered on every single mouse wheel scroll. 
            We need to reduce these events to expected application behaviour  */
        $(window).on('scroll', _.throttle(function() {
            // Throtte some actions, in that case, call an auxilliar function every .35s
            showOrHideArrow();
            console.log("SCROLLING...")
            // console.log($(window).scrollTop(), $(document).height(), $(window).height(), $(document).height() + $(window).height())
        }, 350));

    });

    // Auxiliar function to check if arrowDown needs to be hidden or showed up, as the text appended to it
    function showOrHideArrow() {

        if($(window).scrollTop() >= $(window).height() / 3 && ($(document).height() != ($(window).scrollTop() + $(window).height()))){
            show();
            
            // if ($(document).height() == ($(window).scrollTop() + $(window).height())) {
            //     hide();
            // }
        }
        else{
            hide();
        }

        function show() {
            arrowDown.style.color = "orange";
            arrowDown.style.zIndex = 999;
            arrowDown.style.fontWeight = "800";
            arrowDown.style.borderRadius = "50%";
            arrowDown.style.paddingLeft = "20px";
            arrowDown.style.paddingRight = "20px";
            arrowDown.style.paddingBottom = "15px";
            arrowDown.style.paddingTop = "15px";
            arrowDown.style.background = "rgb(242, 243, 242, .15)";
            $('.hideableText').hide();
        }

        function hide() {
            $(arrowDown).css('color', 'black')
                .css('zIndex', 0)
                .css('fontWeight', 500)
                .css('borderRadius', 0)
                .css('padding', 0)
                .css('background', 'none')
            $('.hideableText').fadeIn(1200);
        }
    }

});

// Listening events for both goTop and goDown arrows/buttons
$(document).on('click', '#arrowDown', function(e){
    // Goes to end of document
    $('html, body').animate(
        {scrollTop: $(document).height()}, 200
    );
    $(arrowDown).fadeOut(100);
    // PreventDefault avoids the default action on click event, which is visiting the hyperlink and filling the URL with an indicator (#)
    e.preventDefault();
});

$(document).on('click', '#arrowUp', function(e){
    // Goes to top
    $('html, body').animate(
        {scrollTop: 0}, 200
    );
    // Shows the opposite arrow and hides itself
    $(arrowDown).fadeIn(1000);
    $(arrowUp).fadeOut(100);
    // PreventDefault avoids the default action on click event, which is visiting the hyperlink and filling the URL with an indicator (#)
    e.preventDefault();
});
