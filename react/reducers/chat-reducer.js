import * as types from '../actions/action-types'

/**
 * @property {boolean}          isShow              - состояние модального окна с чатом
 * @property {boolean}          isFetchChats        - флаг загрузки списка пользователей и чатов
 * @property {Array}            chats               - список пользователей и чатов
 * @property {Array}            unread              - информация о новых сообщениях
 * @property {Array}            online              - информация об онлайн пользователей
 * @property {string}           filterText          - текст для фильтрации списка пользователей
 * @property {boolean | object} recipient           - выбранный пользователь или чат
 * @property {boolean | object} recipientChatInfo   - информация о выбранном пользователе (только для чатов с типом 2)
 * @property {boolean}          spyMode             - флаг режима "Шпион"
 * @property {boolean | object} spySender           - отправитель в режиме "Шпион"
 * @property {boolean | object} spyRecipient        - получатель в режиме "Шпион" 
 */
const initialState = {
    isShow: false,
    isFetchChats: false,
    chats: [],
    unread: [],
    online: [],
    filterText: '',
    recipient: false,
    recipientChatInfo: false,
    spyMode: false,
    spySender: false,
    spyRecipient: false
};

export default function(state = initialState, action) {
    switch (action.type) {
        case types.CHAT_SHOW:
            return {...state, isShow: true};
        case types.CHAT_HIDE:
            return {...state, isShow: false, recipient: false};
        case types.CHANGE_RECIPIENT:
            return {...state, recipient: action.recipient};
        case types.SPY_MODE_ON:
            return {...state, spyMode: true, spySender: false, spyRecipient: false, recipientChatInfo: false, recipient: false};
        case types.SPY_MODE_OFF:
            return {...state, spyMode: false};
        case types.SPY_CHANGE_SENDER:
            return {...state, spySender: action.sender};
        case types.SPY_CHANGE_RECIPIENT:
            return {...state, spyRecipient: action.recipient};
        case types.FETCH_CHATS_START:
            return {...state, isFetchChats: true};
        case types.FETCH_CHATS_SUCCESS:
            return {
                ...state, isFetchChats: false, 
                chats: sortChats(action.chats, action.unread, action.userNameIsFirst),
                unread: action.unread, 
                online: action.online
            };
        case types.FETCH_CHATS_FAILED:
            return {...state, isFetchChats: false};
        case types.FETCH_ONLINE_SUCCESS:
            return {...state, online: action.online};
        case types.FETCH_UNREAD_SUCCESS:
            return {
                ...state, unread: action.unread, 
                chats: sortChats(state.chats, action.unread, action.userNameIsFirst)};
        case types.CHANGE_CHAT_FILTER:
            return {...state, filterText: action.filterText};
        case types.CHANGE_RECIPIENT_CHAT_INFO:
            return {...state, recipientChatInfo: action.recipientChatInfo};
        case types.SAVE_CHAT_SUCCESS:
            var chatsUpdate = [...state.chats];
            var chatIndex = null;

            chatsUpdate.find(function (chat, index) {
                var isUpdateChat = (parseInt(chat.id) == parseInt(action.chat.id))
                    && (parseInt(chat.isChat) == parseInt(action.chat.isChat));

                if (isUpdateChat) {
                    chatIndex = index;
                }

                return isUpdateChat;
            });

            // Если чат найден, то обновляем его
            if (chatIndex != null) {
                chatsUpdate[chatIndex] = action.chat;
            } else {
                chatsUpdate.push(action.chat);
            }

            return {...state, chats: sortChats(chatsUpdate, state.unread, state.userNameIsFirst), recipient: action.chat};
        case types.REMOVE_CHAT_SUCCESS:
            var chatsRemoveUpdate = [...state.chats];
            var chatRemoveIndex = null;

            chatsRemoveUpdate.find(function (chat, index) {
                var isUpdateChat = (parseInt(chat.id) == parseInt(action.id)) && (parseInt(chat.isChat) == 1);

                if (isUpdateChat) {
                    chatRemoveIndex = index;
                }

                return isUpdateChat;
            });

            // Если чат найден, то удаляем из списка
            if (chatRemoveIndex != null) {
                chatsRemoveUpdate.splice(chatRemoveIndex, 1);
            }

            return {...state, chats: sortChats(chatsRemoveUpdate, state.unread, state.userNameIsFirst), recipient: false};
        default:
            return state;
    }
};

/**
 * Поиск пользователя по ID
 *
 * @param id ид пользователя
 * @param users список пользователей
 * @returns {*}
 */
function findUserById(id, users) {
    id = parseInt(id);

    for(var i=0; i < users.length; i++) {
        var item = users[i];
        if (parseInt(item.id) == id) {
            return item;
        }
    }

    return false;
}

/** Сортировка списка пользователей */
function sortChats(chats, unread, userNameIsFirst) {

    /* Добавление ID непрочитанных сообщений пользователям */
    unread.forEach(function (item) {
        // 1. Поиск отправителя в списке пользователей
        var user = findUserById(item.sender, chats);
        // 2. Если пользователь найден, то переписываем у него максимальный ID сообщения
        if (user) {
            user.MaxMessageID = parseInt(item.maxMessageId);
        }
    });

    /* Сортировка списка чатов */
    return chats.sort(function (a, b) {
        // 1. Сортируем по типу чата
        var aIsChat = Boolean(parseInt(a.isChat));
        var bIsChat = Boolean(parseInt(b.isChat));
        if (aIsChat || bIsChat) {
            return (aIsChat && bIsChat)
                ? a.name.localeCompare(b.name) // сортировка по имени чата
                : aIsChat ? -1 : 1; // сортировка по типу записи
        }

        // 2. Сортировка по максимальному ID сообщения
        if (a.MaxMessageID || b.MaxMessageID) {
            var aMessageID = parseInt(a.MaxMessageID) || 0;
            var bMessageID = parseInt(b.MaxMessageID) || 0;

            // Сравнение в обратном порядке. Больше значение уходит в начало списка
            if (aMessageID < bMessageID) return 1; // Сдвигаем первого пользователя вниз, если ID его сообщения меньше
            if (aMessageID > bMessageID) return -1; // Сдвигаем первого пользователя вверх, если ID его сообщения больше

            return 0;
        }

        // 3. Сортировка по ФИ
        var aName = userNameIsFirst
            ? a.FName + ' ' + a.SName // Сортировка по имени
            : a.SName + ' ' + a.FName; // Сортировка по фамилии

        var bName = userNameIsFirst
            ? b.FName + ' ' + b.SName // Сортировка по имени
            : b.SName + ' ' + b.FName; // Сортировка по фамилии

        return aName.localeCompare(bName);
    });
}