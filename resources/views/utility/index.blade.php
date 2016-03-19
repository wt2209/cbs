@extends('header')
@section('title', '水电费明细')
@section('css')
    <link rel="stylesheet" href="{{ url('/css/utility/index.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small">
        <li role="presentation" class="active"><a href="#">水电费明细</a></li>
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
                        房间号：
                        <input type="text" class="form-control">&nbsp;&nbsp;&nbsp;
                        <label class="no-bold"><input type="radio" name="status" />空房间</label>&nbsp;&nbsp;&nbsp;
                        <label class="no-bold"><input type="radio" name="status" />正在使用的房间</label>&nbsp;&nbsp;&nbsp;
                    </div>
                    <button type="submit" class="btn btn-primary">搜索</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="function-area">
        <button class="btn btn-info btn-sm">导出到文件</button>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-condensed">
            <thead>
            <tr class="active">
                <th>房间号</th>
                <th>所属公司</th>
                <th>公司状态</th>
                <th>费用月份</th>
                <th>电费</th>
                <th>水费</th>
                <th>合计</th>
                <th>备注</th>
                <th>操作</th>
            </tr>
            </thead>

                <tr>
                    <td>1-101</td>
                    <td>海博</td>
                    <td>正常</td>
                    <td>2016-1—2016-2</td>
                    <td>123</td>
                    <td>20</td>
                    <td>153</td>
                    <td>我是备注</td>
                    <td>
                        <a href="{{ url('utility/edit/') }}" class="btn btn-success btn-xs">修改</a>
                        <a href="javascript:;" room_id="" class="btn btn-danger btn-xs">删除</a>
                    </td>
                </tr>
                <tr>
                    <td>1-101</td>
                    <td>春波</td>
                    <td><span style="color: red">已退租</span></td>
                    <td>2016-1—2016-2</td>
                    <td>123</td>
                    <td>20</td>
                    <td>153</td>
                    <td>我是备注</td>
                    <td>
                        <a href="{{ url('utility/edit/') }}" class="btn btn-success btn-xs">修改</a>
                        <a href="javascript:;" room_id="" class="btn btn-danger btn-xs">删除</a>
                    </td>
                </tr>

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
    <p>haha </p>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>

@endsection