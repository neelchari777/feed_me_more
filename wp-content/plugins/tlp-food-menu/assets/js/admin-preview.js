(function ($) {

    $("#fmsc_sc_settings_meta").on('change', 'select,input', function () {
        renderFmpPreview();
    });
    $("#fmsc_sc_settings_meta").on("input propertychange", function () {
        renderFmpPreview();
    });

    $(document).ready(function () {
        renderFmpPreview();
    });

     window.renderFmpPreview = function() {
        var target = $("#fmsc_sc_settings_meta");
        if (target.length) {
            var data = target.find('input[name],select[name],textarea[name]').serialize();
            data = data + '&' + $.param({'sc_id': $('#post_ID').val() || 0});
            fmpPreviewAjaxCall(null, 'fmpPreviewAjaxCall', data, function (data) {
                if (!data.error) {
                    $("#fmp-preview-container").html(data.data);
                }
            });
        }
    }

    function fmpPreviewAjaxCall(element, action, arg, handle) {
        var data;
        if (action) data = "action=" + action;
        if (arg)    data = arg + "&action=" + action;
        if (arg && !action) data = arg;

        var n = data.search(fmp.nonceID);
        if (n < 0) {
            data = data + "&" + fmp.nonceID + "=" + fmp.nonce;
        }
        $.ajax({
            type: "post",
            url: fmp.ajaxurl,
            data: data,
            beforeSend: function () {
                $('#fmsc_sc_preview_meta').addClass('loading');
                $('.fmp-response .spinner').addClass('is-active');
            },
            success: function (data) {
                $('#fmsc_sc_preview_meta').removeClass('loading');
                $('.fmp-response .spinner').removeClass('is-active');
                handle(data);
            }
        });
    }

    if ($(".fmp-color").length) {
        var cOptions = {
            defaultColor: false,
            change: function (event, ui) {
                setTimeout(function(){
                    renderFmpPreview();
                },1);
            },
            clear: function () {
                renderFmpPreview();
            },
            hide: true,
            palettes: true
        };
        $(".fmp-color").wpColorPicker(cOptions);
    }

})(jQuery);