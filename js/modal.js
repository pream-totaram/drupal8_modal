(function($) {
    "use strict";
    $(document).ready(function () {
        $.ajax("http://localhost:8088/modal/content").then(function(res) {
            $(res).appendTo('body').modal();
        });
    }).load();
})(jQuery);