var Products = (function () {
	$('.delete-product').on('click', function() {
		// var site_type = $('#add_site_name').val();
        // var site_url = $('#add_product_url').val();
        // if(!site_url)
        // {
        //     toastr['error']('商品のURLを入力してください。', '失敗');
        //     $('#addModal').modal('hide');
        //     return;
        // }
		// var _token = $('#token_hidden').val();
		// $('#addModal').modal('hide');
		$.ajax({
			url: 'auto/deleteProduct',
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
            
        }
    }
})()

jQuery(document).ready(function () {
    Products.init()
})
