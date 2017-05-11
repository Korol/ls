$(document).ready(function(){

    /** Количество записей на страницу */
    var pageRecordLimit = 20;
    /** Количество страниц */
    var pageCount = 1;
    /** Текущая страница */
    var currentPage = 1;

    // Объект для публичного использования
    $.Employees = {
        /** Инициализация объекта */
        Init: function() {
            this.InitActions();
            this.InitDynamicData();
            this.InitTemplate();
        },
        /** Инициализация событий */
        InitActions: function() {
            $(document).on("click", ".action-blocked-employee", function (e) {
                var employeeWrap = $(e.target).closest('.employee-wrap');
                var idEmployee = employeeWrap.attr('id-employee');
                var IsBlocked = $(e.target).prop("checked");

                bootbox.confirm((IsBlocked ? 'Заблокировать' : 'Разблокировать') + ' сотрудника №<strong>' + idEmployee + '</strong>?', function(result) {
                    if (result) {
                        $.Employees.SaveBlock(idEmployee, IsBlocked);
                    } else {
                        $(e.target).prop("checked", !IsBlocked);
                    }
                });
            });

            $(document).on("click", "#EmployeesStatus input:radio", function (e) {
                $('#CurrentPage').val(1);
                $.Employees.ReloadEmployeesList();
            });

            $(document).on("click", "#UserRole input:radio", function (e) {
                $('#CurrentPage').val(1);
                $.Employees.ReloadEmployeesList();
            });

            $(document).on("click", "#FilterSite input:radio", function (e) {
                $('#CurrentPage').val(1);
                $.Employees.ReloadEmployeesList();
            });

            $(document).on("keyup", ".filter-input", function (e) {
                delay(function(){
                    $('#CurrentPage').val(1);
                    $.Employees.ReloadEmployeesList();
                }, 500);
            });

            $(document).on("keyup", "#CurrentPage", function (e) {
                delay(function(){
                    $.Employees.ReloadEmployeesList();
                }, 500);
            });

            $('.assol-pagination-arrs .next').click(function () {
                $('#CurrentPage').val((parseInt($('#CurrentPage').val()) || 0) + 1);
                $.Employees.ReloadEmployeesList();
            });

            $('.assol-pagination-arrs .prev').click(function () {
                $('#CurrentPage').val((parseInt($('#CurrentPage').val()) || 0) - 1);
                $.Employees.ReloadEmployeesList();
            });
        },
        /** Инициализация динамичных данных */
        InitDynamicData: function() {
            this.ReloadEmployeesList();
        },
        /** Предварительная компиляция шаблонов */
        InitTemplate: function() {
            $("#employeeTemplate").template('employeeTemplate');
        },
        /** Загрузка списка пользователей */
        ReloadEmployeesList: function () {
            this.ReloadData('#employees', 'employeeTemplate')
        },
        /**
         * Загрузка и рендер данных
         *
         * @param TargetSelector селектор контейнера для загрузки данных
         * @param TemplateName имя шаблона для рендера
         * @param category
         */
        ReloadData: function(TargetSelector, TemplateName){
            $(TargetSelector).html('<span class="load-inform">Загрузка данных...</span>');

            function callback(data) {
                if (data.status) {
                    if (data.data.records){
                        $(TargetSelector).empty();
                        $.tmpl(TemplateName, data.data.records).appendTo(TargetSelector);
                    }
                    // Устанавливаем количество страниц
                    pageCount = Math.ceil(data.data.count / pageRecordLimit) || 1;
                    // Сброс текущего значения при привышение лимита
                    currentPage = parseInt($('#CurrentPage').val()) || 1;
                    if (currentPage > pageCount)
                        $('#CurrentPage').val(1);

                    $('#CountPage').html(pageCount);
                } else {
                    showErrorAlert(data.message)
                }
            }

            currentPage = parseInt($('#CurrentPage').val()) || 0;
            if (currentPage < 1)
                $('#CurrentPage').val(1);
            if (currentPage > pageCount)
                $('#CurrentPage').val(pageCount);

            var data = {
                Status: $('#EmployeesStatus').find('input:radio:checked').val(),
                FIO: $('#FilterFIO').val(),
                UserRole: $('#UserRole').find('input:radio:checked').val(),
                Site: $('#FilterSite').find('input:radio:checked').val(),
                Limit: pageRecordLimit,
                Offset: (($('#CurrentPage').val() || 1) - 1) * pageRecordLimit
            };

            var urlData = BaseUrl + 'employee/data';

            $.post(urlData, {data: data}, callback, 'json');
        },
        SaveBlock: function(idEmployee, IsBlocked) {
            var data = {
                IsBlocked: (IsBlocked ? 1 : 0)
            };

            function callback(data) {
                if (data.status) {
                    $.Employees.ReloadEmployeesList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'employee/'+idEmployee+'/update', {data: data}, callback, 'json');
        }
    };

    // Инициализация объекта
    $.Employees.Init();
});