import { observer } from "mobx-react-lite"
import ReactDOM from "react-dom"
import React, {useEffect} from "react"
import filter from "../../controllers/filter.controller"
import { Row, Col, DatePicker, Space, Button, Select } from "antd"
import moment from "moment"
const { Option } = Select

export const Header = observer(() => {

    return (
        <>
            <form onSubmit={e => filter.getData(e)} className="header">
                <Row gutter={[20,20]}>
                    <Col xs={24} lg={4}>
                        <Select
                            mode="multiple"
                            allowClear
                            placeholder="Менеджер"
                        >
                            <Option key="1" value="1">1</Option>
                            <Option key="2" value="2">2</Option>
                        </Select>
                    </Col>
                    <Col xs={24} lg={4}>
                        <Select
                            allowClear
                            placeholder="Год"
                        >
                            <Option key="1" value="1">1</Option>
                            <Option key="2" value="2">2</Option>
                        </Select>
                    </Col>
                    <Col xs={24} lg={4}>
                        <Select
                            mode="multiple"
                            allowClear
                            placeholder="Месяц"
                        >
                            <Option key="1" value="1">1</Option>
                            <Option key="2" value="2">2</Option>
                        </Select>
                    </Col>
                    <Col xs={24} lg={4}>
                        <Select
                            allowClear
                            placeholder="Воронка"
                        >
                            <Option key="1" value="1">1</Option>
                            <Option key="2" value="2">2</Option>
                        </Select>
                    </Col>
                    <Col xs={24} lg={4}>
                        <Button htmlType="submit">Перейти</Button>
                    </Col>
                </Row>
            </form>
        </>
    )
})

export default Header
