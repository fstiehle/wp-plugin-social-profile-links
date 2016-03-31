(function($) {
    function init() {
       $('.spiw-color-field').wpColorPicker(); 
    }
    
    $(document).ready(function() {
       init(); 
    });    
    $(document).on('widget-updated', init);
}(jQuery));