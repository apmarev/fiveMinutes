import { observer } from "mobx-react-lite"
import React, {useEffect} from "react"
import filter from "../../controllers/filter.controller"
import MainColumn from "./components/MainColumn"
import Column from "./components/Column"

export const Manager = observer(() => {

    useEffect(() => {
        filter.searchDisabled = true
        filter.setDefaultFilterData()
        filter.getYears()
        filter.getManagers()
        filter.getFilterManager()
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
            name: "План количество продаж",
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
                value: source?.leads_count,
                subrows: []
            },
            {
                value: source?.sum_month + source?.sum_package,
                finance: true,
                subrows: [
                    {
                        value: source?.sum_month,
                        finance: true,
                        subrows: []
                    },
                    {
                        value: source?.sum_package,
                        finance: true,
                        subrows: []
                    },
                    {
                        value: source?.sum_pro,
                        finance: true,
                        subrows: []
                    },
                ]
            },
            {
                value: source?.count_month + source?.count_package,
                subrows: [
                    {
                        value: source?.count_month,
                        subrows: []
                    },
                    {
                        value: source?.count_package,
                        subrows: []
                    },
                    {
                        value: source?.count_pro,
                        subrows: []
                    },
                ]
            },
            {
                value: source?.count_clients_month + source?.count_clients_package,
                subrows: [
                    {
                        value: source?.count_clients_month,
                        subrows: []
                    },
                    {
                        value: source?.count_clients_package,
                        subrows: []
                    },
                    {
                        value: source?.count_clients_pro,
                        subrows: []
                    },
                ]
            },
            {
                value: source?.average_check,
                finance: true,
                subrows: []
            },
            {
                value: source?.plan?.month,
                finance: true,
                subrows: [],
                additionalClass: "lightRed",
            },
            {
                value: source?.plan?.month_percent,
                subrows: [],
                additionalClass: "lightRed",
                percent: true
            },
            {
                value: source?.plan?.month_remainder,
                subrows: [],
                additionalClass: "lightRed",
                finance: true
            },
            {
                value: source?.plan?.package,
                subrows: [],
                additionalClass: "lightBlue",
                finance: true
            },
            {
                value: source?.plan?.package_percent,
                subrows: [],
                additionalClass: "lightBlue",
                percent: true
            },
            {
                value: source?.plan?.package_remainder,
                subrows: [],
                additionalClass: "lightBlue",
                finance: true
            },
            {
                value: source?.plan?.pro,
                subrows: [],
                additionalClass: "purple",
            },
            {
                value: source?.plan?.pro_percent,
                subrows: [],
                additionalClass: "purple",
                percent: true
            },
            {
                value: source?.plan?.pro_remainder,
                subrows: [],
                additionalClass: "purple",
            },
            {
                value: source?.plan?.count,
                subrows: [],
                additionalClass: "lightOrange",
            },
            {
                value: source?.plan?.count_percent,
                subrows: [],
                additionalClass: "lightOrange",
                percent: true
            },
            {
                value: source?.plan?.count_remainder,
                subrows: [],
                additionalClass: "lightOrange",
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
                <div className="report-columns max-content">
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
