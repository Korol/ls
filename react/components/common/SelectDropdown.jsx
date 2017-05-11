import React from 'react'

/** Выпадающий список с кнопкой */
export default class SelectDropdown extends React.Component {

    static propTypes = {
        data: React.PropTypes.array.isRequired,
        onChange: React.PropTypes.func.isRequired
    };

    state = {
        name: Math.random().toString(36).substring(7),
        select: SelectDropdown.findSelectByValue(this.props.data, this.props.defaultValue)
    };

    /** Поиск записи по значению. Если запись не найдена, то возвращаем первый элемент массива */
    static findSelectByValue = (data, value) => {
        return data.find(item => item.value == value) || data[0]
    };

    render() {
        const { data } = this.props;
        const { name, select } = this.state;

        return (
            <div className="btn-group assol-select-dropdown">
                <div className="label-placement-wrap">
                    <button className="btn" data-label-placement>
                        <span className="data-label">{select.label}</span>
                    </button>
                </div>
                <button data-toggle="dropdown" className="btn dropdown-toggle">
                    <span className="caret" />
                </button>
                <ul className="dropdown-menu">
                    {data.map(item => (
                        <li key={item.value}>
                            <input
                                type="radio"
                                id={`${name}_${item.value}`}
                                name={name}
                                value={item.value}
                                checked={item.value == select.value}
                                onChange={this.onChange}
                            />
                            <label htmlFor={`${name}_${item.value}`}>{item.label}</label>
                        </li>
                    ))}
                </ul>
            </div>
        );
    }

    // Обработчик выбора записи
    onChange = (e) => {
        this.setState({select: SelectDropdown.findSelectByValue(this.props.data, e.target.value)});
        this.props.onChange(e);
    }

}