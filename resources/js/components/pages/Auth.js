import { observer } from "mobx-react-lite"
import React from "react"
import { Row, Col, Input, Button, Typography, Space } from 'antd'
import userController from "../../controllers/user.controller"
const { Title } = Typography

export const Auth = observer(() => {

    return (
        <Row className="auth" justify="center" align="middle">
            <Col xs={24} md={8} lg={6} xl={4}>
                <form onSubmit={(e) => userController.logIn(e)}>
                    <Space direction="vertical" size="middle" align="center" className="auth-form">
                        <Title>Авторизация</Title>
                        <Input
                            placeholder="Email"
                            value={userController.data.login}
                            onChange={(e) => userController.onChangeAuth('login', e.target.value)}
                            required
                        />
                        <Input.Password
                            placeholder="Пароль"
                            value={userController.data.password}
                            onChange={(e) => userController.onChangeAuth('password', e.target.value)}
                            required
                        />
                        <Button htmlType="submit" type="primary" block>Войти</Button>
                    </Space>
                </form>
            </Col>
        </Row>
    )
})
