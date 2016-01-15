$(document).ready(
    function(){

        var container = $("#data-container");
        var read_form = $("#read-form");

        $(document).on('beforeSubmit', "#read-form", function(){
           // console.log($.param( read_form ) );

            $.post(read_form.attr('action'), read_form.serialize()).done(
                function(result) {
                container.text( result );
            }
            ).fail(
                function(){
                    container.text( 'Ошибка сервера' );
                }
            );
            return false;
        } );

    }
)
