import React from 'react'
import { connect } from 'react-redux'
import TinyMCE from 'react-tinymce'
import Modal from 'react-bootstrap-modal'
import DateField from '../common/DateField'
import SelectCheckboxDropdown from '../common/SelectCheckboxDropdown'

import { changeConfirmation, changeEditText, changeTaskName, hideTask, createNewTask } from '../../actions/task-actions'

import { FormGroup, ControlLabel, Alert } from 'react-bootstrap'

import { TINYMCE_DEFAULT_CONFIG } from '../../constants/tinymce'

@connect((store) => {
    return {
        employees: store.configState.employees,
        sites: store.taskCardState.sites,
        createTaskForm: store.taskCardState.createTaskForm,
        confirmation: store.taskCardState.confirmation,
        name: store.taskCardState.name,
        editText: store.taskCardState.editText,
        error: store.taskCardState.error
    }
})
export default class TasksBlock_CreateForm extends React.Component {

    state = {
        taskDate: '',
        employees: [],
        sites: []
    };

    componentDidUpdate(prevProps, prevState, prevContext) {
        if (!prevProps.createTaskForm && this.props.createTaskForm) {
            this.setState({
                taskDate: '',
                employees: [],
                sites: []
            })
        }
    }

    render() {
        const {employees, sites, createTaskForm, name, confirmation, dispatch} = this.props;

        return (
            <Modal show={createTaskForm} onHide={this.close} className>
                <Modal.Header closeButton>
                    <Modal.Title>ДОБАВИТЬ ЗАДАЧУ</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <div className="add-task">
                        <FormGroup>
                            <ControlLabel>Название задачи:</ControlLabel>
                            <input
                                type="text"
                                className="assol-input-style"
                                placeholder="Название задачи"
                                value={name}
                                onChange={(e) => dispatch(changeTaskName(e.target.value))} />
                        </FormGroup>

                        <div className="add-task-setting">
                            <table>
                                <tbody>
                                <tr>
                                    <td>
                                        <FormGroup style={{maxWidth: "220px"}}>
                                            <ControlLabel>Ответственный:</ControlLabel>
                                            <SelectCheckboxDropdown
                                                data={
                                                    employees.map((employee) =>
                                                        ({ value: employee.ID, label: employee.name }))
                                                }
                                                selected={this.state.employees}
                                                checkAll={true}
                                                onChange={(selected) => this.setState({employees: selected})}
                                            />
                                        </FormGroup>
                                    </td>
                                    <td>
                                        <FormGroup style={{maxWidth: "220px"}}>
                                            <ControlLabel>По сайтам:</ControlLabel>
                                            <SelectCheckboxDropdown
                                                data={
                                                    sites ? sites.map((site) =>
                                                        ({ value: site.ID, label: site.Name })) : []
                                                }
                                                selected={this.state.sites}
                                                onChange={(selected) => this.setState({sites: selected})}
                                            />
                                        </FormGroup>
                                    </td>
                                    <td>
                                        <FormGroup>
                                            <ControlLabel>Крайний срок:</ControlLabel>
                                            <DateField
                                                className="assol-input-style fullwidth defaultheight"
                                                placeholder="Крайний срок"
                                                value={this.state.taskDate}
                                                onChange={(e) => this.setState({taskDate: e.target.value})}
                                            />
                                        </FormGroup>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <FormGroup>
                            <ControlLabel>Описание задачи:</ControlLabel>
                            <TinyMCE
                                config={TINYMCE_DEFAULT_CONFIG}
                                onChange={(e) => dispatch(changeEditText(e.target.getContent()))}
                            />
                        </FormGroup>

                        {this.renderAlert()}

                        <div className="clear save-edit-wrap">
                            <div className="checkbox-line">
                                <label>
                                    <input
                                        type="checkbox"
                                        checked={confirmation}
                                        onChange={(e) => dispatch(changeConfirmation(e.target.checked))}
                                    />
                                    <mark />
                                    <span style={{paddingLeft: "10px"}}>Требует подтверждение</span>
                                </label>
                            </div>
                            <button
                                className="btn assol-btn add right"
                                onClick={this.save}
                            >СОХРАНИТЬ</button>
                        </div>
                    </div>
                </Modal.Body>
            </Modal>
        );
    }

    renderAlert = () => {
        if (this.props.error) {
            return (
                <Alert bsStyle="danger">
                    <strong>Ошибка!</strong> {this.props.error}.
                </Alert>
            )
        }
    };

    // Обработчик для кнопки закрытия модальной формы
    close = () => {
        this.props.dispatch(hideTask());
        this.props.refresh();
    };

    save = () => {
        const { name, editText, confirmation, refresh } = this.props;
        const { taskDate, employees, sites } = this.state;

        this.props.dispatch(createNewTask(name, taskDate, editText, employees, sites, confirmation, refresh));
    };

}