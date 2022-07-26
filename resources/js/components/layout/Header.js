import { observer } from "mobx-react-lite"
import ReactDOM from "react-dom"
import React, {useEffect} from "react"
import filter from "../../controllers/filter.controller"
import { Row, Col, Button, Select } from "antd"
import moment from "moment"
const { Option } = Select

export const Header = observer(() => {

    return (
        <>
            <form onSubmit={e => filter.getData(e)} className="header">
                <Row gutter={[20,20]} className="filter-type">
                    <Col xs={24} lg={4} xl={3}>
                        <Select
                            value={filter.filterType}
                            onChange={e => {
                                filter.setDefaultFilterData()
                                filter.filterType = e
                            }}
                        >
                            <Option value="1">План</Option>
                            <Option value="2">Общий отчет</Option>
                            <Option value="3">Менеджер</Option>
                        </Select>
                    </Col>
                </Row>
                <Row gutter={[20,20]}>
                    {filter.filterType === "3" &&
                        <Col xs={24}>
                            <Select
                                mode="multiple"
                                allowClear
                                placeholder="Менеджер"
                                value={filter.filter.managers}
                                onChange={e => filter.filter.managers = e}
                            >
                                {filter.managers.map((item, k) => (
                                    <Option key={k} value={item.id}>{item.name}</Option>
                                ))}
                            </Select>
                        </Col>
                    }
                    {filter.filterType !== "3" &&
                        <Col xs={24} lg={4} xl={3}>
                            <Select
                                allowClear
                                placeholder="Воронка"
                                value={filter.filter.pipeline}
                                onChange={e => filter.filter.pipeline = e}
                            >
                                {filter.pipelines.map((item, k) => (
                                    <Option key={k} value={item.id}>{item.name}</Option>
                                ))}
                            </Select>
                        </Col>
                    }
                    <Col xs={24} lg={4} xl={3}>
                        <Select
                            allowClear
                            placeholder="Год"
                            value={filter.filter.year}
                            onChange={e => filter.filter.year = e}
                        >
                            {filter.years.map((item, k) => (
                                <Option key={k} value={item?.year ?? item}>{item?.year ?? item}</Option>
                            ))}
                        </Select>
                    </Col>
                    <Col xs={24} lg={4} xl={3}>
                        <Select
                            allowClear
                            placeholder="Месяц"
                            value={filter.filter.month}
                            onChange={e => filter.filter.month = e}
                        >
                            {filter.months.map((item, k) => (
                                <Option key={k} value={item?.month ?? item.id}>{item?.month_name ?? item.name}</Option>
                            ))}
                        </Select>
                    </Col>
                    <Col xs={24} lg={4} xl={3}>
                        <Button htmlType="submit">Перейти</Button>
                    </Col>
                </Row>
            </form>
        </>
    )
})

export default Header
