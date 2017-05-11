import React from 'react'
import { BootstrapTable, TableHeaderColumn } from 'react-bootstrap-table'

import { connect } from 'react-redux';
import { Button, Modal, FormGroup, FormControl, Glyphicon } from 'react-bootstrap'

import { addQuestion, editQuestion, removeQuestion } from './../../actions/setting-actions'

import LoadingPanel from '../LoadingPanel';

@connect((store) => {
    return {
        email: store.configState.setting.email,
        isFetchQuestions: store.configState.setting.isFetchQuestions,
        questions: store.configState.setting.questions
    }
})
export default class CustomerProfileTabPane extends React.Component {

    state = {
        id: 0,
        isNew: true,
        question: '',
        showRemoveConfirm: false,
        showModal: false
    };

    // Обработчик для кнопки сохранить модальной формы
    save = () => {
        const { id, question } = this.state;

        // TODO: Добавить валидацию формы + вывод ошибки + вывод ошибки от сервера
        if (question.trim().length) {
            if (this.state.isNew) {
                this.props.dispatch(addQuestion(question));
            } else {
                this.props.dispatch(editQuestion(id, question));
            }
        }

        // TODO: Как привязать к сохранению? Вынести showModal в глобальную область видимости?
        this.setState({ showModal: false });
    };

    // Обработчик для кнопки закрытия модальной формы
    close = () => this.setState({ showModal: false });

    removeConfirmOpen = (row) => this.setState({ showRemoveConfirm: true, id: row.id, question: row.question });

    removeConfirmClose = () => this.setState({ showRemoveConfirm: false });

    // Обработчик для кнопки сохранить модальной формы
    remove = () => {
        this.props.dispatch(removeQuestion(this.state.id));

        // TODO: Как привязать к сохранению? Вынести showModal в глобальную область видимости?
        this.setState({ showRemoveConfirm: false });
    };

    // Обработчик для кнопки "Добавить вопрос"
    open = () => this.setState({ showModal: true, isNew: true, id: 0, question: '' });

    // Обработчик для кнопки "Редактировать вопрос"
    edit = (row) => this.setState({ showModal: true, isNew: false, id: row.id, question: row.question });

    // Обработчик изменения текста вопроса
    changeQuestion = (e) => this.setState({ question: e.target.value });

    editFormatter = (cell, row) => {
        return (
            <a onClick={this.edit.bind(this, row)} className="btn-remove-site" style={{color: "green"}} role="button" title="Редактировать">
                <Glyphicon glyph="edit" />
            </a>
        )
    };

    removeFormatter = (cell, row) => {
        return (
            <a onClick={this.removeConfirmOpen.bind(this, row)} className="btn-remove-site" role="button" title="Удалить">
                <Glyphicon glyph="remove-circle" />
            </a>
        )
    };

    render() {
        return this.props.isFetchQuestions ? <LoadingPanel /> : (
            <div>
                {this.renderTable(this.props.questions)}

                <Button className="btn assol-btn add right" onClick={this.open}>
                    Добавить вопрос
                </Button>

                {this.renderForm()}
                {this.renderRemoveConfirm()}
            </div>
        );
    }

    renderTable = (questions) => {
        return (
            <BootstrapTable tableBodyClass="reactTableBody" data={questions} striped={true} hover={true} options={{noDataText: "Нет данных для отображения"}}>
                <TableHeaderColumn dataField='id' isKey={ true } className="colBtn" columnClassName="colBtn" >#</TableHeaderColumn>
                <TableHeaderColumn dataField='question'>Вопрос</TableHeaderColumn>
                <TableHeaderColumn dataField='editButton' dataFormat={this.editFormatter} className="colBtn" columnClassName="colBtn" dataAlign="center" />
                <TableHeaderColumn dataField='removeButton' dataFormat={this.removeFormatter} className="colBtn" columnClassName="colBtn" dataAlign="center" />
            </BootstrapTable>
        )
    };
    
    renderForm = () => {
        return (
            <Modal show={this.state.showModal} onHide={this.close}>
                <Modal.Header closeButton>
                    <Modal.Title>{this.state.isNew ? 'Добавление нового вопроса' : 'Редактирование вопроса'}</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <FormGroup>
                        <FormControl type="text" value={this.state.question} onChange={this.changeQuestion} placeholder="Текст вопроса" />
                    </FormGroup>
                </Modal.Body>
                <Modal.Footer>
                    <Button bsStyle="primary" onClick={this.save}>Сохранить</Button>
                    <Button onClick={this.close}>Отмена</Button>
                </Modal.Footer>
            </Modal>
        )
    };

    renderRemoveConfirm = () => {
        return (
            <Modal show={this.state.showRemoveConfirm} onHide={this.removeConfirmClose}>
                <Modal.Header closeButton>
                    <Modal.Title>Удаление вопроса</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    Внимание! Вместе с вопросом будут удалены ответы из карточек клиентов! Вы действительно хотите удалить вопрос: <b>"{this.state.question}"</b>?
                </Modal.Body>
                <Modal.Footer>
                    <Button bsStyle="danger" onClick={this.remove}>Удалить</Button>
                    <Button onClick={ this.removeConfirmClose }>Отмена</Button>
                </Modal.Footer>
            </Modal>
        )
    };

}