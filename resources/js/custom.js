$(function () {
    "use strict";
    $(function () {
        $(".preloader").fadeOut();
    });
    jQuery(document).on('click', '.mega-dropdown', function (e) {
        e.stopPropagation()
    });

    // ==============================================================
    // Auto select left navbar
    // ==============================================================
    $(function () {
        var url = window.location;
        var element = $('ul#sidebarnav a').filter(function () {
            return this.href == url;
        }).addClass('active').parent().addClass('active');
        while (true) {
            if (element.is('li')) {
                element = element.parent().addClass('in').parent().addClass('active');
            }
            else {
                break;
            }
        }

    });
    // ==============================================================
    //tooltip
    // ==============================================================
    $('.tooltips').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    })
        // ==============================================================
        //Popover
        // ==============================================================
    $(function () {
            $('[data-toggle="popover"]').popover()
        })
        // ==============================================================
        // Sidebarmenu
        // ==============================================================
    $(function () {
        $('#sidebarnav').metisMenu();
    });
    // ==============================================================
    // Slimscrollbars
    // ==============================================================

     // ==============================================================
    // Collapsable cards
    // ==============================================================
    $(document).on("click", ".card-actions a", function(e) {
    if (e.preventDefault(), $(this).hasClass("btn-close")) $(this).parent().parent().parent().fadeOut();
    });

    (function ($, window, document) {
        var panelSelector = '[data-perform="card-collapse"]';
        $(panelSelector).each(function () {
            var $this = $(this)
                , parent = $this.closest('.card')
                , wrapper = parent.find('.card-body')
                , collapseOpts = {
                    toggle: false
                };
            if (!wrapper.length) {
                wrapper = parent.children('.card-heading').nextAll().wrapAll('<div/>').parent().addClass('card-body');
                collapseOpts = {};
            }
            wrapper.collapse(collapseOpts).on('hide.bs.collapse', function () {
                $this.children('i').removeClass('fas fa-times').addClass('fas fa-plus');
            }).on('show.bs.collapse', function () {
                $this.children('i').removeClass('fas fa-plus').addClass('fas fa-times');
            });
        });
        $(document).on('click', panelSelector, function (e) {
            e.preventDefault();
            var parent = $(this).closest('.card');
            var wrapper = parent.find('.card-body');
            wrapper.collapse('toggle');
        });
    }(jQuery, window, document));
});

