@extends('header')

@section('title', '承包商公司明细')


@section('css')
    <link rel="stylesheet" href="{{ asset('/css/company/index.css') }}"/>
@endsection
@section('header')
    <ul class="nav nav-pills nav-small nav-fixed">
        <li role="presentation" class="active"><a href="#">承包商公司明细</a></li>
    </ul>
    <div id="return-btn">
        <a href="{{ url('company/index') }}" class="refresh"></a>
    </div>
    <nav class="navbar navbar-default navbar-small">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <form class="navbar-form navbar-left" role="search" method="get" action="{{ url('company/search') }}">
                    <div class="form-group">
                        <input type="text" class="form-control" value="{{ $_GET['company_name'] or '' }}" name="company_name" placeholder="公司名称">&nbsp;或者
                        <input type="text" class="form-control" value="{{ $_GET['person_name'] or '' }}" name="person_name"  placeholder="负责人/联系人">&nbsp;&nbsp;&nbsp;
                    </div>
                    <button type="submit" class="btn btn-primary">搜索</button>
                    <button class="btn btn-info btn-sm export">导出到文件</button>
                    <script>
                        $('.export').click(function(){
                            var sParam = 'is_export=1&'+$('form.navbar-form').serialize();
                            var sUrl = '{{ url('company/search') }}' + '?' + sParam;
                            maskShow();
                            window.location = sUrl;
                            setTimeout(maskHide,2000);
                            return false;
                        })
                    </script>
                </form>
            </div>
        </div>
    </nav>
    <div class="function-area">
        <button class="btn btn-success btn-sm" onclick="javascript:location='{{ url('company/add') }}';">新公司入住</button>
    </div>
@endsection
@section('content')
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-condensed">
            <thead>
            <tr class="active">
                <th>公司名</th>
                <th>日常联系人</th>
                <th>联系人电话</th>
                <th>入住时间</th>
                <th>居住房间个数</th>
                <th>备注</th>
                <th>操作</th>
            </tr>
            </thead>
            @foreach($companies as $company)
                <tr>
                    <td>
                        {{ $company->company_name }}
                    </td>
                    <td>{{ $company->linkman }}</td>
                    <td>{{ $company->linkman_tel }}</td>
                    <td>{{ substr($company->created_at, 0, 10) }}</td>
                    <td>
                        {{ isset($count['livingRoomNumber'][$company->company_id]) ?
                            $count['livingRoomNumber'][$company->company_id] :
                            0 }}
                    </td>
                    <td>{{ $company->company_remark }}</td>
                    <td>
                        <a href="{{ url('company/company-detail/'.$company->company_id) }}" class="btn btn-info btn-xs" >详细</a>
                        <a href="{{ url('company/company-utility/'.$company->company_id) }}" class="btn btn-primary btn-xs">水电</a>
                        <a href="{{ url('punish/create/'.$company->company_id) }}" class="btn btn-primary btn-xs">处罚</a>
                        <a href="{{ url('company/change-rooms/'.$company->company_id) }}" class="btn btn-success btn-xs">调房</a>
                        <a href="{{ url('company/edit/'.$company->company_id) }}" class="btn btn-success btn-xs">修改</a>
                        <button  delete_id="{{ $company->company_id }}" class="btn btn-danger btn-xs delete-button">退租</button>
                    </td>
                </tr>
            @endforeach
        </table>
        {!! $companies->appends([
                'company_name'=>isset($_GET['company_name']) ? $_GET['company_name'] : '',
                'person_name'=>isset($_GET['person_name']) ? $_GET['person_name'] : ''
            ])->render() !!}
    </div>
@endsection
@section('modal')
        <!-- delete modal -->
    <div id="delete-modal" class="modal fade bs-example-modal-sm">
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
                    <button id="delete-confirm" type="button" class="btn btn-primary">确认</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('bottom')
    <p>共有 {{ $count['company'] }} 个公司</p>
    <p>共占用 {{ $count['livingRoom'] }} 个居住房间，{{ $count['diningRoom'] }} 个餐厅，{{ $count['serviceRoom'] }} 个服务用房</p>
@endsection
@section('js')
    <script src="{{ asset('/bootstrap-3.3.5/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script>
        ajaxDelete('{{ url('company/quit') }}')
    </script>
@endsection