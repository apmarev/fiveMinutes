import {makeAutoObservable} from "mobx"
import moment from "moment"

class filterController {
    filter = {
        managers: [],
        year: 0,
        month: 0
    }
    years = []
    months = []
    managers = []

    data = {}

    constructor() {
        makeAutoObservable(this)
    }

    getYears() {
        axios.get("/api/years")
            .then(result => {
                this.years = result.data
            })
            .catch(error => console.log(error))
    }

    getMonthsByYear(year) {
        axios.get(`/api/month/${year}`)
            .then(result => {
                this.months = result.data
            })
            .catch(error => console.log(error))
    }

    getManagers() {
        axios.get("/api/managers")
            .then(result => {
                this.managers = result.data
            })
            .catch(error => console.log(error))
    }

    getData(e) {
        e.preventDefault()
        let filter = ""
        if(this.filter.year > 0){
            filter += `?year=${this.filter.year}`
        }
        if(this.filter.month > 0){
            filter += `&month=${this.filter.month}`
        }
        if(this.filter.managers.length > 0){
            this.filter.managers.map(item => filter = `${filter}&managers[]=${item}`)
        }
        axios.get(`/api/info/${filter}`)
            .then(result => {
                console.log(result.data)
                this.data = result.data
            })
            .catch(error => console.log(error))
    }
}

export default new filterController()
