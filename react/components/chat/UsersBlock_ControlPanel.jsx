import React from 'react'
import { connect } from 'react-redux'
import { BootstrapTable, TableHeaderColumn } from 'react-bootstrap-table'
import { Button, Glyphicon, FormGroup, FormControl, ControlLabel, Radio, Alert, OverlayTrigger, Popover } from 'react-bootstrap'
import Modal from 'react-bootstrap-modal'

import { fetchChatUsers, saveChat, removeChat, chatPanelSelectHandler, chatPanelOpen, chatPanelClose } from './../../actions/chat_control_panel-actions'

import AvatarForm from './UsersBlock_Toolbar_AvatarForm'

@connect((store) => {
    return {
        id: store.chatControlPanelState.id,
        employees: store.configState.employees,
        avatar: store.chatControlPanelState.avatar,
        selected: store.chatControlPanelState.selected,
        showModal: store.chatControlPanelState.showModal,
        error: store.chatControlPanelState.error,
        recipient: store.chatState.recipient
    }
})
export default class ControlPanel extends React.Component {

    state = {
        name: '',
        showRemoveConfirm: false,
        typeChat: 1,
        accessChat: 1
    };

    render() {
        // Флаг отключения управления для выбранного чата если:
        const disabled = !this.props.recipient                  // не выбран чат
            || (parseInt(this.props.recipient.isChat) == 0);     // это пользовательский чат
            // || (parseInt(this.props.recipient.id) == 1);        // выбран "Общий чат" с ID=1

        return (
            <div className="control-panel">
                <Button onClick={this.createHandler} style={{width: "60px"}} bsStyle="primary" title="Добавить новый чат">
                    <Glyphicon glyph="plus" />
                </Button>
                <Button onClick={this.editHandler} disabled={disabled} style={{width: "60px", marginLeft: "10px"}} bsStyle="success" title="Редактировать выбранный чат">
                    <Glyphicon glyph="edit" />
                </Button>
                <Button onClick={this.removeConfirmOpen} disabled={disabled} style={{width: "60px", marginLeft: "10px"}} bsStyle="danger" title="Удалить выбранный чат">
                    <Glyphicon glyph="remove-circle" />
                </Button>
                {this.renderForm()}
                {this.renderRemoveConfirm()}
            </div>
        );
    }

    renderForm = () => {
        const isNew = (parseInt(this.props.id) == 0);

        const popoverAccess = (
            <Popover id="popover-trigger-hover-focus" title="Настройки прав доступа">
                Карточка сотрудника -> Работа -> Доступ к сотрудникам.
            </Popover>
        );
        
        return (
            <Modal show={this.props.showModal} onHide={this.closeHandler} className>
                <Modal.Header closeButton>
                    <Modal.Title>{isNew ? "Добавление нового чата" : "Редактирование чата"}</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <FormGroup>
                        <ControlLabel>Название чата</ControlLabel>
                        <FormControl type="text" value={this.state.name} onChange={this.changeNameHandler} placeholder="Название чата" />
                    </FormGroup>

                    <div className="row">
                        <div className="col-md-2">
                            <FormGroup>
                                <ControlLabel>Изображение</ControlLabel><br/>
                                <AvatarForm />
                            </FormGroup>
                        </div>
                        <div className="col-md-4">
                            <FormGroup>
                                <ControlLabel>Тип чата</ControlLabel><br/>
                                <Radio
                                    style={{fontWeight: 'normal'}}
                                    name="typeChat"
                                    inline
                                    value="1"
                                    checked={this.state.typeChat == 1}
                                    disabled={!isNew}
                                    onChange={this.changeTypeChatHandler}
                                >
                                    Рабочий чат
                                </Radio>
                                {' '}
                                <Radio
                                    style={{fontWeight: 'normal'}}
                                    name="typeChat"
                                    inline
                                    value="0"
                                    checked={this.state.typeChat == 0}
                                    disabled={!isNew}
                                    onChange={this.changeTypeChatHandler}
                                >
                                    Общий чат
                                </Radio>
                            </FormGroup>
                        </div>
                        <div className="col-md-6">
                            <FormGroup>
                                <ControlLabel>Отображение сообщений</ControlLabel><br/>
                                <Radio
                                    style={{fontWeight: 'normal'}}
                                    name="accessChat"
                                    inline
                                    value="1"
                                    checked={this.state.accessChat == 1}
                                    onChange={this.changeAccessChatHandler}
                                >
                                    Согласно прав доступа {' '}
                                    <OverlayTrigger trigger={['hover', 'click']} placement="bottom" overlay={popoverAccess}>
                                        <Glyphicon glyph="question-sign" style={{color: "blue"}} />
                                    </OverlayTrigger>
                                </Radio>
                                {' '}
                                <Radio
                                    style={{fontWeight: 'normal'}}
                                    name="accessChat"
                                    inline
                                    value="0"
                                    checked={this.state.accessChat == 0}
                                    onChange={this.changeAccessChatHandler}
                                >
                                    Всех участников
                                </Radio>
                            </FormGroup>
                        </div>
                    </div>

                    <br />

                    {this.renderTable()}
                    {this.renderAlert()}
                </Modal.Body>
                <Modal.Footer>
                    <Button bsStyle={isNew ? "primary" : "success"} onClick={this.saveHandler}>{isNew ? "Добавить" : "Сохранить"}</Button>
                    <Button onClick={this.closeHandler}>Отмена</Button>
                </Modal.Footer>
            </Modal>
        )
    };

    renderAlert = () => {
        if (this.props.error) {
            return (
                <Alert bsStyle="danger">
                    <strong>Ошибка!</strong> {this.props.error}.
                </Alert>
            )
        }
    };

    // Обработчик для кнопки создания чата
    createHandler = () => {
        const {dispatch, id} = this.props;

        // Запрос списка пользователей для текущего чата
        this.setState({ name: '', typeChat: 1, accessChat: 1 });
        dispatch(chatPanelOpen(0));
        dispatch(fetchChatUsers(id));
    };

    // Обработчик для кнопки редактирования
    editHandler = () => {
        const {dispatch, recipient} = this.props;

        // Запрос списка пользователей для текущего чата
        this.setState({ name: recipient.name, typeChat: parseInt(recipient.type), accessChat: parseInt(recipient.access) });
        dispatch(chatPanelOpen(recipient.id));
        dispatch(fetchChatUsers(recipient.id));
    };

    // Обработчик для кнопки закрытия модальной формы
    closeHandler = () => this.props.dispatch(chatPanelClose());

    // Обработчик изменения текста вопроса
    changeNameHandler = (e) => this.setState({ name: e.target.value });

    // Обработчик изменения типа чата
    changeTypeChatHandler = (e) => this.setState({ typeChat: e.target.value });

    // Обработчик изменения прав доступа
    changeAccessChatHandler = (e) => this.setState({ accessChat: e.target.value });

    // Обработчик для кнопки "Добавить"
    saveHandler = () => {
        var employees = this.refs.table ? this.refs.table.state.selectedRowKeys : [];

        this.props.dispatch(saveChat(this.props.id, this.state.typeChat, this.state.accessChat, this.props.avatar, this.state.name, employees));
    };

    selectHandler = () => this.props.dispatch(chatPanelSelectHandler(this.refs.table.state.selectedRowKeys));
    onSelectAllHandler = (isSelected, rows) => {
        if (isSelected) {
            var selected = rows.map(function (row) {
                return row.ID;
            });
            this.props.dispatch(chatPanelSelectHandler(selected));
        } else {
            this.props.dispatch(chatPanelSelectHandler([]))
        }
    };

    renderTable = () => {
        var selectRowProp = {
            mode: "checkbox",  //checkbox for multi select, radio for single select.
            clickToSelect: true,   //click row will trigger a selection on that row.
            bgColor: "rgb(238, 193, 213)",   //selected row background color
            selected: this.props.selected,
            onSelectAll : this.onSelectAllHandler
        };

        // Отображение списка пользователей для рабочих чатов
        return (this.state.typeChat == 1) ? (
            <FormGroup>
                <ControlLabel>Список участников</ControlLabel>
                <BootstrapTable
                    data={this.props.employees}
                    striped={true}
                    hover={true}
                    selectRow={selectRowProp}
                    ref='table'
                    options={{noDataText: "Нет данных для отображения", onRowClick: this.selectHandler}}
                    height="450px"
                >
                    <TableHeaderColumn dataField='ID' isKey={ true } hidden={true} >#</TableHeaderColumn>
                    <TableHeaderColumn dataField='name'>Сотрудник</TableHeaderColumn>
                </BootstrapTable>
            </FormGroup>
        ) : null;
    };

    renderRemoveConfirm = () => {
        return (
            <Modal show={this.state.showRemoveConfirm} onHide={this.removeConfirmClose}>
                <Modal.Header closeButton>
                    <Modal.Title>Удаление чата</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    Внимание! Вместе с чатом будут удалены все сообщения! Вы действительно хотите удалить чат: <b>"{this.props.recipient.name}"</b>?
                </Modal.Body>
                <Modal.Footer>
                    <Button bsStyle="danger" onClick={this.remove}>Удалить</Button>
                    <Button onClick={ this.removeConfirmClose }>Отмена</Button>
                </Modal.Footer>
            </Modal>
        )
    };

    remove = () => {
        this.props.dispatch(removeChat(this.props.recipient.id));
        this.removeConfirmClose();
    };
    removeConfirmOpen = () => this.setState({ showRemoveConfirm: true });
    removeConfirmClose = () => this.setState({ showRemoveConfirm: false });

}