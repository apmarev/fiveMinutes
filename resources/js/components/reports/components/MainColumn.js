import {observer} from "mobx-react-lite"
import filter from "../../../controllers/filter.controller"
import React from "react"

export const MainColumn = observer(({item, k}) => {
    let itemClass = item?.additionalClass ?? ""
    return (
        <>
            <div
                key={k}
                className={((item.subrows.length > 0) ? "column-row has-subrows " : "column-row ") + itemClass}
                onClick={_ => filter.toggleRow(k, _)}
            >
                {item.name}
                {item.subrows.length > 0 &&
                    item.subrows.map((subitem, sk) => (
                        <div
                            key={sk}
                            className={(subitem.subrows.length > 0) ? "sub-row has-subrows" : "sub-row"}
                            onClick={_ => filter.toggleSubRow(k, sk, _)}
                        >
                            {subitem.name}
                            {subitem.subrows.length > 0 &&
                                subitem.subrows.map((subsubitem, ssk) => (
                                    <div key={ssk} className="sub-row">{subsubitem.name}</div>
                                ))
                            }
                        </div>
                    ))
                }
            </div>
        </>
    )
})

export default MainColumn
