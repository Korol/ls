import React from 'react'
import { connect } from 'react-redux'

import Modal from 'react-bootstrap-modal'

import LoadingPanel from '../LoadingPanel'
import UsersBlock from './UsersBlock'
import MessagesBlock from './MessagesBlock'

import { showChat, hideChat } from '../../actions/chat-actions'
import { fetchConfig } from '../../actions/config-actions'

/** Панель сообщений */
@connect((store) => {
    return {
        config: store.configState.config,
        isShow: store.chatState.isShow
    }
})
export default class ApplicationPanel extends React.Component {

    componentDidMount() {
        // Загрузка настроек с сервера
        this.props.dispatch(fetchConfig());

        // Привязка кнопки "Сообщения"
        let handler = this.openHandler;
        $(function () {
            $('#reactMessageDialog').click(function () {
                handler();
            });
        });
    }

    openHandler = () => {
        this.props.dispatch(showChat());
    };

    render() {
        return (
            <div>
                {this.renderForm()}
            </div>
        )
    }

    renderForm = () => {
        return (
            <Modal
                show={this.props.isShow}
                onHide={this.closeHandler}
                className="reactMessageDialog"
                aria-labelledby="messageDialogLabel"
            >
                <Modal.Header closeButton>
                    <Modal.Title id="messageDialogLabel">СООБЩЕНИЯ</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    {this.renderContentForm()}
                </Modal.Body>
            </Modal>
        )
    };

    renderContentForm = () => {
        return this.props.config ? (
            <div className="chatPanel">
                <UsersBlock />
                <MessagesBlock />
            </div>
        ) : ( <LoadingPanel /> );
    };

    // Обработчик для кнопки закрытия модальной формы
    closeHandler = () => this.props.dispatch(hideChat());
    
}