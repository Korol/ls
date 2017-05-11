import React from 'react'
import { connect } from 'react-redux'

import Toolbar from './TasksBlock_Toolbar'
import List from './TasksBlock_List'

import LoadingPanel from '../LoadingPanel'

import { fetchTasks } from './../../actions/task-actions'

@connect((store) => {
    return {
        config: store.configState.config,
        mode: store.taskState.mode,
        filterWhomTask: store.taskState.filterWhomTask,
        filterByWhomTask: store.taskState.filterByWhomTask
    }
})
export default class TasksBlock extends React.Component {

    render() {
        return this.props.config ? (
            <div>
                <Toolbar refresh={this.onRefresh} />
                <List refresh={this.onRefresh} />
            </div>
        ) : ( <LoadingPanel /> );
    }

    onRefresh = () => {
        const { dispatch, mode, filterWhomTask, filterByWhomTask } = this.props;
        // Загрузка списка задач
        dispatch(fetchTasks(mode, filterWhomTask, filterByWhomTask));
    }

}