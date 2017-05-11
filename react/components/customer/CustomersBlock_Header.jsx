import React from 'react'
import { connect } from 'react-redux'

import { Button, Modal, Alert, FormGroup, FormControl, ControlLabel, Glyphicon } from 'react-bootstrap'

import SelectDropdown from './../common/SelectDropdown'

import { 
    changeFilterId, changeFilterFI, changeFilterCity, changeFilterStatus, changeFilterAgeMin, changeFilterAgeMax, fetchMetaCustomers, appendCustomer 
} from './../../actions/customer-actions'

@connect((store) => {
    return {
        showStateFilter: store.configState.config.customers.showStateFilter,
        showAppendButton: store.configState.config.customers.showAppendButton,
        ageMin: store.customersState.customers.ageMin,
        isLoveStoryCustomerCard: store.customersState.customers.isLoveStoryCustomerCard,
        ageMax: store.customersState.customers.ageMax,
        filter: store.customersState.customers.filter
    }
})
export default class CustomersBlock_Header extends React.Component {

    state = {
        error: false,
        fName: '',
        sName: '',
        mName: '',
        showModal: false
    };

    componentDidMount() {
        this.props.dispatch(fetchMetaCustomers());
    }

    componentWillUpdate(nextProps) {
        const { status, id, fi, city, ageMin, ageMax } = this.props.filter;
        const {
            status: nextStatus,
            id: nextId,
            fi: nextFi,
            city: nextCity,
            ageMin: nextAgeMin,
            ageMax: nextAgeMax
        } = nextProps.filter;

        // Обновление списка клиентов, если был изменен один из фильтров
        if ((status != nextStatus) || (ageMin != nextAgeMin) || (ageMax != nextAgeMax)) {
            this.props.refresh();
        } else if ((id != nextId) || (fi != nextFi) || (city != nextCity)) {
            // Задержка для текстовых полей
            if (this.timer)
                clearTimeout(this.timer);
            this.timer = setTimeout(this.props.refresh, 500);
        }
    }

    render() {
        return (
            <div className="panel assol-grey-panel">
                <table className="clients-view-table">
                    <tbody>
                        <tr>
                            <td style={{width: "25%"}}>
                                {this.renderStateFilter()}
                            </td>
                            <td style={{width: "25%"}}>
                                {this.filter_1()}
                            </td>
                            <td style={{width: "25%"}}>
                                {this.filter_2()}
                            </td>
                            <td style={{width: "25%"}}>
                                {this.renderAgeFilter()}
                            </td>
                            <td>
                                {this.renderAppendForm()}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        );
    }

    filter_1 = () => {
        const { filter, isLoveStoryCustomerCard } = this.props;

        return isLoveStoryCustomerCard
            ? (
                <FormGroup>
                    <ControlLabel>ID / ФИ</ControlLabel>
                    <input type="text" className="assol-input-style" value={filter.id} onChange={this.onChangeFilterId} />
                </FormGroup>
            )
            : (
                <FormGroup>
                    <ControlLabel>ID</ControlLabel>
                    <input type="number" className="assol-input-style" value={filter.id} onChange={this.onChangeFilterId} />
                </FormGroup>
            )
    };

    filter_2 = () => {
        const { filter, isLoveStoryCustomerCard } = this.props;

        return isLoveStoryCustomerCard
            ? (
                <FormGroup>
                    <ControlLabel>Город</ControlLabel>
                    <input type="text" className="assol-input-style" value={filter.city} onChange={this.onChangeFilterCity} />
                </FormGroup>
            )
            : (
                <FormGroup>
                    <ControlLabel>ФИ</ControlLabel>
                    <input type="text" className="assol-input-style" value={filter.fi} onChange={this.onChangeFilterFI} />
                </FormGroup>
            )
    };
    
    renderStateFilter = () => {
        const { filter, showStateFilter } = this.props;
        
        return showStateFilter ? (
            <FormGroup>
                <ControlLabel>Статус</ControlLabel>
                <SelectDropdown
                    data={[
                        { value: 0, label: "Активные" },
                        { value: 1, label: "Удаленные" }
                    ]}
                    defaultValue={filter.status}
                    onChange={this.onChangeFilterStatus}
                />
            </FormGroup>
        ) : null
    };

    renderAgeFilter = () => {
        const { filter, ageMin, ageMax } = this.props;

        // Список лет
        let ageList = [];
        for (var i = ageMin; i < ageMax; i++) {
            ageList.push(<option key={i} value={i}>{i}</option>);
        }

        return (
            <FormGroup className="clearfix">
                <ControlLabel>Возраст</ControlLabel>
                <div>
                    <div className="select-block">
                        <FormControl
                            componentClass="select"
                            className="assol-btn-style"
                            value={filter.ageMin}
                            onChange={this.onChangeFilterAgeMin}
                        >
                            <option value="0">От</option>
                            {ageList}
                        </FormControl>
                    </div>
                    <div className="select-block">
                        <FormControl
                            componentClass="select"
                            className="assol-btn-style"
                            value={filter.ageMax}
                            onChange={this.onChangeFilterAgeMax}
                        >
                            <option value="0">До</option>
                            {ageList}
                        </FormControl>
                    </div>
                </div>
            </FormGroup>
        )
    };
    
    renderAppendForm = () => {
        return this.props.showAppendButton ? (
            <div>
                <FormGroup>
                    <button
                        className="btn assol-btn add"
                        title="Добавить клиента"
                        onClick={() => this.setState({ fName: '', sName: '', mName: '', error: false, showModal: true })}
                    >
                        <Glyphicon glyph="plus" /> КЛИЕНТА
                    </button>
                </FormGroup>

                <Modal show={this.state.showModal} onHide={this.close}>
                    <Modal.Header closeButton>
                        <Modal.Title>ДОБАВИТЬ НОВОГО КЛИЕНТА</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        <FormGroup>
                            <ControlLabel>Фамилия</ControlLabel>
                            <input
                                type="text"
                                className="assol-input-style fullwidth defaultheight"
                                value={this.state.sName}
                                placeholder="Фамилия"
                                onChange={(e) => this.setState({sName: e.target.value})}
                            />
                        </FormGroup>
                        <FormGroup>
                            <ControlLabel>Имя</ControlLabel>
                            <input
                                type="text"
                                className="assol-input-style fullwidth defaultheight"
                                value={this.state.fName}
                                placeholder="Имя"
                                onChange={(e) => this.setState({fName: e.target.value})}
                            />
                        </FormGroup>
                        <FormGroup>
                            <ControlLabel>Отчество</ControlLabel>
                            <input
                                type="text"
                                className="assol-input-style fullwidth defaultheight"
                                value={this.state.mName}
                                placeholder="Отчество"
                                onChange={(e) => this.setState({mName: e.target.value})}
                            />
                        </FormGroup>

                        {this.renderAlert()}
                    </Modal.Body>
                    <Modal.Footer>
                        <Button bsStyle="primary" onClick={this.save}>Сохранить</Button>
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
    close = () => this.setState({ showModal: false });
    
    save = () => {
        const { fName, sName, mName } = this.state;

        const onSuccess = (id) => window.location = `${BaseUrl}customer/${id}/profile`;
        const onFailure = (error) => this.setState({error: error});

        appendCustomer(fName, sName, mName, onSuccess, onFailure);
    };

    onChangeFilterId = (e) => this.props.dispatch(changeFilterId(e.target.value));

    onChangeFilterFI = (e) => this.props.dispatch(changeFilterFI(e.target.value));
    
    onChangeFilterCity = (e) => this.props.dispatch(changeFilterCity(e.target.value));

    onChangeFilterStatus = (e) => this.props.dispatch(changeFilterStatus(parseInt(e.target.value)));

    onChangeFilterAgeMin = (e) => {
        let value = parseInt(e.target.value);
        this.props.dispatch(changeFilterAgeMin(value));
        // Корректируем данные, если минимальный возраст привышает максимальный
        if ((this.props.filter.ageMax > 0) && (value > this.props.filter.ageMax)) {
            this.props.dispatch(changeFilterAgeMax(value));
        }
    };

    onChangeFilterAgeMax = (e) => {
        let value = parseInt(e.target.value);
        this.props.dispatch(changeFilterAgeMax(value));

        // Корректируем данные, если максимальный возраст меньше минимального
        if ((this.props.filter.ageMin > 0) && (value < this.props.filter.ageMin)) {
            this.props.dispatch(changeFilterAgeMin(value));
        }
    };

}