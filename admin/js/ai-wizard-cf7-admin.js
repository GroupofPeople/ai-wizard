(function ($) {
    $(document).ready(function () {
        $('input[type=checkbox][name="ai-wizard[active]"]').change(function () {
            if (this.checked) {
                $('div.ai-wizard-content').css("display", "block");
            } else {
                $('div.ai-wizard-content').css("display", "none");
            }
        });

        $(document).on('click', function (event) {
            var tooltips = $('.ai-wizard-popup');
            tooltips.each(function () {
                var tooltip = $(this);
                if (!tooltip.is(event.target) && tooltip.has(event.target).length === 0) {
                    tooltip.removeClass('open');
                }
            });
        });

        $('.popup-text').on('click', function (event) {
            event.stopPropagation();
            var tooltip = $(this).parent();
            tooltip.toggleClass('open');
        });
    });
})(jQuery);
