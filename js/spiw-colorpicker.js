(function($){
    $('.spiw-color-field').wpColorPicker();
    
    $(document).ajaxSuccess(function(e, xhr, settings) {
        $('.spiw-color-field').wpColorPicker();
    });
}(jQuery));

