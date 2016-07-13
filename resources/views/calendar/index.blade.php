@extends('header')

@section('title', '日程')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/common.css') }}"/>
    <link href='{{ asset('/css/fullcalendar.min.css') }}' rel='stylesheet' />
    <link href='{{ asset('/css/fullcalendar.print.css') }}' rel='stylesheet' media='print' />
    <style>
        #calendar {
            max-width: 900px;
            margin: 5px auto;
        }
    </style>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small nav-fixed">
        <li role="presentation" class="active"><a href="#">日程</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('calendar/index') }}" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <form class="navbar-form navbar-left" role="search" method="get" action="{{ url('company/search') }}">
                    <div class="form-group">
                        {{--使用js实现？--}}
                        指定日期：<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="text" class="form-control" placeholder="格式：2016-6-4">&nbsp;
                    </div>
                    <button type="submit" class="btn btn-primary">确定</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="function-area">
        <button class="btn btn-info btn-sm">导出到文件</button>
    </div>
@endsection
@section('content')
    <div id='calendar'></div>
@endsection
@section('bottom')

@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script src='{{ asset('/js/moment.min.js') }}'></script>
    <script type="text/javascript" src="{{ asset('/js/fullcalendar.min.js') }}"></script>
    <script src='{{ asset('/js/lang-all.js') }}'></script>
    <script>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                theme: false,
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek'
                },
                lang:'zh-cn',
                // defaultDate: '2015-02-12',
                selectable:true,
                selectHelper: true,

                select: function(start, end) {


//                    $(this).ondblclick(alert(1));
                   /* var title = prompt('Event Title:');
                    var eventData;
                    if (title) {
                        eventData = {
                            title: title,
                            start: start,
                            end: end
                        };
                        $('#calendar').fullCalendar('renderEvent', eventData, true); // stick? = true
                    }
                    $('#calendar').fullCalendar('unselect');*/
                },
                minTime:'07:00:00',
                maxTime:'18:00:00',
                timeFormat:'H:mm',
                views: {
                    day: { // name of view
                        titleFormat: 'YYYY, MM, DD',
                        timeFormat:'H:mm'

                        // other view-specific options here
                    }
                },
                editable: false,
                eventLimit: true, // allow "more" link when too many events
                events: [
                    {
                        title: '当天的事情1',
                        start: '2016-06-05'
                    },
                    {
                        title: '几天内的事情',
                        start: '2016-06-01',
                        end: '2016-06-10'
                    },
                    {
                        title: '测试2',
                        start: '2016-06-07T10:30:00',
                        end: '2016-06-07T12:30:00'
                    },
                    {
                        title: '优先级高的事情',
                        start: '2016-06-06T12:00:00',
                        color:'#ff0000'
                    }
                ]
            });

        });

    </script>
@endsection