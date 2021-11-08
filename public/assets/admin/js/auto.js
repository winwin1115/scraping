var Products = (function () {
	var handleTables = function () {
    	$('#remove-datatable').DataTable({
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
		// new $.fn.dataTable.FixedColumns( table, { rightColumns: 1 } );
  	}
	  
	$('.delete-product').on('click', function() {
		var _token = $('#token_hidden').val();
		$.ajax({
			url: 'auto/deleteProduct',
			type: 'POST',
			data: {
				_token: _token
			},
			success: function(response) {
				if(response['status'] == '200')
				{
					if(response["count"])
						toastr['success'](response["count"] + 'つの商品が取り下げされました。', '成功');
					else
						toastr['success']('取り下げされた商品がありません。', '成功');
					setTimeout('location.reload()', 5000);
				}
				else
				{
					toastr['error']('取り下げが失敗しました。', '失敗');
				}
			},
			error: function() {
				toastr['error']('取り下げが失敗しました。', '失敗');
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
