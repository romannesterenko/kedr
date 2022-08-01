function collectStars(className){
    return $('.feedback-rating__row.'+className+' .rating-star.rating-star_selected').length;
}
$(function (){
    $('.tabs-content').on('click', '.send_request', function (){
        var form_data = {};
        form_data['common'] = collectStars('common');
        form_data['quality'] = collectStars('quality');
        form_data['easily'] = collectStars('easily');
        form_data['benefit'] = collectStars('benefit');
        form_data['product_id'] = $('#product_id').val();
        form_data['recommend'] = $('[name="req"]:checked').val();
        form_data['review_title'] = $('[name="review_title"]').val();
        form_data['review_text'] = $('[name="review_text"]').val();
        form_data['review_pluses'] = $('[name="review_pluses"]').val();
        form_data['review_minuses'] = $('[name="review_minuses"]').val();
        form_data['review_nickname'] = $('[name="review_nickname"]').val();
        form_data['review_place'] = $('[name="review_place"]').val();
        $.ajax({
            type: 'POST',
            url: $('#ajax_url').val(),
            data: {
                data: form_data,
            },
            dataType: 'json',
            beforeSend: function () {
            },
            success: function(response){
                if(response.success) {
                    $('.feedback.send').empty().html('<div class="block-title">' + response.message + '</div>');
                }else {
                    alert(response.message)
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
            },
        });
    });
});