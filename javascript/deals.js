( function( $, undefined ) {
    $(document).ready( function() {
        $('a.link').click( function( e ) {
            e.preventDefault();
            if ( !$(this).parent().hasClass( 'current') ) {
                var page_no = $(this).parent().data('page-no');
                $('.deal-item').addClass('hidden');
                $('.page' + page_no ).removeClass('hidden');     
                $('.current').removeClass('current').addClass('active');
                $(this).parent().addClass('current');
                $('.start-page').text(page_no);
            }
        } );
    } );
} )( jQuery );
