+function ($) { "use strict";

    $(document).ready(function() {
        if ($('#Form-field-User-email').length > 0) {
            $('#Form-field-User-email').emailautocomplete({
                domains: ["seznam.cz", "octobercms.com", "mail.cz"]
            });
        }
    })

}(window.jQuery);
