import React from "react"
import { connect } from 'react-redux';
import { Form, FormGroup, ControlLabel, FormControl, Button } from 'react-bootstrap'

import { changeReportEmail, saveReportEmail } from '../../actions/setting-actions'

@connect((store) => {
    return {
        defaultEmail: store.configState.config.email,
        email: store.configState.setting.email
    }
})
export default class ReportTab extends React.Component {

    componentDidMount() {
        const { dispatch, defaultEmail } = this.props;

        dispatch(changeReportEmail(defaultEmail));
    }

    /** Обработчик кнопки "Сохранить" */
    onClick = (e) => this.props.dispatch(saveReportEmail(this.props.email));

    /** Обработчик поля ввода */
    onChange = (e) => this.props.dispatch(changeReportEmail(e.target.value));

    render() {
        const { defaultEmail, email } = this.props;
        
        return (
            <div>
                <FormGroup controlId="settingFormEmailReport">
                    <ControlLabel>E-Mail для отчетов:</ControlLabel>
                    <FormControl 
                        type="email" 
                        placeholder="E-Mail для отчетов" 
                        value={this.props.email} 
                        onChange={this.onChange} />
                </FormGroup>
                <Button
                    type="submit"
                    className="btn assol-btn save right"
                    onClick={this.onClick}
                    style={{display: defaultEmail == email ? 'none' : ''}}
                >
                    Сохранить
                </Button>
            </div>
        );
    }

}