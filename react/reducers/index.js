import { combineReducers } from 'redux'

import chatReducer from './chat-reducer'
import taskReducer from './task-reducer'
import taskCardReducer from './task-card-reducer'
import chatControlPanelReducer from './chat_control_panel-reducer'
import messageReducer from './message-reducer'
import customersReducer from './customer-reducer'
import configReducer from './config-reducer'
import scheduleReducer from './schedule-reducer'

export default combineReducers({
    taskState: taskReducer,
    taskCardState: taskCardReducer,
    chatState: chatReducer,
    chatControlPanelState: chatControlPanelReducer,
    messageState: messageReducer,
    customersState: customersReducer,
    configState: configReducer,
    scheduleState: scheduleReducer
});