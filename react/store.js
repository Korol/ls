import { applyMiddleware, createStore } from 'redux'

import think from 'redux-thunk'
import promise from 'redux-promise-middleware'

import reducers from './reducers'

const middleware = applyMiddleware(promise(), think);

export default createStore(reducers, middleware);