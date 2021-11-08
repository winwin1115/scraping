@extends('admin.layouts.layout-basic')

@section('content')
    <div class="main-content">
        <div class="page-header">
            <h3 class="page-title">換率, 利益率設定</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">ホーム</a></li>
                <li class="breadcrumb-item active">換率, 利益率設定</li>
            </ol>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header bg-danger">
                        <h6><i class="icon-fa icon-fa-bar-chart"></i>換率</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('currencys.updateCurrency') }}" method="post">
                            {{ csrf_field() }}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputFirstName">USD($)</label>
                                    <input type="text" class="form-control" id="inputFirstName" placeholder="1$" readonly disabled>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputFirstName">JPY(¥)</label>
                                    <input type="text" class="form-control" name="currency_rate" id="currency_rate" placeholder="¥" value="{{ $currency_rate }}¥">
                                </div>
                            </div>
                            <button class="btn btn-danger" style="float: right;">保存</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header bg-success">
                        <h6><i class="icon-fa icon-fa-line-chart"></i>利益率</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('currencys.updateProfit') }}" method="post">
                            {{ csrf_field() }}
                            <div class="form-group row">
                                <label for="firstName" class="col-sm-2 col-form-label">利益率</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="profit_rate" id="profit_rate" placeholder="利益率" value="{{ $profit_rate }}">
                                </div>
                            </div>
                            <button class="btn btn-success" style="float: right;">保存</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
