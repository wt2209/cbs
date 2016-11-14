<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>承包商公寓管理 - 日程)</title>
    <link rel="stylesheet" href="{{ asset('/bootstrap-3.3.5/css/bootstrap.css') }}"/>
    <link rel="stylesheet" href="{{ asset('/css/common.css') }}"/>
    <link href='{{ asset('/css/fullcalendar.min.css') }}' rel='stylesheet' />
    <link href='{{ asset('/css/fullcalendar.print.css') }}' rel='stylesheet' media='print' />
    <style>
        #calendar {
            max-width: 900px;
            margin: 5px auto;
        }
    </style>
    <script src="{{ asset('js/jquery-1.11.3.js') }}"></script>
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
</head>
<body>

{{--日历--}}
{{--<div id='calendar'></div>--}}

<h1>欢迎使用 :)</h1>

</body>
</html>




