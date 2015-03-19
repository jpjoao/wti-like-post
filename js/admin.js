(function($) {
    $(function() {

        // Check to make sure the input box exists
        if( 0 < $('#wti_like_post_expiration_date').length ) {
            $('#wti_like_post_expiration_date').datepicker({ dateFormat: 'dd/mm/yy' });
        } // end if

    });
}(jQuery));