import { observer } from "mobx-react-lite"
import ReactDOM from "react-dom"
import React, {useEffect} from "react"
import filter from "../../controllers/filter.controller"
import { Row, Col, Button, Select } from "antd"
import moment from "moment"
const { Option } = Select

export const Header = observer(() => {

    useEffect(() => {
        filter.getYears()
        filter.getManagers()
        filter.getMonthsByYear(2022)
    }, [])

    return (
        <>
            <form onSubmit={e => filter.getData(e)} className="header">
                <Row gutter={[20,20]}>
                    <Col xs={24} lg={4}>
                        <Select
                            mode="multiple"
                            allowClear
                            placeholder="Менеджер"
                            onChange={e => filter.filter.managers = e}
                        >
                            {filter.managers.map((item, k) => (
                                <Option key={k} value={item.id}>{item.name}</Option>
                            ))}
                        </Select>
                    </Col>
                    <Col xs={24} lg={4}>
                        <Select
                            allowClear
                            placeholder="Год"
                            onChange={e => filter.filter.year = e}
                        >
                            {filter.years.map((item, k) => (
                                <Option key={k} value={item.year}>{item.year}</Option>
                            ))}
                        </Select>
                    </Col>
                    <Col xs={24} lg={4}>
                        <Select
                            allowClear
                            placeholder="Месяц"
                            onChange={e => filter.filter.month = e}
                        >
                            {filter.months.map((item, k) => (
                                <Option key={k} value={item.month}>{item.month_name}</Option>
                            ))}
                        </Select>
                    </Col>
                    {/*<Col xs={24} lg={4}>*/}
                    {/*    <Select*/}
                    {/*        allowClear*/}
                    {/*        placeholder="Воронка"*/}
                    {/*    >*/}
                    {/*        <Option key="1" value="1">1</Option>*/}
                    {/*        <Option key="2" value="2">2</Option>*/}
                    {/*    </Select>*/}
                    {/*</Col>*/}
                    <Col xs={24} lg={4}>
                        <Button htmlType="submit">Перейти</Button>
                    </Col>
                </Row>
            </form>
        </>
    )
})

export default Header
