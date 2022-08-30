import { observer } from "mobx-react-lite"
import React, { useEffect } from "react"
import userController from "../../controllers/user.controller"
import { Space, Table, Button, Modal, Row, Col, Input, Select, Popconfirm, Typography } from "antd"
const { Title } = Typography

const columns = [
    {
        title: "Email",
        dataIndex: "login",
        key: "login"
    },
    {
        title: "Тип",
        dataIndex: "super",
        key: "super",
        width: "220px",
        render: (el) => el > 0 ? "Редактор" : "Читатель"
    },
    {
        title: "Действия",
        key: "actions",
        width: "220px",
        render: (_, record) => (
            <Space>
                <Button
                    type="dashed"
                    onClick={() => userController.selectUser(record.id)}
                >
                    Изменить
                </Button>
                <Popconfirm
                    title="Вы уверены?"
                    onConfirm={_ => userController.delete(record.id)}
                    okText="Да"
                    cancelText="Нет"
                >
                    <Button
                        type="dashed"
                        danger
                    >
                        Удалить
                    </Button>
                </Popconfirm>
            </Space>
        )
    },
]

const User = observer(() => {

    return(
        <Row gutter={[20, 20]}>
            <Col span={24}>
                <Input
                    placeholder="Укажите email"
                    value={userController.element.login}
                    onChange={(e) => userController.onChangeUser("login", e.target.value)}
                    required
                />
            </Col>
            <Col span={24}>
                <Input.Password
                    placeholder="Укажите пароль"
                    value={userController.element.password}
                    onChange={(e) => userController.onChangeUser("password", e.target.value)}
                    required={!userController.element.id || userController.element.id < 1}
                />
            </Col>
            <Col span={24}>
                <Select
                    defaultValue={0}
                    style={{ width: `100%` }}
                    onChange={(e) => userController.onChangeUser("super", e)}
                    value={userController.element.super}
                    required
                >
                    <Select.Option value={0}>Читатель</Select.Option>
                    <Select.Option value={1}>Редактор</Select.Option>
                </Select>
            </Col>
        </Row>
    )
})

export const Users = observer(() => {

    useEffect(() => {
        userController.getUsersList()
    }, [])

    return (
        <>
            <Row justify="center" className="users">
                <Col xs={24} lg={18} xl={12}>
                    <Row gutter={[20, 20]}>
                        <Col xs={24} className="users-actions">
                            <Title level={3}>Список пользователей</Title>
                            <Button
                                type="dashed"
                                onClick={_ => userController.createUser()}
                            >
                                Новый пользователь
                            </Button>
                        </Col>
                        <Col xs={24}>
                            <Table
                                columns={columns}
                                dataSource={userController.list}
                                rowKey="id"
                            />
                        </Col>
                    </Row>
                </Col>
            </Row>
            <Modal
                title="Редактирование пользователя"
                visible={userController.modal}
                onOk={() => userController.saveUser()}
                onCancel={() => userController.clearElement()}
                cancelText="Отменить"
                okText="Сохранить"
            >
                <User />
            </Modal>
        </>
    )
})
