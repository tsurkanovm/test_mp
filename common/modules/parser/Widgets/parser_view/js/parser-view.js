$(document).ready(
    function(event){

        var container = $("#data-container");
        var read_form = $("#read-form");
        var safe_action = read_form.data('save_action');

        var files;

        // Add event for file input
        $(document).on('change', "input[type=file]" ,prepareUpload);
        // Add event for read form submit
        $(document).on('beforeSubmit', "#read-form", beforeSubmitReadForm);
        // Add event for write form submit
        $(document).on('beforeSubmit', "#write-form", beforeSubmitWriteForm);
        // Add event for write form submit
        $(document).on('click', "#update", updateButtonClick);

        // Grab the files and set them to our variable
        function prepareUpload(event)
        {
            files = event.target.files;
        }

        function updateButtonClick (  ) {
            // marked that update-button was clicked
            var update_field = $("input[name|='UploadFileParsingForm[update]']");
            update_field.val( true );

        }

        function beforeSubmitReadForm (){
            // collect files for post request
            var data = new FormData(read_form);
            $.each(files, function(key, value)
            {
                data.append(key, value);
            });

            // first reqest for saving file
            $.ajax({
                url: safe_action,
                type: 'POST',
                data: data,
                cache: false,
                processData: false,
                contentType: false,
                success: function(data, textStatus, jqXHR)
                {
                    // second request for validate form and perform read action
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


        function beforeSubmitWriteForm (  ){
            var write_form = $("#write-form");
            var post_data = write_form.serialize();

            $.post( write_form.attr('action'), post_data ).done(
                function( result ) {
                    container.html( result );
                }
            ).fail(
                function(){
                    container.text( 'Ошибка ответа с сервера при записи данных' );
                }
            );
            return false;
        }
    }
)
