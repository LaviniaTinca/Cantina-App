$(document).ready(function() {
    $("#search-input").on("input", function() {
        const filterValue = $(this).val().toLowerCase(); // Get the entered keyword in lowercase for case-insensitive comparison

        $(".box").each(function() {
            const filteredContent = $(this).find(".filter").text().toLowerCase(); // Get the content of the card to compare

            // Check if the subscriber content includes the entered keyword
            if (filteredContent.includes(filterValue)) {
                $(this).show(); // Show the card if it matches the keyword
            } else {
                $(this).hide(); // Hide the card if it does not match the keyword
            }
        });
    });
});
