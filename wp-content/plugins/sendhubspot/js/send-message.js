jQuery(document).ready(function($){
    $('#hs_form').submit(function(){
        var data = $(this).serialize();
        $.ajax({
            type: "POST",
            url: objajax.url,
            data: {
                formData: data,
                security: objajax.nonce,
                action: 'send_message'
            },
            beforeSend: function(){
                $('#res').empty();
                $('#loader').fadeIn();
            },
            success: function(res){
                let obj = JSON.parse(res)
                // console.log(obj)
                if(obj.success)
                {
                    $('#loader').fadeOut(300, function(){
                        $('#res').text(obj.mess);
                        $('#hs_form').find('input:not(#hs_submit)').val('');
                        $('#hs_form').find('textarea').val('');
                    });
                }
                else{
                    $('#loader').fadeOut(300, function(){
                        $('#res').text(obj.mess);
                    });
                }
            },
            error: function(){
                alert('Ошибка!');
            }
        });
        return false;
    });
});