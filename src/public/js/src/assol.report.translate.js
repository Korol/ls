$(document).ready(function(){
    "use strict";

    // Объект для публичного использования
    $.ReportTranslate = {
        /** Инициализация объекта */
        Init: function() {
            this.InitActions();
            this.InitTemplate();
            this.InitDynamicData();
        },
        /** Инициализация событий */
        InitActions: function () {
            //region actions ReportIndividualDaily
            $(document).on('click', '.is-edit>div', function () {
                var cell = $(this).parent();

                updateCellModel(cell, cell.find('div').html());
            });

            $(document).on('focusout', '.is-edit>input', function () {
                var cell = $(this).parent();
                var lastValue = parseFloat($(this).attr('last-value'));
                var newValue = parseFloat($(this).val() || 0.00);

                function hideInput(value) {
                    cell.html($('<div/>', {text: value}));
                }

                function callback(data) {
                    if (data.status) {
                        hideInput(newValue);
                        $.ReportTranslate.RefreshReportDailyDataSummary();
                    } else {
                        hideInput(lastValue);
                        alert('Ошибка сохранения данных');
                    }
                }

                if (lastValue != newValue) {
                    var date = moment([
                        $('#daily-year').data("DateTimePicker").date().year(),
                        $('#daily-month').val(),
                        $('#daily-day').val()
                    ]).format('YYYY-MM-DD');

                    var data = {
                        dateRecord: date,
                        idCross: cell.attr('id-cross')
                    };

                    data[cell.hasClass('mail') ? 'mails' : 'chat'] = newValue;

                    $.post(BaseUrl + 'reports/daily/save', data, callback, 'json');
                } else {
                    hideInput(lastValue);
                }
            });

            //endregion

            //region ReportIndividualMailing

            $(document).on("click", "#mailingSite input:radio", function (e) {
                $.ReportTranslate.ReloadReportMailingMeta($(e.target).val());
            });

            $(document).on('click', '#ReportIndividualMailing_data td[day]>div', function () {
                if ($.ReportTranslate.getEmployee() == 0) {
                    var isAge = $(this).parent().attr('day') == 102;
                    updateCellModel($(this).parent(), $(this).html(), isAge ? 'text' : 'number');
                }
            });

            $(document).on('focusout', '#ReportIndividualMailing_data td[day]>input', function () {
                var cell = $(this).parent();
                var day = parseInt(cell.attr('day'));

                var lastValue = parseInt($(this).attr('last-value') || 0);
                var newValue = day != 102
                    ? parseInt($(this).val() || 0)
                    : $(this).val();

                function hideInput(value) {
                    cell.html($('<div/>', {text: value}));
                }

                function callback(data) {
                    if (data.status) {
                        hideInput(newValue);
                    } else {
                        hideInput(lastValue);
                        alert('Ошибка сохранения данных');
                    }
                }

                if (lastValue != newValue) {
                    var year = $('#mailing-year').data("DateTimePicker").date().year();
                    var month = $('#mailing-month').val();

                    var data = {idCross: cell.closest('tr').attr('id-cross')};

                    if (day == 101) {
                        data.year = year;
                        data.month = month;
                        data.id = newValue;
                    } else if (day == 102) {
                        data.year = year;
                        data.month = month;
                        data.age = newValue;
                    } else {
                        data.dateRecord = moment([year, month, day]).format('YYYY-MM-DD');
                        data.value = newValue;
                    }

                    $.post(BaseUrl + 'reports/mailing/save', data, callback, 'json');
                } else {
                    hideInput(lastValue);
                }

            });

            //endregion

            //region ReportCorrespondence
            $(document).on("click", ".action-correspondence-remove", function (e) {
                var idRecord = $(e.target).closest('[id-record]').attr('id-record');

                function callback(data) {
                    if (data.status) {
                        $.ReportTranslate.ReloadReportCorrespondenceMeta();
                    } else {
                        alert('Ошибка: ' + data.message);
                    }
                }

                confirmRemove(function(){
                    $.post(BaseUrl + 'reports/correspondence/remove', {record: idRecord}, callback, 'json');
                });
            });

            $(document).on('click', '#ReportIndividualCorrespondence_data td[day]>div', function () {
                if ($.ReportTranslate.getEmployee() == 0) {
                    var day = $(this).parent().attr('day');
                    if (day == 102 || day == 104) {
                        updateCellModel($(this).parent(), $(this).html());
                    } else {
                        updateCellModel($(this).parent(), $(this).html(), 'text');
                    }
                }
            });

            $(document).on('focusout', '#ReportIndividualCorrespondence_data td[day]>input', function () {
                var cell = $(this).parent();
                var day = parseInt(cell.attr('day'));

                var isNumber = (day == 102 || day == 104);

                var lastValue = isNumber ? parseInt($(this).attr('last-value') || 0) : $(this).attr('last-value');
                var newValue = isNumber ? parseInt($(this).val() || 0) : $(this).val();

                function hideInput(value) {
                    cell.html($('<div/>', {text: value}));
                }

                function callback(data) {
                    if (data.status) {
                        hideInput(newValue);
                    } else {
                        hideInput(lastValue);
                        alert('Ошибка сохранения данных');
                    }
                }

                if (lastValue != newValue) {
                    var year = $('#correspondence-year').data("DateTimePicker").date().year();
                    var month = $('#correspondence-month').val();

                    var data = {idRecord: cell.closest('tr').attr('id-record')};

                    if (day <= 31 ) {
                        data.dateRecord = moment([year, month, day]).format('YYYY-MM-DD');
                        data.value = newValue;
                    } else {
                        data.year = year;
                        data.month = month;

                        switch (day) {
                            case 102:
                                data.idInfo = newValue;
                                break;
                            case 103:
                                data.menInfo = newValue;
                                break;
                            case 104:
                                data.idMenInfo = newValue;
                                break;
                        }
                    }

                    $.post(BaseUrl + 'reports/correspondence/save', data, callback, 'json');
                } else {
                    hideInput(lastValue);
                }

            });

            $(document).on("click", "#correspondenceSite input:radio", function (e) {
                $.ReportTranslate.ReloadReportCorrespondenceMeta($(e.target).val());
            });

            $("#addCorrespondenceRecord").click(function () {
                var es2c = $('#correspondenceCustomer').find("input:radio:checked").val();

                function callback() {
                    $('#addCorrespondenceRecordForm').modal('hide');
                    $.ReportTranslate.ReloadReportCorrespondenceMeta();
                }

                if (es2c) {
                    var data = {
                        employee: $.ReportTranslate.getEmployee(),
                        es2c: es2c,
                        year: $('#correspondence-year').data("DateTimePicker").date().year(),
                        month: $('#correspondence-month').val(),
                        SiteID: $('#correspondenceSite').find("input:radio:checked").val(),
                        offset: $('#addCorrespondenceRecordOffset').val()
                    };

                    $.post(BaseUrl + 'reports/correspondence/save', data, callback, 'json');
                } else {
                    alert("Не выбран клиент!");
                }
            });

            //endregion

            //region ReportSalary

            $('#submit-report-salary').click(function () {
                var data = {
                    employee: $.ReportTranslate.getEmployee(),
                    year: $('#salary-year').data("DateTimePicker").date().year(),
                    month: $('#salary-month').val()
                };

                function callback(data) {
                    if (data.status) {
                        alert('Данные успешно отправлены в сводную таблицу');
                    } else {
                        alert('Ошибка: ' + data.message);
                    }
                }

                $.post(BaseUrl + 'reports/salary/close', data, callback, 'json');
            });

            $(document).on('click', '#ReportIndividualSalary_data>tbody>tr>td[type]>div', function () {
                if ($.ReportTranslate.getEmployee() == 0) {
                    var type = $(this).parent().hasClass('decimal') ? 'decimal' : 'number';
                    updateCellModel($(this).parent(), $(this).html(), type);
                }
            });

            $(document).on('focusout', '#ReportIndividualSalary_data td[type]>input', function () {
                var cell = $(this).parent();
                var isDecimal = cell.hasClass('decimal');

                var lastValue = isDecimal
                    ? parseFloat($(this).attr('last-value') || 0.00)
                    : parseInt($(this).attr('last-value') || 0);
                var newValue = isDecimal
                    ? parseFloat($(this).val() || 0.00)
                    : parseInt($(this).val() || 0);

                function hideInput(value) {
                    cell.html($('<div/>', {text: value}));
                }

                function callback(data) {
                    if (data.status) {
                        hideInput(newValue);
                        $.ReportTranslate.RefreshReportSalaryDataSummary();
                    } else {
                        hideInput(lastValue);
                        alert('Ошибка: ' + data.message);
                    }
                }

                if (lastValue != newValue) {
                    var year = $('#salary-year').data("DateTimePicker").date().year();
                    var month = $('#salary-month').val();
                    var type = cell.attr('type');

                    var data = {
                        idEmployeeSite: cell.closest('tr').attr('id-employee-site'),
                        year: year,
                        month: month,
                        type: type,
                        value: newValue
                    };

                    $.post(BaseUrl + 'reports/salary/save', data, callback, 'json');
                } else {
                    hideInput(lastValue);
                }

            });

            //endregion

            function updateCellModel(cell, value, type) {
                var lastValue = value;

                switch ((type || 'number')) {
                    case 'number':
                        value = (value > 0 ? value : '');
                        break;
                    case 'decimal':
                        value = (value > 0.00 ? value : '');
                        break;
                }

                if ($(cell).contentEditable != null) {
                    $(cell).attr("contentEditable", isEdit);
                } else {
                    var input = $('<input/>', {type: 'text', min: 0, value: value}).attr('last-value', lastValue);

                    $(cell).html(input);

                    switch ((type || 'number')) {
                        case 'number':
                            input.numeric();
                            break;
                        case 'decimal':
                            input.numeric();
                            input.keydown(function (e) {
                                if(e.keyCode == '188' || e.charCode == '188' || e.keyCode == '110' || e.charCode == '110'){
                                    e.preventDefault();

                                    var input = $(e.target);
                                    var isExistDecimal = input.val().indexOf('.');
                                    if (isExistDecimal > 0) return;

                                    input.val(input.val() + '.');
                                }
                            });

                            //input.numeric({decimalPlaces: 2});
                            break;
                    }

                    input.focus();
                }
            }

        },
        /** Инициализация динамичных данных */
        InitDynamicData: function () {

        },
        /** Предварительная компиляция шаблонов */
        InitTemplate: function () {
            $("#reportIndividualDailyTemplate").template('reportIndividualDailyTemplate');
            $("#reportIndividualMailingTemplate").template('reportIndividualMailingTemplate');
            $("#reportIndividualSalaryTemplate").template('reportIndividualSalaryTemplate');
            $("#reportIndividualCorrespondenceTemplate").template('reportIndividualCorrespondenceTemplate');
            $("#reportIndividualDaily_fixedWrapBody_Template").template('reportIndividualDaily_fixedWrapBody_Template');
            $("#reportIndividualDaily_total_Template").template('reportIndividualDaily_total_Template');
            $("#workSitesTemplate").template('workSitesTemplate');
            $("#correspondenceCustomerTemplate").template('correspondenceCustomerTemplate');
            $("#reportApprovedSalaryTemplate").template('reportApprovedSalaryTemplate');
        },
        RefreshReportDailyDate: function (year) {
            var years = $('#daily-year');
            var months = $('#daily-month');
            var days = $('#daily-day');

            function getYear() {
                return years.data("DateTimePicker").date().year();
            }

            var lastDay = moment([year || getYear(), months.val()]).endOf('month').date();

            days.empty();
            days.append($("<option/>", {value: 0, text: 'за месяц'}));
            for (var i = 1; i <= lastDay; i++)
                days.append($("<option/>", {value: i, text: i}));

            if (months.val() == moment().month())
                days.find("[value='" + moment().date() + "']").attr("selected", "selected");

            $.ReportTranslate.ReloadReportDailyData();
        },
        ReloadReportDailyMeta: function () {
            $('#ReportIndividualDaily_data').empty();
            $('#ReportIndividualDaily_fixedWrapBody').find('>tbody').empty();
            $('#ReportIndividualDaily_total').find('>tbody').empty();

            function callback(data) {
                if (data.status) {
                    $.tmpl('reportIndividualDailyTemplate', data.records).appendTo('#ReportIndividualDaily_data');
                    $.tmpl('reportIndividualDaily_fixedWrapBody_Template', data.records.customers).appendTo('#ReportIndividualDaily_fixedWrapBody>tbody');
                    $.tmpl('reportIndividualDaily_total_Template', data.records.customers).appendTo('#ReportIndividualDaily_total>tbody');

                    $.ReportTranslate.RefreshReportDailyDate();
                }
            }

            var data = {
                employee: $.ReportTranslate.getEmployee()
            };

            $.post(BaseUrl + 'reports/daily/meta', data, callback, 'json');
        },
        ReloadReportDailyData: function () {
            var data = {
                employee: $.ReportTranslate.getEmployee(),
                year: $('#daily-year').data("DateTimePicker").date().year(),
                month: $('#daily-month').val(),
                day: $('#daily-day').val()
            };

            // Редактирование записей возможно только для переводчика и если выбран конкретный день
            var isEdit = ($.ReportTranslate.getEmployee() == 0) &&  // Проверяем что это не директор
                         (data.day > 0) &&                          // Проверяем что не выбран отчет за месяц
                         (data.month == moment().month());          // Проверяем что это текущий месяц

            function callback(data) {
                function initEditCell(cell, isEdit, typeClass, es2cID, idCustomer, idEmployeeSite, value) {
                    // 1. Настройка редактирования
                    isEdit ? cell.addClass('is-edit') : cell.removeClass('is-edit');

                    // 2. Установка свойств
                    cell.addClass(typeClass)
                        .attr('id-cross', es2cID)
                        .attr('id-customer', idCustomer)
                        .attr('id-employee-site', idEmployeeSite)
                        .find('div').html(value || 0);
                }

                // Очищаем данные
                $('#ReportIndividualDaily_data').find('.mail div, .chat div').html(0);

                // Заполняем данные
                $.each(data.records, function(key, value) {
                    var suffix = value.CustomerID + '_' + value.EmployeeSiteID;

                    initEditCell($('#mail_' + suffix), isEdit, 'mail', value.es2cID, value.CustomerID, value.EmployeeSiteID, value.emails);
                    initEditCell($('#chat_' + suffix), isEdit, 'chat', value.es2cID, value.CustomerID, value.EmployeeSiteID, value.chat);
                });

                $.ReportTranslate.RefreshReportDailyDataSummary();
            }

            $.post(BaseUrl + 'reports/daily/data', data, callback, 'json');
        },
        RefreshReportDailyDataSummary: function () {
            var reportData = $('#ReportIndividualDaily_data');                  // Таблица с данными
            var summaryData = $('#ReportIndividualDaily_total');                // Таблица с сводными данными
            var fixedSummaryData = $('#ReportIndividualDaily_fixedWrapBody');   // Фиксированная таблица с сводными данными

            // Очищаем данные и сводные данные
            $(reportData).find('tfoot td div').html(0.00);
            $(summaryData).find('tbody td, tfoot td').html(0.00);
            $(fixedSummaryData).find('tbody td, tfoot td').html(0.00);

            // Заполняем сводные данные по письмам в разрезе сайта + в разрезе клиента
            $(reportData).find('tbody .mail').each(function() {
                // Подсчет количества email в разрезе сайта
                var idEmployeeSite = $(this).attr('id-employee-site');
                var footMail = $('#foot_mail_' + idEmployeeSite);
                footMail.html(parseFloat(footMail.html()) + parseFloat($(this).find('div').html()));

                // Подсчет количества email в разрезе клиента
                var idCustomer = $(this).attr('id-customer');
                var totalMail = $('#rid_total_' + idCustomer).find('td:eq(0)');
                totalMail.html(parseFloat(totalMail.html()) + parseFloat($(this).find('div').html()));
            });

            // Заполняем сводные данные по чатам в разрезе сайта + в разрезе клиента + сумма писем и чатов
            $(reportData).find('tbody .chat').each(function() {
                // Подсчет количества сообщений чата в разрезе сайта
                var idEmployeeSite = $(this).attr('id-employee-site');
                var footChat = $('#foot_chat_' + idEmployeeSite);
                footChat.html(parseFloat(footChat.html()) + parseFloat($(this).find('div').html()));

                // Подсчет количества сообщений чата в разрезе клиента
                var idCustomer = $(this).attr('id-customer');
                var total = $('#rid_total_' + idCustomer);
                var totalMail = total.find('td:eq(0)');
                var totalChat = total.find('td:eq(1)');
                var totalAll = total.find('td:eq(2)');

                totalChat.html(parseFloat(totalChat.html()) + parseFloat($(this).find('div').html()));
                totalAll.html(parseFloat(totalChat.html()) + parseFloat(totalMail.html()));
                $('#rid_slide_total_' + idCustomer).find('td').html(totalAll.html());
            });

            // Заполняем футер для сводных данных в разрезе клиента
            var totalFooter = $(summaryData).find('tfoot>tr');
            var totalFooterMail = totalFooter.find('td:eq(0)');
            var totalFooterChat = totalFooter.find('td:eq(1)');
            var totalFooterAll = totalFooter.find('td:eq(2)');

            $(summaryData).find('tbody>tr').each(function () {
                totalFooterMail.html(parseFloat(totalFooterMail.html()) + parseFloat($(this).find('td:eq(0)').html()));
                totalFooterChat.html(parseFloat(totalFooterChat.html()) + parseFloat($(this).find('td:eq(1)').html()));
                totalFooterAll.html(parseFloat(totalFooterAll.html()) + parseFloat($(this).find('td:eq(2)').html()));
            });

            $(fixedSummaryData).find('tfoot>tr>td').html(totalFooterAll.html());
        },
        ReloadReportMailing: function () {
            var mailingSites = $('#mailingSite').find('ul');
                mailingSites.empty();

            function callback(data) {
                if (data.status) {
                    $.tmpl('workSitesTemplate', data.records).appendTo(mailingSites);
                    mailingSites.find('input:first').click();
                } else {
                    alert('Ошибка получения списка сайтов');
                }
            }

            $.post(BaseUrl + 'reports/sites', {employee: $.ReportTranslate.getEmployee()}, callback, 'json');
        },
        ReloadReportMailingMeta: function (SiteID) {
            SiteID = SiteID || $('#mailingSite').find("input:radio:checked").val();

            $('#ReportIndividualMailing_data').empty();

            function callback(data) {
                if (data.status) {
                    var year = $('#mailing-year').data("DateTimePicker").date().year();
                    var month = $('#mailing-month').val();
                    var lastDay = moment([year, month]).endOf('month').date();

                    data.records.days = [];
                    for (var i = 1; i <= lastDay; i++) {
                        data.records.days.push(i);
                    }

                    $.tmpl('reportIndividualMailingTemplate', data.records).appendTo('#ReportIndividualMailing_data');

                    $.ReportTranslate.ReloadReportMailingData();
                }
            }

            var data = {
                employee: $.ReportTranslate.getEmployee(),
                SiteID: SiteID
            };

            $.post(BaseUrl + 'reports/mailing/meta', data, callback, 'json');
        },
        ReloadReportMailingData: function () {
            var data = {
                employee: $.ReportTranslate.getEmployee(),
                year: $('#mailing-year').data("DateTimePicker").date().year(),
                month: $('#mailing-month').val(),
                SiteID: $('#mailingSite').find("input:radio:checked").val()
            };

            function callback(data) {
                if (data.status) {
                    // Заполняем данные
                    $.each(data.records.days, function(key, record) {
                        var cross = record.EmployeeSiteCustomerID;
                        var day = moment(record.date).date();

                        $('#ReportIndividualMailing_data')
                            .find('tbody>tr[id-cross="' + cross + '"]>td[day="' + day + '"]>div')
                            .html(record.value);
                    });

                    $.each(data.records.info, function(key, record) {
                        var cross = record.EmployeeSiteCustomerID;

                        var idInfo = record['id-info'];
                        var ageInfo = record['age-info'];

                        if (idInfo > 0) {
                            $('#ReportIndividualMailing_data')
                                .find('tbody>tr[id-cross="' + cross + '"]>td[day="101"]>div')
                                .html(idInfo);
                        }

                        $('#ReportIndividualMailing_data')
                            .find('tbody>tr[id-cross="' + cross + '"]>td[day="102"]>div')
                            .html(ageInfo);
                    });
                } else {
                    alert('Ошибка получения данных');
                }
            }

            $.post(BaseUrl + 'reports/mailing/data', data, callback, 'json');
        },
        ReloadReportCorrespondence: function () {
            var correspondenceSite = $('#correspondenceSite').find('ul');
            correspondenceSite.empty();

            function callback(data) {
                if (data.status) {
                    $.tmpl('workSitesTemplate', data.records).appendTo(correspondenceSite);
                    correspondenceSite.find('input:first').click();
                } else {
                    alert('Ошибка получения списка сайтов');
                }
            }

            $.post(BaseUrl + 'reports/sites', {employee: $.ReportTranslate.getEmployee()}, callback, 'json');
        },
        ReloadReportCorrespondenceCustomers: function () {
            var data = {
                employee: $.ReportTranslate.getEmployee(),
                SiteID: $('#correspondenceSite').find("input:radio:checked").val()
            };

            var correspondenceCustomer = $('#correspondenceCustomer');
            var correspondenceCustomerUl = correspondenceCustomer.find('ul');
            correspondenceCustomerUl.empty();
            correspondenceCustomer.find('.label-placement-wrap button').html('Выбрать');

            function callback(data) {
                if (data.status) {
                    $.tmpl('correspondenceCustomerTemplate', data.records).appendTo(correspondenceCustomerUl);
                    correspondenceCustomerUl.find('input:first').click();
                } else {
                    alert('Ошибка получения данных');
                }
            }

            $.post(BaseUrl + 'reports/correspondence/customers', data, callback, 'json');
        },
        ReloadReportCorrespondenceMeta: function (SiteID) {
            SiteID = SiteID || $('#correspondenceSite').find("input:radio:checked").val();

            $('#ReportIndividualCorrespondence_data').empty();

            function callback(data) {
                if (data.status) {
                    var year = $('#correspondence-year').data("DateTimePicker").date().year();
                    var month = $('#correspondence-month').val();
                    var lastDay = moment([year, month]).endOf('month').date();

                    data.records.days = [];
                    for (var i = 1; i <= lastDay; i++) {
                        data.records.days.push(i);
                    }

                    $.tmpl('reportIndividualCorrespondenceTemplate', data.records).appendTo('#ReportIndividualCorrespondence_data');

                    $.ReportTranslate.ReloadReportCorrespondenceData();
                    $.ReportTranslate.ReloadReportCorrespondenceCustomers();
                }
            }

            var data = {
                employee: $.ReportTranslate.getEmployee(),
                year: $('#correspondence-year').data("DateTimePicker").date().year(),
                month: $('#correspondence-month').val(),
                SiteID: SiteID
            };

            $.post(BaseUrl + 'reports/correspondence/meta', data, callback, 'json');
        },
        ReloadReportCorrespondenceData: function () {
            var data = {
                employee: $.ReportTranslate.getEmployee(),
                year: $('#correspondence-year').data("DateTimePicker").date().year(),
                month: $('#correspondence-month').val(),
                SiteID: $('#correspondenceSite').find("input:radio:checked").val()
            };

            function callback(data) {
                if (data.status) {
                    // Заполняем данные
                    $.each(data.records, function(key, record) {
                        var cross = record.CorrespondenceInfoID;
                        var day = moment(record.date).date();
                        //
                        $('#ReportIndividualCorrespondence_data')
                            .find('tbody>tr[id-record="' + cross + '"]>td[day="' + day + '"]>div')
                            .html(record.value);
                    });
                } else {
                    alert('Ошибка получения данных');
                }
            }

            $.post(BaseUrl + 'reports/correspondence/data', data, callback, 'json');
        },
        ReloadReportSalary: function () {
            var report_data = $('#ReportIndividualSalary_data').find('>tbody');
            report_data.empty();

            var data = {
                employee: $.ReportTranslate.getEmployee(),
                year: $('#salary-year').data("DateTimePicker").date().year(),
                month: $('#salary-month').val()
            };

            function callback(data) {
                if (data.status) {
                    $.tmpl('reportIndividualSalaryTemplate', data.records).appendTo(report_data);
                    $.ReportTranslate.RefreshReportSalaryDataSummary();
                } else {
                    alert('Ошибка получения данных');
                }
            }

            $.post(BaseUrl + 'reports/salary/data', data, callback, 'json');
        },
        RefreshReportSalaryDataSummary: function () {
            var reportData = $('#ReportIndividualSalary_data'); // Таблица с данными

            // Очищаем сумму по сайтам и итого
            $(reportData).find('tr').find('td:last').html(0.00);

            // Заполнение суммы по сайтам
            $(reportData).find('tbody>tr').each(function() {
                var emailAmount     = parseFloat($(this).find('td:eq(2) div').html()) || 0.00;
                var chatAmount      = parseFloat($(this).find('td:eq(4) div').html()) || 0.00;
                var deliveryAmount  = parseFloat($(this).find('td:eq(6) div').html()) || 0.00;
                var dealerAmount    = parseFloat($(this).find('td:eq(8) div').html()) || 0.00;

                $(this).find('td:last').html((emailAmount + chatAmount + deliveryAmount + dealerAmount).toFixed(2));
            });

            // Заполнение общей суммы по сайтам
            var total = $(reportData).find('tfoot td:last');
            $(reportData).find('tbody>tr').find('td:last').each(function() {
                var siteAmount = parseFloat($(this).html()) || 0.00;
                var totalAmount = parseFloat(total.html()) || 0.00;

                total.html((totalAmount + siteAmount).toFixed(2));
            });

        },
        ReloadReportApprovedSalary: function (year) {
            var report_data = $('#ReportApprovedSalary_data').find('>tbody');
            report_data.empty();

            var data = {
                year: year || $('#approved-salary-year').data("DateTimePicker").date().year(),
                month: $('#approved-salary-month').val()
            };

            function callback(data) {
                if (data.status) {
                    $.tmpl('reportApprovedSalaryTemplate', data.records).appendTo(report_data);

                    var footer = $('#ReportApprovedSalary_data').find('>tfoot');
                    var charge = footer.find('strong:eq(0)');
                    var issued = footer.find('strong:eq(1)');
                    var balance = footer.find('strong:eq(2)');

                    footer.find('strong').html(0);

                    report_data.find('tr').each(function () {
                        var paid = parseFloat($(this).attr('paid')) == 1;
                        var value = parseFloat($(this).find('td:eq(1)').html());

                        charge.html((parseFloat(charge.html()) + value).toFixed(2));
                        if (paid) {
                            issued.html((parseFloat(issued.html()) + value).toFixed(2));
                        } else {
                            balance.html((parseFloat(balance.html()) + value).toFixed(2));
                        }
                    });
                } else {
                    alert('Ошибка получения данных');
                }
            }

            $.post(BaseUrl + 'reports/approved/salary/data', data, callback, 'json');
        },
        employee: 0,
        setEmployee: function (employee) {
            this.employee = employee;
        },
        getEmployee: function () {
            return this.employee;
        }
    };

    // Инициализация объекта
    $.ReportTranslate.Init();
});