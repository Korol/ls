$(document).ready(function(){
    // Объект для публичного использования
    $.AssolDocument = {
        /** Инициализация объекта */
        Init: function() {
            this.InitActions();
            this.InitDynamicData();
            this.InitTemplate();
        },
        /** Инициализация событий */
        InitActions: function() {
            $(document).on("click", ".action-folder-open", function (e) {
                $.AssolDocument.SetParent($(e.target).closest('[record]').attr('record'));
                $.AssolDocument.ReloadDocumentList();
            });

            $(document).on("click", ".action-folder-remove", function (e) {
                confirmRemove(function(){
                    $.AssolDocument.RemoveDocument($(e.target).closest('[record]').attr('record'));
                });
            });

            $(document).on("click", ".action-document-remove", function (e) {
                confirmRemove(function(){
                    $.AssolDocument.RemoveDocument($(e.target).closest('[record]').attr('record'));
                });
            });
        },
        /** Инициализация динамичных данных */
        InitDynamicData: function() {
            this.ReloadDocumentList();
        },
        /** Предварительная компиляция шаблонов */
        InitTemplate: function() {
            $("#documentsTemplate").template('documentsTemplate');
        },
        /** Загрузка списка папок и документов */
        ReloadDocumentList: function (data) {
            this.ReloadData('#documents', 'documentsTemplate', data)
        },
        /**
         * Загрузка и рендер данных
         *
         * @param TargetSelector селектор контейнера для загрузки данных
         * @param TemplateName имя шаблона для рендера
         * @param Data данные для POST запроса
         */
        ReloadData: function(TargetSelector, TemplateName, Data){
            $(TargetSelector).html('Загрузка данных...');

            function callback(data) {
                if (data.status) {
                    if (data.records){
                        $(TargetSelector).empty();
                        $.tmpl(TemplateName, data.records).appendTo(TargetSelector);
                    }
                } else {
                    showErrorAlert(data.message)
                }
            }

            Data = Data || {Parent: $.AssolDocument.GetParent()};

            $.post(BaseUrl + 'documents/data', Data, callback, 'json');
        },
        RemoveDocument: function (id) {
            function callback(data) {
                if (data.status) {
                    $.AssolDocument.ReloadDocumentList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'documents/remove', {id: id}, callback, 'json');
        },
        /** ID родительского каталога */
        parent: 0,
        SetParent: function(id) {
            this.parent = id;
        },
        /**
         * @return {number}
         */
        GetParent: function() {
            return this.parent;
        }
    };

    // Инициализация объекта
    $.AssolDocument.Init();
});