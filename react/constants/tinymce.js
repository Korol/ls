export const TINYMCE_DEFAULT_CONFIG = {
    height: 300,
    plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table contextmenu paste responsivefilemanager code'
    ],
    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright',
    toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
    toolbar2: "responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
    language: 'ru',
    paste_data_images: true,
    image_advtab: true ,
    external_filemanager_path: BaseUrl + "/public/tinymce/filemanager/",
    filemanager_title:"Файловый менеджер" ,
    external_plugins: { "filemanager" : BaseUrl + "/public/tinymce/filemanager/plugin.min.js"}
};