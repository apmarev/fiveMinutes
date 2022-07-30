import { observer } from "mobx-react-lite"
import React, {useEffect} from "react"
import filter from "../../controllers/filter.controller"
import { Row, Col, Button, Select, Radio, Space } from "antd"
const { Option } = Select

export const Header = observer(() => {
    useEffect(() => {

    }, [])

    const scrollTo = (direction) => {
        const el = document.querySelector(".report")
        const y = el.scrollLeft
        let offset
        if(direction === "left") offset = y - 200
        else if(direction === "right") offset = y + 200
        el.scrollTo({
            behavior: "smooth",
            left: offset
        })
    }

    return (
        <>
            <form onSubmit={e => filter.getData(e)} className="header">
                <Row gutter={[20,20]}>
                    <Col xs={24} lg={6} xl={5}>
                        <Radio.Group
                            disabled={filter.searchDisabled}
                            onChange={e => {
                                filter.setDefaultFilterData()
                                filter.filterType = e.target.value
                            }}
                            value={filter.filterType}
                        >
                            <Radio.Button value="1">План</Radio.Button>
                            <Radio.Button value="2">Общий отчет</Radio.Button>
                            <Radio.Button value="3">Менеджер</Radio.Button>
                        </Radio.Group>
                    </Col>
                    {filter.filterType === "3" &&
                        <Col xs={24} lg={5} xl={4}>
                            <Select
                                disabled={filter.searchDisabled}
                                mode="multiple"
                                allowClear
                                placeholder="Менеджер"
                                maxTagCount="responsive"
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
                                disabled={filter.searchDisabled}
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
                            disabled={filter.searchDisabled}
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
                            disabled={filter.searchDisabled}
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
                    <Col xs={24} lg={3} xl={2}>
                        <Space size="middle">
                            <Button
                                disabled={filter.searchDisabled}
                                htmlType="submit"
                            >
                                Показать
                            </Button>
                            {filter.filterType === "1" && filter.data.length > 0 &&
                                <Button
                                    type="primary"
                                    disabled={filter.searchDisabled}
                                    onClick={e => filter.savePlan(e)}
                                >
                                    Сохранить
                                </Button>
                            }
                        </Space>
                    </Col>
                    <Col xs={24} className="controls">
                        <Space size="middle">
                            <Button
                                type="primary"
                                disabled={filter.searchDisabled}
                                onClick={e => scrollTo("left")}
                            >
                                Налево
                            </Button>
                            <Button
                                type="primary"
                                disabled={filter.searchDisabled}
                                onClick={e => scrollTo("right")}
                            >
                                Направо
                            </Button>
                        </Space>
                    </Col>
                </Row>
            </form>
        </>
    )
})

export default Header
