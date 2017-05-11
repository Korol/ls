$(document).ready(function(){
    // Объект для публичного использования
    $.AssolTraining = {
        /** Инициализация объекта */
        Init: function() {
            this.InitActions();
            this.InitDynamicData();
            this.InitTemplate();
        },
        /** Инициализация событий */
        InitActions: function() {
            $(document).on("click", ".action-training-add", function () {
                window.location.href = BaseUrl + 'training/' + $.AssolTraining.GetParent() + '/add/file';
            });

            $(document).on("click", ".action-training-edit", function (e) {
                window.location.href = BaseUrl + 'training/' + $.AssolTraining.GetParent() + '/edit/' + $(e.target).closest('[record]').attr('record');
            });

            $(document).on("click", ".action-training-open", function (e) {
                window.location.href = BaseUrl + 'training/' + $.AssolTraining.GetParent() + '/show/' + $(e.target).closest('[record]').attr('record');
            });

            $(document).on("click", ".action-folder-open", function (e) {
                $.AssolTraining.SetParent($(e.target).closest('[record]').attr('record'));
                $.AssolTraining.ReloadTrainingList();
            });

            $(document).on("click", ".action-folder-remove", function (e) {
                confirmRemove(function(){
                    $.AssolTraining.RemoveTraining($(e.target).closest('[record]').attr('record'));
                });
            });

            $(document).on("click", ".action-document-remove", function (e) {
                confirmRemove(function(){
                    $.AssolTraining.RemoveTraining($(e.target).closest('[record]').attr('record'));
                });
            });
        },
        /** Инициализация динамичных данных */
        InitDynamicData: function() {
            this.ReloadTrainingList();
        },
        /** Предварительная компиляция шаблонов */
        InitTemplate: function() {
            $("#trainingTemplate").template('trainingTemplate');
        },
        /** Загрузка списка папок и документов */
        ReloadTrainingList: function (data) {
            this.ReloadData('#training', 'trainingTemplate', data)
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

            Data = Data || {Parent: $.AssolTraining.GetParent()};

            $.post(BaseUrl + 'training/data', Data, callback, 'json');
        },
        RemoveTraining: function (id) {
            function callback(data) {
                if (data.status) {
                    $.AssolTraining.ReloadTrainingList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'training/remove', {id: id}, callback, 'json');
        },
        /** ID родительского каталога */
        parent: UrlParent,
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
    $.AssolTraining.Init();
});