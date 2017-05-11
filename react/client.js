import axios from 'axios';

var client = axios.create({
    baseURL: BaseUrl
});

export default client;