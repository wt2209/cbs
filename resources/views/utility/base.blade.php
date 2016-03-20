@extends('header')
@section('title', '水电费底数')
@section('css')
    <link rel="stylesheet" href="{{ url('/css/utility/base.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="#">水电费底数</a></li>
    </ul>
    <div id="return-btn">
        <a href="" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <form class="navbar-form navbar-left" role="search">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="房间号">&nbsp;&nbsp;&nbsp;
                        <input type="text" class="form-control" placeholder="月份，格式为：2016-3">&nbsp;&nbsp;&nbsp;
                    </div>
                    <button type="submit" class="btn btn-primary">搜索</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="function-area">
        <button class="btn btn-success btn-sm" onclick="javascript:location='{{ url('utility/add') }}';">录入底数</button>
        <button class="btn btn-danger btn-sm">从文件导入</button>
        <button class="btn btn-info btn-sm">导出到文件</button>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-condensed">
            <thead>
            <tr class="active">
                <th>房间号</th>
                <th>月份</th>
                <th>电表底数</th>
                <th>水表底数</th>
                <th>抄表人</th>
                <th>抄表时间</th>
                <th>备注</th>
                <th>操作</th>
            </tr>
            </thead>

            @foreach($bases as $base)
                <tr>
                    <td>{{ $base->room }}</td>
                    <td>{{ $base->year }}-{{ $base->month }} </td>
                    <td>{{ $base->electric_base }}</td>
                    <td>{{ $base->water_base }}</td>
                    <td>{{ $base->recorder }}</td>
                    <td>{{ $base->record_time }}</td>
                    <td>{{ $base->u_base_remark }}</td>
                    <td>
                        <a href="{{ url('utility/edit-base/'.$base->u_base_id) }}" class="btn btn-success btn-xs">修改</a>
                        <a href="javascript:;" room_id="" class="btn btn-danger btn-xs">删除</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

    <!-- delete modal -->
    <div id="modal" class="modal bs-example-modal-sm">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="gridSystemModalLabel">删除确认</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        确认要删除吗？
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="modal-confirm" type="button" class="btn btn-primary">确认</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('bottom')
    <p>共有 {{ count($bases) }} 条记录</p>
    <div class="container-fluid">
        <div class="navbar-header">
            <form id="calculate" class="navbar-form navbar-left" role="search">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="form-group">
                    计算
                    <input type="text" name="year" class="form-control" placeholder="年">
                    <input type="text" name="month" class="form-control" placeholder="月">
                    的水电费
                </div>
                <button id="submit" class="btn btn-primary">计算</button>
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script src="{{ url('/js/jquery.validate.min.js') }}"></script>
    <script>
        var s = true;
        var validate = $("#calculate").validate({
            debug: true, //调试模式取消submit的默认提交功能
            errorClass: "validate_error", //默认为错误的样式类为：error
            focusInvalid: false, //当为false时，验证无效时，没有焦点响应
            onkeyup: false,
            submitHandler: function(){   //表单提交句柄,为一回调函数，带一个参数：form
                if (s) {
                    s = false;
                    maskShow();
                    $.post('{{ url('utility/calculate') }}', $('#calculate').serialize(), function(e){
                        maskHide();
                        popdown({'message':e.message, 'status': e.status});
                        /*if (e.status) {
                            $('#calculate')[0].reset();
                        }*/
                        s = true;
                    }, 'json');
                }
                return false;
            },
            rules:{
                year:{
                    required:true,
                    number:true,
                    min:1970,
                    max:3000
                },
                month:{
                    required:true,
                    number:true,
                    min:1,
                    max:12
                }
            },
            messages:{
                year:{
                    required:"年份必须填写！",
                    number:"请正确填写年份！",
                    min:"请正确填写年份！",
                    max:"请正确填写年份！"
                },
                month:{
                    required:"月份必须填写！",
                    number:"请正确填写月份！",
                    min:"请正确填写月份！",
                    max:"请正确填写月份！"
                }
            }
        });
    </script>
@endsection