import * as React from 'react'
import { connect } from 'react-redux'

import * as types from '../../actions/action-types'

/** Форма отправки изображений в чат */

@connect((store) => {
    return {
        isSpyForm: store.configState.config.isSpyForm,
        config: store.configState.config,
        recipient: store.chatState.recipient
    }
})
export default class ImageForm extends React.Component {

    // TODO: Переделать
    componentDidMount() {
        const {dispatch} = this.props;

        $("#addMessageImage").change(function (e) {
            $('#MessageImageForm').submit();
        });

        $('#MessageImageForm').ajaxForm(function(data) {
            if (!data.status) {
                dispatch({type: types.SEND_MESSAGE_FAILED, payload: data.message});
            } else {
                dispatch({type: types.SEND_MESSAGE_SUCCESS, message: data.message});
            }
        });
    }

    render() {
        const {recipient, isSpyForm} = this.props;

        return (
            <form id="MessageImageForm" action={BaseUrl + 'chat/upload'} className="avatar-file" method="post">
                <input type="hidden" id="MessageImageRecipient" name="recipient" value={recipient.id} />
                <input type="hidden" id="MessageImageRecipient" name="isChat" value={recipient.isChat} />
                <input type="file" id="addMessageImage" name="upload" tabIndex="-1" style={{display: 'none'}} />
                <div className="bootstrap-filestyle input-group" style={{position:'absolute', right: isSpyForm ? '130px' : '90px', top: '29px', cursor: 'pointer', fontSize: '24px'}}>
                    <span className="group-span-filestyle " tabIndex="0">
                        <label htmlFor="addMessageImage">
                            <span className="glyphicon glyphicon-picture" aria-hidden="true"></span>
                        </label>
                    </span>
                </div>
            </form>
        );
    }

}