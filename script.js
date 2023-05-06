const header = document.querySelector('header');
// function fixedNavbar() {
//     header.classList.toggle('scrolled', window.pageYOffset > 0)
// }
// fixedNavbar();
// window.addEventListener('scroll', fixedNavbar);

let menu = document.querySelector('#menu-btn');
let userBtn = document.querySelector('#user-btn');

userBtn.addEventListener('click', function(){
    let userBox = document.querySelector('.user-box');
    userBox.classList.toggle('active')
})

//newsletter
// const emailNewsletterInput = document.querySelector(".subscribe-input");
//     const subscribeButton = document.querySelector(".subscribe-button");

//     subscribeButton.addEventListener("click", () => {
//       const email = emailNewsletterInput.value.trim();

//       if (email !== "") {
//         // Send email to server to subscribe to newsletter
//         console.log("Subscribed with email:", email);
//       } else {
//         alert("Please enter a valid email address.");
//       }
//     });
$(document).ready(function() {
  const emailNewsletterInput = $(".subscribe-input");
  const subscribeButton = $(".subscribe-button");

  subscribeButton.click(function() {
    const email = emailNewsletterInput.val().trim();

    if (email !== "") {
      // Send email to server to subscribe to newsletter
      console.log("Subscribed with email:", email);
    } else {
      alert("Please enter a valid email address.");
    }
  });
});

  
// --homepage slider---
// --popup image

// --visibility of add element button--JQuery  

$(document).ready(function() {
    $("#add-product-btn").click(function() {
        $(".add-products").toggle();
    });
});


//toggle review button ONLY WHEN THE PAGE LOADS
// $(document).ready(function() {
//     $(".toggle-reviews-btn").click(function() {
//         var id = $(this).attr("id").split("-")[3]; // get the user id from the button id
//         $("#review-row-"+id+" .review-table").toggle(); // toggle the display property of the review table
//     });
// });

//toggle review button when actions are performed, page loads + search (dynamically created toggle buttons
  $(document).ready(function() {
    // Hide reviews table rows
    $('.review-row').hide();
  
    // Bind the event listener to a parent element
    // and specify the child selector for the dynamically created toggle buttons
    $('.product-table').on('click', '.toggle-reviews-btn', function() {
      var reviewsRow = $(this).closest('tr').next('.review-row');
      reviewsRow.toggle();
    });
  });
  
  




//toggle nested table review
// $(document).ready(function() {
//     // Hide the reviews table by default
//     $(".review-table").hide();

//     // Add a click event listener to the toggle button
//     $("#toggle-reviews-btn").click(function() {
//         // Toggle the visibility of the reviews table
//         $(".review-table").toggle();
//         //     $(this).next(".review-table").toggle(); //this is to set a unique id BUT something is NOT WORKING properly

//     });
// });

//TOGGLE REVIEWS
// Get all the toggle review buttons
// const toggleReviewButtons = document.querySelectorAll('.toggle-reviews-btn');

// // Loop through the buttons
// toggleReviewButtons.forEach(button => {
//   // Get the user ID from the button's ID
//    const userId = button.id.split('-')[2];

//   // Get the review table for the corresponding user
//   const reviewTable = document.querySelector(`#review-row-${userId}`);

//   // Hide the review table initially
//   reviewTable.style.display = 'none';

//   // Add an event listener to the button
//   button.addEventListener('click', () => {
//     // Toggle the visibility of the review table
//     if (reviewTable.style.display === 'none') {
//       reviewTable.style.display = 'table-row';
//     } else {
//       reviewTable.style.display = 'none';
//     }
//   });
// });


  

//sortable table -- JQuery
$(document).ready(function() {

    // Search by keyword
    $('#search-input').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    $('#product-table tbody tr').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
    });

    // Sorting by columns
    $('.sortable').click(function() {
    var column = $(this).data('column');
    var order = $(this).hasClass('asc') ? 'desc' : 'asc';
    $('.sortable').removeClass('asc').removeClass('desc');
    $(this).addClass(order);
    var rows = $('#product-table tbody tr').toArray();
    rows.sort(compare(column, order));
    $('#product-table tbody').empty().append(rows);
    });

    function compare(column, order) {
    return function(a, b) {
        var aValue = $(a).find('td').eq(getColumnIndex(column)).text();
        var bValue = $(b).find('td').eq(getColumnIndex(column)).text();
        var result = aValue.localeCompare(bValue, undefined, {
        numeric: true,
        sensitivity: 'base'
        });
        return order === 'asc' ? result : -result;
    }
    }

    function getColumnIndex(column) {
    return $('.sortable').index($('[data-column="' + column + '"]'));
    }

    });
