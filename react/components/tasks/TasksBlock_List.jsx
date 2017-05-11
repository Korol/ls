import React from 'react'
import { connect } from 'react-redux'

import { Button, Modal, Alert, Glyphicon } from 'react-bootstrap'

import LoadingPanel from '../LoadingPanel'
import FormTask from './TasksBlock_FormTask'

import { TASK_MODE_INBOX, TASK_MODE_OUTBOX, TASK_MODE_ARCHIVE, TASK_MODE_EXPIRED } from "../../constants/task";

import { showTask, doneTask } from '../../actions/task-actions'

@connect((store) => {
    return {
        taskViewExtended: store.configState.config.taskViewExtended,
        mode: store.taskState.mode,
        tasks: store.taskState.tasks,
        isFetch: store.taskState.isFetch
    }
})
export default class TasksBlock_List extends React.Component {

    componentDidMount() {
        this.props.refresh();
    }

    render() {
        return (
            <div className="tasks-table-wrap">
                {this.renderTaskTable()}
                <FormTask refresh={this.props.refresh} />
            </div>
        )
    }

    renderTaskTable() {
        const { taskViewExtended, mode } = this.props;

        switch (mode) {
            case TASK_MODE_INBOX:
                return taskViewExtended ? this.renderInboxExTask() : this.renderInboxTask();
            case TASK_MODE_OUTBOX:
                return taskViewExtended ? this.renderOutboxExTask() : this.renderOutboxTask();
            case TASK_MODE_EXPIRED:
                return this.renderExpiredExTask();
            case TASK_MODE_ARCHIVE:
                return taskViewExtended ? this.renderArchiveExTask() : this.renderArchiveTask();
            default:
                return <div>Указан некорректный режим работы для раздела задач!</div>;
        }
    }

    renderInboxTask = () => {
        return (
            <table>
                <thead>
                <tr>
                    <th>Постановщик</th>
                    <th>Название</th>
                    <th>Крайний срок</th>
                    <th>Дата выполнения</th>
                    <th>Ответственный</th>
                    <th/>
                </tr>
                </thead>
                {this.renderInboxTaskBody()}
            </table>
        )
    };

    renderInboxTaskBody = () => {
        const { confirm, execute } = this;
        const { dispatch, tasks } = this.props;

        function status(task) {
            return (task.State == 1)
                ? <a href="#" className="confirm" onClick={() => confirm(task)}>подтвердить</a>
                : <a href="#" className="execute" onClick={() => execute(task)}>выполнить</a>
        }

        function unread(task) {
            if (task.IsRead==0 || task.CountNewComment > 0) {
                return (<Glyphicon glyph="bell" />)
            }

            return null;
        }

        const data = tasks.map(function (task) {
            return (
                <tr className="task-block" key={task.ID}>
                    <td>
                        <div className="task-director">
                            {`${task.Author_SName} ${task.Author_FName}`}
                        </div>
                    </td>
                    <td>
                        <div className="task-name">
                            <a href="#" onClick={() => dispatch(showTask(task))}>
                                {unread(task)}
                                <span className="title">{task.Title}</span>
                            </a>
                        </div>
                    </td>
                    <td>
                        <div className="task-date">
                            {toClientDate(task.Deadline)}
                        </div>
                    </td>
                    <td>
                        {task.DateClose ? toClientDate(task.DateClose) : null}
                    </td>
                    <td>
                        <div className="task-responsible">
                            {`${task.Employee_SName} ${task.Employee_FName}`}
                        </div>
                    </td>
                    <td>
                        <div className="task-status">
                            {status(task)}
                        </div>
                    </td>
                </tr>
            )
        });

        return this.props.isFetch
            ? this.renderTableLoadingPanel(6) : (<tbody>{data}</tbody>)
    };

    renderInboxExTask = () => {
        return (
            <table>
                <thead>
                    <tr>
                        <th>Постановщик</th>
                        <th>Задача</th>
                        <th>Крайний срок</th>
                        <th>Выполнено</th>
                    </tr>
                </thead>
                {this.renderInboxExTaskBody()}
            </table>
        )
    };

    renderInboxExTaskBody = () => {
        const { confirm, execute } = this;
        const { dispatch, tasks } = this.props;

        function status(task) {
            return (task.State == 1)
                ? <a href="#" className="confirm" onClick={() => confirm(task)}>подтвердить</a>
                : <a href="#" className="execute" onClick={() => execute(task)}>выполнить</a>
        }

        function unread(task) {
            if (task.IsRead==0 || task.CountNewComment > 0) {
                return (<Glyphicon glyph="bell" />)
            }

            return null;
        }

        const data = tasks.map(function (task) {
            return (
                <tr className="task-block" key={task.ID}>
                    <td>
                        <div className="task-director">
                            {/* Постановщик задачи. Для задач в состояние "на подтверждение" выставляем сотрудника как поставщика*/}
                            {(task.State == 1)
                                ? `${task.Employee_SName} ${task.Employee_FName}`
                                : `${task.Author_SName} ${task.Author_FName}`}
                        </div>
                    </td>
                    <td>
                        <div className="task-name">
                            <a href="#" onClick={() => dispatch(showTask(task))}>
                                {unread(task)}
                                <span className="title">{task.Title}</span>
                            </a>
                        </div>
                    </td>
                    <td>
                        <div className="task-date">
                            {toClientDate(task.Deadline)}
                        </div>
                    </td>
                    <td>
                        <div className="task-status">
                            {status(task)}
                        </div>
                    </td>
                </tr>
            )
        });

        return this.props.isFetch
            ? this.renderTableLoadingPanel(4) : (<tbody>{data}</tbody>)
    };

    renderTableLoadingPanel = (count) => {
        return (
            <tbody>
                <tr>
                    <td colSpan={count}>
                        <LoadingPanel />
                    </td>
                </tr>
            </tbody>
        )
    };

    renderOutboxTask = () => {
        return (
            <table>
                <thead>
                <tr>
                    <th>Постановщик</th>
                    <th>Название</th>
                    <th>Крайний срок</th>
                    <th>Дата выполнения</th>
                    <th>Ответственный</th>
                    <th/>
                </tr>
                </thead>
                {this.renderOutboxTaskBody()}
            </table>
        )
    };

    renderOutboxTaskBody = () => {
        const { dispatch, tasks } = this.props;

        function status(task) {
            if (task.State == 2) {
                return <span className="done">выполнено</span>
            }

            if (task.IsExpired == 1) {
                return <span className="overdue">просрочена</span>
            }

            return <span className="in-work">в работе</span>
        }

        function unread(task) {
            if (task.IsRead==0 || task.CountNewComment > 0) {
                return (<Glyphicon glyph="bell" />)
            }

            return null;
        }

        const data = tasks.map(function (task) {
            return (
                <tr className="task-block" key={task.ID}>
                    <td>
                        <div className="task-director">
                            {`${task.Author_SName} ${task.Author_FName}`}
                        </div>
                    </td>
                    <td>
                        <div className="task-name">
                            <a href="#" onClick={() => dispatch(showTask(task))}>
                                {unread(task)}
                                <span className="title">{task.Title}</span>
                            </a>
                        </div>
                    </td>
                    <td>
                        <div className="task-date">
                            {toClientDate(task.Deadline)}
                        </div>
                    </td>
                    <td>
                        {task.DateClose ? toClientDate(task.DateClose) : null}
                    </td>
                    <td>
                        <div className="task-responsible">
                            {`${task.Employee_SName} ${task.Employee_FName}`}
                        </div>
                    </td>
                    <td>
                        <div className="task-status">
                            {status(task)}
                        </div>
                    </td>
                </tr>
            )
        });

        return this.props.isFetch
            ? this.renderTableLoadingPanel(6) : (<tbody>{data}</tbody>)
    };

    renderOutboxExTask = () => {
        return (
            <table>
                <thead>
                <tr>
                    <th>Ответственный</th>
                    <th>Задача</th>
                    <th>Крайний срок</th>
                    <th>Требует подтверждение</th>
                    <th>Выполнено</th>
                </tr>
                </thead>
                {this.renderOutboxExTaskBody()}
            </table>
        )
    };

    renderOutboxExTaskBody = () => {
        const { dispatch, tasks } = this.props;

        function unreadComment(task) {
            if (task.CountNewComment > 0) {
                return (<Glyphicon glyph="bell" />)
            }

            return null;
        }

        const data = tasks.map(function (task) {
            return (
                <tr className="task-block" key={task.ID}>
                    <td>
                        <div className="task-responsible">
                            {`${task.Employee_SName} ${task.Employee_FName}`}
                        </div>
                    </td>
                    <td>
                        <div className="task-name">
                            <a href="#" onClick={() => dispatch(showTask(task))}>
                                {unreadComment(task)}
                                <span className="title">{task.Title}</span>
                            </a>
                        </div>
                    </td>
                    <td>
                        <div className="task-date">
                            {toClientDate(task.Deadline)}
                        </div>
                    </td>
                    <td>
                        {task.Confirmation > 0 ? 'Да' : ''}
                    </td>
                    <td>
                        {task.DateClose ? toClientDate(task.DateClose) : ''}
                    </td>
                </tr>
            )
        });

        return this.props.isFetch
            ? this.renderTableLoadingPanel(5) : (<tbody>{data}</tbody>)
    };

    renderExpiredExTask = () => {
        return (
            <table>
                <thead>
                <tr>
                    <th>Ответственный</th>
                    <th>Задача</th>
                    <th>Крайний срок</th>
                    <th>Требует подтверждение</th>
                </tr>
                </thead>
                {this.renderExpiredExTaskBody()}
            </table>
        )
    };

    renderExpiredExTaskBody = () => {
        const { dispatch, tasks } = this.props;

        function unreadComment(task) {
            if (task.CountNewComment > 0) {
                return (<Glyphicon glyph="bell" />)
            }

            return null;
        }

        const data = tasks.map(function (task) {
            return (
                <tr className="task-block" key={task.ID}>
                    <td>
                        <div className="task-responsible">
                            {`${task.Employee_SName} ${task.Employee_FName}`}
                        </div>
                    </td>
                    <td>
                        <div className="task-name">
                            <a href="#" onClick={() => dispatch(showTask(task))}>
                                {unreadComment(task)}
                                <span className="title">{task.Title}</span>
                            </a>
                        </div>
                    </td>
                    <td>
                        <div className="task-date">
                            {toClientDate(task.Deadline)}
                        </div>
                    </td>
                    <td>
                        {task.Confirmation > 0 ? 'Да' : ''}
                    </td>
                </tr>
            )
        });

        return this.props.isFetch
            ? this.renderTableLoadingPanel(4) : (<tbody>{data}</tbody>)
    };

    renderArchiveTask = () => {
        return (
            <table>
                <thead>
                <tr>
                    <th>Постановщик</th>
                    <th>Название</th>
                    <th>Крайний срок</th>
                    <th>Дата выполнения</th>
                    <th>Ответственный</th>
                    <th/>
                </tr>
                </thead>
                {this.renderArchiveTaskBody()}
            </table>
        )
    };

    renderArchiveTaskBody = () => {
        const { dispatch, tasks } = this.props;

        const data = tasks.map(function (task) {
            return (
                <tr className="task-block" key={task.ID}>
                    <td>
                        <div className="task-director">
                            {`${task.Author_SName} ${task.Author_FName}`}
                        </div>
                    </td>
                    <td>
                        <div className="task-name">
                            <a href="#" onClick={() => dispatch(showTask(task))}>
                                <span className="title">{task.Title}</span>
                            </a>
                        </div>
                    </td>
                    <td>
                        <div className="task-date">
                            {toClientDate(task.Deadline)}
                        </div>
                    </td>
                    <td>
                        {task.DateClose ? toClientDate(task.DateClose) : null}
                    </td>
                    <td>
                        <div className="task-responsible">
                            {`${task.Employee_SName} ${task.Employee_FName}`}
                        </div>
                    </td>
                    <td>
                        <div className="task-status">
                            <span className="done">выполнено</span>
                        </div>
                    </td>
                </tr>
            )
        });

        return this.props.isFetch
            ? this.renderTableLoadingPanel(6) : (<tbody>{data}</tbody>)
    };

    renderArchiveExTask = () => {
        return (
            <table>
                <thead>
                <tr>
                    <th>Постановщик</th>
                    <th>Задача</th>
                    <th>Крайний срок</th>
                    <th>Ответственный</th>
                    <th/>
                </tr>
                </thead>
                {this.renderArchiveExTaskBody()}
            </table>
        )
    };

    renderArchiveExTaskBody = () => {
        const { dispatch, tasks } = this.props;

        const data = tasks.map(function (task) {
            return (
                <tr className="task-block" key={task.ID}>
                    <td>
                        <div className="task-director">
                            {`${task.Author_SName} ${task.Author_FName}`}
                        </div>
                    </td>
                    <td>
                        <div className="task-name">
                            <a href="#" onClick={() => dispatch(showTask(task))}>
                                <span className="title">{task.Title}</span>
                            </a>
                        </div>
                    </td>
                    <td>
                        <div className="task-date">
                            {toClientDate(task.Deadline)}
                        </div>
                    </td>
                    <td>
                        <div className="task-responsible">
                            {`${task.Employee_SName} ${task.Employee_FName}`}
                        </div>
                    </td>
                    <td>
                        <div className="task-status">
                            <span className="done">выполнено</span>
                        </div>
                    </td>
                </tr>
            )
        });

        return this.props.isFetch
            ? this.renderTableLoadingPanel(5) : (<tbody>{data}</tbody>)
    };

    /** Подтвердить выполнение задачи */
    confirm = (task) => {
        const { dispatch, refresh } = this.props;

        bootbox.confirm(`Подтвердить выполнение задачи <strong> ${task.Title} </strong>?`, function(result) {
            if (result) {
                dispatch(doneTask(task.ID, refresh, (error) => alert(error)));
            }
        });
    };

    /** Поставить метку выполнения для задачи */
    execute = (task) => {
        const { dispatch, refresh } = this.props;

        bootbox.confirm(`Поставить метку выполнения для задачи <strong> ${task.Title} </strong>?`, function(result) {
            if (result) {
                dispatch(doneTask(task.ID, refresh, (error) => alert(error)));
            }
        });
    };

}