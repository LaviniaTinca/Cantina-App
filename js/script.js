let menu = document.querySelector('#menu-btn');
let userBtn = document.querySelector('#user-btn');

userBtn.addEventListener('click', function(){
    let userBox = document.querySelector('.user-box');
    userBox.classList.toggle('active')
})

document.cookie = "myCookie=myValue; SameSite=Strict";

// popup image
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

//read-more MENU0 btn
$("#read-more-btn").on("click", function() {
    window.location.href = "view_menu.php";
});

//collapse btn
$(document).ready(function() {
    $("#collapse-btn").click(function() {
        $(".sidebar").toggleClass("sidebar-collapsed");
        $(".panel-container").toggleClass("sidebar-collapsed");
    });
});


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
