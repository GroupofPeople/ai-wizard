(function ($) {
    if ($('input[type=radio][name="gofp-wpcf7-chatgpt[response-filter]"]:checked').val() === 'regex') {
        $("#container-response-filter-regex").css("display", "block");
    }
    $('input[type=radio][name="gofp-wpcf7-chatgpt[response-filter]"]').on('change', function () {
        if (this.value === 'regex') {
            $("#container-response-filter-regex").css("display", "block");
        }
        if (this.value !== 'regex') {
            $("#container-response-filter-regex").css("display", "none");
        }
    });
    $('#chatGPT-panel fieldset legend .mailtag').on('click', function () {
        var range = document.createRange();
        range.selectNodeContents(this);
        window.getSelection().addRange(range);
    });
    $('input[type=checkbox][name="gofp-wpcf7-chatgpt[max-tokens-bool]"]').change(function () {
        $('input[name="gofp-wpcf7-chatgpt[max-tokens]"]').prop('disabled', this.checked);
    });
    $('input[type=checkbox][name="gofp-wpcf7-chatgpt[active]"]').change(function () {
        if(this.checked){
            $('div.chat-gpt-page').css("display", "block");
        }else {
            $('div.chat-gpt-page').css("display", "none");
        }
    });
    $('input[type=range][name="gofp-wpcf7-chatgpt[temp]"]').on('input', function () {
        $('input[type=number][name="gofp-wpcf7-chatgpt[temp]-number"]').val($(this).val());
    });
    $('input[type=number][name="gofp-wpcf7-chatgpt[temp]-number"]').on('change', function () {
        if($(this).val() >= 0 && $(this).val() <=2){
            $('input[type=range][name="gofp-wpcf7-chatgpt[temp]"]').val($(this).val());
        }
        if($(this).val() > 2){
            $(this).val(2);
            $('input[type=range][name="gofp-wpcf7-chatgpt[temp]"]').val(2);
        }
        if($(this).val() < 0){
            $(this).val(0);
            $('input[type=range][name="gofp-wpcf7-chatgpt[temp]"]').val(0);
        }
    });

})(jQuery);
