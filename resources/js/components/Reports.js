import { observer } from "mobx-react-lite"
import ReactDOM from "react-dom"
import React, {useEffect} from "react"
import Header from "./layout/Header";

export const Reports = observer(() => {

    return (
        <>
            <Header />
        </>
    )
})

export default Reports

if(document.getElementById('app')) {
    ReactDOM.render(<Reports />, document.getElementById('app'));
}
