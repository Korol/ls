$(document).ready(function(){

    /**
     * Функция сбора измененных данных
     *
     * @param fields массив полей, среди которых необходимо искать измененные данные
     */
    function getChangeData(fields) {
        var data = {};

        $.each(fields, function (index, field){
            // Получаем элемент содержащий значение
            var source = $('#'+field);

            if (source.length) {
                // Получаем значение элемента
                var currentValue = source.is('div')
                    ? $(source).find('input:checked').val()
                    : $(source).val();

                // Получаем оригинальное значение элемента
                var originalValue = EmployeeRecord[field];

                // Добавляем в результат если новое или оригинальное значение указано и они отличаются
                if (!($.isBlank(currentValue) && $.isBlank(originalValue)) && (currentValue != originalValue)) {
                    // Дополнительная обработка даты
                    data[field] = isDateField(source) ? toServerDate(currentValue) : currentValue;
                    EmployeeRecord[field] = currentValue;
                }
            }
        });

        return data;
    }

    // Объект для публичного использования
    $.EmployeeCard = {
        /** Инициализация объекта */
        Init: function() {
            this.InitActions();
            this.InitDynamicData();
            this.InitTemplate();
        },
        /** Инициализация событий */
        InitActions: function() {
            $('#SavePersonalData').click(this.SavePersonalData);
            $('#SaveContacts').click(this.SaveContacts);
            $('#SaveRemove').click(this.SaveRemove);
            $('#SaveWork').click(this.SaveWork)
                          .click(this.SaveWorkSites);
            $('#SavePhotoAndVideo').click(this.SavePhotoAndVideo);

            $('#EmployeeRemove').click(this.EmployeeRemove);
            $('#EmployeeMarkRemove').click(this.EmployeeMarkRemove);
            $('#EmployeeRestore').click(this.EmployeeRestore);
            $('#EmployeeSavePassword').click(this.EmployeeSavePassword);

            $(document).on("click", ".action-remove-agreement", function (e) {
                confirmRemove(function(){
                    $.EmployeeCard.RemoveAgreementRecord($(e.target).attr('record'));
                });
            });
            $(document).on("click", ".action-remove-passport", function (e) {
                confirmRemove(function(){
                    $.EmployeeCard.RemovePassportRecord($(e.target).attr('record'));
                });
            });
            $(document).on("click", ".action-remove-site", function (e) {
                var siteBlock = $(e.target).closest('.work-sites-block');

                var idWorkSite = siteBlock.attr('id-work-site');
                var idSite = siteBlock.attr('id-site');

                confirmRemove(function(){
                    $.EmployeeCard.RemoveSiteRecord(idSite, idWorkSite);
                });
            });
            $(document).on("click", ".action-append-customer", function (e) {
                var siteBlock = $(e.target).closest('.work-sites-block');

                var idWorkSite = siteBlock.attr('id-work-site');
                var idSite = siteBlock.attr('id-site');
                var idCustomer = $(e.target).attr('id-customer');

                var message = (idCustomer > 0)
                    ? 'Добавить клиента к сайту <strong>' + Sites[idSite] + '</strong>?'
                    : 'Добавить всех клиентов привязанных к сайту <strong>' + Sites[idSite] + '</strong>?';

                bootbox.confirm(message, function(result) {
                    if (result) {
                        $.EmployeeCard.SaveSiteCustomer(idSite, idWorkSite, idCustomer);
                    }
                });
            });
            $(document).on("click", ".action-remove-customer", function (e) {
                confirmRemove(function(){
                    var siteBlock = $(e.target).closest('.work-sites-block');

                    var idWorkSite = siteBlock.attr('id-work-site');
                    var idSite = siteBlock.attr('id-site');

                    $.EmployeeCard.RemoveSiteCustomer(idSite, idWorkSite, $(e.target).attr('record'));
                });
            });
            $(document).on("click", ".action-save-children", function (e) {
                $.EmployeeCard.SaveChildrenRecord($(e.target).closest('button').attr('record'));
            });
            $(document).on("click", ".action-remove-children", function (e) {
                confirmRemove(function(){
                    $.EmployeeCard.RemoveChildrenRecord($(e.target).closest('button').attr('record'));
                });
            });
            $(document).on("click", ".action-save-relative", function (e) {
                $.EmployeeCard.SaveRelativeRecord($(e.target).closest('button').attr('record'));
            });
            $(document).on("click", ".action-remove-relative", function (e) {
                confirmRemove(function(){
                    $.EmployeeCard.RemoveRelativeRecord($(e.target).closest('button').attr('record'));
                });
            });
            $(document).on("click", ".action-save-phone", function (e) {
                $.EmployeeCard.SavePhoneRecord($(e.target).closest('button').attr('record'));
            });
            $(document).on("click", ".action-remove-phone", function (e) {
                confirmRemove(function(){
                    $.EmployeeCard.RemovePhoneRecord($(e.target).closest('button').attr('record'));
                });
            });
            $(document).on("click", ".action-save-email", function (e) {
                $.EmployeeCard.SaveEmailRecord($(e.target).closest('button').attr('record'));
            });
            $(document).on("click", ".action-remove-email", function (e) {
                confirmRemove(function(){
                    $.EmployeeCard.RemoveEmailRecord($(e.target).closest('button').attr('record'));
                });
            });
            $(document).on("click", ".action-save-skype", function (e) {
                $.EmployeeCard.SaveSkypeRecord($(e.target).closest('button').attr('record'));
            });
            $(document).on("click", ".action-remove-skype", function (e) {
                confirmRemove(function(){
                    $.EmployeeCard.RemoveSkypeRecord($(e.target).closest('button').attr('record'));
                });
            });
            $(document).on("click", ".action-save-socnet", function (e) {
                $.EmployeeCard.SaveSocnetRecord($(e.target).closest('button').attr('record'));
            });
            $(document).on("click", ".action-remove-socnet", function (e) {
                confirmRemove(function(){
                    $.EmployeeCard.RemoveSocnetRecord($(e.target).closest('button').attr('record'));
                });
            });
            $(document).on("keyup", ".user-id-input", function (e) {
                delay(function(){
                    var siteID = $(e.target).closest('.work-sites-block').attr('id-site');
                    var userID = $(e.target).val();

                    $.EmployeeCard.FindCustomer(siteID, userID);
                }, 500);
            });
            $(document).on("change", "#addEmployeeAvatar", function (e) {
                $('#AvatarForm').submit();
            });

            $('#AvatarForm').ajaxForm(function(data) {
                if (data.status) {
                    $.EmployeeCard.RefreshAvatar(data.id, data.FileName);
                } else {
                    showErrorAlert(data.message)
                }
            });

            if (IsLoveStory) {
                $(document).on("click", ".action-blocked-employee", function (e) {
                    var IsBlocked = $(e.target).prop("checked");

                    bootbox.confirm((IsBlocked ? 'Заблокировать' : 'Разблокировать') + ' сотрудника №<strong>' + EmployeeID + '</strong>?', function(result) {
                        if (result) {
                            function callback(data) {
                                if (data.status) {
                                    showSuccessAlert('Пользователь успешно ' + (IsBlocked ? 'заблокирован' : 'разблокирован'));
                                } else {
                                    showErrorAlert(data.message)
                                }
                            }

                            $.post(BaseUrl + 'employee/'+EmployeeID+'/update', {data: {IsBlocked: (IsBlocked ? 1 : 0)}}, callback, 'json');
                        } else {
                            $(e.target).prop("checked", !IsBlocked);
                        }
                    });
                });
            }

            // Права доступа
            $.each(EmployeeRights, function(key, right) {
                $('#employeeAccess_'+right.TargetEmployeeID).click();
            });

            $('#SaveEmployeeAccess').click(function () {
                function callback(data) {
                    if (data.status) {
                        showSuccessAlert('Права пользователя успешно сохранены');
                    } else {
                        showErrorAlert(data.message)
                    }
                }

                var employees = [];
                $('#employeeAccess').find("input:checked").each(function(){
                    employees.push($(this).val());
                });

                var data = {
                    Employee:  EmployeeID,
                    Employees: employees
                };

                $.post(BaseUrl + '/employee/'+EmployeeID+'/rights', data, callback, 'json');
            });

            $('a[href="#Contacts"]').on('shown.bs.tab', function () {
                $.EmployeeCard.ReloadPhoneList();
                $.EmployeeCard.ReloadEmailList();
                $.EmployeeCard.ReloadSkypeList();
                $.EmployeeCard.ReloadSocnetList();
            });

            $('a[href="#PhotoAndVideo"]').on('shown.bs.tab', function () {
                $.EmployeeCard.ReloadAgreementList();
                $.EmployeeCard.ReloadPassportList();
            });

            $('a[href="#Work"]').on('shown.bs.tab', function () {
                $.EmployeeCard.ReloadSiteList();
            });
        },
        /** Инициализация динамичных данных */
        InitDynamicData: function() {
            // Обновление списка детей
            this.ReloadChildrenList();
            // Обновление списка родственников
            this.ReloadRelativesList();
        },
        /** Предварительная компиляция шаблонов */
        InitTemplate: function() {
            $("#childrenTemplate").template('childrenTemplate');
            $("#relativeTemplate").template('relativeTemplate');
            $("#phoneTemplate").template('phoneTemplate');
            $("#skypeTemplate").template('skypeTemplate');
            $("#emailTemplate").template('emailTemplate');
            $("#socnetTemplate").template('socnetTemplate');
            $("#agreementTemplate").template('agreementTemplate');
            $("#passportTemplate").template('passportTemplate');
            $("#siteTemplate").template('siteTemplate');
            $("#clientsTemplate").template('clientsTemplate');
            $("#userIdFieldTemplate").template('userIdFieldTemplate');
        },
        FindCustomer: function(siteID, userID) {
            var targetSelector = '#UserIdField_' + siteID;

            $(targetSelector).html('Поиск...');

            if (userID) {
                function callback(data) {
                    if (data.status) {
                        if (data.records){
                            $(targetSelector).empty();
                            $.tmpl('userIdFieldTemplate', data.records).appendTo(targetSelector);
                        } else {
                            $(targetSelector).html('Нет данных...');
                        }
                    } else {
                        showErrorAlert(data.message)
                    }
                }

                var urlData = BaseUrl + 'employee/'+EmployeeID+'/site/'+siteID+'/customer/'+userID+'/find';

                $.post(urlData, {}, callback, 'json');
            } else {
                $(targetSelector).html('<a href="javascript: void(0);" class="action-append-customer" id-customer="0">Выбрать всех</a>');
            }
        },
        RefreshAvatar: function(id, FileName) {
            var src = (id > 0)
                    ? BaseUrl + 'thumb/?src=/files/images/' + FileName
                    : BaseUrl + 'public/img/avatar-example.png';

            $('#AvatarBig').attr('src', src);
            $('#AvatarCard').attr('src', src);
        },
        /** Загрузка списка договоров */
        ReloadAgreementList: function () {
            this.ReloadData('#agreement', 'agreement', 'agreementTemplate');
        },
        /** Загрузка списка сканов паспорта */
        ReloadPassportList: function () {
            this.ReloadData('#passport', 'passport', 'passportTemplate');
        },
        /** Обновление списка детей */
        ReloadChildrenList: function() {
            this.ReloadData('#childrenList', 'children', 'childrenTemplate', {ID:0, EmployeeID:EmployeeID, SexID:0, FIO:'', DOB:''});
        },
        /** Обновление списка родственников */
        ReloadRelativesList: function() {
            this.ReloadData('#relativeList', 'relative', 'relativeTemplate', {ID:0, EmployeeID:EmployeeID, FIO:'', Occupation:''});
        },
        /** Обновление списка телефонов */
        ReloadPhoneList: function() {
            this.ReloadData('#phoneList', 'phone', 'phoneTemplate', {ID:0, EmployeeID:EmployeeID, Phone:''});
        },
        /** Обновление списка E-Mail */
        ReloadEmailList: function() {
            this.ReloadData('#emailList', 'email', 'emailTemplate', {ID:0, EmployeeID:EmployeeID, Email:''});
        },
        /** Обновление списка сайтов */
        ReloadSiteList: function() {
            this.ReloadData('#siteList', 'site', 'siteTemplate', null, function() {
                $(this).each(function(index, site) {
                    $.EmployeeCard.ReloadSiteCustomerList(site.ID);
                });
            });
        },
        /** Обновление списка сайтов */
        ReloadSiteCustomerList: function(SiteID) {
            this.ReloadData('#ClientsList_' + SiteID, 'site/' + SiteID + '/customer', 'clientsTemplate');
        },
        /** Обновление списка Skype */
        ReloadSkypeList: function() {
            this.ReloadData('#skypeList', 'skype', 'skypeTemplate', {ID:0, EmployeeID:EmployeeID, Skype:''});
        },
        /** Обновление списка соцсетей */
        ReloadSocnetList: function() {
            this.ReloadData('#socnetList', 'socnet', 'socnetTemplate', {ID:0, EmployeeID:EmployeeID, Socnet:''});
        },
        /**
         * Загрузка и рендер данных
         *
         * @param TargetSelector селектор контейнера для загрузки данных
         * @param TargetSegment сегмент для загрузки данных
         * @param TemplateName имя шаблона для рендера
         * @param EmptyRecord пустая запись
         * @param callbackData функция обратного вызова
         *
         */
        ReloadData: function(TargetSelector, TargetSegment, TemplateName, EmptyRecord, callbackData){
            $(TargetSelector).html('Загрузка данных...');

            function callback(data) {
                if (data.status) {
                    if (data.records){
                        $(TargetSelector).empty();
                        $.tmpl(TemplateName, data.records).appendTo(TargetSelector);
                        if (EmptyRecord){
                            $.tmpl(TemplateName, EmptyRecord).appendTo(TargetSelector);
                        }
                        if (typeof(callbackData) === "function") {
                            callbackData.call(data.records);
                        }
                        $('input:checked').change();
                    }
                } else {
                    showErrorAlert(data.message)
                }
            }

            var urlData = BaseUrl + 'employee/'+EmployeeID+'/'+TargetSegment+'/data';

            $.post(urlData, {}, callback, 'json');
        },
        /** Сохранение статичных полей с вкладки "Личные данные" */
        SavePersonalData: function() {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Личные данные успешно сохранены')
                } else {
                    showErrorAlert(data.message)
                }
            }

            var data = getChangeData(['SName', 'FName', 'MName', 'DOB', 'CardNumber', 'MaritalStatus', 'NameSatellite',
                'OccupationSatellite', 'NameFather', 'OccupationFather', 'NameMother', 'OccupationMother', 'Forming',
                'FormingNameInstitution', 'FormingFormStudy', 'FormingFaculty', 'WorkOccupation', 'WorkReasonLeaving',
                'WorkLatestDirector', 'Smoking']);

            if (!$.isBlank(data))
                $.post(BaseUrl + 'employee/'+EmployeeID+'/update', {data: data}, callback, 'json');
        },
        /** Сохранение статичных полей с вкладки "Контакты" */
        SaveContacts: function() {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Контакты успешно сохранены')
                } else {
                    showErrorAlert(data.message)
                }
            }

            var data = getChangeData(IsLoveStory ? ['Country', 'City', 'HomeAddress'] : ['City', 'HomeAddress']);

            if (!$.isBlank(data))
                $.post(BaseUrl + 'employee/'+EmployeeID+'/update', {data: data}, callback, 'json');
        },
        /** Сохранение статичных полей с вкладки "Удаление" */
        SaveRemove: function() {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Причина успешно сохранена')
                } else {
                    showErrorAlert(data.message)
                }
            }

            var data = getChangeData(['ReasonForDeleted', 'ReasonForBlocked']);

            if (!$.isBlank(data))
                $.post(BaseUrl + 'employee/'+EmployeeID+'/update', {data: data}, callback, 'json');
        },
        /** Сохранение статичных полей с вкладки "Работа" */
        SaveWork: function() {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Рабочая информация успешно сохранена')
                } else {
                    showErrorAlert(data.message)
                }
            }

            var data = getChangeData(['UserRole']);

            if (!$.isBlank(data))
                $.post(BaseUrl + 'employee/'+EmployeeID+'/update', {data: data}, callback, 'json');

            if (IsLoveStory) {
                function clearTableValue(element) {
                    return element.has('span').length
                        ? element.find('span').html()
                        : element.html();
                }

                data = {
                    Monday: clearTableValue($('#Monday')),
                    MondayNote: clearTableValue($('#MondayNote')),
                    Tuesday: clearTableValue($('#Tuesday')),
                    TuesdayNote: clearTableValue($('#TuesdayNote')),
                    Wednesday: clearTableValue($('#Wednesday')),
                    WednesdayNote: clearTableValue($('#WednesdayNote')),
                    Thursday: clearTableValue($('#Thursday')),
                    ThursdayNote: clearTableValue($('#ThursdayNote')),
                    Friday: clearTableValue($('#Friday')),
                    FridayNote: clearTableValue($('#FridayNote')),
                    Saturday: clearTableValue($('#Saturday')),
                    SaturdayNote: clearTableValue($('#SaturdayNote')),
                    Sunday: clearTableValue($('#Sunday')),
                    SundayNote: clearTableValue($('#SundayNote'))
                };
                $.post(BaseUrl +  'schedule/save', {employee: EmployeeID, data: data}, callback, 'json');
            }
        },
        SaveWorkSites: function() {
            hideAlerts();

            var sites = [];
            $('#WorkSite').find("input:checked").each(function(){
                sites.push($(this).val());
            });

            if (sites.length == 0) return;

            var data = {
                sites: sites
            };

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Рабочая информация успешно сохранена');
                    if (data.insert) {
                        $.EmployeeCard.ReloadSiteList();
                    }
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'employee/'+EmployeeID+'/site/save', {data: data}, callback, 'json');
        },
        /** Сохранение статичных полей с вкладки "Фото / Видео" */
        SavePhotoAndVideo: function() {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Видео подтверждение успешно сохранено');
                    $('#videoConfirmFrame').attr('src', $('#VideoConfirm').val());
                } else {
                    showErrorAlert(data.message)
                }
            }

            var data = getChangeData(['VideoConfirm']);

            if (!$.isBlank(data))
                $.post(BaseUrl + 'employee/'+EmployeeID+'/update', {data: data}, callback, 'json');
        },
        /** Сохранение пароля сотрудника на вкладке "Удаление" */
        EmployeeSavePassword: function() {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Пароль успешно сохранен')
                } else {
                    showErrorAlert(data.message)
                }
            }

            var data = getChangeData(['Password']);

            if (!$.isBlank(data))
                $.post(BaseUrl + 'employee/'+EmployeeID+'/update', {data: data}, callback, 'json');
        },
        /** Сохранение ребенка */
        SaveChildrenRecord: function(RecordID) {
            hideAlerts();

            var data = {
                RecordID: RecordID,
                SexID: $('#ChildrenSex_' + RecordID).find('input:radio:checked').val(),
                FIO: $('#ChildrenFIO_' + RecordID).val(),
                DOB: toServerDate($('#ChildrenDOB_' + RecordID).val())
            };

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Ребенок успешно сохранен');
                    $.EmployeeCard.ReloadChildrenList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'employee/'+EmployeeID+'/children/save', data, callback, 'json');
        },
        /** Сохранение родственника */
        SaveRelativeRecord: function(RecordID) {
            hideAlerts();

            var data = {
                RecordID: RecordID,
                FIO: $('#RelativeFIO_' + RecordID).val(),
                Occupation: $('#RelativeOccupation_' + RecordID).val()
            };

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Родственник успешно сохранен');
                    $.EmployeeCard.ReloadRelativesList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'employee/'+EmployeeID+'/relative/save', data, callback, 'json');
        },
        /** Сохранение телефона */
        SavePhoneRecord: function(RecordID) {
            hideAlerts();

            var data = {
                RecordID: RecordID,
                Phone: $('#Phone_' + RecordID).val()
            };

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Телефон успешно сохранен');
                    $.EmployeeCard.ReloadPhoneList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'employee/'+EmployeeID+'/phone/save', data, callback, 'json');
        },
        /** Сохранение E-Mail */
        SaveEmailRecord: function(RecordID) {
            hideAlerts();

            var data = {
                RecordID: RecordID,
                Email: $('#Email_' + RecordID).val()
            };

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('E-Mail успешно сохранен');
                    $.EmployeeCard.ReloadEmailList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'employee/'+EmployeeID+'/email/save', data, callback, 'json');
        },
        /** Сохранение Skype */
        SaveSkypeRecord: function(RecordID) {
            hideAlerts();

            var data = {
                RecordID: RecordID,
                Skype: $('#Skype_' + RecordID).val()
            };

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Skype успешно сохранен');
                    $.EmployeeCard.ReloadSkypeList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'employee/'+EmployeeID+'/skype/save', data, callback, 'json');
        },
        /** Сохранение социальной сети */
        SaveSocnetRecord: function(RecordID) {
            hideAlerts();

            var data = {
                RecordID: RecordID,
                Socnet: $('#Socnet_' + RecordID).val()
            };

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Социальная сеть успешно сохранена');
                    $.EmployeeCard.ReloadSocnetList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'employee/'+EmployeeID+'/socnet/save', data, callback, 'json');
        },
        SaveSiteCustomer: function(idSite, idWorkSite, idCustomer) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    if (idCustomer > 0) {
                        showSuccessAlert('Клиент успешно добавлен к сайту ' + Sites[idSite]);
                    } else {
                        showSuccessAlert('Клиенты успешно добавлен к сайту ' + Sites[idSite]);
                    }
                    $.EmployeeCard.ReloadSiteCustomerList(idWorkSite);
                } else {
                    showErrorAlert(data.message)
                }
            }

            var urlData = BaseUrl + 'employee/'+EmployeeID+'/site/'+idWorkSite+'/customer/'+idCustomer+'/save';

            $.post(urlData, {}, callback, 'json');
        },
        /** Удаление договора по ID */
        RemoveAgreementRecord: function(AgreementID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Договор успешно удален');
                    $.EmployeeCard.ReloadAgreementList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'employee/'+EmployeeID+'/agreement/'+AgreementID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление скана паспорта по ID */
        RemovePassportRecord: function(PassportID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Скан паспорта успешно удален');
                    $.EmployeeCard.ReloadPassportList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'employee/'+EmployeeID+'/passport/'+PassportID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление ребенка по ID */
        RemoveChildrenRecord: function(RecordID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.EmployeeCard.ReloadChildrenList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'employee/'+EmployeeID+'/children/'+RecordID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление родственника по ID */
        RemoveRelativeRecord: function(RecordID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.EmployeeCard.ReloadRelativesList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'employee/'+EmployeeID+'/relative/'+RecordID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление телефона по ID */
        RemovePhoneRecord: function(RecordID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.EmployeeCard.ReloadPhoneList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'employee/'+EmployeeID+'/phone/'+RecordID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление E=Mail по ID */
        RemoveEmailRecord: function(RecordID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.EmployeeCard.ReloadEmailList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'employee/'+EmployeeID+'/email/'+RecordID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление Skype по ID */
        RemoveSkypeRecord: function(RecordID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.EmployeeCard.ReloadSkypeList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'employee/'+EmployeeID+'/skype/'+RecordID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление клиента из указанного сайта */
        RemoveSiteCustomer: function(idSite, idWorkSite, RecordID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.EmployeeCard.ReloadSiteCustomerList(idWorkSite);
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'employee/'+EmployeeID+'/site/'+idSite+'/customer/' + RecordID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление социальной сети по ID */
        RemoveSocnetRecord: function(RecordID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.EmployeeCard.ReloadSocnetList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'employee/'+EmployeeID+'/socnet/'+RecordID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление сайта по ID */
        RemoveSiteRecord: function(SiteID, SiteWorkID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.EmployeeCard.ReloadSiteList();
                    $('#WorkSite_'+SiteID).click();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'employee/'+EmployeeID+'/site/'+SiteWorkID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление сотрудника */
        EmployeeRemove: function () {
            confirmRemove(function(){
                hideAlerts();

                function callback(data) {
                    if (data.status) {
                        alert('Запись успешно удалена');
                        window.location = BaseUrl + 'employee';
                    } else {
                        showErrorAlert(data.message)
                    }
                }

                var url = BaseUrl + 'employee/'+EmployeeID+'/remove';
                $.post(url, {IsFull: true}, callback, 'json');
            }, "Вы действительно хотите БЕЗВОЗВРАТНО удалить запись?");
        },
        /** Метка удаления сотрудника */
        EmployeeMarkRemove: function () {
            confirmRemove(function(){
                hideAlerts();

                function callback(data) {
                    if (data.status) {
                        showSuccessAlert('Запись успешно удалена');
                    } else {
                        showErrorAlert(data.message)
                    }
                }

                var url = BaseUrl + 'employee/'+EmployeeID+'/remove';
                $.post(url, {}, callback, 'json');
            });
        },
        /** Восстановление сотрудника */
        EmployeeRestore: function () {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно восстановлена');
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'employee/'+EmployeeID+'/restore';
            $.post(url, {}, callback, 'json');
        }
    };

    // Инициализация объекта
    $.EmployeeCard.Init();

    // Hide alerts when changing tabs
    $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
        hideAlerts();
    });

    $('body').on('hidden.bs.modal', '.remoteModal', function () {
        $(this).removeData('bs.modal');
    });

    function showErrorAlert(message) {
        $('#alertErrorMessage').text(message);
        $('#alertError').slideDown();
    }
    function showSuccessAlert(message) {
        $('#alertSuccessMessage').text(message);
        $('#alertSuccess').slideDown();
    }
    function hideAlerts() {
        $('#alertError').hide();
        $('#alertSuccess').hide();
    }
});