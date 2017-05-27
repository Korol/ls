import React from 'react'
import { connect } from 'react-redux'
import TinyMCE from 'react-tinymce'

import { Button, Modal, Alert, Glyphicon } from 'react-bootstrap'

import {
    hideTask, changeTaskCardMode, changeEditText, appendComment, readComments, updateTask, readTask, removeTask, changeConfirmation, doneTask
} from '../../actions/task-actions'

import LoadingPanel from '../LoadingPanel'

import { TASK_CARD_MODE_VIEW, TASK_CARD_MODE_EDIT, TASK_CARD_MODE_COMMENT } from "../../constants/task";
import { TINYMCE_DEFAULT_CONFIG } from '../../constants/tinymce'

@connect((store) => {
    return {
        employee: store.configState.config.employee,
        mode: store.taskCardState.mode,
        showTaskForm: store.taskCardState.showTaskForm,
        isFetch: store.taskCardState.isFetch,
        confirmation: store.taskCardState.confirmation,
        comments: store.taskCardState.comments,
        editText: store.taskCardState.editText,
        error: store.taskCardState.error,
        task: store.taskCardState.task
    }
})
export default class TasksBlock_FormTask extends React.Component {
    
    componentDidUpdate(prevProps) {
        const { dispatch, task, employee, showTaskForm } = this.props;
        
        // 1. Прокрутка списка комментариев
        setTimeout(function () {
            var block = document.getElementById("taskCommentsList");
            if (block) {
                block.scrollTop = block.scrollHeight;
            }
        }, 100);

        // 2. Выставление метки прочтения задачи, если форма только открылась, задача не прочитана и она предназначена для текущего пользователя
        let idCurrentEmployee = parseInt(employee.id);
        let idTargetEmployee = parseInt(task.EmployeeID);
        let isUnread = parseInt(task.IsRead) == 0;

        if ((!prevProps.showTaskForm && showTaskForm) && (idCurrentEmployee == idTargetEmployee) && isUnread) {
            dispatch(readTask(task.ID));
        }
        
        // 3. Выставление метки прочтения комментарий, если форма только открылась
        if (!prevProps.showTaskForm && showTaskForm) {
            dispatch(readComments(task.ID));
        }
    }

    render() {
        const { showTaskForm, task, mode, confirmation, dispatch } = this.props;

        return (
            <Modal show={showTaskForm} onHide={this.close}>
                <Modal.Header closeButton>
                    <Modal.Title>{task.Title}</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <div className="change-task-info-table">
                        <table className="width100">
                            <thead>
                            <tr>
                                <th>Постановщик</th>
                                <th>Крайний срок</th>
                                <th>Ответственный</th>
                                <th>Сайты</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td className="width25">
                                    <div className="task-director">
                                        {`${task.Author_SName} ${task.Author_FName}`}
                                    </div>
                                </td>
                                <td className="width25">
                                    <div className="task-date">
                                        {toClientDate(task.Deadline)}
                                    </div>
                                </td>
                                <td className="width25">
                                    <div className="task-responsible">
                                        {`${task.Employee_SName} ${task.Employee_FName}`}
                                    </div>
                                </td>
                                <td className="width25">
                                    <div className="green">
                                        {task.SitesList ? `${task.SitesList}` : ``}
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div className="change-task-description-wrap">
                        <strong  style={{color: "#535c69"}}>{mode == TASK_CARD_MODE_COMMENT ? 'Новый комментарий' : 'Описание задачи'}:</strong>
                        <div style={{paddingTop: "10px"}}>
                            {this.renderTaskDescription()}
                        </div>
                    </div>

                    {this.renderAlert()}

                    {this.renderButtonGroup()}
                    {this.renderCommentGroup()}

                    <div className="checkbox-line" style={{display: mode == TASK_CARD_MODE_EDIT? "" : "none"}}>
                        <label>
                            <input
                                type="checkbox"
                                checked={confirmation}
                                onChange={(e) => dispatch(changeConfirmation(e.target.checked))}
                            />
                            <mark/>
                            <span style={{paddingLeft: "10px"}}>Требует подтверждение</span>
                        </label>
                    </div>
                </Modal.Body>
            </Modal>
        )
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

    renderTaskDescription = () => {
        const { task, mode, dispatch } = this.props;

        switch (mode) {
            case TASK_CARD_MODE_VIEW:
                return (
                    <div
                        className="change-task-description"
                        style={{wordWrap: "break-word", overflow: "auto", maxHeight: "250px", border: "solid 1px #e8e8e8", padding: "10px"}}
                        dangerouslySetInnerHTML={{__html: task.Description}}
                    ></div>
                );
            case TASK_CARD_MODE_EDIT:
                return (
                    <TinyMCE
                        content={task.Description}
                        config={TINYMCE_DEFAULT_CONFIG}
                        onChange={(e) => dispatch(changeEditText(e.target.getContent()))}
                    />
                );
            case TASK_CARD_MODE_COMMENT:
                return (
                    <TinyMCE
                        config={TINYMCE_DEFAULT_CONFIG}
                        onChange={(e) => dispatch(changeEditText(e.target.getContent()))}
                    />
                );
            default:
                return null;
        }
    };

    renderButtonGroup = () => {
        const { mode, dispatch, employee, task, refresh } = this.props;

        let idEmployee = parseInt(employee.id);
        let idAuthor = parseInt(task.AuthorID);

        let buttons = [];

        switch (mode) {
            case TASK_CARD_MODE_VIEW:
                if (idAuthor == idEmployee) {
                    buttons.push(
                        <button
                            key="edit"
                            className="btn assol-btn download right"
                            onClick={() => {
                                dispatch(changeEditText(task.Description));
                                dispatch(changeConfirmation(parseInt(task.Confirmation) == 1))
                                dispatch(changeTaskCardMode(TASK_CARD_MODE_EDIT));
                            }}
                        >РЕДАКТИРОВАТЬ</button>
                    );
                    buttons.push(
                        <button
                            key="remove"
                            className="btn assol-btn remove right"
                            onClick={() => {
                                confirmRemove(function(){
                                    dispatch(removeTask(task.ID, refresh));
                                });
                            }}
                        >УДАЛИТЬ</button>
                    );
                }
                if(task.State == 0){
                    buttons.push(
                        <button
                        key="confirm"
                        className="btn assol-btn save"
                        onClick={this.confirm}
                        >ВЫПОЛНИТЬ</button>
                    );
                }
                if(task.State == 1){
                    buttons.push(
                        <button
                        key="execute"
                        className="btn assol-btn save"
                        onClick={this.execute}
                        >ПОДТВЕРДИТЬ</button>
                    );
                }
                break;
            case TASK_CARD_MODE_EDIT:
                buttons.push(
                    <button
                        key="cancel"
                        className="btn assol-btn add right"
                        onClick={() => dispatch(changeTaskCardMode(TASK_CARD_MODE_VIEW))}
                    >ОТМЕНА</button>
                );
                buttons.push(
                    <button
                        key="save"
                        className="btn assol-btn save right"
                        onClick={this.saveTaskHandler}
                    >СОХРАНИТЬ</button>
                );
                break;
            case TASK_CARD_MODE_COMMENT:
                buttons.push(
                    <button
                        key="cancel"
                        className="btn assol-btn add right"
                        onClick={() => dispatch(changeTaskCardMode(TASK_CARD_MODE_VIEW))}
                    >ОТМЕНА</button>
                );
                buttons.push(
                    <button
                        key="save"
                        className="btn assol-btn save right"
                        onClick={this.saveCommentHandler}
                    >СОХРАНИТЬ</button>
                );
                break;
        }

        return (
            <div className="clear save-edit-wrap">
                {buttons}
            </div>
        )
    };

    renderCommentGroup = () => {
        const { dispatch, mode, comments, isFetch } = this.props;

        return mode == TASK_CARD_MODE_VIEW ? (
            <div>
                <div className="change-task-description-wrap">
                    <strong style={{color: "#535c69"}}>Комментарии:</strong>
                    <div
                        id="taskCommentsList"
                        className="change-task-description"
                        style={{wordWrap: "break-word", overflow: "auto", maxHeight: "250px", border: "solid 1px #e8e8e8", padding: "10px", marginTop: "10px"}}
                    >
                        {isFetch ? <LoadingPanel /> : comments.map((comment, index) => (
                            <div className="comment" key={comment.ID}>
                                { index > 0 ? <hr /> : null }
                                <b>{`${comment.SName} ${comment.FName} (${toClientDateTime(comment.DateCreate)})`}</b>
                                <p style={{paddingLeft: "15px"}} dangerouslySetInnerHTML={{__html: comment.Text}} />
                            </div>
                        ))}
                    </div>
                </div>

                <div className="clear" style={{paddingTop: "15px"}}>
                    <button
                        className="btn assol-btn add right"
                        onClick={() => {
                            dispatch(changeTaskCardMode(TASK_CARD_MODE_COMMENT));
                            dispatch(changeEditText(''));
                        }}
                    >ДОБАВИТЬ КОММЕНТАРИЙ</button>
                </div>
            </div>
        ) : null;
    };

    // Обработчик для кнопки закрытия модальной формы
    close = () => {
        this.props.dispatch(hideTask());
        this.props.refresh();
    };

    /** Сохранить изменения по задачи */
    saveTaskHandler = () => {
        const { dispatch, task, editText, confirmation } = this.props;
        
        dispatch(updateTask(task.ID, editText, confirmation));
    };

    /** Сохранить комментарий */
    saveCommentHandler = () => {
        const { dispatch, task, editText } = this.props;
        
        dispatch(appendComment(task.ID, editText));
    };

    /** Подтвердить выполнение задачи */
    confirm = () => {
        this.props.dispatch(hideTask());
        const { dispatch, refresh, task } = this.props;

        bootbox.confirm(`Подтвердить выполнение задачи <strong> ${task.Title} </strong>?`, function(result) {
            if (result) {
                dispatch(doneTask(task.ID, refresh, (error) => alert(error)));
            }
        });
    };

    /** Поставить метку выполнения для задачи */
    execute = () => {
        this.props.dispatch(hideTask());
        const { dispatch, refresh, task } = this.props;

        bootbox.confirm(`Поставить метку выполнения для задачи <strong> ${task.Title} </strong>?`, function(result) {
            if (result) {
                dispatch(doneTask(task.ID, refresh, (error) => alert(error)));
            }
        });
    };

}