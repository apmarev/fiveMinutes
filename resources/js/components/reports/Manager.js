import { observer } from "mobx-react-lite"
import React, {useEffect} from "react"
import filter from "../../controllers/filter.controller"
import Formatter from "../helpers/formatter"

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
                {name: "Месяц"},
                {name: "Пакет"},
                {name: "Тариф Про"},
            ]
        },
        {
            name: "Количество продаж",
            subrows: [
                {name: "Месяц"},
                {name: "Пакет"},
                {name: "Тариф Про"},
            ]
        },
        {
            name: "Количество клиентов",
            subrows: [
                {name: "Месяц"},
                {name: "Пакет"},
                {name: "Тариф Про"},
            ]
        },
        {
            name: "Средний чек",
            subrows: []
        },
    ]

    return (
        <>
            <div className="report">
                <div className="report-columns">
                    <div className="main-column column bold">
                        <div className="column-row"></div>
                        {mainRows.map((item, k) => (
                            <>
                                <div
                                    key={k}
                                    className={(item.subrows.length > 0) ? "column-row has-subrows" : "column-row"}
                                    onClick={_ => filter.toggleRow(k, _)}
                                >
                                    {item.name}
                                    {item.subrows.length > 0 &&
                                        item.subrows.map((subitem, sk) => (
                                            <div key={sk} className="sub-row">{subitem.name}</div>
                                        ))
                                    }
                                </div>
                            </>
                        ))}
                    </div>
                    <div className="month-column column bold">
                        <div className="column-row">Месяц</div>
                        <div className="column-row">
                            <Formatter number={filter.data.all?.leads_count ?? 0} />
                        </div>
                        <div className="column-row has-subrows">
                            &nbsp;
                            <div className="sub-row">
                                <Formatter number={filter.data.all?.sum_month ?? 0} finance={true} />
                            </div>
                            <div className="sub-row">
                                <Formatter number={filter.data.all?.sum_package ?? 0} finance={true} />
                            </div>
                            <div className="sub-row">
                                <Formatter number={filter.data.all?.sum_pro ?? 0} finance={true} />
                            </div>
                        </div>
                        <div className="column-row has-subrows">
                            {filter.data.all?.count ?? 0}
                            <div className="sub-row">
                                <Formatter number={filter.data.all?.count_month ?? 0} />
                            </div>
                            <div className="sub-row">
                                <Formatter number={filter.data.all?.count_package ?? 0} />
                            </div>
                            <div className="sub-row">
                                <Formatter number={filter.data.all?.count_pro ?? 0} />
                            </div>
                        </div>
                        <div className="column-row has-subrows">
                            &nbsp;
                            <div className="sub-row">
                                <Formatter number={filter.data.all?.count_clients_month ?? 0} />
                            </div>
                            <div className="sub-row">
                                <Formatter number={filter.data.all?.count_clients_package ?? 0} />
                            </div>
                            <div className="sub-row">
                                <Formatter number={filter.data.all?.count_clients_pro ?? 0} />
                            </div>
                        </div>
                        <div className="column-row">
                            <Formatter number={filter.data.all?.average_check ?? 0} finance={true} />
                        </div>
                    </div>
                    {Object.keys(filter.data).length > 1 && filter.data.days.length > 0 &&
                        filter.data.days.map((item, k) => (
                            <div
                                className={(item.date === "Недельный план") ? "column bold" : "column"}
                                key={k}
                            >
                                <div className="column-row">{item.date}</div>
                                <div className="column-row">
                                    <Formatter number={item?.leads_count ?? 0} />
                                </div>
                                <div className="column-row has-subrows">
                                    &nbsp;
                                    <div className="sub-row">
                                        <Formatter number={item?.sum_month ?? 0} finance={true} />
                                    </div>
                                    <div className="sub-row">
                                        <Formatter number={item?.sum_package ?? 0} finance={true} />
                                    </div>
                                    <div className="sub-row">
                                        <Formatter number={item?.sum_pro ?? 0} finance={true} />
                                    </div>
                                </div>
                                <div className="column-row has-subrows">
                                    {item?.count ?? 0}
                                    <div className="sub-row">
                                        <Formatter number={item?.count_month ?? 0} />
                                    </div>
                                    <div className="sub-row">
                                        <Formatter number={item?.count_package ?? 0} />
                                    </div>
                                    <div className="sub-row">
                                        <Formatter number={item?.count_pro ?? 0} />
                                    </div>
                                </div>
                                <div className="column-row has-subrows">
                                    &nbsp;
                                    <div className="sub-row">
                                        <Formatter number={item?.count_clients_month ?? 0} />
                                    </div>
                                    <div className="sub-row">
                                        <Formatter number={item?.count_clients_package ?? 0} />
                                    </div>
                                    <div className="sub-row">
                                        <Formatter number={item?.count_clients_pro ?? 0} />
                                    </div>
                                </div>
                                <div className="column-row">
                                    <Formatter number={item?.average_check ?? 0} finance={true} />
                                </div>
                            </div>
                        ))
                    }
                </div>
            </div>
        </>
    )
})

export default Manager
