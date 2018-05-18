$(function () {
    //初始化iframe高度
    var mainIframeHeight = $(window).height() - $('.main-header').outerHeight(true);
    $('#iframeWamp').height(mainIframeHeight);

    //打开默认页面
    $('.iframe-jump').each(function () {
        if ($(this).parent("li.active").length > 0) {
            $(this).trigger('click');
        }
    });
});

$(document).on('click', '.iframe-jump', function () {
    var href = $.trim($(this).attr('data-href'));
    if (!href) {
        return true;
    }

    //按钮选中和展开导航
    $('.iframe-jump').parent("li").removeClass('active');
    $(this).parent("li").addClass('active');
    if ($(this).parents("li.treeview").length) {
        $('li.treeview').removeClass('active');
        $(this).parents("li.treeview").addClass('active');
    }

    //地址写入iframe
    $('#iframeWamp iframe').attr('src', href);
});