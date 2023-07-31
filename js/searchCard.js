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
        $(".tr").each(function() {
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

// $(document).ready(function() {
//     // Function to filter elements based on the entered keyword
//     function filterElements(filterValue, elementsSelector) {
//         $(elementsSelector).each(function() {
//             const filteredContent = $(this).find(".filter").text().toLowerCase();

//             // Check if the content includes the entered keyword
//             if (filteredContent.includes(filterValue)) {
//                 $(this).show(); // Show the element if it matches the keyword
//             } else {
//                 $(this).hide(); // Hide the element if it does not match the keyword
//             }
//         });
//     }

//     $("#search-input").on("input", function() {
//         const filterValue = $(this).val().toLowerCase(); // Get the entered keyword in lowercase for case-insensitive comparison

//         // Filter box cards
//         filterElements(filterValue, ".box");

//         // Filter table rows
//         filterElements(filterValue, ".table-pagination .tr");
//     });
// });
