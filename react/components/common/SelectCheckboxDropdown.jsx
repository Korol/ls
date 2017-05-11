import React from 'react'

/** Выпадающий список с кнопкой */
export default class SelectCheckboxDropdown extends React.Component {

    static propTypes = {
        data: React.PropTypes.array.isRequired,
        selected: React.PropTypes.array.isRequired,
        onChange: React.PropTypes.func.isRequired
    };

    /**
     * @property checkAll {bool} - отображения строки выбора всех элементов
     */
    static defaultProps = {
        checkAll: false
    };

    state = {
        name: Math.random().toString(36).substring(7)
    };

    render() {
        const { data, checkAll, selected } = this.props;
        const { name } = this.state;

        let records = [];

        if (checkAll) {
            records.push(
                <li key="all">
                    <input
                        type="checkbox"
                        id={`${name}_all`}
                        value={0}
                        checked={selected.length == data.length}
                        onChange={this.onAllChange}
                    />
                    <label htmlFor={`${name}_all`}>Все</label>
                </li>
            );
        }

        data.forEach(item => records.push(
            <li key={item.value}>
                <input
                    type="checkbox"
                    id={`${name}_${item.value}`}
                    value={item.value}
                    checked={selected.indexOf(parseInt(item.value)) > -1}
                    onChange={this.onChange}
                />
                <label htmlFor={`${name}_${item.value}`}>{item.label}</label>
            </li>
        ));

        return (
            <div className="btn-group assol-select-dropdown">
                <div className="label-placement-wrap">
                    <button className="btn" data-label-placement>Выбрать</button>
                </div>
                <button data-toggle="dropdown" className="btn dropdown-toggle">
                    <span className="caret" />
                </button>
                <ul className="dropdown-menu">
                    {records}
                </ul>
            </div>
        );
    }

    onAllChange = (e) => {
        this.props.onChange(e.target.checked ? this.props.data.map(item => (parseInt(item.value))) : []);
    };

    // Обработчик выбора записи
    onChange = (e) => {
        let records = [...this.props.selected];
        let value = parseInt(e.target.value);
        let index = records.indexOf(value);

        if (e.target.checked) {
            if (index == -1) {
                records.push(value);
            }
        } else {
            if (index > -1) {
                records.splice(index, 1);
            }
        }

        this.props.onChange(records);
    }

}