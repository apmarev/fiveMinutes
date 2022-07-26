import { observer } from "mobx-react-lite"
import React, {useEffect} from "react"
import filter from "../../controllers/filter.controller"

export const Plan = observer(() => {

    useEffect(() => {
        filter.setDefaultFilterData()
        filter.getFilterPlan()
    }, [])

    return (
        <>
            <div className="report">
                <div className="report-columns">
                    {filter.data.length > 0 &&
                        <div className="main-column column bold">
                            <div className="column-row"></div>
                            {filter.data.map((item, k) => (
                                <div key={k} className="column-row">{item.manager_name}</div>
                            ))}
                        </div>
                    }
                </div>
            </div>
        </>
    )
})

export default Plan
