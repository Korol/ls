$(document).ready(function(){
    "use strict";

    /** Список отчетов */
    const REPORT_INDIVIDUAL_LIST = {Level: 0};
    /** Индивидуальные отчеты -> Ежедневный отчет */
    const REPORT_INDIVIDUAL_DAILY = {Level: 1, Name: 'Ежедневный отчет'};
    /** Индивидуальные отчеты -> Отчет по зарплате */
    const REPORT_INDIVIDUAL_SALARY = {Level: 2, Name: 'Отчет по зарплате'};
    /** Индивидуальные отчеты -> Подтвержденная зарплата */
    const REPORT_INDIVIDUAL_APPROVED_SALARY = {Level: 3, Name: 'Подтвержденная зарплата'};
    /** Индивидуальные отчеты -> Таблица распределения */
    const REPORT_INDIVIDUAL_ALLOCATION = {Level: 4, Name: 'Таблица распределения'};

    var pathLevel = REPORT_INDIVIDUAL_LIST.Level;

    function getIindividualBread() {
        switch (pathLevel) {
            case REPORT_INDIVIDUAL_DAILY.Level:
                return {Name: REPORT_INDIVIDUAL_DAILY.Name, IsLast: true};
            case REPORT_INDIVIDUAL_SALARY.Level:
                return {Name: REPORT_INDIVIDUAL_SALARY.Name, IsLast: true};
            case REPORT_INDIVIDUAL_APPROVED_SALARY.Level:
                return {Name: REPORT_INDIVIDUAL_APPROVED_SALARY.Name, IsLast: true};
            case REPORT_INDIVIDUAL_ALLOCATION.Level:
                return {Name: REPORT_INDIVIDUAL_ALLOCATION.Name, IsLast: true};
            default:
                return [];
        }
    }

    var path = {
        individual: {
            data: [
                {Level: REPORT_INDIVIDUAL_DAILY.Level, Name: REPORT_INDIVIDUAL_DAILY.Name, IsDoc: true},
                {Level: REPORT_INDIVIDUAL_SALARY.Level, Name: REPORT_INDIVIDUAL_SALARY.Name, IsDoc: true},
                {Level: REPORT_INDIVIDUAL_APPROVED_SALARY.Level, Name: REPORT_INDIVIDUAL_APPROVED_SALARY.Name, IsDoc: true},
                {Level: REPORT_INDIVIDUAL_ALLOCATION.Level, Name: REPORT_INDIVIDUAL_ALLOCATION.Name, IsDoc: true}
            ]
        }
    };

    // Объект для публичного использования
    $.ReportListTranslate = {
        /** Инициализация объекта */
        Init: function() {
            this.InitActions();
            this.InitTemplate();
            this.InitDynamicData();
        },
        /** Инициализация событий */
        InitActions: function() {
            $(document).on('click', '.report-folder>a, .report-bread', function (e) {
                var record = $(e.target).closest('[level]');
                pathLevel = parseInt(record.attr('level'));

                $.ReportListTranslate.ReloadReportList();
            });
        },
        /** Инициализация динамичных данных */
        InitDynamicData: function() {
            $.ReportListTranslate.ReloadReportList();
        },
        /** Предварительная компиляция шаблонов */
        InitTemplate: function() {
            $("#reportsTemplate").template('reportsTemplate');
        },
        /** Загрузка списка папок и отчетов */
        ReloadReportList: function () {
            $("#reports").html('Загрузка данных...');

            function render(records) {
                $("#reports").empty();
                $.tmpl('reportsTemplate', records).appendTo("#reports");
            }

            switch (pathLevel) {
                case REPORT_INDIVIDUAL_LIST.Level:
                    render(path.individual);
                    break;
                case REPORT_INDIVIDUAL_DAILY.Level:
                case REPORT_INDIVIDUAL_SALARY.Level:
                case REPORT_INDIVIDUAL_APPROVED_SALARY.Level:
                case REPORT_INDIVIDUAL_ALLOCATION.Level:
                    render({bread: getIindividualBread()});
                    break;
                default:
                    alert('Ошибка загрузки данных');
            }

            $.ReportListTranslate.ShowReport(pathLevel);
        },
        ShowReport: function (level) {
            $('#ReportIndividualDaily').toggle(level == REPORT_INDIVIDUAL_DAILY.Level);
            $('#ReportIndividualSalary').toggle(level == REPORT_INDIVIDUAL_SALARY.Level);
            $('#ReportApprovedSalary').toggle(level == REPORT_INDIVIDUAL_APPROVED_SALARY.Level);
            $('#ReportAllocation').toggle(level == REPORT_INDIVIDUAL_ALLOCATION.Level);

            switch (level) {
                case REPORT_INDIVIDUAL_DAILY.Level:
                    $.ReportTranslate.ReloadReportMountMeta();
                    break;
                case REPORT_INDIVIDUAL_SALARY.Level:
                    $.ReportTranslate.ReloadReportSalary();
                    break;
                case REPORT_INDIVIDUAL_APPROVED_SALARY.Level:
                    $.ReportTranslate.ReloadReportApprovedSalary();
                    break;
                case REPORT_INDIVIDUAL_ALLOCATION.Level:
                    $.ReportTranslate.ReloadReportAllocation();
                    break;
            }
        }
    };

    // Инициализация объекта
    $.ReportListTranslate.Init();
});