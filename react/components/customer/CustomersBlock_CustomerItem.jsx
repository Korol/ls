import React from 'react'
import { connect } from 'react-redux'

import { FormGroup, FormControl, ControlLabel, Button, Modal, Alert, Glyphicon } from 'react-bootstrap'

import DateField from '../common/DateField'

import { restoreCustomer, updateCustomer } from './../../actions/customer-actions'

@connect((store) => {
    return {
        isEditPhotoSessionDate: store.configState.config.customers.isEditPhotoSessionDate,
        isEditMeetings: store.configState.config.customers.isEditMeetings,
        isEditDelivery: store.configState.config.customers.isEditDelivery,
        isLoveStoryCustomerCard: store.customersState.customers.isLoveStoryCustomerCard,
        isAssolCustomerCard: store.customersState.customers.isAssolCustomerCard
    }
})
export default class CustomersBlock_CustomerItem extends React.Component {

    state = {
        error: false,
        showModalDateSession: false,
        showModalMeetings: false,
        showModalDelivery: false,
        meetings: this.props.customer.Meetings || '',
        delivery: this.props.customer.Delivery || '',
        DateLastPhotoSession: toClientDate(this.props.customer.DateLastPhotoSession),
        showModal: false
    };

    render() {
        const { renderFormEditMeetings, renderFormEditDelivery } = this;
        const { customer, isLoveStoryCustomerCard, isAssolCustomerCard } = this.props;

        function printPhone() {
            const phone = customer.Phone_1 || customer.Phone_2;

            if (phone) {
                return <a href={`tel:${phone}`}>{phone}</a>
            }

            return 'не указан'
        }

        function printEmail() {
            const email = isLoveStoryCustomerCard ? customer.Email : customer.FirstEmail;

            if (email) {
                return <a href={`mailto:${email}`}>{email}</a>;
            }

            return 'не указан'
        }
        
        function printDOB() {
            if (customer.DOB) {
                return `${-(moment(customer.DOB).diff(moment(), 'years'))} лет`;
            }
            
            return 'не указан'
        }

        function printWishesForManAge() {
            const { WishesForManAgeMin, WishesForManAgeMax } = customer;

            if (WishesForManAgeMin) {
                return WishesForManAgeMin + (WishesForManAgeMax ? ('-' + WishesForManAgeMax) : '') + ' лет';
            }
            
            return 'не указан'
        }

        function printAdditionally() {
            return isAssolCustomerCard ? (
                <div className="client-footnote">
                    <strong>Дополнительно:</strong>
                    <br />
                    <div className="assol-input-style client-footnote-block-wrap">
                        <div className="client-footnote-block">
                            <div className="assol-input-style client-footnote-block-in">
                                {customer.Additionally}
                            </div>
                        </div>
                    </div>
                </div>
            ) : null
        }

        function printMeetings() {
            return isLoveStoryCustomerCard ? (
                <div className="client-footnote">
                    <strong>Встречи:</strong>
                    { ' ' }
                    {renderFormEditMeetings()}
                    <br />
                    <div className="assol-input-style client-footnote-block-wrap min">
                        <div className="client-footnote-block">
                            <div className="assol-input-style client-footnote-block-in">
                                {customer.Meetings}
                            </div>
                        </div>
                    </div>
                </div>
            ) : null
        }

        function printDelivery() {
            return isLoveStoryCustomerCard ? (
                <div className="client-footnote">
                    <strong>Доставки:</strong>
                    { ' ' }
                    {renderFormEditDelivery()}
                    <br />
                    <div className="assol-input-style client-footnote-block-wrap min">
                        <div className="client-footnote-block">
                            <div className="assol-input-style client-footnote-block-in">
                                {customer.Delivery}
                            </div>
                        </div>
                    </div>
                </div>
            ) : null
        }

        return (
            <div className="client-block clear">
                <div className="client-img">
                    <a href={`${BaseUrl}customer/${customer.ID}/profile`} className="client-img-wrap">
                        <div className="client-img-in">
                            <img
                                src={
                                    BaseUrl + (
                                        (customer.Avatar > 0)
                                            ? `thumb/?src=/files/images/${customer.FileName}&w=160`
                                            : 'public/img/avatar.jpeg'
                                    )
                                }
                                alt="avatar"
                            />
                        </div>
                    </a>
                </div>
                <div className="client-info-wrap">
                    <div className="client-info">
                        <ul>
                            <li>
                                <div className="client-id">
                                    <strong>ID: </strong>
                                    <a href={`${BaseUrl}customer/${customer.ID}/profile`}>{customer.ID}</a>
                                </div>
                                {this.renderRestoreForm()}
                                <div>
                                    <strong>Фамилия:</strong> {customer.SName}
                                </div>
                                <div>
                                    <strong>Имя:</strong> {customer.FName}
                                </div>
                                <div>
                                    <strong>Телефон:</strong> {printPhone()}
                                </div>
                                <div className="email-block">
                                    <strong>E-mail:</strong> {printEmail()}
                                </div>
                                <div>
                                    <strong>Возраст:</strong> {printDOB()}
                                </div>
                                <div>
                                    <strong>Возраст мужчины:</strong> {printWishesForManAge()}
                                </div>
                                <div>
                                    <strong>Город:</strong> {customer.City ? customer.City : 'не указан'}
                                </div>
                            </li>
                            <li>
                                <div>
                                    <strong>Статус:</strong> <a href="#">{parseInt(customer.IsDeleted) ? 'удален' : 'активный'}</a>
                                </div>
                                <div>
                                    <strong>Регистрация:</strong> {toClientDate(customer.DateCreate)}
                                </div>

                                <div>
                                    <strong>Фотосессия: </strong>
                                    {(customer.DateLastPhotoSession && customer.DateLastPhotoSession != "0000-00-00 00:00:00") ? toClientDate(customer.DateLastPhotoSession) : 'не указано'}
                                    { ' ' }
                                    {this.renderFormDateLastPhotoSession()}
                                </div>

                                {printAdditionally()}
                                {printMeetings()}
                                {printDelivery()}
                            </li>
                            <li>
                                <div className="client-footnote">
                                    <strong>Последние изменения:</strong>
                                    <br />
                                    <div className="assol-input-style client-footnote-block-wrap full">
                                        <div className="client-footnote-block">
                                            <div className="assol-input-style client-footnote-block-in">
                                                {customer.WhoUpdate ? (`${customer.SNameUpdate} ${customer.FNameUpdate}`) : 'последние изменения'}
                                                <br />
                                                {toClientDateTime(customer.DateUpdate)}
                                                <br />
                                                {customer.Note}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        );
    }

    renderFormDateLastPhotoSession = () => {
        return this.props.isEditPhotoSessionDate ? (
            <a
                onClick={() => this.setState({ error: false, showModalDateSession: true, DateLastPhotoSession: toClientDate(this.props.customer.DateLastPhotoSession) })}
                className="btn-remove-site"
                style={{color: "green"}}
                role="button"
                title="Редактировать"
            >
                <Glyphicon glyph="edit" />

                <Modal show={this.state.showModalDateSession} bsSize="small" onHide={this.close}>
                    <Modal.Header closeButton>
                        <Modal.Title>Дата последней фотосессии</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        <FormGroup>
                            <ControlLabel>Дата последней фотосессии для клиента №<strong>{this.props.customer.ID}</strong></ControlLabel>
                            <DateField
                                className="assol-input-style fullwidth defaultheight"
                                placeholder="Дата последней фотосессии"
                                value={this.state.DateLastPhotoSession}
                                onChange={(e) => this.setState({DateLastPhotoSession: e.target.value})}
                            />
                        </FormGroup>

                        {this.renderAlert()}
                    </Modal.Body>
                    <Modal.Footer>
                        <Button bsStyle="primary" onClick={this.saveDateLastPhotoSession}>Сохранить</Button>
                        <Button onClick={this.close}>Отмена</Button>
                    </Modal.Footer>
                </Modal>
            </a>
        ) : null
    };

    renderFormEditMeetings = () => {
        return this.props.isEditMeetings ? (
            <a
                onClick={() => this.setState({ error: false, showModalMeetings: true, meetings: this.props.customer.Meetings || '' })}
                className="btn-remove-site"
                style={{color: "green"}}
                role="button"
                title="Редактировать"
            >
                <Glyphicon glyph="edit" />

                <Modal show={this.state.showModalMeetings} bsSize="small" onHide={this.close}>
                    <Modal.Header closeButton>
                        <Modal.Title>Встречи</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        <FormGroup>
                            <ControlLabel>Встречи клиента №<strong>{this.props.customer.ID}</strong></ControlLabel>
                            <FormControl
                                componentClass="textarea"
                                value={this.state.meetings}
                                onChange={(e) => this.setState({meetings: e.target.value})}
                            />
                        </FormGroup>

                        {this.renderAlert()}
                    </Modal.Body>
                    <Modal.Footer>
                        <Button bsStyle="primary" onClick={this.saveMeetings}>Сохранить</Button>
                        <Button onClick={this.close}>Отмена</Button>
                    </Modal.Footer>
                </Modal>
            </a>
        ) : null
    };

    renderFormEditDelivery = () => {
        return this.props.isEditDelivery ? (
            <a
                onClick={() => this.setState({ error: false, showModalDelivery: true, delivery: this.props.customer.Delivery || '' })}
                className="btn-remove-site"
                style={{color: "green"}}
                role="button"
                title="Редактировать"
            >
                <Glyphicon glyph="edit" />

                <Modal show={this.state.showModalDelivery} bsSize="small" onHide={this.close}>
                    <Modal.Header closeButton>
                        <Modal.Title>Доставки</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        <FormGroup>
                            <ControlLabel>Доставки клиента №<strong>{this.props.customer.ID}</strong></ControlLabel>
                            <FormControl
                                componentClass="textarea"
                                value={this.state.delivery}
                                onChange={(e) => this.setState({delivery: e.target.value})}
                            />
                        </FormGroup>
                        {this.renderAlert()}
                    </Modal.Body>
                    <Modal.Footer>
                        <Button bsStyle="primary" onClick={this.saveDelivery}>Сохранить</Button>
                        <Button onClick={this.close}>Отмена</Button>
                    </Modal.Footer>
                </Modal>
            </a>
        ) : null
    };

    saveDateLastPhotoSession = () => {
        const {refresh, customer} = this.props;

        const onSuccess = () => {
            this.close();
            refresh();
        };
        const onFailure = (error) => this.setState({error: error});

        updateCustomer(customer.ID, {DateLastPhotoSession: toServerDate(this.state.DateLastPhotoSession)}, onSuccess, onFailure);
    };

    saveMeetings = () => {
        const {refresh, customer} = this.props;

        const onSuccess = () => {
            this.close();
            refresh();
        };
        const onFailure = (error) => this.setState({error: error});

        updateCustomer(customer.ID, {Meetings: this.state.meetings}, onSuccess, onFailure);
    };

    saveDelivery = () => {
        const {refresh, customer} = this.props;

        const onSuccess = () => {
            this.close();
            refresh();
        };
        const onFailure = (error) => this.setState({error: error});

        updateCustomer(customer.ID, {Delivery: this.state.delivery}, onSuccess, onFailure);
    };

    renderRestoreForm = () => {
        const { ID, IsDeleted } = this.props.customer;

        return (IsDeleted > 0) ? (
            <div className="client-btn clear">
                <button
                    className="btn assol-btn add"
                    onClick={() => this.setState({ error: false, showModal: true })}
                >
                    ВОССТАНОВИТЬ
                </button>
                <Modal show={this.state.showModal} onHide={this.close}>
                    <Modal.Header closeButton>
                        <Modal.Title>ВОССТАНОВЛЕНИЕ КЛИЕНТА</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        Восстановить клиента №<strong>{ID}</strong>?

                        {this.renderAlert()}
                    </Modal.Body>
                    <Modal.Footer>
                        <Button bsStyle="primary" onClick={this.restore}>Восстановить</Button>
                        <Button onClick={this.close}>Отмена</Button>
                    </Modal.Footer>
                </Modal>
            </div>
        ) : null
    };

    renderAlert = () => {
        if (this.state.error) {
            return (
                <Alert bsStyle="danger">
                    <strong>Ошибка!</strong> {this.state.error}.
                </Alert>
            )
        }
    };

    // Обработчик для кнопки закрытия модальной формы
    close = () => this.setState({ showModal: false, showModalDateSession: false, showModalDelivery: false, showModalMeetings: false });

    restore = () => {
        const {refresh, customer} = this.props;

        const onSuccess = () => {
            this.close();
            refresh();
        };
        const onFailure = (error) => this.setState({error: error});

        restoreCustomer(customer.ID, onSuccess, onFailure);
    };
    
}