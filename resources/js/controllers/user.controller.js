import { makeAutoObservable } from 'mobx'
import { request } from './request'
import store from 'store'
import sha1 from 'sha1'
import { message } from 'antd'
import filter from "./filter.controller"

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
    modal = false

    constructor() {
        makeAutoObservable(this)
    }

    onChangeAuth(name, value) {
        this.data[name] = value
    }

    onChangeUser(name, value) {
        this.element[name] = value
    }

    logIn(e) {
        e.preventDefault()

        const data = new FormData()
        data.append('login', this.data.login)
        data.append('password', sha1(this.data.password))

        request.post(`/user/login`, data)
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

    logOut() {
        store.clearAll()
        window.location.reload()
    }

    getUsersList() {
        filter.searchDisabled = true
        request.get('/user').then(result => {
            this.list = result.data
            filter.searchDisabled = false
        })
    }

    clearElement() {
        this.element = { id: 0, login: '', password: '', super: 0 }
        this.modal = false
    }

    selectUser(userID) {
        this.element = this.list.find(el => el.id === userID)
        this.element.password = ''
        this.modal = true
    }

    createUser() {
        this.element = { id: 0, login: '', password: '', super: 0 }
        this.modal = true
    }

    saveUser() {
        if(this.element.login === '' || this.element.super <0) {
            return message.error('Укажите Email и выберите права пользователя')
        }

        if(this.element.password === '') {
            return message.error('Укажите пароль для пользователя')
        }

        const post_data = new FormData()
        post_data.append('login', this.element.login)
        post_data.append('password', sha1(this.element.password))
        post_data.append('super', `${this.element.super}`)

        const put_data = {
            login: this.element.login,
            password: sha1(this.element.password),
            super: `${this.element.super}`
        }

        if(this.element.id && this.element.id > 0)
            request.put(`/user/${this.element.id}`, put_data)
                .then(() => {
                    this.clearElement()
                    this.getUsersList()
                })
        else
            request.post(`/user`, post_data)
                .then(() => {
                    this.clearElement()
                    this.getUsersList()
                })

        this.modal = false
    }

    delete(userID) {
        request.delete(`/user/${userID}`)
            .then(() => {
                this.getUsersList()
            })
    }

}

export default new userController()
