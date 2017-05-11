$(document).ready(function(){
    "use strict";

    // Объект для публичного использования
    $.AssolServices = {
        /** Инициализация объекта */
        Init: function() {
            this.InitActions();
            this.InitDynamicData();
            this.InitTemplate();
        },
        /** Инициализация событий */
        InitActions: function() {
            $(document).on("click", "#btnShow, #EmployeeFilter input:radio", this.ReloadServiceLists);

            $(document).on("click", ".action-western-send", function (e) {
                e.preventDefault(); // Отключаем смену флажка

                var id = $(e.target).closest('td').attr('id-western');

                bootbox.confirm('Изменить флаг "' + (IsLoveStory ? '% Кли-ки' : 'Выслали') + '" для услуги?', function(result) {
                    if (result) {
                        $.AssolServices.SendWesternService(id, $(e.target).prop('checked') ? 0 : 1);
                    }
                });
            });

            $(document).on("click", ".action-western-per", function (e) {
                e.preventDefault(); // Отключаем смену флажка

                var id = $(e.target).closest('td').attr('id-western');

                bootbox.confirm('Изменить флаг "% Пер-ка" для услуги?', function(result) {
                    if (result) {
                        $.AssolServices.PerWesternService(id, $(e.target).prop('checked') ? 0 : 1);
                    }
                });
            });

            $(document).on("click", ".action-western-done", function (e) {
                var id = $(e.target).closest('td').attr('id-western');

                bootbox.confirm('Поставить метку выполнения на услугу?', function(result) {
                    if (result) {
                        $.AssolServices.DoneService(id, 'western');
                    }
                });
            });

            $(document).on("click", ".action-meeting-done", function (e) {
                var id = $(e.target).closest('td').attr('id-meeting');

                bootbox.confirm('Поставить метку выполнения на услугу?', function(result) {
                    if (result) {
                        $.AssolServices.DoneService(id, 'meeting');
                    }
                });
            });

            $(document).on("click", ".action-delivery-done", function (e) {
                var id = $(e.target).closest('td').attr('id-delivery');

                bootbox.confirm('Поставить метку выполнения на услугу?', function(result) {
                    if (result) {
                        $.AssolServices.DoneService(id, 'delivery');
                    }
                });
            });
        },
        /** Инициализация динамичных данных */
        InitDynamicData: function() {
            this.ReloadServiceLists();
        },
        /** Предварительная компиляция шаблонов */
        InitTemplate: function() {
            $("#westernTemplate").template('westernTemplate');
            $("#meetingTemplate").template('meetingTemplate');
            $("#deliveryTemplate").template('deliveryTemplate');
        },
        DoneService: function (id, type) {
            function callback() {
                $.AssolServices.ReloadServiceLists();
            }

            $.post(BaseUrl + 'services/'+type+'/done', {id: id}, callback, 'json');
        },
        SendWesternService: function (id, isSend) {
            function callback() {
                $.AssolServices.ReloadServiceLists();
            }

            $.post(BaseUrl + 'services/western/send', {id: id, isSend: isSend}, callback, 'json');
        },
        PerWesternService: function (id, isPer) {
            function callback() {
                $.AssolServices.ReloadServiceLists();
            }

            $.post(BaseUrl + 'services/western/per', {id: id, isPer: isPer}, callback, 'json');
        },
        FilterList: function () {
            var format = 'YYYY-MM-DD';
            var data = {};

            var dpStart = $('#date-start').data("DateTimePicker");
            if (dpStart)
                data['start'] = dpStart.date().format(format);

            var dpEnd = $('#date-end').data("DateTimePicker");
            if (dpEnd)
                data['end'] = dpEnd.date().format(format);

            var employee = $('#EmployeeFilter');
            if (employee.length > 0)
                data['employee'] = employee.find('input:checked').val();

            return data;
        },
        ReloadServiceLists: function () {
            var filter = $.AssolServices.FilterList();

            $.AssolServices.ReloadWesternList(filter);
            $.AssolServices.ReloadMeetingList(filter);
            $.AssolServices.ReloadDeliveryList(filter);
        },
        /** Загрузка списка вестернов */
        ReloadWesternList: function (data) {
            this.ReloadData('#western-list', 'western', 'westernTemplate', data);
        },
        /** Загрузка списка встреч */
        ReloadMeetingList: function (data) {
            this.ReloadData('#meeting-list', 'meeting', 'meetingTemplate', data);
        },
        /** Загрузка списка доставок */
        ReloadDeliveryList: function (data) {
            this.ReloadData('#delivery-list', 'delivery', 'deliveryTemplate', data);
        },
        /**
         * Загрузка и рендер данных
         *
         * @param TargetSelector селектор контейнера для загрузки данных
         * @param TargetSegment сегмент для загрузки данных
         * @param TemplateName имя шаблона для рендера
         * @param Data данные запроса
         *
         */
        ReloadData: function(TargetSelector, TargetSegment, TemplateName, Data){
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

            $.post(BaseUrl + 'services/'+TargetSegment+'/data', Data || {}, callback, 'json');
        }
    };

    // Инициализация объекта
    $.AssolServices.Init();
});