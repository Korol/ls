import * as React from "react";

/** Пользователь */
export default class UserImg extends React.Component {
    
    render() {
        return (
            <div className="user-avatar-wrap">
                <div className="user-avatar-img">
                    <img src={
                        this.props.fileName
                            ? BaseUrl + 'thumb/?src=/files/images/' + this.props.fileName + '&w=39'
                            : this.props.isChat
                                ? BaseUrl + 'public/img/chat.png'
                                : BaseUrl + 'public/img/avatar.jpeg'
                        }
                    />
                </div>

                {(() => {
                    if (!Boolean(this.props.isChat)) {
                        return <div className={'user-avatar-status ' + (this.props.online ? 'online' : 'offline')}></div>
                    }
                })()}
            </div>
        );
    }
    
}