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


// --homepage slider---

//sortable table
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
