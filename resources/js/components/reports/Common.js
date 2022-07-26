import { observer } from "mobx-react-lite"
import React, {useEffect} from "react"
import filter from "../../controllers/filter.controller"

export const Common = observer(() => {

    useEffect(() => {
        filter.setDefaultFilterData()
    }, [])

    return (
        <>

        </>
    )
})

export default Common
