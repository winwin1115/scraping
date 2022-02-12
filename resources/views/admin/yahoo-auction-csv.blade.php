@extends('admin.layouts.layout-basic')

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
                        <div class="tabs tabs-primary">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#home3" role="tab">期間別生成</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#profile3" role="tab">ページ別生成</a>
                                </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="tab-pane active" id="home3" role="tabpanel">
                                    <form action="{{ route('csv.yahoo-auction.putDateCsv') }}" method="post">
                                        {{ csrf_field() }}
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="start_date">商品登録期間(から)</label>
                                                <div class="input-group">
                                                    <input type="text" id="start_date" name="start_date" class="form-control required ls-datepicker" value="{{ date('m/d/Y') }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">
                                                            <i class="icon-fa icon-fa-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="end_date">商品登録期間(まで)</label>
                                                <div class="input-group">
                                                    <input type="text" id="end_date" name="end_date" class="form-control required ls-datepicker" value="{{ date('m/d/Y') }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">
                                                            <i class="icon-fa icon-fa-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary" style="float: right;"><i class="icon-fa icon-fa-cloud-download" style="margin-right: 5px;"></i>CSV生成</button>
                                    </form>
                                </div>
                                <div class="tab-pane" id="profile3" role="tabpanel">
                                    <form action="{{ route('csv.yahoo-auction.putPageCsv') }}" method="post">
                                        {{ csrf_field() }}
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label for="start_date">商品ページ</label>
                                                <div>
                                                    {{-- <select class="form-control ls-select2" multiple="multiple" style="width: 100%;" id="site_url" name="site_url[]">
                                                        @foreach ($urls as $item)
                                                            <option value="{{ $item->site_url }}">{{ $item->site_url }}</option>
                                                        @endforeach
                                                    </select> --}}
                                                    <select class="form-control ls-select2" style="width: 100%;" id="site_url" name="site_url" placeholder="商品ページを選択してください。">
                                                        @foreach ($urls as $item)
                                                            <option value="{{ $item->site_url }}">{{ $item->site_url }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary" style="float: right;"><i class="icon-fa icon-fa-cloud-download" style="margin-right: 5px;"></i>CSV生成</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
