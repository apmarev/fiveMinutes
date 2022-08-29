import { observer } from "mobx-react-lite"
import React, { useEffect } from "react"
import userController from "../../controllers/user.controller"
import { Space, Table, Button, Modal, Row, Col, Input, Select } from 'antd'

const columns = [
    { title: 'Email', dataIndex: 'login', key: 'login' },
    { title: 'Тип', dataIndex: 'super', key: 'super', render: (el) => el > 0 ? 'Редактор' : 'Читатель' },
    {
        title: 'Действия',
        key: 'actions',
        render: (_, record) => (
            <Space>
                <Button
                    type="dashed"
                    onClick={() => userController.selectUser(record.id)}
                >
                    Изменить
                </Button>
                <Button
                    type="dashed"
                    onClick={() => userController.delete(record.id)}
                    danger
                >
                    Удалить
                </Button>
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
                    onChange={(e) => userController.onChangeUser('login', e.target.value)}
                    required
                />
            </Col>
            <Col span={24}>
                <Input.Password
                    placeholder="Укажите пароль"
                    value={userController.element.password}
                    onChange={(e) => userController.onChangeUser('password', e.target.value)}
                    required={!userController.element.id || userController.element.id < 1}
                />
            </Col>
            <Col span={24}>
                <Select
                    defaultValue={0}
                    style={{ width: `100%` }}
                    onChange={(e) => userController.onChangeUser('super', e)}
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
        userController.getList()
    }, [])

    return (
        <>
            {userController.list.length > 0 &&
                <>
                    <Table
                        columns={columns}
                        dataSource={userController.list}
                        rowKey="id"
                    />
                </>
            }

            <Modal
                title="Редактирование пользователя"
                visible={userController.modal}
                onOk={() => userController.save()}
                onCancel={() => userController.clearElement()}
            >
                <User />
            </Modal>
        </>
    )
})
