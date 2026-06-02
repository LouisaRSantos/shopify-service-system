$(document).ready(function () {

    $(document).on('click', '.ajax-link', function (e) {

        e.preventDefault();

        let url = $(this).attr('href');

        $("#app-content").html(`
            <div class="content-wrapper">
                <div class="d-flex justify-content-center p-5">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
        `);

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {

                $('#app-content').html(response);

                window.history.pushState({}, '', url);

            },
            error: function () {

                $('#app-content').html(`
                    <div class="content-wrapper">
                        <div class="alert alert-danger">
                            Failed to load page.
                        </div>
                    </div>
                `);

            }
        });

    });

});