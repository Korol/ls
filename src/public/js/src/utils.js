function htmlEncode(value){
    //create a in-memory div, set it's inner text(which jQuery automatically encodes)
    //then grab the encoded contents back out.  The div never exists on the page.
    return $('<div/>').text(value).html();
}

function htmlDecode(value){
    return $('<div/>').html(value).text();
}

/**
 * Подтверждение удаления записи
 *
 * @param callback функция удаления
 * @param message пользовательское сообщение
 * @param title пользовательский заголовок
 */
function confirmRemove(callback, message, title) {
    message = message || 'Вы действительно хотите удалить запись?';
    title = title || 'Удаление записи';

    bootbox.dialog({
        message: message,
        title: title,
        buttons: {
            danger: {
                label: "Удалить",
                className: "btn-danger",
                callback: callback
            },
            main: {
                label: "Отмена",
                className: "btn-default"
            }
        }
    });
}

$.isBlank = function(obj){
    return(!obj || $.trim(obj) === "" || $.isEmptyObject(obj));
};

function isDateField(source) {
    return source.is('input') && source.parent().hasClass('date-field');
}

function toClientDate(date) {
    if ($.inArray(date, [null, '', '0000-00-00', '0000-00-00 00:00:00']) > -1)
        return '';

    return moment(date).format('DD.MM.YYYY');
}

function toClientDateTime(date) {
    if ($.inArray(date, [null, '', '0000-00-00 00:00:00']) > -1)
        return '';

    return moment(date).format('DD.MM.YYYY HH:mm:ss');
}

function toServerDate(date) {
    if ($.inArray(date, ['', '0000-00-00']) > -1)
        return null;

    return moment(date, 'DD.MM.YYYY').format('YYYY-MM-DD');
}

function toServerDateTime(date) {
    if ($.inArray(date, ['', '0000-00-00 00:00:00']) > -1)
        return null;

    return moment(date, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss');
}

var delay = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

function initTinymce(BaseUrl, selector) {
    selector = selector || '.tinymce-editor';

    tinymce.init({
        selector: selector,
        height: 300,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table contextmenu paste responsivefilemanager code'
        ],
        toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
        toolbar2: "responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
        language: 'ru',
        paste_data_images: true,
        image_advtab: true ,

        external_filemanager_path: BaseUrl + "/public/tinymce/filemanager/",
        filemanager_title:"Файловый менеджер" ,
        external_plugins: { "filemanager" : BaseUrl + "/public/tinymce/filemanager/plugin.min.js"}
    });
}