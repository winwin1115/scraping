var Products = (function () {
	$('.delete-product').on('click', function() {
		var _token = $('#token_hidden').val();
		$.ajax({
			url: 'auto/deleteProduct',
			type: 'GET',
			success: function(response) {
				if(response['status'] == '200')
				{
					if(response["count"])
						toastr['success'](response["count"] + 'つの商品が削除されました。', '成功');
					else
						toastr['success']('削除された商品がありません。', '成功');
				}
				else
				{
					toastr['error']('削除が失敗しました。', '失敗');
				}
			},
			error: function() {
				toastr['error']('削除が失敗しました。', '失敗');
			}
		})
	});

    return {
        // main function to initiate the module
        init: function () {
            
        }
    }
})()

jQuery(document).ready(function () {
    Products.init()
})
