$(function(){
    //初始化show_text的信息
    $('#show_text').html($('.man_nav').eq(0).find('.sub_message').html())
    //点击导航后改变导航栏目
    $('.man_nav').click(function(){
        var index = $(this).index();
        //改变当前导航栏目
        $('.man_nav').removeClass('active')
        $(this).addClass('active')
        //改变show_text的信息
        $('#show_text').html($(this).find('.sub_message').html())
        //改变左侧内容
        $('.list_item').removeClass('active');
        $('.list_item').eq(index).addClass('active');
/*        //取消最左侧竖导航的效果
        $('.left_back').removeClass('current');*/
    })

    //点击最左侧竖导航时，筛选
    $('.left_back').click(function(){
        var index = $(this).index();
        $('.left_back').removeClass('current');
        $(this).addClass('current');

        var oListTitles = $(this).parents('.list_item').find('.list_title');
        //alert(oListTitles.length)
        oListTitles.addClass('hide');
        oListTitles.eq(index).removeClass('hide');
    })

    //隐藏显示左侧边栏，TODO
    $('#switchpic').click(function(){
        //可以暂时不写
    })
})

