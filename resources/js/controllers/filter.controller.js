import {makeAutoObservable} from "mobx"
import moment from "moment"

class filterController {
    filter = {}

    constructor() {
        makeAutoObservable(this)
    }

    getData(e) {
        e.preventDefault()
    }
}

export default new filterController()
