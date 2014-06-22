(function ($) {

    var gf_placeholder = function () {

        var support = (!('placeholder' in document.createElement('input'))); // borrowed from Modernizr.com
        if (support && jquery_placeholder_url) {

            $.ajax({
                cache:true,
                dataType:'script',
                url:jquery_placeholder_url,
                success:function () {
                    $('input[placeholder], textarea[placeholder]').placeholder({
                        blankSubmit:true
                    });
                },
                type:'get'
            });
        }
    };

    $(document).ready(function () {

        //load placeholder support if we need it
        gf_placeholder();

        //for when we change forms on multi-forms
        $(document).bind('gform_page_loaded', gf_placeholder);
    });

})(jQuery);