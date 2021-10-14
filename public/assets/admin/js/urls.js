var Products = (function () {
    var handleTables = function () {
        var table = $('#products-datatable').DataTable({
            scrollX: true,
            scrollCollapse: true,
            language: {
                oPaginate: {
                    // sFirst: "初へ",
                    sPrevious: "前へ",
                    sNext: "次へ",
                    // sLast: "後へ"
                },
                search: "検索",
                lengthMenu: "表示 _MENU_ エントリ",
                info: '表示中 _START_ まで _END_ の _TOTAL_ エントリ',
                sEmptyTable: "テーブルにデータがありません。",
				sZeroRecords: "該当する記録は見つかりません。"
            }
        });
    }

    $('.add-modal-btn').on('click', function() {
		$('#addModal').modal();
		$('#token_hidden').val($(this).data('token'));
	});

	$('#add-confirm-btn').on('click', function() {
		var site_type = $('#add_site_name').val();
        var site_url = $('#add_product_url').val();
        if(!site_url)
        {
            toastr['error']('商品のURLを入力してください。', '失敗');
            $('#addModal').modal('hide');
            return;
        }
		var _token = $('#token_hidden').val();
		$('#addModal').modal('hide');
		$.ajax({
			url: 'urls/add',
			type: 'POST',
			data: {
				site_type: site_type,
                site_url: site_url,
				_token: _token
			},
			success: function(response) {
				if(response['status'] == '200')
				{
					toastr['success']('追加が完了しました。', '成功');
					setTimeout('location.reload()', 2000);
				}
				else
				{
					toastr['error']('追加が失敗しました。', '失敗');
				}
			},
			error: function() {
				toastr['error']('追加が失敗しました。', '失敗');
			}
		})
	});

    return {
        // main function to initiate the module
        init: function () {
            handleTables();
        }
    }
})()

jQuery(document).ready(function () {
    Products.init()
})
