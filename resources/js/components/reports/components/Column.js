import {observer} from "mobx-react-lite"
import React from "react"
import Formatter from "../../helpers/formatter"

export const Column = observer(({column}) => {
    let columnClass = "column " + column.additionalClass
    if(column?.name === "Недельный план") columnClass += " bold"

    return (
        <>
            <div className={columnClass}>
                <div className="column-row">{column?.name ?? "&nbsp;"}</div>
                {column.rows.map((row, k) => {
                    let rowClass = "column-row " + row.additionalClass
                    if(row.subrows.length > 0) rowClass += " has-subrows"
                    return (
                        <div className={rowClass} key={k}>
                            <Formatter number={row.value ?? 0} finance={row?.finance ?? false} />
                            {row.subrows.length > 0 &&
                                row.subrows.map((subrow, sk) => {
                                    let subrowClass = "sub-row " + row.additionalClass
                                    if(subrow.subrows.length > 0) subrowClass += " has-subrows"
                                    return (
                                        <div className={subrowClass} key={sk}>
                                            <Formatter number={subrow.value ?? 0} finance={subrow?.finance ?? false} />
                                            {subrow.subrows.map((subsubrow, ssk) => (
                                                <div className="sub-row" key={ssk}>
                                                    <Formatter number={subsubrow.value ?? 0} finance={subsubrow?.finance ?? false} />
                                                </div>
                                            ))}
                                        </div>
                                    )
                                })
                            }
                        </div>
                    )
                })}
            </div>
        </>
    )
})

export default Column
