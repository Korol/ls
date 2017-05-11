import React from 'react'
import { connect } from 'react-redux'

import { Button, Modal, Alert, Badge, FormGroup, FormControl, ControlLabel, Glyphicon } from 'react-bootstrap'
import SelectDropdown from './../common/SelectDropdown'
import CreateForm from './TasksBlock_CreateForm'

import { TASK_MODE_INBOX, TASK_MODE_OUTBOX, TASK_MODE_ARCHIVE, TASK_MODE_EXPIRED } from "../../constants/task";
import { changeMode, changeFilterWhomTask, changeFilterByWhomTask, createTask } from './../../actions/task-actions'

@connect((store) => {
    return {
        taskViewExtended: store.configState.config.taskViewExtended,
        employees: store.configState.employees,
        mode: store.taskState.mode,
        expired: store.taskState.expired,
        filterWhomTask: store.taskState.filterWhomTask,
        filterByWhomTask: store.taskState.filterByWhomTask
    }
})
export default class TasksBlock_Toolbar extends React.Component {

    componentWillUpdate(nextProps) {
        const { mode, filterWhomTask, filterByWhomTask } = this.props;

        // Обновление списка задач, если был изменен один из фильтров
        if ((mode != nextProps.mode) || (filterWhomTask != nextProps.filterWhomTask) || (filterByWhomTask != nextProps.filterByWhomTask)) {
            this.props.refresh();
        }
    }

    render() {
        return (
            <div className="panel assol-grey-panel">
                <table className="clients-view-table tasks-view-table">
                    <tbody>
                        <tr>
                            <td>
                                {this.renderNavigationGroup()}
                            </td>
                            <td style={{width: "228px"}}>
                                {this.renderFilterWhomTask()}
                            </td>
                            <td style={{width: "228px"}}>
                                {this.renderFilterByWhomTask()}
                            </td>
                            <td>
                                {this.renderAppendForm()}
                                <CreateForm  refresh={this.props.refresh} />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        )
    }

    renderNavigationGroup = () => {
        const { dispatch, mode, expired } = this.props;

        return (
            <div className="form-group">
                <div className="tasks-view-table-btns">
                    <ul>
                        <li className={mode == TASK_MODE_INBOX ? 'active' : ''}>
                            <button
                                className="btn assol-btn download"
                                onClick={(e) => dispatch(changeMode(TASK_MODE_INBOX))}
                            >
                                <span className="glyphicon" /> Входящие
                            </button>
                        </li>
                        <li className={mode == TASK_MODE_OUTBOX ? 'active' : ''}>
                            <button
                                className="btn assol-btn upload"
                                onClick={(e) => dispatch(changeMode(TASK_MODE_OUTBOX))}
                            >
                                <span className="glyphicon" /> Исходящие
                            </button>
                        </li>
                        <li className={mode == TASK_MODE_EXPIRED ? 'active' : ''}>
                            <button
                                className="btn assol-btn expired"
                                onClick={(e) => dispatch(changeMode(TASK_MODE_EXPIRED))}
                            >
                                Просроченные <Badge>{expired}</Badge>
                            </button>
                        </li>
                        <li className={mode == TASK_MODE_ARCHIVE ? 'active' : ''}>
                            <button
                                className="btn assol-btn archive"
                                onClick={(e) => dispatch(changeMode(TASK_MODE_ARCHIVE))}
                            >
                                Архив
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        )
    };

    renderFilterWhomTask = () => {
        const { mode, dispatch, filterWhomTask } = this.props;

        if ((mode == TASK_MODE_OUTBOX) || (mode == TASK_MODE_ARCHIVE )|| (mode == TASK_MODE_EXPIRED)) {
            const employees = [{ value: 0, label: "Всем" }];
            this.props.employees.forEach((employee) =>
                employees.push({ value: employee.ID, label: employee.name }));

            return (
                <FormGroup>
                    <ControlLabel>Кому поставлена</ControlLabel>
                    <SelectDropdown
                        data={employees}
                        defaultValue={filterWhomTask}
                        onChange={(e) => dispatch(changeFilterWhomTask(parseInt(e.target.value)))}
                    />
                </FormGroup>
            )
        }

        return null;
    };

    renderFilterByWhomTask = () => {
        const { mode, dispatch, filterByWhomTask } = this.props;

        if ((mode == TASK_MODE_INBOX) || (mode == TASK_MODE_ARCHIVE)) {
            const employees = [{ value: 0, label: "Всеми" }];
            this.props.employees.forEach((employee) =>
                employees.push({ value: employee.ID, label: employee.name }));

            return (
                <FormGroup>
                    <ControlLabel>Кем поставлена</ControlLabel>
                    <SelectDropdown
                        data={employees}
                        defaultValue={filterByWhomTask}
                        onChange={(e) => dispatch(changeFilterByWhomTask(parseInt(e.target.value)))}
                    />
                </FormGroup>
            )
        }

        return null;
    };

    renderAppendForm = () => {
        return (
            <button 
                className="btn assol-btn add" 
                title="Добавить задачу" 
                style={{marginBottom: "15px"}}
                onClick={(e) => this.props.dispatch(createTask(e.target.value))}
            >
                <Glyphicon glyph="plus" />
            </button>
        )
    };

}