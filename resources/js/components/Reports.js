import { observer } from "mobx-react-lite"
import ReactDOM from "react-dom"
import React, {useEffect} from "react"
import Header from "./layout/Header";
import Manager from "./reports/Manager";

export const Reports = observer(() => {

    return (
        <>
            <Header />
            <Manager />
        </>
    )
})

export default Reports

if(document.getElementById('app')) {
    ReactDOM.render(<Reports />, document.getElementById('app'));
}
