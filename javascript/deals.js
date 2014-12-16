( function( $, undefined ) {
    /**
     * We need to wait for the document 
     * to load as we have included this file 
     * in <head> element.
     */
    //var HOST = 'https://fathomless-wildwood-9268.herokuapp.com/';
    var HOST = 'http://akshay.local/exercise/';
    $(document).ready( function() {
        $(document.body).on( 'click', 'a.link', function( e ) {
            e.preventDefault();
            if ( !$(this).parent().hasClass( 'current') ) {
                var page_no = $(this).parent().data('page-no');
                $('.grid-item').addClass('hidden');
                $('.page' + page_no ).removeClass('hidden');     
                $('.current').removeClass('current').addClass('active');
                $(this).parent().addClass('current');
                $('.start-page').text(page_no);
            }
        } );


        $(document.body).click( function( event ) {
            event.preventDefault();    
            if ( !($(event.target).hasClass('preselect-option') || $(event.target).hasClass('sort-options') || $(event.target).hasClass('sort-option')) ) {
                $('.sort-options').addClass('hidden');
            }
        } );

        $('.sort-option').click( function( event ) {
            event.preventDefault();
            $('#mainframe').addClass('hidden');
            $('.loading-img').removeClass('hidden');
            $('.sort-selected').removeClass('sort-selected');
            $(this).addClass('sort-selected');
            $('.selected-rating-filter').removeClass('selected-rating-filter');
            $('.preselect-option').text( $(this).data('name') );
            $('.preselect-option').trigger('click');
            filter_data();
            
        } );
        $('.preselect-option').click( function( event ) {
            event.preventDefault();


            if ( $('.sort-options').hasClass( 'hidden' ) ) {
                $('.sort-options').removeClass('hidden');
            } else {
                $('.sort-options').addClass('hidden');
            }
        } );
       /**                                                                             
        */                                                                              
        /*$(document.body).on( 'keyup', function( event ) {                               
            var code = event.keyCode ? event.keyCode : event.which;                     
            var target = event.target;                                                  


            if ( code == 13 ) {          
                filter_data();
                //location.href = HOST + '?city=' + encodeURIComponent(city);
            }                                                                           
        } );*/

        $('.filter-rating').click( function() {
            $('#mainframe').addClass('hidden');
            $('.loading-img').removeClass('hidden');
            $('.preselect-option').text('Rating');
            $('.selected-rating-filter').removeClass('selected-rating-filter');
            $(this).addClass('selected-rating-filter');
            filter_data();
        } );

        function filter_data() {
            var filter_type = $('.selected-rating-filter').data('filter') || '';
            var sort_by = $('.sort-selected').data('sort') || '';
            var data = {
                "filter": filter_type,
                "sortby": sort_by 
            };

            $.ajax({
                url: HOST + 'ajax/getdata.php',
                dataType: 'html',
                type: 'POST',
                data: data,
                success: function( response ) {
                    $('.loading-img').addClass('hidden');
                    $('#mainframe').html( $(response).find('#mainframe').html() );
                    $('#mainframe').removeClass('hidden');
                },
                error: function() {

                } 
            });
        }


    } );
} )( jQuery );
