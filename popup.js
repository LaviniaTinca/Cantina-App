    //jquery for the popup
    $(document).ready(function() {
        $('.product-image').on('click', function() {
            var src = $(this).attr('src');
            $('#popup-image').attr('src', src);
            $('#popup-container').fadeIn();
        });

        $('#popup-container').on('click', function() {
            $(this).fadeOut();
        });
    });