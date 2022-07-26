import { observer } from "mobx-react-lite"
import ReactDOM from "react-dom"
import React, {useEffect} from "react"
import Header from "./layout/Header"
import filter from "../controllers/filter.controller"
import Plan from "./reports/Plan"
import Common from "./reports/Common"
import Manager from "./reports/Manager"

export const Reports = observer(() => {

    return (
        <>
            <Header />
            {filter.filterType === "1" &&
                <Plan />
            }
            {filter.filterType === "2" &&
                <Common />
            }
            {filter.filterType === "3" &&
                <Manager />
            }
        </>
    )
})

export default Reports

if(document.getElementById('app')) {
    ReactDOM.render(<Reports />, document.getElementById('app'));
}
