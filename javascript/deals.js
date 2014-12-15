( function( $, undefined ) {
    /**
     * We need to wait for the document 
     * to load as we have included this file 
     * in <head> element.
     */
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


       /**                                                                             
        */                                                                              
        $(document.body).on( 'keyup', function( event ) {                               
            var code = event.keyCode ? event.keyCode : event.which;                     
            var target = event.target;                                                  
            var city = $('.city-input-js').val();
            if ( code == 13 ) {          
                location.href = document.URL + '&city=' + encodeURIComponent(city);
            }                                                                           
        } );
    } );
} )( jQuery );
