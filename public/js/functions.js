//用法 popdown({'message':'hahaha'})

function popdown(e) {
    e.timeout = e.timeout || 1, popclose();
    e.status = e.status ? 1 : 0;
    if (e.status == 1) {
        var t = '<div id="popdown" style="background: #5bc0de;">' + e.message + "</div>";
    } else {
        var t = '<div id="popdown" style="background: #d9534f;">' + e.message + "</div>";
    }

    $("body").append(t), $("#popdown").animate({top: "-30px"}, 500).delay(1e3 * e.timeout).animate({top: "-100px"}, 500, "", function () {
        e.callback && e.callback()
    })
}
function popclose() {
    $("#popdown").remove()
}

function maskShow() {
    //添加并显示遮罩层
    $("<div id='mask'><p class='loading_icon'></p></div>")
        .appendTo("body");
    var height = $('#mask').height();
    var width = $('#mask').width();
    $('#mask').find('.loading_icon').css({
        'left':(width - 20) / 2,
        'top':(height - 20) / 2 - 32
    })
}

function maskHide() {
    $("#mask").remove();
}