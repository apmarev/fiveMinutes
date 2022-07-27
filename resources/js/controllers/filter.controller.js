import { makeAutoObservable, configure } from "mobx"
import { request } from './request'
import moment from "moment"
import {message} from "antd";

configure({
    enforceActions: "never",
})

class filterController {
    filterType = "1"
    filter = {}
    managers = []
    pipelines = []
    years = []
    months = []

    data = {}
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
            })
            .catch(error => console.log(error))
    }

    getYears() {
        request.get("/years")
            .then(result => {
                this.filter.year = result.data[0].year
                this.years = result.data
                this.getMonthsByYear(this.filter.year)
            })
            .catch(error => console.log(error))
    }

    getMonthsByYear(year) {
        request.get(`/month/${year}`)
            .then(result => {
                this.filter.month = result.data[0].month
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
                    this.data = planFormatted
                } else {
                    console.log(result.data)
                    this.data = result.data
                }
                this.searchDisabled = false
            })
            .catch(error => console.log(error))
    }

    savePlan(e) {
        e.preventDefault()
        this.searchDisabled = true
        let data = []
        this.data.map(item => {
            item.weeks.map((week, k) => {
                if(k === 0) return
                data.push({
                    "id": item.id,
                    "week": k,
                    "month_sum": week.month_sum,
                    "package_sum": week.package_sum,
                    "pro_count": week.pro_count,
                    "count": week.count
                })
            })
        })
        let fd = new FormData()
        data.map(item => fd.append("managers[]", JSON.stringify(item)))
        fd.append("pipeline_id", 5322871)
        fd.append("month", 6)
        fd.append("year", 2022)
        request.post("/plan", fd)
            .then(result => {
                console.log(result)
                this.searchDisabled = false
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
