import { observer } from "mobx-react-lite"
import React, {useEffect} from "react"
import filter from "../../controllers/filter.controller"
import Formatter from "../helpers/formatter"
import MainColumn from "./components/MainColumn"
import Column from "./components/Column"

export const Manager = observer(() => {

    useEffect(() => {
        filter.searchDisabled = true
        filter.setDefaultFilterData()
        filter.getYears()
        filter.getManagers()
        filter.reportOpened = true
    }, [])

    const mainRows = [
        {
            name: "Количество лидов",
            subrows: []
        },
        {
            name: "Сумма продаж",
            subrows: [
                {
                    name: "Месяц",
                    subrows: []
                },
                {
                    name: "Пакет",
                    subrows: []
                },
                {
                    name: "Тариф Про",
                    subrows: []
                },
            ]
        },
        {
            name: "Количество продаж",
            subrows: [
                {
                    name: "Месяц",
                    subrows: []
                },
                {
                    name: "Пакет",
                    subrows: []
                },
                {
                    name: "Тариф Про",
                    subrows: []
                },
            ]
        },
        {
            name: "Количество клиентов",
            subrows: [
                {
                    name: "Месяц",
                    subrows: []
                },
                {
                    name: "Пакет",
                    subrows: []
                },
                {
                    name: "Тариф Про",
                    subrows: []
                },
            ]
        },
        {
            name: "Средний чек",
            subrows: []
        },
        {
            name: "План месяц сумма",
            subrows: [],
            additionalClass: "lightRed"
        },
        {
            name: "% выполнения",
            subrows: [],
            additionalClass: "lightRed"
        },
        {
            name: "Остаток плана",
            subrows: [],
            additionalClass: "lightRed"
        },
        {
            name: "План Пакет сумма",
            subrows: [],
            additionalClass: "lightBlue"
        },
        {
            name: "% выполнения",
            subrows: [],
            additionalClass: "lightBlue"
        },
        {
            name: "Остаток плана",
            subrows: [],
            additionalClass: "lightBlue"
        },
        {
            name: "План ПРО количество",
            subrows: [],
            additionalClass: "purple"
        },
        {
            name: "% выполнения",
            subrows: [],
            additionalClass: "purple"
        },
        {
            name: "Остаток плана",
            subrows: [],
            additionalClass: "purple"
        },
        {
            name: "План Месяц количество",
            subrows: [],
            additionalClass: "lightOrange"
        },
        {
            name: "% выполнения",
            subrows: [],
            additionalClass: "lightOrange"
        },
        {
            name: "Остаток плана",
            subrows: [],
            additionalClass: "lightOrange"
        }
    ]

    const allRows = (source) => {
        return [
            {
                "value": source?.leads_count,
                "subrows": []
            },
            {
                "value": "&nbsp;",
                "finance": true,
                "subrows": [
                    {
                        "value": source?.sum_month,
                        "finance": true,
                        "subrows": []
                    },
                    {
                        "value": source?.sum_package,
                        "finance": true,
                        "subrows": []
                    },
                    {
                        "value": source?.sum_pro,
                        "finance": true,
                        "subrows": []
                    },
                ]
            },
            {
                "value": source?.count,
                "subrows": [
                    {
                        "value": source?.count_month,
                        "subrows": []
                    },
                    {
                        "value": source?.count_package,
                        "subrows": []
                    },
                    {
                        "value": source?.count_pro,
                        "subrows": []
                    },
                ]
            },
            {
                "value": "&nbsp;",
                "finance": true,
                "subrows": [
                    {
                        "value": source?.count_clients_month,
                        "finance": true,
                        "subrows": []
                    },
                    {
                        "value": source?.count_clients_package,
                        "finance": true,
                        "subrows": []
                    },
                    {
                        "value": source?.count_clients_pro,
                        "finance": true,
                        "subrows": []
                    },
                ]
            },
            {
                "value": filter.data.all?.average_check,
                "finance": true,
                "subrows": []
            }
        ]
    }

    let monthRows = {
        "name": "Месяц",
        "additionalClass": "month-column bold",
        "rows": allRows(filter.data.all)
    }

    let daysRows = []

    if(Object.keys(filter.data).length > 1 && filter.data.days.length > 0){
        filter.data.days.map((item, k) => {
            let rowClass = (item.date === "Недельный план") ?? "bold"
            return daysRows.push({
                "name": item.date,
                "additionalClass": rowClass,
                "rows": allRows(item)
            })
        })

    }

    return (
        <>
            <div className="report">
                <div className="report-columns">
                    <div className="main-column column bold">
                        <div className="column-row"></div>
                        {mainRows.map((item, k) => (
                            <MainColumn item={item} k={k} />
                        ))}
                    </div>
                    <Column column={monthRows} />
                    {daysRows.length > 0 &&
                        daysRows.map((item, k) => (
                            <Column column={item} key={k}/>
                        ))
                    }
                </div>
            </div>
        </>
    )
})

export default Manager
