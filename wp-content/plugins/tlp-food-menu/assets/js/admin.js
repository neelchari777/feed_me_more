(function ($) {

    "use strict";

    $(document).ready(function () {
        $(".rt-tab-nav li:first-child a").trigger('click');
    });

    if ($(".fmp-select2").length) {
        $("select.fmp-select2").select2({
            minimumResultsForSearch: Infinity
        });
    }
    if ($(".fmp-color").length) {
        $(".fmp-color").wpColorPicker();
    }

    /* rt tab active navigation */
    $(".rt-tab-nav li").on('click', 'a', function (e) {
        e.preventDefault();
        var container = $(this).parents('.rt-tab-container');
        var nav = container.children('.rt-tab-nav');
        var content = container.children(".rt-tab-content");
        var $this, $id;
        $this = $(this);
        $id = $this.attr('href');
        content.hide();
        nav.find('li').removeClass('active');
        $this.parent().addClass('active');
        container.find($id).show();
    });

    $("#fmp-settings-form").on('submit', function (e) {
        e.preventDefault();
        var form = $(this),
            arg = form.serialize(),
            btnHandler = $("#fmp-saveButton"),
            response_wrapper = form.next('.rt-response');
        response_wrapper.hide();
        RtFmAjaxCall(btnHandler, 'fmpSettingsUpdate', arg, function (data) {

            $("#fmp-settings-form").trigger('fmp_update_settings_form', data);

            if (!data.error) {
                response_wrapper.removeClass('error').addClass('success');
                response_wrapper.show('slow').text(data.msg);
            } else {
                response_wrapper.addClass('error').removeClass('success');
                response_wrapper.show('slow').text(data.msg);
            }
        });
    });

    $("#fmp_source").on("click", "input[type='radio']", function () {
        var self = $(this),
            source = self.val();
        if (source) {
            var target = $("#fmp_categories_holder select"),
                targetWrap = self.parents(".field");
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {
                    action: "fmp_sc_source_change",
                    source: source
                },
                beforeSend: function () {
                    targetWrap.append('<span class="fmp-loading fmp-animate-spin dashicons dashicons-update"></span>');
                },
                success: function (response) {
                    //console.log(response);
                    target.html(response.cat_list).val('').trigger('change');
                    targetWrap.find('.fmp-loading').remove();
                },
                error: function (jqXHR, exception) {
                    targetWrap.find('.fmp-loading').remove();
                    if (jqXHR.status === 0) {
                        alert('Not connect.\n Verify Network.');
                    } else if (jqXHR.status == 404) {
                        alert('Requested page not found. [404]');
                    } else if (jqXHR.status == 500) {
                        alert('Internal Server Error [500].');
                    } else if (exception === 'parsererror') {
                        alert('Requested JSON parse failed.');
                    } else if (exception === 'timeout') {
                        alert('Time out error.');
                    } else if (exception === 'abort') {
                        alert('Ajax request aborted.');
                    } else {
                        alert('Uncaught Error.\n' + jqXHR.responseText);
                    }
                }
            });
        }
    });

    window.RtFmAjaxCall = function(element, action, arg, handle) {
        var data;
        if (action) data = "action=" + action;
        if (arg) data = arg + "&action=" + action;
        if (arg && !action) data = arg;
        var n = data.search(fmp_var.nonceID);
        if (n < 0) {
            data = data + "&" + fmp_var.nonceID + "=" + fmp_var.nonce;
        }
        $.ajax({
            type: "post",
            url: ajaxurl,
            data: data,
            beforeSend: function () {
                $('body').append($("<div id='fmp-loading'><span class='fmp-loading'>Updating ...</span></div>"));
            },
            success: function (data) {
                console.log(data);
                jQuery("#fmp-loading").remove();
                handle(data);
            }
        });
    }

})(jQuery);