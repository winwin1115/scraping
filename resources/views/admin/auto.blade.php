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
                        <table id="remove-datatable" class="table table-striped table-bordered nowrap" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>自動取り下げ状態</th>
                                <th width="30%">時間</th>
                            </tr>
                            </thead>
                            @foreach($withdraw_info as $i => $item)
                                <tr>
                                    <td>{{$i + 1}}</td>
                                    <td style="text-align: left;">
                                        @if ($item->withdraw_count)
                                            {{$item->withdraw_count}}つの商品が取り下げされました。
                                        @else
                                            取り下げされた用品がありません。
                                        @endif
                                    </td>
                                    <td>{{$item->created_at}}</td>
                                </tr>
                            @endforeach
                            <tbody>
                            </tbody>
                        </table>
                        
                        <input type="hidden" value="{{ csrf_token() }}" id="token_hidden" />
                        <button class="btn btn-danger delete-product" style="float: right; margin-right: 10px; color: #fff; margin-top: 20px;"><i class="icon-fa icon-fa-trash" style="margin-right: 5px;"></i>自動削除</button>
                        {{-- <form> --}}
                            {{-- {{ csrf_field() }} --}}
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
                        {{-- </form> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
