import {makeAutoObservable} from "mobx"
import moment from "moment"

class filterController {
    filterType = "1"
    filter = {}
    managers = []
    pipelines = []
    years = []
    months = []

    data = {}

    constructor() {
        makeAutoObservable(this)
    }

    setDefaultFilterData() {
        this.filter = {
            managers: [],
            year: null,
            month: null
        }
        this.managers = []
        this.pipelines = []
        this.years = []
        this.months = []
        this.data = {
            all: {}
        }
    }

    getFilterPlan() {
        axios.get("/api/filter_plan")
            .then(result => {
                console.log(result.data)
                this.pipelines = result.data.pipelines
                this.years = result.data.years
                this.months = result.data.month
            })
            .catch(error => console.log(error))
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
        if(this.filterType === "1"){
            filter = "/api/plan/"
        } else if(this.filterType === "2") {

        } else {
            filter = "/api/info/"
        }
        if(this.filter.year > 0){
            filter += `?year=${this.filter.year}`
        }
        if(this.filter.pipeline > 0){
            filter += `&pipeline_id=${this.filter.pipeline}`
        }
        if(this.filter.month > 0){
            filter += `&month=${this.filter.month}`
        }
        if(this.filter.managers.length > 0){
            this.filter.managers.map(item => filter = `${filter}&managers[]=${item}`)
        }

        this.closeAllRows()
        axios.get(filter)
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
