@extends('admin.layouts.layout-basic')

@section('scripts')
    <script src="/assets/admin/js/urls.js"></script>
@endsection

@section('content')
    <div class="main-content">
        <div class="page-header">
            <h3 class="page-title">商品登録</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">ホーム</a></li>
                <li class="breadcrumb-item active">商品登録</li>
            </ol>
            <div class="page-actions">
                <a href="#" class="btn btn-primary add-modal-btn" data-token="{{ csrf_token() }}"><i class="icon-fa icon-fa-plus"></i> 商品を追加</a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h6><i class="icon-fa icon-fa-shopping-cart"></i>商品</h6>

                        <div class="card-actions">

                        </div>
                    </div>
                    <div class="card-body">
                        <table id="products-datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">サイト名</th>
                                <th>商品のURL</th>
                            </tr>
                            </thead>
                            @foreach($product_info as $i => $item)
                                <tr>
                                    <td>{{$i + 1}}</td>
                                    <td>{{ config('consts.site_type')[$item->site_type - 1]['title'] }}</td>
                                    <td>{{$item->site_url}}</td>
                                </tr>
                            @endforeach
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 style="color: #fff;" class="modal-title" id="exampleModalLabel">商品追加</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>サイト名</label>
                        <select class="custom-select" id="add_site_name" style="margin-bottom: 15px;">
                            <option value="1">楽天</option>
                            <option value="2">ヤフオク</option>
                            <option value="3">フリマ・オークション</option>
                        </select>

                        <label>商品のURL</label>
                        <input type="text" id="add_product_url" value="" class="form-control" style="margin-bottom: 15px;" />

                        <input type="hidden" id="token_hidden" value="" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="add-confirm-btn" class="btn btn-success">保存する</button>
                </div>
            </div>
        </div>
    </div>
@stop