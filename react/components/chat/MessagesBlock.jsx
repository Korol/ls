import * as React from "react";
import { connect } from 'react-redux';

import HeaderMessages from './MessagesBlock_Header';
import ListMessages from './MessagesBlock_List';
import FormMessages from './MessagesBlock_Form';
import SpyFormMessages from './MessagesBlock_SpyForm';
import LoadingPanel from '../LoadingPanel'

/** Панель сообщений */
@connect((store) => {
    return {
        isFetchMessages: store.messageState.isFetchMessages,
        recipient: store.chatState.recipient,
        isSpyForm: store.configState.config.isSpyForm,
        spyMode: store.chatState.spyMode
    }
})
export default class MessagesBlock extends React.Component {
    render() {
        const {recipient, isFetchMessages, spyMode, isSpyForm} = this.props;
        
        return (
            <div className="messageBlock">
                {/* Выводим шапку если выбран пользователь или включен режим "Шпион" */}
                {(() => (recipient || spyMode) ? <HeaderMessages /> : null)()}

                {/* Выводим список сообщений если выбран пользователь или включен режим "Шпион" + выводим панель загрузки если происходит запрос данных */}
                {(() => (recipient || spyMode) ? (isFetchMessages ? <LoadingPanel /> : <ListMessages />) : null)()}
                
                {/* Выводм форму отправки если выбран пользователь или разрешен режим "Шпион" + выводим панель выбора пользователей чата, если включен режим "Шпион" */}
                {(() => (recipient || isSpyForm) ? (spyMode ? <SpyFormMessages /> : <FormMessages />) : null)()}
            </div>
        );


    }
}