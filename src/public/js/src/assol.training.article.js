$(document).ready(function(){

    function callback(data) {
        if (data.status) {
            window.location.href = BaseUrl + 'training/' + $('#Parent').find("input:radio:checked").val();
        } else {
            showErrorAlert(data.message)
        }
    }

    $('#bSubmit').click(function () {
        $('#alertError').hide();

        var employees = [];
        $('#employeeAccess').find("input:checked").each(function(){
            employees.push($(this).val());
        });


        var data = {
            TrainingName: $('#TrainingName').val(),
            TrainingContent: tinyMCE.activeEditor.getContent(),
            Parent: $('#Parent').find("input:radio:checked").val(),
            Employees: employees
        };

        $.post(CurrentURL, data, callback, 'json');
    });

    function showErrorAlert(message) {
        $('#alertErrorMessage').text(message);
        $('#alertError').slideDown();
    }

});