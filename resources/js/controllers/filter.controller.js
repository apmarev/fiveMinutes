import { makeAutoObservable, configure } from "mobx"
import { request } from './request'
import {message} from "antd";

configure({
    enforceActions: "never",
})

class filterController {
    filterType = "2"
    filter = {}
    managers = []
    pipelines = []
    years = []
    months = []

    data = {}
    originalData = {}
    plan = []

    searchDisabled = false
    reportOpened = true

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
        this.setDefaultFilterData()
        request.get("/filter_plan")
            .then(result => {
                this.pipelines = result.data.pipelines
                this.years = result.data.years
                this.months = result.data.month
                this.filter = {
                    pipeline: this.pipelines[1].id,
                    year: new Date().getFullYear(),
                    month: new Date().getMonth() + 1
                }
                if(this.filterType === "1") this.getData(false)
            })
            .catch(error => console.log(error))
    }

    getYears() {
        request.get("/years")
            .then(result => {
                this.filter.year = result.data[result.data.length - 1].year
                this.years = result.data
                this.getMonthsByYear(this.filter.year)
            })
            .catch(error => console.log(error))
    }

    getMonthsByYear(year) {
        request.get(`/month/${year}`)
            .then(result => {
                console.log(result.data)
                this.filter.month = result.data[result.data.length - 1].month
                this.months = result.data
                if(this.reportOpened === true) this.getData(false)
            })
            .catch(error => console.log(error))
    }

    getManagers() {
        request.get("/managers")
            .then(result => {
                this.filter.managers = []
                result.data.map(item => this.filter.managers.push(item.id))
                this.managers = result.data
            })
            .catch(error => console.log(error))
    }

    getData(e) {
        if(e !== false) e.preventDefault()

        let filter = ""
        if(this.filterType === "1"){
            filter = "/plan/"
        } else if(this.filterType === "2") {
            filter = "/main/"
        } else {
            filter = "/info/"
        }

        if(this.filter.year > 0)
            filter += `?year=${this.filter.year}`
        else
            return message.error("Выберите год")

        if(this.filterType !== "3"){
            if(this.filter.pipeline > 0)
                filter += `&pipeline_id=${this.filter.pipeline}`
            else
                return message.error("Выберите воронку")
        }

        if(this.filter.month > 0)
            filter += `&month=${this.filter.month}`
        else
            return message.error("Выберите месяц")

        if(this.filterType === "3") {
            if (this.filter.managers.length > 0)
                this.filter.managers.map(item => filter = `${filter}&managers[]=${item}`)
            else
                return message.error("Выберите хотя бы одного менеджера")
        }

        this.closeAllRows()
        this.searchDisabled = true

        request.get(filter)
            .then(result => {
                if(this.filterType === "1"){
                    let planFormatted = []
                    result.data.map(item => {
                        let weeks = []
                        let totalMonthSum = 0,
                            totalPackageSum = 0,
                            totalProCount = 0,
                            totalCount = 0
                        if(item.plan.length > 0){
                            item.plan.map(week => {
                                totalMonthSum = totalMonthSum + week.month_sum
                                totalPackageSum = totalPackageSum + week.package_sum
                                totalProCount = totalProCount + week.pro_count
                                totalCount = totalCount + week.count
                                weeks[week.week] = {
                                    "month_sum": week.month_sum,
                                    "package_sum": week.package_sum,
                                    "pro_count": week.pro_count,
                                    "count": week.count
                                }
                            })
                        } else {
                            for(let i = 1; i <= 4; i++){
                                weeks[i] = {
                                    "month_sum": 0,
                                    "package_sum": 0,
                                    "pro_count": 0,
                                    "count": 0
                                }
                            }
                        }
                        planFormatted.push({
                            "id": item.manager_id,
                            "name": item.manager_name,
                            "weeks": weeks,
                            "totals": {
                                "month_sum": totalMonthSum,
                                "package_sum": totalPackageSum,
                                "pro_count": totalProCount,
                                "count": totalCount
                            }
                        })
                    })
                    console.log(planFormatted)
                    this.originalData = planFormatted
                    this.data = planFormatted
                } else {
                    console.log(result.data)
                    this.originalData = result.data
                    this.data = result.data
                }
                this.searchDisabled = false
            })
            .catch(error => console.log(error))
    }

    arrayDifference(a, b){
        return JSON.stringify(a) === JSON.stringify(b)
    }

    savePlan(e) {
        e.preventDefault()
        this.searchDisabled = true
        let data = []
        this.data.map((item, z) => {
            item.weeks.map((week, k) => {
                if(k === 0) return
                if(this.arrayDifference(week, this.originalData[z].weeks[k]) === false){
                    data.push({
                        "id": item.id,
                        "week": k,
                        "month_sum": week.month_sum,
                        "package_sum": week.package_sum,
                        "pro_count": week.pro_count,
                        "count": week.count
                    })
                }
            })
        })
        let fd = new FormData()
        data.map(item => fd.append("managers[]", JSON.stringify(item)))
        fd.append("pipeline_id", this.filter.pipeline)
        fd.append("month", this.filter.month)
        fd.append("year", this.filter.year)
        request.post("/plan", fd)
            .then(result => {
                this.searchDisabled = false
            })
            .catch(error => console.log(error))
    }

    toggleRow(index, e) {
        if(this.searchDisabled === true) return
        if(!e.target.classList.contains("column-row")) return
        index = index + 2
        document.querySelectorAll(`.column > *:nth-child(${index})`).forEach(item => {
            item.classList.toggle("active")
        })
    }

    toggleSubRow(rowIndex, index, e) {
        if(this.searchDisabled === true) return
        if(!e.target.classList.contains("has-subrows")) return
        index = index + 1
        document.querySelectorAll(`.column > *:nth-child(${rowIndex + 2}) > *:nth-child(${index})`).forEach(item => {
            item.classList.toggle("active")
        })
    }

    closeAllRows() {
        document.querySelectorAll(".column *").forEach(item => {
            item.classList.remove("active")
        })
    }
}

export default new filterController()
