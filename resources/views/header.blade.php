<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>承包商公寓管理 - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('/bootstrap-3.3.5/css/bootstrap.css') }}"/>
    @yield('css')
    <script src="{{ asset('js/jquery-1.11.3.js') }}"></script>
</head>
<body>
<div id="header">
    @yield('header')
</div>
<div id="content">
    @yield('content')
</div>

<div id="bottom">
    @yield('bottom')
</div>

@yield('modal')

@yield('js')
<script>
    $('a').focus(function(){
        $(this).blur();
    })

    //计算content的top和bottom值
    var iTop = $('#header').height();
    var iBottom = 0;
    if ($('#bottom').html().trim() == '') {
        $("#bottom").css({
            'height':0,
            'border':'none',
            'padding' : 0
        })
    } else {
        iBottom = $('#bottom').height();
    }
    $('#content').css({
        'top': iTop,
        'bottom': iBottom
    })
</script>
</body>
</html>