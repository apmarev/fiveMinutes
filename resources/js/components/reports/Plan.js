import { observer } from "mobx-react-lite"
import React, {useEffect} from "react"
import filter from "../../controllers/filter.controller"
import { Input } from "antd"
import Formatter from "../helpers/formatter"

export const Plan = observer(() => {

    useEffect(() => {
        filter.setDefaultFilterData()
        filter.getFilterPlan()
    }, [])

    const fields = ["month_sum", "package_sum", "pro_count", "count"]

    const columns = [
        {id: "month", name: "План месяц"},
        {id: 1, name: "Неделя 1"},
        {id: 2, name: "Неделя 2"},
        {id: 3, name: "Неделя 3"},
        {id: 4, name: "Неделя 4"},
    ]

    return (
        <>
            <div className="report">
                {filter.data.length > 0 &&
                    <>
                        <div className="heading-columns">
                            <div className="plan-heading-column">Месяц сумма</div>
                            <div className="plan-heading-column">Пакет сумма</div>
                            <div className="plan-heading-column">Про количество</div>
                            <div className="plan-heading-column">План количество продаж</div>
                        </div>
                        <div className="report-columns">
                            <div className="main-column column bold">
                                <div className="column-row"></div>
                                {filter.data.map((item, k) => (
                                    <div key={k} className="column-row">{item.name}</div>
                                ))}
                            </div>
                            {fields.map((field, f) => {
                                return columns.map((col, c) => {
                                    let finance = false
                                    if(field.includes("_sum")) finance = true
                                    if(col.id === "month"){
                                        return (
                                            <div className="column bold" key={c}>
                                                <div className="column-row">{col.name}</div>
                                                {filter.data.map((item, k) => (
                                                    <div key={k} className="column-row">
                                                        <Formatter number={item.totals[field]} finance={finance} />
                                                    </div>
                                                ))}
                                            </div>
                                        )
                                    } else {
                                        return (
                                            <div className="column" key={c}>
                                                <div className="column-row">{col.name}</div>
                                                {filter.data.map((item, k) => (
                                                    <div key={k} className="column-row with-input">
                                                        <Input
                                                            disabled={filter.searchDisabled}
                                                            value={item.weeks[col.id][field]}
                                                            onChange={e => {
                                                                let oldValue = Number(item.weeks[col.id][field])
                                                                let newValue = Number(e.target.value)
                                                                item.weeks[col.id][field] = newValue
                                                                item.totals[field] = Number(item.totals[field]) - oldValue + newValue
                                                            }}
                                                        />
                                                    </div>
                                                ))}
                                            </div>
                                        )
                                    }
                                })
                            })}
                        </div>
                    </>
                }
            </div>
        </>
    )
})

export default Plan
