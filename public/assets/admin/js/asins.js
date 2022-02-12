var Products = (function () {
    var handleTables = function () {
        var table = $('#asins-datatable').DataTable({
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
        var _token = $('#token_hidden').val();
        var asin_info = $('#add_asin').val();
        var asin_array = [];
        if(!asin_info)
        {
            toastr['error']('ASINコードを入力してください。', '失敗');
            $('#addModal').modal('hide');
            return;
        }
        asin_array = asin_info.split(/\n/);
		$('#addModal').modal('hide');
		$.ajax({
			url: 'asins-add',
			type: 'POST',
			data: {
				asin_info: asin_array,
				_token: _token
			},
			success: function(response) {
				if(response['status'] == '200')
				{
					toastr['success']('追加に成功しました。', '成功');
					setTimeout('location.reload()', 2000);
				}
				else
				{
					toastr['error']('追加に失敗しました。', '失敗');
				}
			},
			error: function() {
				toastr['error']('追加に失敗しました。', '失敗');
			}
		})
	});

    $('.delete-btn').on('click', function() {
        var asin_id = $(this).data('id');
        var _token = $(this).data('token');
        $.ajax({
            url: 'asins-delete',
            type: 'POST',
            data: {
                asin_id: asin_id,
                _token: _token
            },
            success: function(response) {
                if(response['status'] == '200')
				{
					toastr['success']('削除に成功しました。', '成功');
					setTimeout('location.reload()', 2000);
				}
				else
				{
					toastr['error']('削除に失敗しました。', '失敗');
				}
            },
            error: function() {
                toastr['error']('削除に失敗しました。', '失敗');
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
