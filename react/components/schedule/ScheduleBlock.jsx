import React from "react";
import {connect} from "react-redux";
import LoadingPanel from "../LoadingPanel";

import { fetchSchedules, saveSchedule } from '../../actions/schedule-actions';

@connect((store) => {
    return {
        config: store.configState.config,
        isFetch: store.scheduleState.isFetch,
        schedules: store.scheduleState.schedules,
        schedule: store.scheduleState.schedule
    }
})
export default class ScheduleBlock extends React.Component {

    componentDidMount() {
        const { dispatch } = this.props;

        dispatch(fetchSchedules());

        // TODO: переписать этот бред

        var isEdit = false;

        $(document).on('click', '#editSchedule', function() {
            isEdit = !isEdit;

            updateTableMode();

            if (!isEdit) saveSchedule2();

            $('#editSchedule')
                .html(isEdit ? 'СОХРАНИТЬ' : 'РЕДАКТИРОВАТЬ')
                .removeClass(isEdit ? 'add' : 'save')
                .addClass(isEdit ? 'save' : 'add');
        });

        function saveSchedule2() {
            function clearTableValue(element) {
                return element.has('span').length
                    ? element.find('span').html()
                    : element.html();
            }

            var data = {
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

            dispatch(saveSchedule(data, () => dispatch(fetchSchedules())));
        }

        function updateTableMode() {
            $('.schedule-data').each(function(index, cell) {
                if (cell.contentEditable != null) {
                    $(cell).attr("contentEditable", isEdit);
                } else {
                    if (isEdit) {
                        $(cell).html("<input type='text' style='width: 100%' value='"+$(cell).html()+"'>");
                    } else {
                        $(cell).html($(cell).find('input').val());
                    }
                }
            });
        }

    }

    render() {
        return this.props.config && !this.props.isFetch ? (
            <div>
                { this.renderTable() }
                { this.renderCard() }
            </div>
        ) : ( <LoadingPanel /> );
    }

    renderTable = () => {
        return (
            <table className="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Сотрудники</th>
                    <th>ПН</th>
                    <th>ВТ</th>
                    <th>СР</th>
                    <th>ЧТ</th>
                    <th>ПТ</th>
                    <th>СБ</th>
                    <th>ВС</th>
                </tr>
                </thead>
                <tbody>
                {
                    this.props.schedules.map((schedule, index) =>
                        <tr key={index} style={parseInt(schedule.IsOnline) > 0 ? {background: "#3aff3a"} : null}>
                            <td>{`${schedule.SName} ${schedule.FName}`}</td>
                            <td>{schedule.Monday}</td>
                            <td>{schedule.Tuesday}</td>
                            <td>{schedule.Wednesday}</td>
                            <td>{schedule.Thursday}</td>
                            <td>{schedule.Friday}</td>
                            <td>{schedule.Saturday}</td>
                            <td>{schedule.Sunday}</td>
                        </tr>
                    )
                }
                </tbody>
            </table>
        )
    };

    renderCard = () => {
        const {schedule} = this.props;

        return (
            <div>
                <p className="my-schedule">Мой график</p>

                <table className="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Дни работы</th>
                        <th>Часы работы</th>
                        <th>Примечание</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Понедельник</td>
                            <td className="schedule-data" id="Monday">{schedule.Monday}</td>
                            <td className="schedule-data" id="MondayNote">{schedule.MondayNote}</td>
                        </tr>
                        <tr>
                            <td>Вторник</td>
                            <td className="schedule-data" id="Tuesday">{schedule.Tuesday}</td>
                            <td className="schedule-data" id="TuesdayNote">{schedule.TuesdayNote}</td>
                        </tr>
                        <tr>
                            <td>Среда</td>
                            <td className="schedule-data" id="Wednesday">{schedule.Wednesday}</td>
                            <td className="schedule-data" id="WednesdayNote">{schedule.WednesdayNote}</td>
                        </tr>
                        <tr>
                            <td>Четверг</td>
                            <td className="schedule-data" id="Thursday">{schedule.Thursday}</td>
                            <td className="schedule-data" id="ThursdayNote">{schedule.ThursdayNote}</td>
                        </tr>
                        <tr>
                            <td>Пятница</td>
                            <td className="schedule-data" id="Friday">{schedule.Friday}</td>
                            <td className="schedule-data" id="FridayNote">{schedule.FridayNote}</td>
                        </tr>
                        <tr>
                            <td>Суббота</td>
                            <td className="schedule-data" id="Saturday">{schedule.Saturday}</td>
                            <td className="schedule-data" id="SaturdayNote">{schedule.SaturdayNote}</td>
                        </tr>
                        <tr>
                            <td>Воскресенье</td>
                            <td className="schedule-data" id="Sunday">{schedule.Sunday}</td>
                            <td className="schedule-data" id="SundayNote">{schedule.SundayNote}</td>
                        </tr>
                    </tbody>
                </table>

                <button
                    id="editSchedule"
                    className="btn assol-btn add right"
                    style={{marginBottom: "50px", width: "220px"}}
                >
                    РЕДАКТИРОВАТЬ
                </button>
            </div>
        )
    };
    
}