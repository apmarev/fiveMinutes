import { observer } from "mobx-react-lite"
import React, {useEffect} from "react"
import filter from "../../controllers/filter.controller"
import userController from "../../controllers/user.controller"
import { Row, Col, Button, Select, Radio, Space } from "antd"
import { ArrowLeftOutlined, ArrowRightOutlined } from '@ant-design/icons'
import store from "store"
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
                <Row gutter={[10,10]} justify="space-between">
                    <Col xs={24} lg={5}>
                        <Radio.Group
                            disabled={filter.searchDisabled}
                            onChange={e => {
                                filter.setDefaultFilterData()
                                filter.filterType = e.target.value
                            }}
                            value={filter.filterType}
                            size="small"
                        >
                            {store.get("super") === 1 &&
                                <Radio.Button value="1">План</Radio.Button>
                            }
                            <Radio.Button value="2">Общий отчет</Radio.Button>
                            <Radio.Button value="3">Менеджер</Radio.Button>
                        </Radio.Group>
                    </Col>
                    {filter.filterType !== "4" &&
                        <>
                            {filter.filterType === "3" &&
                                <Col xs={24} lg={2}>
                                    <Select
                                        disabled={filter.searchDisabled}
                                        mode="multiple"
                                        allowClear
                                        placeholder="Менеджер"
                                        maxTagCount="responsive"
                                        value={filter.filter.managers}
                                        onChange={e => filter.filter.managers = e}
                                        size="small"
                                    >
                                        {filter.managers.map((item, k) => (
                                            <Option key={k} value={item.id}>{item.name}</Option>
                                        ))}
                                    </Select>
                                </Col>
                            }
                            {filter.filterType !== "3" &&
                                <Col xs={24} lg={2}>
                                    <Select
                                        disabled={filter.searchDisabled}
                                        allowClear
                                        placeholder="Воронка"
                                        value={filter.filter.pipeline}
                                        onChange={e => filter.filter.pipeline = e}
                                        size="small"
                                    >
                                        {filter.pipelines.map((item, k) => (
                                            <Option key={k} value={item.id}>{item.name}</Option>
                                        ))}
                                    </Select>
                                </Col>
                            }
                            <Col xs={24} lg={2}>
                                <Select
                                    disabled={filter.searchDisabled}
                                    allowClear
                                    placeholder="Год"
                                    value={filter.filter.year}
                                    onChange={e => filter.filter.year = e}
                                    size="small"
                                >
                                    {filter.years.map((item, k) => (
                                        <Option key={k} value={item?.year ?? item}>{item?.year ?? item}</Option>
                                    ))}
                                </Select>
                            </Col>
                            <Col xs={24} lg={2}>
                                <Select
                                    disabled={filter.searchDisabled}
                                    allowClear
                                    placeholder="Месяц"
                                    value={filter.filter.month}
                                    onChange={e => filter.filter.month = e}
                                    size="small"
                                >
                                    {filter.months.map((item, k) => (
                                        <Option key={k} value={item?.month ?? item.id}>{item?.month_name ?? item.name}</Option>
                                    ))}
                                </Select>
                            </Col>
                            <Col xs={24} lg={4}>
                                <Space size="middle">
                                    <Button
                                        type="primary"
                                        disabled={filter.searchDisabled}
                                        htmlType="submit"
                                        size="small"
                                    >
                                        Показать
                                    </Button>
                                    {filter.filterType === "1" && filter.data.length > 0 &&
                                        <Button
                                            type="primary"
                                            disabled={filter.searchDisabled}
                                            onClick={e => filter.savePlan(e)}
                                            size="small"
                                        >
                                            Сохранить
                                        </Button>
                                    }
                                </Space>
                            </Col>
                            <Col xs={24} lg={4} className="controls">
                                <Space size="middle">
                                    <Button
                                        type="dashed"
                                        disabled={filter.searchDisabled}
                                        onClick={_ => scrollTo("left")}
                                        size="small"
                                    >
                                        <ArrowLeftOutlined />
                                    </Button>
                                    <Button
                                        type="dashed"
                                        disabled={filter.searchDisabled}
                                        onClick={_ => scrollTo("right")}
                                        size="small"
                                    >
                                        <ArrowRightOutlined />
                                    </Button>
                                </Space>
                            </Col>
                        </>
                    }
                    <Col xs={24} lg={5}>
                        <Space size="middle" className="user-actions">
                            {store.get("super") === 1 &&
                                <Button
                                    type="primary"
                                    disabled={filter.searchDisabled}
                                    onClick={_ => filter.filterType = "4"}
                                    size="small"
                                >
                                    Пользователи
                                </Button>
                            }
                            <Button
                                type="dashed"
                                danger
                                disabled={filter.searchDisabled}
                                onClick={_ => userController.logOut()}
                                size="small"
                            >
                                Выход
                            </Button>
                        </Space>
                    </Col>
                </Row>
            </form>
        </>
    )
})

export default Header
