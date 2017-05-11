import React from "react"
import { connect } from 'react-redux';
import { BootstrapTable, TableHeaderColumn } from 'react-bootstrap-table'
import { FormGroup, FormControl, Glyphicon } from 'react-bootstrap'

import { fetchQuestions, saveQuestion } from '../../actions/customer-actions'

@connect((store) => {
    return {
        questions: store.customersState.questions,
        isEditQuestionAnswer: store.configState.config.customers.isEditQuestionAnswer
    }
})
export default class Questionnaire extends React.Component {

    state = {
        id: 0,
        answer: ''
    };

    componentDidMount() {
        this.props.dispatch(fetchQuestions(CustomerID)); // TODO: CustomerID берется из profile.php - переделать!!!
    }

    render() {
        return (
            <div>
                {this.renderTable(this.props.questions)}
            </div>
        );
    }

    renderTable = (questions) => {
        return (
            <BootstrapTable tableBodyClass="reactTableBody" data={questions} striped={true} hover={true} options={{noDataText: "Нет данных для отображения"}}>
                <TableHeaderColumn dataField='id' isKey={true} className="colBtn" columnClassName="colBtn" >#</TableHeaderColumn>
                <TableHeaderColumn dataField='question'>Вопрос</TableHeaderColumn>
                <TableHeaderColumn dataField='answer' dataFormat={this.answerFormatter} >Ответ</TableHeaderColumn>
                <TableHeaderColumn
                    dataField='editButton'
                    dataFormat={this.editFormatter}
                    className="colBtn"
                    columnClassName="colBtn"
                    dataAlign="center" />
            </BootstrapTable>
        )
    };

    answerFormatter = (cell, row) => {
        return (row.id != this.state.id)
            ?   (<div>{row.answer}</div>)
            :   (
                    <FormGroup controlId="formControlsTextarea">
                        <FormControl
                            onChange={this.onChange}
                            componentClass="textarea"
                            value={this.state.answer}
                        />
                    </FormGroup>
                )
    };

    // Обработчик редактирования ответа
    onChange = (e) => this.setState({answer: e.target.value});

    editFormatter = (cell, row) => {
        if (!this.props.isEditQuestionAnswer) return null;

        if (this.state.id == 0) {
            return (
                <a
                    onClick={this.edit.bind(this, row)}
                    className="btn-remove-site"
                    style={{color: "green"}}
                    role="button"
                    title="Редактировать"
                >
                    <Glyphicon glyph="edit" />
                </a>
            )
        }

        if (this.state.id == row.id) {
            return (
                <a
                    onClick={this.save.bind(this, row)}
                    className="btn-remove-site"
                    style={{color: "green"}}
                    role="button"
                    title="Сохранить"
                >
                    <Glyphicon glyph="floppy-disk" />
                </a>
            )
        }

        return null;
    };

    // Обработчик для кнопки "Редактировать ответ"
    edit = (row) => this.setState({ id: row.id, answer: row.answer });

    // Обработчик для кнопки "Сохранить ответ"
    save = (row) => {
        const {id, answer} = this.state;
        this.props.dispatch(saveQuestion(id, CustomerID, answer));
        this.setState({ id: 0, answer: '' });
    }

}