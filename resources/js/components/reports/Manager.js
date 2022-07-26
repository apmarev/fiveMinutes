import { observer } from "mobx-react-lite"
import filter from "../../controllers/filter.controller"

export const Manager = observer(() => {

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

    const toggleRow = (index) => {
        index = index + 2
        document.querySelectorAll(`.column > *:nth-child(${index})`).forEach(item => {
            item.classList.toggle("active")
        })
    }

    return (
        <>
            <div className="report">
                <div className="report-columns">
                    <div className="main-column column">
                        <div className="column-row"></div>
                        {mainRows.map((item, k) => (
                            <>
                                <div
                                    key={k}
                                    className={(item.subrows.length > 0) ? "column-row has-subrows" : "column-row"}
                                    onClick={_ => toggleRow(k)}
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
                    {Object.keys(filter.data).length > 0 && filter.data.days.length > 0 &&
                        filter.data.days.map((item, k) => (
                            <div className="column" key={k}>
                                <div className="column-row">{item.date}</div>
                                <div className="column-row">{item?.leads_count ?? 0}</div>
                                <div className="column-row has-subrows">
                                    {item?.sum_month ?? 0}
                                    <div className="sub-row">{item?.sum_month ?? 0}</div>
                                    <div className="sub-row">{item?.sum_month ?? 0}</div>
                                    <div className="sub-row">{item?.sum_month ?? 0}</div>
                                </div>
                                <div className="column-row has-subrows">
                                    {item?.sum_month ?? 0}
                                    <div className="sub-row">{item?.sum_month ?? 0}</div>
                                    <div className="sub-row">{item?.sum_month ?? 0}</div>
                                    <div className="sub-row">{item?.sum_month ?? 0}</div>
                                </div>
                                <div className="column-row has-subrows">
                                    {item?.sum_month ?? 0}
                                    <div className="sub-row">{item?.sum_month ?? 0}</div>
                                    <div className="sub-row">{item?.sum_month ?? 0}</div>
                                    <div className="sub-row">{item?.sum_month ?? 0}</div>
                                </div>
                                <div className="column-row">
                                    {item?.sum_month ?? 0}
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
