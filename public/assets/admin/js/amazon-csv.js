jQuery(document).ready(function () {
    $('#insert_date').on('change', function() {
        var insert_date = $(this).val();
        var _token = $('#hidden_token').val();
        
		$.ajax({
			url: 'amazon/getimport',
			type: 'POST',
			data: {
				date: insert_date,
                _token: _token
			},
			success: function(response) {
				if(response['import_name'].length != 0)
				{
					var import_div = '';
                    import_div += '<select class="form-control ls-select2" style="width: 100%;" id="import_name" name="import_name" placeholder="商品ページを選択してください。">';
                    for(var i = 0; i < response.import_name.length; i++)
                        import_div += "<option value='" + response.import_name[i].import_name + "'>" + response.import_name[i].import_name + "</option>";
                    import_div += '</select>';
                    $('.import_div').html(import_div);
                    $('#import_name').select2({});
				}
				else
				{
					var import_div = '';
                    import_div += '<select class="form-control ls-select2" style="width: 100%;" id="import_name" name="import_name" placeholder="商品ページを選択してください。">';
                    for(var i = 0; i < response.import_name.length; i++)
                        import_div += "<option value='" + response.import_name[i].import_name + "'>" + response.import_name[i].import_name + "</option>";
                    import_div += '</select>';
                    $('.import_div').html(import_div);
                    $('#import_name').select2({
                        placeholder: 'インポート名を選択してください。'
                    });
				}
			},
			error: function() {
				toastr['error']('資料取得に失敗しました。', '失敗');
			}
		})
	});
})
