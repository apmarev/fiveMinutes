import {makeAutoObservable} from "mobx"
import moment from "moment"

class filterController {
    filter = {}
    years = []
    months = []
    managers = []

    data = {}

    constructor() {
        makeAutoObservable(this)
    }

    setDefaultFilterData() {
        this.filter = {
            managers: [],
            year: 0,
            month: 0
        }
        this.years = []
        this.months = []
        this.managers = []
        this.data = {
            all: {}
        }
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
        this.closeAllRows()
        axios.get(`/api/info/${filter}`)
            .then(result => {
                console.log(result.data)
                this.data = result.data
            })
            .catch(error => console.log(error))
    }

    toggleRow(index, e) {
        if(!e.target.classList.contains("column-row")) return
        index = index + 2
        document.querySelectorAll(`.column > *:nth-child(${index})`).forEach(item => {
            item.classList.toggle("active")
        })
    }

    closeAllRows() {
        document.querySelectorAll(".column > *").forEach(item => {
            item.classList.remove("active")
        })
    }
}

export default new filterController()
