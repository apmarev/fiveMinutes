import { observer } from "mobx-react-lite"
import React from "react"
import { Row, Col, Input, Button } from 'antd'
import userController from "../../controllers/user.controller";

export const Auth = observer(() => {

    return (
        <Row className="auth" justify="space-around" align="middle">
            <Col span={4}>
                <form onSubmit={(e) => userController.login(e)}>
                    <Row gutter={[20, 20]}>
                        <Col span={24}>
                            <Input
                                placeholder="Email"
                                value={userController.data.login}
                                onChange={(e) => userController.onChange('login', e.target.value)}
                                required
                            />
                        </Col>
                        <Col span={24}>
                            <Input.Password
                                placeholder="Пароль"
                                value={userController.data.password}
                                onChange={(e) => userController.onChange('password', e.target.value)}
                                required
                            />
                        </Col>
                        <Col span={24}>
                            <Button htmlType="submit" type="primary">Войти</Button>
                        </Col>
                    </Row>
                </form>
            </Col>
        </Row>
    )
})
