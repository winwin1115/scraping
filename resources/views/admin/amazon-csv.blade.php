@extends('admin.layouts.layout-basic')

@section('scripts')
    <script src="/assets/admin/js/amazon-csv.js"></script>
@endsection

@section('content')
    <div class="main-content">
        <div class="page-header">
            <h3 class="page-title">CSV生成</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">ホーム</a></li>
                <li class="breadcrumb-item active">CSV生成</li>
            </ol>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h6><i class="icon-fa icon-fa-cloud-download"></i>生成</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('csv.amazon.putAsinCsv') }}" method="post">
                            {{ csrf_field() }}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="insert_date">ASINコード登録日付</label>
                                    <div class="input-group">
                                        <input type="text" id="insert_date" name="insert_date" class="form-control required ls-datepicker" value="{{ date('m/d/Y') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="icon-fa icon-fa-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="import_name">インポート名</label>
                                    <div class="import_div">
                                        <select class="form-control ls-select2" style="width: 100%;" id="import_name" name="import_name" placeholder="インポート名を選択してください。">
                                            @foreach ($import_name as $item)
                                                <option value="{{ $item->import_name }}">{{ $item->import_name }}</option>
                                            @endforeach                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="hidden_token" value="{{csrf_token()}}">
                            <button class="btn btn-primary" style="float: right;"><i class="icon-fa icon-fa-cloud-download" style="margin-right: 5px;"></i>CSV生成</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
