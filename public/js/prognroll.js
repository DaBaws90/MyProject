/* PrognRoll | https://mburakerman.github.io/prognroll/ | @mburakerman | License: MIT */
(function ($) {
    $.fn.prognroll = function (options) {

        var settings = $.extend({
            height: 4, // progress bar height
            color: "#50bcb6", // progress bar background color
            custom: false // if you make it true, you can add your custom div and see it's scroll progress on the page
        }, options);

        return this.each(function () {
            if ($(this).data('prognroll')) {
                return false;
            }
            $(this).data('prognroll', true);

            var $span = $("<span>", {
                class: "bar"
            });
            // $("body").prepend($span);
            $("body").append($span);

            $span.css({
                position: "fixed",
                // top: 0,
                bottom: 0,
                left: 0,
                width: 0,
                height: settings.height,
                backgroundColor: settings.color,
                zIndex: 9999999
            });

            if (settings.custom === false) {

                $(window).scroll(function (e) {
                    e.preventDefault();
                    var windowScrollTop = $(window).scrollTop();
                    var windowHeight = $(window).outerHeight();
                    var bodyHeight = $(document).height();

                    var total = (windowScrollTop / (bodyHeight - windowHeight)) * 100;

                    $(".bar").css("width", total + "%");
                });

            } else {

                $(this).scroll(function (e) {
                    e.preventDefault();
                    var customScrollTop = $(this).scrollTop();
                    var customHeight = $(this).outerHeight();
                    var customScrollHeight = $(this).prop("scrollHeight");

                    var total = (customScrollTop / (customScrollHeight - customHeight)) * 100;

                    $(".bar").css("width", total + "%");
                });

            }

            // get scroll position on on page load 
            var windowScrollTop = $(window).scrollTop();
            var windowHeight = $(window).outerHeight();
            var bodyHeight = $("body").outerHeight();

            var total = (windowScrollTop / (bodyHeight - windowHeight)) * 100;
            $(".bar").css("width", total + "%");

        });
    };
})(jQuery);