@extends('admin.layouts.layout-basic')

@section('scripts')
    <script src="/assets/admin/js/asins.js"></script>
@endsection

@section('content')
    <div class="main-content">
        <div class="page-header">
            <h3 class="page-title">ASINコード登録</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">ホーム</a></li>
                <li class="breadcrumb-item active">ASINコード登録</li>
            </ol>
            <div class="page-actions">
                <a href="#" class="btn btn-primary add-modal-btn" data-token="{{ csrf_token() }}"><i class="icon-fa icon-fa-plus"></i> ASINコードを追加</a>
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
                        <table id="asins-datatable" class="table table-striped table-bordered nowrap" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">ASINコード</th>
                                <th width="20%">インポート名</th>
                                <th width="20%">操作</th>
                            </tr>
                            </thead>
                            @foreach($asin_info as $i => $item)
                                <tr>
                                    <td>{{$i + 1}}</td>
                                    <td>{{$item->asin}}</td>
                                    <td>{{$item->import_name}}</td>
                                    <td>
                                        <button class="btn btn-danger delete-btn btn-sm" data-token="{{csrf_token()}}" data-id="{{$item->id}}" data-confirmation="notie"> <i class="icon-fa icon-fa-trash"></i> 削除</button>
                                    </td>
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
                    <h5 style="color: #fff;" class="modal-title" id="exampleModalLabel">商品URLを追加</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>商品のURL</label>
                        <textarea id="add_asin" rows="15" value="" class="form-control" style="margin-bottom: 15px;"></textarea>

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