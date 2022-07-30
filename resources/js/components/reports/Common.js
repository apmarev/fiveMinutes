import { observer } from "mobx-react-lite"
import React, {useEffect} from "react"
import filter from "../../controllers/filter.controller"
import MainColumn from "./components/MainColumn"
import Column from "./components/Column"

export const Common = observer(() => {

    useEffect(() => {
        filter.setDefaultFilterData()
        filter.getFilterPlan()
    }, [])

    const mainRows = [
        {
            name: "Сумма продаж",
            subrows: [
                {
                    name: "ЕГЭ",
                    subrows: []
                },
                {
                    name: "ОГЭ",
                    subrows: []
                },
                {
                    name: "10 класс",
                    subrows: []
                },
                {
                    name: "Дети",
                    subrows: [
                        {name: "ЕГЭ"},
                        {name: "ОГЭ"},
                        {name: "10 класс"},
                    ]
                },
                {
                    name: "Родители",
                    subrows: [
                        {name: "ЕГЭ"},
                        {name: "ОГЭ"},
                        {name: "10 класс"},
                    ]
                },
            ]
        },
        {
            name: "Сумма продаж Месяц",
            subrows: [
                {
                    name: "ЕГЭ",
                    subrows: []
                },
                {
                    name: "ОГЭ",
                    subrows: []
                },
                {
                    name: "10 класс",
                    subrows: []
                },
                {
                    name: "Дети",
                    subrows: [
                        {name: "ЕГЭ"},
                        {name: "ОГЭ"},
                        {name: "10 класс"},
                    ]
                },
                {
                    name: "Родители",
                    subrows: [
                        {name: "ЕГЭ"},
                        {name: "ОГЭ"},
                        {name: "10 класс"},
                    ]
                },
            ]
        },
        {
            name: "Сумма продаж Пакет",
            subrows: [
                {
                    name: "ЕГЭ",
                    subrows: []
                },
                {
                    name: "ОГЭ",
                    subrows: []
                },
                {
                    name: "10 класс",
                    subrows: []
                },
                {
                    name: "Дети",
                    subrows: [
                        {name: "ЕГЭ"},
                        {name: "ОГЭ"},
                        {name: "10 класс"},
                    ]
                },
                {
                    name: "Родители",
                    subrows: [
                        {name: "ЕГЭ"},
                        {name: "ОГЭ"},
                        {name: "10 класс"},
                    ]
                },
            ]
        },
        {
            name: "Количество лидов",
            subrows: [
                {
                    name: "ЕГЭ",
                    subrows: []
                },
                {
                    name: "ОГЭ",
                    subrows: []
                },
                {
                    name: "10 класс",
                    subrows: []
                },
                {
                    name: "Напр. не определено",
                    subrows: []
                },
                {
                    name: "Тип лида не определен",
                    subrows: [
                        {name: "Напр. не определено"},
                        {name: "ЕГЭ"},
                        {name: "ОГЭ"},
                        {name: "10 класс"},
                    ]
                },
                {
                    name: "Дети",
                    subrows: [
                        {name: "Напр. не определено"},
                        {name: "ЕГЭ"},
                        {name: "ОГЭ"},
                        {name: "10 класс"},
                    ]
                },
                {
                    name: "Родители",
                    subrows: [
                        {name: "Напр. не определено"},
                        {name: "ЕГЭ"},
                        {name: "ОГЭ"},
                        {name: "10 класс"},
                    ]
                },
            ]
        },
        {
            name: "Количество продаж",
            subrows: [
                {
                    name: "ЕГЭ",
                    subrows: []
                },
                {
                    name: "ОГЭ",
                    subrows: []
                },
                {
                    name: "10 класс",
                    subrows: []
                },
                {
                    name: "Дети",
                    subrows: [
                        {name: "ЕГЭ"},
                        {name: "ОГЭ"},
                        {name: "10 класс"},
                    ]
                },
                {
                    name: "Родители",
                    subrows: [
                        {name: "ЕГЭ"},
                        {name: "ОГЭ"},
                        {name: "10 класс"},
                    ]
                },
            ]
        },
        {
            name: "Количество клиентов",
            subrows: [
                {
                    name: "ЕГЭ",
                    subrows: []
                },
                {
                    name: "ОГЭ",
                    subrows: []
                },
                {
                    name: "10 класс",
                    subrows: []
                },
                {
                    name: "Дети",
                    subrows: [
                        {name: "ЕГЭ"},
                        {name: "ОГЭ"},
                        {name: "10 класс"},
                    ]
                },
                {
                    name: "Родители",
                    subrows: [
                        {name: "ЕГЭ"},
                        {name: "ОГЭ"},
                        {name: "10 класс"},
                    ]
                },
            ]
        },
        {
            name: "Средний чек",
            subrows: [
                {
                    name: "ЕГЭ",
                    subrows: []
                },
                {
                    name: "ОГЭ",
                    subrows: []
                },
                {
                    name: "10 класс",
                    subrows: []
                },
                {
                    name: "Дети",
                    subrows: [
                        {name: "ЕГЭ"},
                        {name: "ОГЭ"},
                        {name: "10 класс"},
                    ]
                },
                {
                    name: "Родители",
                    subrows: [
                        {name: "ЕГЭ"},
                        {name: "ОГЭ"},
                        {name: "10 класс"},
                    ]
                },
            ]
        },
        {
            name: "Некачественных клиентов",
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
            name: "План продаж ПРО количество",
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
                "value": "&nbsp;",
                "finance": true,
                "subrows": [
                    {
                        "value": source?.leads_count,
                        "finance": true,
                        "subrows": []
                    },
                    {
                        "value": source?.leads_count,
                        "finance": true,
                        "subrows": []
                    },
                    {
                        "value": source?.leads_count,
                        "finance": true,
                        "subrows": []
                    },
                    {
                        "value": source?.leads_count,
                        "finance": true,
                        "subrows": [
                            {
                                "value": source?.leads_count,
                                "finance": true
                            },
                            {
                                "value": source?.leads_count,
                                "finance": true
                            },
                            {
                                "value": source?.leads_count,
                                "finance": true
                            },
                        ]
                    },
                    {
                        "value": source?.leads_count,
                        "finance": true,
                        "subrows": [
                            {
                                "value": source?.leads_count,
                                "finance": true
                            },
                            {
                                "value": source?.leads_count,
                                "finance": true
                            },
                            {
                                "value": source?.leads_count,
                                "finance": true
                            },
                        ]
                    }
                ]
            }
        ]
    }

    let monthRows = {
        "name": "Месяц",
        "additionalClass": "month-column bold",
        "rows": allRows(filter.data.all)
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
                </div>
            </div>
        </>
    )
})

export default Common
