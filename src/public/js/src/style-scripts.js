$('input:checked').change();

$(document).on('click', '.assol-btn-select .dropdown-menu [role="menuitem"]', function (event) {
    var $parent = $(this).parents('.assol-btn-select');
    $parent.find('.dropdown-menu li.selected').removeClass('selected');
    $(this).closest('li').addClass('selected');

    $parent.find('.assol-btn-select-checked').html($(this).html());
    $parent.find('.pseudo-select-hidden-input').val($(this).attr('data-value'));
});

$(document).on('focus', '.date-field>input', function (event) {
    $(this).datetimepicker({
        format: "DD.MM.YYYY",
        locale: 'ru',
        widgetPositioning: {
            horizontal: 'auto',
            vertical: 'bottom'
        }
    });
});

$(document).on('click', '#popup', function (event) {
    var elem = event.target;
    if ($(elem).attr('id') == 'popup') {
        $('#popup .close').trigger('click');
    }
});

$(document).on('click', '#popup .close', function (event) {
    $('body').removeClass('popup-show');
    $('#popup').hide();
});

$(document).on('click', '.show-full-news', function (event) {
    $(this).parents('.bodyNewsContent-in').addClass('show-full');
});

var dayReportsTables = $('.day-reports-tables');
var dayMainFixedTable = $('.day-main-fixed-table');

if (dayReportsTables.length > 0) {
    var maxScrollLeft = dayReportsTables[0].scrollWidth - dayReportsTables[0].clientWidth;
    var widthBlock = $('.day-total-table').width();
    var stopScrollWidth = maxScrollLeft - widthBlock;

    dayReportsTables.scroll(function () {
        var documentScrollLeft = dayReportsTables.scrollLeft();
        if (documentScrollLeft > stopScrollWidth) {
            if (!dayMainFixedTable.hasClass('hide'))
                dayMainFixedTable.addClass('hide');
        } else {
            if (dayMainFixedTable.hasClass('hide'))
                dayMainFixedTable.removeClass('hide');
        }
    });
}