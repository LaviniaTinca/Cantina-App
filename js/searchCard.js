$(document).ready(function() {
    $("#search-input").on("input", function() {
        const filterValue = $(this).val().toLowerCase(); 
        $(".tr").each(function() {
            const filteredContent = $(this).find(".filter").text().toLowerCase(); 

            if (filteredContent.includes(filterValue)) {
                $(this).show(); 
            } else {
                $(this).hide(); 
            }
        });

        $(".box").each(function() {
            const filteredContent = $(this).find(".filter").text().toLowerCase(); 

            // Check if the subscriber content includes the entered keyword
            if (filteredContent.includes(filterValue)) {
                $(this).show(); 
            } else {
                $(this).hide();
            }
        });
    });
});
