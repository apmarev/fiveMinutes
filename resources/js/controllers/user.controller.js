import { makeAutoObservable } from 'mobx'
import { request } from './request'
import store from 'store'
import sha1 from 'sha1'
import { message } from 'antd'

class userController {

    data = {
        login: '',
        password: ''
    }

    list = []

    element = {
        id: 0,
        login: '', // Должен быть email
        password: '', // Передавать пароль в sha1
        super: 0 // 0 - Права на чтение, 1 - Права на редактирование плана и пользователей
    }

    constructor() {
        makeAutoObservable(this)
    }

    onChange(name, value) {
        this.data[name] = value
    }

    /**
     * Авторизация пользователя
     */
    login(e) {
        e.preventDefault()
        const data = new FormData()
        data.append('login', this.data.login)
        data.append('password', sha1(this.data.password))

        request.post(`/report/user/login`, data)
            .then(result => {
                const user = result.data
                if(user.token && user.token !== '') {
                    store.set('token', user.token)
                    store.set('super', user.super)
                    window.location.href = '/'
                }
            })
            .catch(error => {
                message.error(error.response.data.message)
            })
    }

    getList() {
        request.get('/report/user').then(result => this.list = result.data)
    }

    selectUser(userID) {
        this.element = this.list.find(el => el.id === userID)
    }

    clearElement() {
        this.element = { id: 0, login: '', password: '', super: 0 }
    }

    save() {
        const data = new FormData()
        data.append('login', this.element.login)
        data.append('password', sha1(this.element.password))
        data.append('super', `${this.element.super}`)

        if(this.element.id && this.element.id > 0)
            request.put(`/report/user/${this.element.id}`, data)
                .then(result => {
                    this.clearElement()
                    this.getList()
                })
        else
            request.post(`/report/user`, data)
                .then(result => {
                    this.clearElement()
                    this.getList()
                })
    }

    delete(userID) {
        request.delete(`/report/user/${userID}`)
            .then(result => {
                this.getList()
            })
    }

}

export default new userController()
