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
                value: source?.sum_sale,
                finance: true,
                subrows: [
                    {
                        value: source?.sum_sale_ege,
                        finance: true,
                        subrows: []
                    },
                    {
                        value: source?.sum_sale_oge,
                        finance: true,
                        subrows: []
                    },
                    {
                        value: source?.sum_sale_ten,
                        finance: true,
                        subrows: []
                    },
                    {
                        value: source?.children_ege + source?.children_oge + source?.children_10,
                        finance: true,
                        subrows: [
                            {
                                value: source?.children_ege,
                                finance: true
                            },
                            {
                                value: source?.children_oge,
                                finance: true
                            },
                            {
                                value: source?.children_10,
                                finance: true
                            },
                        ]
                    },
                    {
                        value: source?.parents_ege + source?.parents_oge + source?.parents_10,
                        finance: true,
                        subrows: [
                            {
                                value: source?.parents_ege,
                                finance: true
                            },
                            {
                                value: source?.parents_oge,
                                finance: true
                            },
                            {
                                value: source?.parents_10,
                                finance: true
                            },
                        ]
                    }
                ]
            },
            {
                value: (source?.children_month_ege + source?.children_month_oge + source?.children_month_10) + (source?.parents_month_ege + source?.parents_month_oge + source?.parents_month_10),
                finance: true,
                subrows: [
                    {
                        value: source?.children_month_ege + source?.parents_month_ege,
                        finance: true,
                        subrows: []
                    },
                    {
                        value: source?.children_month_oge + source?.parents_month_oge,
                        finance: true,
                        subrows: []
                    },
                    {
                        value: source?.children_month_10 + source?.parents_month_10,
                        finance: true,
                        subrows: []
                    },
                    {
                        value: source?.children_month_ege + source?.children_month_oge + source?.children_month_10,
                        finance: true,
                        subrows: [
                            {
                                value: source?.children_month_ege,
                                finance: true
                            },
                            {
                                value: source?.children_month_oge,
                                finance: true
                            },
                            {
                                value: source?.children_month_10,
                                finance: true
                            },
                        ]
                    },
                    {
                        value: source?.parents_month_ege + source?.parents_month_oge + source?.parents_month_10,
                        finance: true,
                        subrows: [
                            {
                                value: source?.parents_month_ege,
                                finance: true
                            },
                            {
                                value: source?.parents_month_oge,
                                finance: true
                            },
                            {
                                value: source?.parents_month_10,
                                finance: true
                            },
                        ]
                    }
                ]
            },
            {
                value: source?.sum_sale_package_ege + source?.sum_sale_package_oge + source?.sum_sale_package_ten,
                finance: true,
                subrows: [
                    {
                        value: source?.sum_sale_package_ege,
                        finance: true,
                        subrows: []
                    },
                    {
                        value: source?.sum_sale_package_oge,
                        finance: true,
                        subrows: []
                    },
                    {
                        value: source?.sum_sale_package_ten,
                        finance: true,
                        subrows: []
                    },
                    {
                        value: source?.children_package_ege + source?.children_package_oge + source?.children_package_10,
                        finance: true,
                        subrows: [
                            {
                                value: source?.children_package_ege,
                                finance: true
                            },
                            {
                                value: source?.children_package_oge,
                                finance: true
                            },
                            {
                                value: source?.children_package_10,
                                finance: true
                            },
                        ]
                    },
                    {
                        value: source?.parents_package_ege + source?.parents_package_oge + source?.parents_package_10,
                        finance: true,
                        subrows: [
                            {
                                value: source?.parents_package_ege,
                                finance: true
                            },
                            {
                                value: source?.parents_package_oge,
                                finance: true
                            },
                            {
                                value: source?.parents_package_10,
                                finance: true
                            },
                        ]
                    }
                ]
            },
            {
                value: (source?.count_none_ege + source?.count_children_ege + source?.count_parents_ege) + (source?.count_none_oge + source?.count_children_oge + source?.count_parents_oge) + (source?.count_none_10 + source?.count_children_10 + source?.count_parents_10) + (source?.count_none_none + source?.count_children_none + source?.count_parents_none),
                finance: false,
                subrows: [
                    {
                        value: source?.count_none_ege + source?.count_children_ege + source?.count_parents_ege,
                        subrows: []
                    },
                    {
                        value: source?.count_none_oge + source?.count_children_oge + source?.count_parents_oge,
                        subrows: []
                    },
                    {
                        value: source?.count_none_10 + source?.count_children_10 + source?.count_parents_10,
                        subrows: []
                    },
                    {
                        value: source?.count_none_none + source?.count_children_none + source?.count_parents_none,
                        finance: false,
                        subrows: []
                    },
                    {
                        value: source?.count_none_none + source?.count_none_ege + source?.count_none_oge + source?.count_none_10,
                        finance: false,
                        subrows: [
                            {
                                value: source?.count_none_none,
                                finance: false
                            },
                            {
                                value: source?.count_none_ege,
                                finance: false
                            },
                            {
                                value: source?.count_none_oge,
                                finance: false
                            },
                            {
                                value: source?.count_none_10,
                                finance: false
                            },
                        ]
                    },
                    {
                        value: source?.count_children_none + source?.count_children_ege + source?.count_children_oge + source?.count_children_10,
                        finance: false,
                        subrows: [
                            {
                                value: source?.count_children_none,
                            },
                            {
                                value: source?.count_children_ege,
                            },
                            {
                                value: source?.count_children_oge,
                            },
                            {
                                value: source?.count_children_10,
                            },
                        ]
                    },
                    {
                        value: source?.count_parents_none + source?.count_parents_ege + source?.count_parents_oge + source?.count_parents_10,
                        finance: true,
                        subrows: [
                            {
                                value: source?.count_parents_none,
                            },
                            {
                                value: source?.count_parents_ege,
                            },
                            {
                                value: source?.count_parents_oge,
                            },
                            {
                                value: source?.count_parents_10,
                            },
                        ]
                    }
                ]
            },
            {
                value: (source?.count_sale_children_ege + source?.count_sale_children_oge + source?.count_sale_children_10) + (source?.count_sale_parents_ege + source?.count_sale_parents_oge + source?.count_sale_parents_10),
                subrows: [
                    {
                        value: source?.count_sale_children_ege + source?.count_sale_parents_ege,
                        subrows: []
                    },
                    {
                        value: source?.count_sale_children_oge + source?.count_sale_parents_oge,
                        subrows: []
                    },
                    {
                        value: source?.count_sale_children_10 + source?.count_sale_parents_10,
                        subrows: []
                    },
                    {
                        value: source?.count_sale_children_ege + source?.count_sale_children_oge + source?.count_sale_children_10,
                        subrows: [
                            {
                                value: source?.count_sale_children_ege,
                            },
                            {
                                value: source?.count_sale_children_oge,
                            },
                            {
                                value: source?.count_sale_children_10,
                            },
                        ]
                    },
                    {
                        value: source?.count_sale_parents_ege + source?.count_sale_parents_oge + source?.count_sale_parents_10,
                        subrows: [
                            {
                                value: source?.count_sale_parents_ege,
                            },
                            {
                                value: source?.count_sale_parents_oge,
                            },
                            {
                                value: source?.count_sale_parents_10,
                            },
                        ]
                    }
                ]
            },
            {
                value: (source?.unique_children_ege + source?.unique_children_oge + source?.unique_children_10) + (source?.unique_parents_ege + source?.unique_parents_oge + source?.unique_parents_10),
                subrows: [
                    {
                        value: source?.unique_children_ege + source?.unique_parents_ege,
                        subrows: []
                    },
                    {
                        value: source?.unique_children_oge + source?.unique_parents_oge,
                        subrows: []
                    },
                    {
                        value: source?.unique_children_10 + source?.unique_parents_10,
                        subrows: []
                    },
                    {
                        value: source?.unique_children_ege + source?.unique_children_oge + source?.unique_children_10,
                        subrows: [
                            {
                                value: source?.unique_children_ege,
                            },
                            {
                                value: source?.unique_children_oge,
                            },
                            {
                                value: source?.unique_children_10,
                            },
                        ]
                    },
                    {
                        value: source?.unique_parents_ege + source?.unique_parents_oge + source?.unique_parents_10,
                        subrows: [
                            {
                                value: source?.unique_parents_ege,
                            },
                            {
                                value: source?.unique_parents_oge,
                            },
                            {
                                value: source?.unique_parents_10,
                            },
                        ]
                    }
                ]
            },
            {
                value: source?.average_check,
                finance: true,
                subrows: [
                    {
                        value: source?.average_check_children_ege + source?.average_check_parents_ege,
                        finance: true,
                        subrows: []
                    },
                    {
                        value: source?.average_check_children_oge + source?.average_check_parents_oge,
                        finance: true,
                        subrows: []
                    },
                    {
                        value: source?.average_check_children_10 + source?.average_check_parents_10,
                        finance: true,
                        subrows: []
                    },
                    {
                        value: source?.average_check_children_ege + source?.average_check_children_oge + source?.average_check_children_10,
                        finance: true,
                        subrows: [
                            {
                                value: source?.average_check_children_ege,
                                finance: true
                            },
                            {
                                value: source?.average_check_children_oge,
                                finance: true
                            },
                            {
                                value: source?.average_check_children_10,
                                finance: true
                            },
                        ]
                    },
                    {
                        value: source?.average_check_parents_ege + source?.average_check_parents_oge + source?.average_check_parents_10,
                        finance: true,
                        subrows: [
                            {
                                value: source?.average_check_parents_ege,
                                finance: true
                            },
                            {
                                value: source?.average_check_parents_oge,
                                finance: true
                            },
                            {
                                value: source?.average_check_parents_10,
                                finance: true
                            },
                        ]
                    }
                ]
            },
            {
                value: source?.substandard_leads,
                subrows: [],
            },
            {
                value: source?.plan?.month,
                subrows: [],
                additionalClass: "lightRed",
            },
            {
                value: source?.plan?.month_percent,
                subrows: [],
                additionalClass: "lightRed",
            },
            {
                value: source?.plan?.month_remainder,
                subrows: [],
                additionalClass: "lightRed",
            },
            {
                value: source?.plan?.package,
                subrows: [],
                additionalClass: "lightBlue",
            },
            {
                value: source?.plan?.package_percent,
                subrows: [],
                additionalClass: "lightBlue",
            },
            {
                value: source?.plan?.package_remainder,
                subrows: [],
                additionalClass: "lightBlue",
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

export default Common
