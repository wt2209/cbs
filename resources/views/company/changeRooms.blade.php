@extends('header')

@section('title', '调整房间')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/company/changeRooms.css') }}"/>

@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="">调整房间</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('company/index') }}"><< 返回列表页</a>
        <a href="" class="refresh"></a>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <form id="form" method="post" action="{{ url('company/store-basic-info') }}">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="company_id" value="{{ $company->company_id }}">
            <input type="hidden" name="is_edit" value="1">
        </form>
        <table class="table table-hover table-condensed">
            <tr>
                <td width="30%" style="border-right: 1px #ddd solid" id="living">
                    居住用房：<br>
                </td>
                <td width="25%" style="border-right: 1px #ddd solid " id="dining">
                    餐厅用房：<br>
                </td>
                <td  id="service">
                    服务用房：<br>
                </td>
            </tr>
        </table>
    </div>
@endsection

@section('bottom')
    <button class="btn btn-success" id="submit">保存并选择房间</button>
@endsection
@section('js')
    {{-- 加载气泡效果js --}}
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script src="{{ asset('/js/jquery.validate.min.js') }}"></script>
    <script>
        var sRoomId = '';
        var sRoomType = '';
        var bStatus = false;
        $(function(){
            maskShow();
            $.get('{{ url('room/all-rent-type') }}', '', function(rentTypeData){
                var rentType = rentTypeData;
                $.get('{{ url('room/all-empty-room') }}', '', function(data){
                    var livingStr = '居住用房：<br>';
                    var diningStr = '餐厅用房：<br>';
                    var serviceStr = '服务用房：<br>';
                    @if ($livingRooms)
                        @foreach($livingRooms as $livingRoom)
                            livingStr += '<div class="col-lg-2">';
                            livingStr += '<div class="input-group">';
                            livingStr += '<label class="input-group-addon">';
                            livingStr += '<input type="checkbox" checked value="{{$livingRoom->room_id}}">&nbsp;{{$livingRoom->room_name}}';
                            livingStr += '</label>';
                            livingStr += '<select class="form-control" name="roomType[{{$livingRoom->company_id}}]">';
                            for (i=0;i<rentType['length']; i++) {
                                if (rentType[i]['rent_type_id'] == '{{$livingRoom->rent_type_id}}') {
                                    livingStr += '<option selected value="'+rentType[i]['rent_type_id']+'">'+rentType[i]['person_number']+'人间</option>;'
                                } else
                                    livingStr += '<option value="'+rentType[i]['rent_type_id']+'">'+rentType[i]['person_number']+'人间</option>;'
                            }
                            livingStr += '</select>';
                            livingStr += '<span class="input-group-addon">';
                            if ('1' == '{{$livingRoom->gender}}') {
                                livingStr += '<label class="no-bold"><input type="radio" checked value="1" checked name="gender[{{$livingRoom->room_id}}]">男</label>&nbsp;';
                                livingStr += '<label class="no-bold"><input type="radio" value="2" name="gender[{{$livingRoom->room_id}}]">女</label>';
                            } else if ('2' == '{{$livingRoom->gender}}') {
                                livingStr += '<label class="no-bold"><input type="radio" value="1" checked name="gender[{{$livingRoom->room_id}}]">男</label>&nbsp;';
                                livingStr += '<label class="no-bold"><input type="radio" checked value="2" name="gender[{{$livingRoom->room_id}}]">女</label>';
                            }
                            livingStr += '</span>';
                            livingStr += '</div>';
                            livingStr += '</div>';
                        @endforeach
                    @endif
                    if (data['living']) {
                        for (var i in data['living']) {
                            var current = data['living'][i]
                            livingStr += '<div class="col-lg-2">';
                            livingStr += '<div class="input-group">';
                            livingStr += '<label class="input-group-addon">';
                            livingStr += '<input type="checkbox"  value="'+current['room_id']+'">&nbsp;'+current['room_name'];
                            livingStr += '</label>';
                            livingStr += '<select class="form-control" name="roomType['+current['room_id']+']">';
                            for (i=0;i<rentType['length']; i++) {
                                livingStr += '<option value="'+rentType[i]['rent_type_id']+'">'+rentType[i]['person_number']+'人间</option>;'
                            }
                            livingStr += '</select>';
                            livingStr += '<span class="input-group-addon">';
                            livingStr += '<label class="no-bold"><input type="radio" value="1" checked name="gender['+current['room_id']+']">男</label>&nbsp;';
                            livingStr += '<label class="no-bold"><input type="radio" value="2" name="gender['+current['room_id']+']">女</label>';
                            livingStr += '</span>';
                            livingStr += '</div>';
                            livingStr += '</div>';
                        }
                    }
                    $('#living').html(livingStr);

                    @if ($diningRooms)
                        @foreach($diningRooms as $diningRoom)
                            diningStr+='<label class="no-bold">';
                            diningStr+='<input type="checkbox" checked value="{{$diningRoom->room_id}}">&nbsp;{{$diningRoom->room_name}}';
                            diningStr+='</label><br>';
                        @endforeach
                    @endif
                    if (data['dining']) {
                        for (var i in data['dining']) {
                            var current = data['dining'][i];
                            diningStr+='<label class="no-bold">';
                            diningStr+='<input type="checkbox" value="'+current['room_id']+'">&nbsp;'+current['room_name'];
                            diningStr+='</label><br>';
                        }
                    }
                    $('#dining').html(diningStr);

                    @if ($serviceRooms)
                        @foreach($serviceRooms as $serviceRoom)
                            serviceStr+='<label class="no-bold">';
                            serviceStr+='<input type="checkbox" checked value="{{$serviceRoom->room_id}}">&nbsp;{{$serviceRoom->room_name}}';
                            serviceStr+='</label><br>';
                        @endforeach
                    @endif
                    if (data['service']) {
                        for (var i in data['service']) {
                            var current = data['service'][i];
                            serviceStr+='<label class="no-bold">';
                            serviceStr+='<input type="checkbox" value="'+current['room_id']+'">&nbsp;'+current['room_name'];
                            serviceStr+='</label><br>';
                        }
                    }
                    $('#service').html(serviceStr);
                    maskHide()
                }, 'json')
            });

            $('#submit').click(function(){
                sRoomId = '';
                sRoomType = '';
                $('#living').find('input[type=checkbox]').each(function(){
                    if ($(this).prop('checked')) {
                        var iRoomId = $(this).val();
                        var iType = $(this).parents('.input-group').find('select').val();
                        var iGender;
                        $(this).parents('.input-group').find('input[type=radio]').each(function(){
                            if ($(this).prop('checked')) {
                                iGender = $(this).val();
                            }
                        });
                        sRoomId += iRoomId + '_';
                        sRoomType += iRoomId+'_'+iType+'_'+iGender+'|';
                    }
                })
                $('#dining').find('input[type=checkbox]').each(function(){
                    if ($(this).prop('checked')) {
                        sRoomId += $(this).val() + '_';
                    }
                })
                $('#service').find('input[type=checkbox]').each(function(){
                    if ($(this).prop('checked')) {
                        sRoomId += $(this).val() + '_';
                    }
                })

                sRoomId = sRoomId.substring(0, sRoomId.length - 1);
                sRoomType = sRoomType.substring(0, sRoomType.length - 1);
                var postStr = 'roomIds='+sRoomId+'&roomTypes='+sRoomType;
                if (bStatus) {
                    return false;
                }
                bStatus = true;
                maskShow();
                $.post('{{ url('company/store-selected-rooms') }}', $('#form').serialize()+"&"+postStr, function(e){
                    maskHide();
                    popdown({'message':e.message, 'status': e.status, 'callback':function(){
                        if (e.status) {
                            /*返回并刷新原页面*/
                            location.href = '{{ url("company/index") }}';
                        }
                    }});
                }, 'json');
            })
        })
    </script>
@endsection