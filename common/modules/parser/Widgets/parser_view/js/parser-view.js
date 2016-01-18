$(document).ready(
    function(event){

        var container = $("#data-container");
        var read_form = $("#read-form");
        var file_input = $("input[type=file]");
        var safe_action = read_form.data('save_action');

        var files;

        // Add events for file input
        $(document).on('change', file_input ,prepareUpload);

        $(document).on('beforeSubmit', read_form, beforeSubmitReadForm);

        // Grab the files and set them to our variable
        function prepareUpload(event)
        {
            files = event.target.files;
        }

        function beforeSubmitReadForm (){

            console.log(files);
            var data = new FormData(read_form);
            $.each(files, function(key, value)
            {
                data.append(key, value);
            });
            console.log(data);
            $.ajax({
                url: safe_action,
                type: 'POST',
                data: data,
                cache: false,
                processData: false,
                contentType: false,
                success: function(data, textStatus, jqXHR)
                {
                    $.post(read_form.attr('action'), read_form.serialize()).done(
                        function(result) {
                            container.html( result );
                        }
                    ).fail(
                        function(){
                            container.text( 'Ошибка сервера' );
                        }
                    );

                },
                error: function(jqXHR, textStatus, errorThrown)
                {

                    container.text( 'Ошибка загрузки файла на сервер' );

                }
            });


            return false;
        }

    }
)
