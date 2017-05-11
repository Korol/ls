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
            ////region actions ReportIndividualDaily
            $(document).on('click', '.is-edit>div', function () {
                // Редактирование записей возможно только для переводчика
                var isEdit = ($.ReportTranslate.getEmployee() == 0);   // Проверяем что это не директор

                if (isEdit) {
                    var cell = $(this).parent();

                    updateCellModel(cell, cell.find('div').html(), 'decimal');
                }
            });

            $(document).on('focusout', '#ReportIndividualDaily_data tbody .is-edit>input', function () {
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
                        $(this).closest('[day]').attr('day')
                    ]).format('YYYY-MM-DD');

                    var data = {
                        dateRecord: date,
                        idCross: cell.attr('id-work-site')
                    };

                    data[cell.hasClass('mail') ? 'mails' : 'chat'] = newValue;

                    $.post(BaseUrl + 'reports/lovestory/daily/save', data, callback, 'json');
                } else {
                    hideInput(lastValue);
                }
            });

            $(document).on('focusout', '#ReportIndividualDaily_data thead .is-edit>input', function () {
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
                    var data = {
                        year: $('#daily-year').data("DateTimePicker").date().year(),
                        month: $('#daily-month').val(),
                        idCross: cell.attr('id-work-site')
                    };

                    data[cell.hasClass('mail') ? 'mails' : 'chat'] = newValue;

                    $.post(BaseUrl + 'reports/lovestory/daily/plan/save', data, callback, 'json');
                } else {
                    hideInput(lastValue);
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
            $("#reportIndividualDaily_fixedWrapBody_Template").template('reportIndividualDaily_fixedWrapBody_Template');
            $("#reportIndividualSalaryTemplate").template('reportIndividualSalaryTemplate');
            $("#reportApprovedSalaryTemplate").template('reportApprovedSalaryTemplate');
            $("#reportAllocation_Template").template('reportAllocation_Template');
        },
        ReloadReportMountMeta: function (year) {
            $('#ReportIndividualDaily_data').empty();
            $('#ReportIndividualDaily_fixedWrapBody').find('>tbody').empty();

            var years = $('#daily-year');
            var months = $('#daily-month');

            function getYear() {
                return years.data("DateTimePicker").date().year();
            }

            function callback(data) {
                if (data.status) {
                    var lYear = year || getYear();
                    var lMonth = months.val();
                    var lastDay = moment([lYear, lMonth]).endOf('month').date();

                    data.records.days = [];
                    for (var day = 1; day <= lastDay; day++) {
                        data.records.days.push({
                            day: day,
                            date: moment([lYear, lMonth, day]).format('YYYY-MM-DD')
                        });
                    }

                    $.tmpl('reportIndividualDailyTemplate', data.records).appendTo('#ReportIndividualDaily_data');
                    $.tmpl('reportIndividualDaily_fixedWrapBody_Template', data.records.days).appendTo('#ReportIndividualDaily_fixedWrapBody>tbody');

                    $.ReportTranslate.ReloadReportMountPlanData(year || getYear(), months.val());
                }
            }

            var data = {
                employee: $.ReportTranslate.getEmployee()
            };

            $.post(BaseUrl + 'reports/lovestory/daily/meta', data, callback, 'json');
        },
        ReloadReportMountPlanData: function (year, mount) {
            var data = {
                employee: $.ReportTranslate.getEmployee(),
                year: year,
                month: mount
            };

            function callback(data) {
                var head = $('#ReportIndividualDaily_data').find('>thead');

                // Заполняем данные
                $.each(data.records, function(key, value) {
                    // Поиск сайта по ID
                    var site = head.find('th[id-work-site="' + value.esID + '"]');

                    $(site[0]).find('div').html(value.emails || 0);
                    $(site[1]).find('div').html(value.chat || 0);
                });

                $.ReportTranslate.ReloadReportMountData(year, mount);
            }

            $.post(BaseUrl + 'reports/lovestory/daily/plan/data', data, callback, 'json');
        },
        ReloadReportMountData: function (year, mount) {
            var data = {
                employee: $.ReportTranslate.getEmployee(),
                year: year,
                month: mount
            };

            function callback(data) {
                var table = $('#ReportIndividualDaily_data').find('>tbody');

                // Заполняем данные
                $.each(data.records, function(key, value) {
                    // Поиск сайта по ID за указанную дату
                    var site = table.find('td[date="' + value.date + '"][id-work-site="' + value.esID + '"]');

                    $(site[0]).find('div').html(value.emails || 0.00);
                    $(site[1]).find('div').html(value.chat || 0.00);
                });

                $.ReportTranslate.RefreshReportDailyDataSummary();
            }

            $.post(BaseUrl + 'reports/lovestory/daily/data', data, callback, 'json');
        },
        RefreshReportDailyDataSummary: function () {
            var reportData = $('#ReportIndividualDaily_data');                  // Таблица с данными
            var fixedSummaryData = $('#ReportIndividualDaily_fixedWrapBody');   // Фиксированная таблица с сводными данными

            //// Очищаем данные и сводные данные
            $(reportData).find('tfoot td[id]').html(0.00);
            $(fixedSummaryData).find('tbody td, tfoot td').html(0.00); // TODO: Добавить очистку thead

            // Заполнение сводных данные по письмам в разрезе сайта + в разрезе плана
            var planEmailTotal = 0.00;
            var planChatTotal = 0.00;
            $(reportData).find('thead .mail').each(function() {
                planEmailTotal += parseFloat($(this).find('div').html());});
            $(reportData).find('thead .chat').each(function() {
                planChatTotal += parseFloat($(this).find('div').html());});

            var totalPlan = $('#rid_plan_total');
            totalPlan.find('th:eq(0)').html(planEmailTotal.toFixed(2));
            totalPlan.find('th:eq(1)').html(planChatTotal.toFixed(2));
            totalPlan.find('th:eq(2)').html((planEmailTotal + planChatTotal).toFixed(2));



            // Заполняем сводные данные по письмам в разрезе сайта + в разрезе даты
            $(reportData).find('tbody .mail').each(function() {
                // Подсчет количества email в разрезе сайта
                var idEmployeeSite = $(this).attr('id-work-site');
                var footMail = $('#foot_mail_total_' + idEmployeeSite);
                footMail.html((parseFloat(footMail.html()) + parseFloat($(this).find('div').html())).toFixed(2));
                // Баланс email в разрезе сайта
                var planMail = parseFloat($('#plan_mail_' + idEmployeeSite).find('div').html());
                var footMailBal = $('#foot_mail_bal_' + idEmployeeSite);
                footMailBal.html((planMail - parseFloat(footMail.html())).toFixed(2));

                // Подсчет количества email в разрезе даты
                var day = $(this).closest('tr').attr('day');
                var totalMail = $('#rid_total_' + day).find('td:eq(0)');
                totalMail.html((parseFloat(totalMail.html()) + parseFloat($(this).find('div').html())).toFixed(2));
            });

            // Заполняем сводные данные по чатам в разрезе сайта + в разрезе даты + сумма писем и чатов
            $(reportData).find('tbody .chat').each(function() {
                // Подсчет количества сообщений чата в разрезе сайта
                var idEmployeeSite = $(this).attr('id-work-site');
                var footChat = $('#foot_chat_total_' + idEmployeeSite);
                footChat.html((parseFloat(footChat.html()) + parseFloat($(this).find('div').html())).toFixed(2));
                // Баланс чата в разрезе сайта
                var planChat = parseFloat($('#plan_chat_' + idEmployeeSite).find('div').html());
                var footChatBal = $('#foot_chat_bal_' + idEmployeeSite);
                footChatBal.html((planChat - parseFloat(footChat.html())).toFixed(2));

                // Подсчет количества сообщений чата в разрезе клиента
                var day = $(this).closest('tr').attr('day');
                var total = $('#rid_total_' + day);
                var totalMail = total.find('td:eq(0)');
                var totalChat = total.find('td:eq(1)');
                var totalAll = total.find('td:eq(2)');

                totalChat.html((parseFloat(totalChat.html()) + parseFloat($(this).find('div').html())).toFixed(2));
                totalAll.html((parseFloat(totalChat.html()) + parseFloat(totalMail.html())).toFixed(2));
            });

            // Заполняем футер для сводных данных в разрезе даты
            var totalFooter = $(fixedSummaryData).find('tfoot>tr:eq(0)');
            var totalFooterMail = totalFooter.find('td:eq(0)');
            var totalFooterChat = totalFooter.find('td:eq(1)');
            var totalFooterAll = totalFooter.find('td:eq(2)');

            $(fixedSummaryData).find('tbody>tr').each(function () {
                totalFooterMail.html((parseFloat(totalFooterMail.html()) + parseFloat($(this).find('td:eq(0)').html())).toFixed(2));
                totalFooterChat.html((parseFloat(totalFooterChat.html()) + parseFloat($(this).find('td:eq(1)').html())).toFixed(2));
                totalFooterAll.html((parseFloat(totalFooterAll.html()) + parseFloat($(this).find('td:eq(2)').html())).toFixed(2));
            });

            // Заполняем футер для сводных данных в разрезе плана
            var totalPlanFooter = $(fixedSummaryData).find('tfoot>tr:eq(1)');
            totalPlanFooter.find('td:eq(0)').html((planEmailTotal - parseFloat(totalFooterMail.html())).toFixed(2));
            totalPlanFooter.find('td:eq(1)').html((planChatTotal - parseFloat(totalFooterChat.html())).toFixed(2));
            totalPlanFooter.find('td:eq(2)').html(((planEmailTotal + planChatTotal) - parseFloat(totalFooterAll.html())).toFixed(2));
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
                } else {
                    alert('Ошибка получения данных');
                }
            }

            $.post(BaseUrl + 'reports/approved/salary/data', data, callback, 'json');
        },
        ReloadReportAllocation: function () {
            var report_data = $('#ReportAllocation_data').find('>tbody');
            report_data.empty();

            function callback(data) {
                if (data.status) {
                    $.tmpl('reportAllocation_Template', data.records).appendTo(report_data);
                } else {
                    alert('Ошибка получения данных');
                }
            }

            $.post(BaseUrl + 'reports/lovestory/allocation/data', {}, callback, 'json');
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