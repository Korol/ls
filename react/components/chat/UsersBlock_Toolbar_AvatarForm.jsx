import * as React from 'react'
import { connect } from 'react-redux'

import * as types from '../../actions/action-types'

import UserImg from './UsersBlock_UserImg'

/** Форма установки аватара для чата */

@connect((store) => {
    return {
        recipient: store.chatState.recipient,
        fileName: store.chatControlPanelState.fileName
    }
})
export default class AvatarForm extends React.Component {
    
    // TODO: Переделать
    componentDidMount() {
        const {dispatch} = this.props;

        $("#addImage").change(function (e) {
            $('#ImageForm').submit();
        });

        $('#ImageForm').ajaxForm(function(data) {
            if (!data.status) {
                dispatch({type: types.SAVE_AVATAR_CHAT_FAILED, payload: data.message});
            } else {
                dispatch({type: types.SAVE_AVATAR_CHAT_SUCCESS, avatar: data.avatar, fileName: data.FileName});
            }
        });
    }

    render() {
        const {recipient, fileName} = this.props;

        return (
            <form id="ImageForm" action={BaseUrl + 'chat/avatar'} method="post">
                <input type="hidden" id="ImageRecipient" name="recipient" value={recipient.id} />
                <input type="file" id="addImage" name="upload" tabIndex="-1" style={{display: 'none'}} />
                <div className="bootstrap-filestyle input-group" style={{cursor: 'pointer'}}>
                    <span className="group-span-filestyle " tabIndex="0">
                        <label htmlFor="addImage">
                            <UserImg
                                isChat={true}
                                online={false}
                                fileName={fileName} />
                        </label>
                    </span>
                </div>
            </form>
        );
    }

}