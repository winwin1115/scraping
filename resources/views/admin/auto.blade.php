@extends('admin.layouts.layout-basic')

@section('scripts')
    <script src="/assets/admin/js/auto.js"></script>
@stop

@section('content')
    <div class="main-content">
        <div class="page-header">
            <h3 class="page-title">自動取り下げ</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">ホーム</a></li>
                <li class="breadcrumb-item active">自動取り下げ</li>
            </ol>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header bg-secondary">
                        <h6 style="color: #fff;"><i class="icon-fa icon-fa-briefcase"></i>自動取り下げ</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('auto.deleteProduct') }}" method="post">
                            {{ csrf_field() }}
                            {{-- <div class="form-row">
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
                            <button class="btn btn-primary" style="float: right;"><i class="icon-fa icon-fa-cloud-upload" style="margin-right: 5px;"></i>自動出品</button> --}}
                            <button class="btn btn-danger" style="float: right; margin-right: 10px; color: #fff;"><i class="icon-fa icon-fa-trash" style="margin-right: 5px;"></i>自動削除</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop