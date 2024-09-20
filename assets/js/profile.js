// jquery
$(document).ready(function () {
    let __TRIMMEDDESC_ele = $("#TRIMMEDDESC");
    let __LONGDESC_ele = $("#LONGDESC");

    __TRIMMEDDESC_ele.find("#desc-readmore").click(function (e) { 
        e.preventDefault();
        __LONGDESC_ele.show();
        __TRIMMEDDESC_ele.hide();
    });

    __LONGDESC_ele.find("#desc-readless").click(function (e) { 
        e.preventDefault();
        __LONGDESC_ele.hide();
        __TRIMMEDDESC_ele.show();
    });
});