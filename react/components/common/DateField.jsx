import React from 'react'

/** Поле для выбора даты */
export default class DateField extends React.Component {

    static propTypes = {
        onChange: React.PropTypes.func.isRequired
    };

    static defaultProps = {
        value: '',
        className: 'assol-input-style',
        placeholder: 'Выбрать дату'
    };

    state = {
        id: Math.random().toString(36).substring(7)
    };

    onChange = (e) => {
        this.props.onChange(e);
    };

    componentDidMount() {
        $(document).on('dp.change', '#' + this.state.id, this.onChange);
    }

    componentWillUnmount() {
        $(document).off('dp.change', '#' + this.state.id);
    }

    render() {
        return (
            <div className="date-field">
                <input
                    type="text"
                    id={this.state.id}
                    value={this.props.value}
                    className={this.props.className}
                    placeholder={this.props.placeholder}
                    onChange={this.onChange}
                />
            </div>
        );
    }
    
}