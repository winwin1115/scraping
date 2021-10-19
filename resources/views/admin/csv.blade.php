@extends('admin.layouts.layout-basic')

@section('content')
    <div class="main-content">
        <div class="page-header">
            <h3 class="page-title">CSVで生成</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">ホーム</a></li>
                <li class="breadcrumb-item active">CSVで出品</li>
            </ol>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h6><i class="icon-fa icon-fa-cloud-download"></i>生成</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('csv.putCsv') }}" method="post">
                            {{ csrf_field() }}
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="site_type">サイト名</label>
                                    <select class="form-control ls-select2" name="site_type">
                                        <option value="0">すべて</option>
                                        <option value="1">楽天</option>
                                        <option value="2">ヤフオク</option>
                                        <option value="3">フリマ・オークション</option>
                                    </select>
                                </div>
                            </div>
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
                            <button class="btn btn-primary" style="float: right;"><i class="icon-fa icon-fa-cloud-download" style="margin-right: 5px;"></i>CSVで生成</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
