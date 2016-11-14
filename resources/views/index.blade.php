<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="{{ asset('/css/index.css') }}" type="text/css" />
    <title>承包商公寓管理系统</title>
</head>
<body>
{{--头部区域--}}
{{--<div class="header_content">
    <div class="logo">
        <img src="{{ asset('/images/man_logo.jpg') }}" alt="logo" />
        <h3 class="nav_list">承包商公寓管理系统</h3>
    </div>
    <div class="right_nav">
        <div class="text_left">
        </div>
        <div class="text_right">
            <ul class="nav_return">
                <li><img src="{{ asset('/images/return.gif') }}" width="13" height="21" />&nbsp;返回 [
                    <a href="">待定1</a>|
                    <a href="">待定2</a> ]
                </li>
                <li> [<a href="">待定3</a>]</li>
                <li> [<a href="">退出</a>]&nbsp;&nbsp;</li>
            </ul>
        </div>
    </div>
</div>--}}
{{--头部区域结束--}}
{{--左侧区域--}}
<div id="left_content">
    <div id="user_info">欢迎！{{--您，<strong></strong>--}}<br />[{{--<a href="">系统管理员</a>，--}}<a href="{{ url('/logout') }}">退出</a>]</div>
    <div id="main_nav">
        <div class="list_item active">
            <div id="left_main_nav">
                <ul>
                    {{--<li class="left_back">个人日程</li>--}}
                    <li class="left_back">承包商公司</li>
                    <li class="left_back">房间管理</li>
                </ul>
            </div>
            <div id="right_main_nav">
{{--                <div class="list_title">
                    <span>个人日程</span>
                    <ul class="list_detail">
                        <li>
                            <a href="{{ url('calendar/index') }}" target="iframe">个人日程</a>
                        </li>
                    </ul>
                </div>--}}
                <div class="list_title">
                    <span>公司管理</span>
                    <ul class="list_detail">
                        <li>
                            <a href="{{ url('company/index') }}" target="iframe">所有公司</a>
                        </li>
                        <li>
                            <a href="{{ url('company/add') }}" target="iframe">新公司入住</a>
                        </li>
                        <li>
                            <a href="{{ url('company-log/index') }}" target="iframe">公司变动记录</a>
                        </li>
                    </ul>
                </div>
                <div class="list_title">
                    <span>房间管理</span>
                    <ul class="list_detail">
                        <li>
                            <a href="{{ url('room/living-room') }}" target="iframe">房间明细</a>
                        </li>
                        <li>
                            <a href="{{ url('room/dining-room') }}" target="iframe">餐厅</a>
                        </li>
                        <li>
                            <a href="{{ url('room/service-room') }}" target="iframe">服务用房</a>
                        </li>
                        <li>
                            <a href="{{ url('room/add') }}" target="iframe">添加房间</a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
        <div class="list_item">
            <div id="left_main_nav">
                <ul>
                    <li class="left_back">水电相关</li>
                </ul>
            </div>
            <div id="right_main_nav">
                <div class="list_title">
                    <span>水电费管理</span>
                    <ul class="list_detail">
                        <li>
                            <a href="{{ url('utility/index') }}" target="iframe">水电费明细</a>
                        </li>
                        <li>
                            <a href="{{ url('utility/base') }}" target="iframe">水电表底数</a>
                        </li>
                        <li>
                            <a href="{{ url('utility/add') }}" target="iframe">录入底数</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="list_item">
            <div id="left_main_nav">
                <ul>
                    <li class="left_back">罚单管理</li>
                </ul>
            </div>
            <div id="right_main_nav">

                <div class="list_title">
                    <span>罚单管理</span>
                    <ul class="list_detail">
                        <li>
                            <a href="{{ url('punish/uncharged-list') }}" target="iframe">未缴费</a>
                        </li>
                        <li>
                            <a href="{{ url('punish/charged-list') }}" target="iframe">已缴费</a>
                        </li>
                        <li>
                            <a href="{{ url('punish/canceled-list') }}" target="iframe">已撤销</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="list_item">
            <div id="left_main_nav">
                <ul>
                    <li class="left_back">每月报表</li>
                </ul>
            </div>
            <div id="right_main_nav">
                <div class="list_title">
                    <span>每月报表</span>
                    <ul class="list_detail">
                        <li>
                            <a href="{{ url('sheet/index') }}" target="iframe">月报表</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="list_item">
            <div id="left_main_nav">
                <ul>
                    <li class="left_back">配置项</li>
                    <li class="left_back">用户与权限</li>
                </ul>
            </div>
            <div id="right_main_nav">
                <div class="list_title">
                    <span>配置项</span>
                    <ul class="list_detail">
                        <li>
                            <a href="{{url('config/index')}}" target="iframe">系统配置</a>
                        </li>
                        <li>
                            <a href="{{url('config/rent-type')}}" target="iframe">房间类型设置</a>
                        </li>
                    </ul>
                </div>
                <div class="list_title">
                    <span>用户与权限</span>
                    <ul class="list_detail">
                        <li>
                            <a href="{{ url('user/users') }}" target="iframe">用户列表</a>
                        </li>
                        <li>
                            <a href="{{ url('user/roles') }}" target="iframe">角色列表</a>
                        </li>
                        <li>
                            <a href="{{ url('register') }}" target="iframe">创建用户</a>
                        </li>
                        <li>
                            <a href="{{ url('user/create-role') }}" target="iframe">创建角色</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    {{--<div id="switchpic">
        <a href="javascript:;">
            <img src="{{ asset('images/switch_left.gif') }}" alt="隐藏左侧导航栏" id="ImgArrow" />
        </a>
    </div>--}}

</div>
{{--左侧区域结束--}}
{{--右侧区域--}}
<div id="right_content">
    <div id="nav">
        <ul>
            <li class="man_nav active">
                基础管理
                <p class="sub_message">
                    公司管理，房间管理等
                </p>
            </li>
            <li class="man_nav" >
                水电相关
                <p class="sub_message">
                    水电表录入、水电费计算、水电费明细等
                </p>
            </li>
            <li class="man_nav">
                罚款管理
                <p class="sub_message">
                    包含所有的罚款项目
                </p>
            </li>
            <li class="man_nav">
                文件管理
                <p class="sub_message">
                    管理必要的文件，如月报表
                </p>
            </li>
            <li class="man_nav">
                系统设置
                <p class="sub_message">
                    系统的各项配置
                </p>
            </li>
        </ul>
        {{--<div class="right_nav">
            <a href=""><img src="{{ asset('/images/return.gif') }}" width="13" height="21" />&nbsp;返回</a>
            <a href="">[待定1]</a>
            <a href="">[待定2]</a>
            <a href="">[待定3]</a>
            <a href="">[退出]</a>
        </div>--}}
    </div>
    <div id="sub_info">
        &nbsp;&nbsp;
        <img src="images/hi.gif" />&nbsp;
        <span id="show_text"></span>
    </div>
    <div id="man_zone">
        <iframe src="{{ url('welcome') }}" name="iframe" frameborder="0"></iframe>
    </div>
</div>
{{--右侧区域结束--}}


<script src="{{ asset('/js/jquery-1.11.3.js') }}"></script>
<script src="{{ asset('/js/index.js') }}"></script>
<script>
    //设置主内容区域以及左边栏的高度
    function setContentHeight() {
        var iManHeight = $(window).outerHeight() - $('.header_content').outerHeight() - $('#nav').outerHeight() - $('#sub_info').outerHeight() - 30;
        $('#man_zone').height(iManHeight);

        var iLeftHeight = $(window).outerHeight() - $('.header_content').outerHeight() - 12;
        $('#left_content').height(iLeftHeight);
    }
    setContentHeight();

    $(window).resize(setContentHeight);


</script>
</body>
</html>
