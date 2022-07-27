import store from "store";

export const request = axios.create({
    baseURL: '/api/v1',
    headers: {
        'User-Token': store.get('token') ? store.get('token') : ''
    },
})
