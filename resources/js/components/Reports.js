import { observer } from "mobx-react-lite"
import ReactDOM from "react-dom"
import React, {useEffect} from "react"
import Header from "./layout/Header"
import filter from "../controllers/filter.controller"
import Plan from "./reports/Plan"
import Common from "./reports/Common"
import Manager from "./reports/Manager"
import { Auth } from './reports/Auth'
import store from 'store'

export const Reports = observer(() => {

    return store.get('token') && store.get('token') !== '' ? (
        <>
            <Header />
            <main>
                {filter.filterType === "1" &&
                    <Plan />
                }
                {filter.filterType === "2" &&
                    <Common />
                }
                {filter.filterType === "3" &&
                    <Manager />
                }
            </main>
            <footer></footer>
        </>
    ) : <Auth />
})

export default Reports

if(document.getElementById('app')) {
    ReactDOM.render(<Reports />, document.getElementById('app'));
}
