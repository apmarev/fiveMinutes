const Selectable = () => {
    return(
        <div className="selectable">
            <Space>
                {report.vars && report.vars.months &&
                    <Select value={report.newMonth} style={{ width: 120 }} onChange={(e) => report.setNew('newMonth', e)}>
                        {report.vars.months.map((el) => (
                            <Select.Option value={el.month}>{el.monthName}</Select.Option>
                        ))}
                    </Select>
                }
                {report.vars && report.vars.years &&
                    <Select value={report.newYear} style={{ width: 120 }} onChange={(e) => report.setNew('newYear', e)}>
                        {report.vars.years.map((el) => (
                            <Select.Option value={el.year}>{el.year}</Select.Option>
                        ))}
                    </Select>
                }
                <Select value={Number(report.newPipeline)} style={{ width: 220 }} onChange={(e) => report.setNew('newPipeline', e)}>
                    <Select.Option value={3493222}>Первичные КЦ</Select.Option>
                    <Select.Option value={5084302}>Продление КЦ</Select.Option>
                </Select>
                <Button type="primary" onClick={() => report.get()}>Перейти</Button>
            </Space>
        </div>
    )
}

const AllSum = () => {

    const list = [
        { key: 'count', title: 'Кол-во лидов' },
        { key: 'priceGeneral', title: 'Сумма продаж (всего)', type: 'r' },
        { key: 'priceMonth', title: 'Сумма продаж (месяц)', type: 'r' },
        { key: 'pricePackage', title: 'Сумма продаж (пакет)', type: 'r' },
        { key: 'countBuy', title: 'Количество покупок' },
        { key: 'conversion', title: 'Конверсия', type: '%' },
        { key: 'percent', title: 'Процент выполнения', type: '%', visible: true },
        { key: 'generalPlan', title: 'Общий план', type: 'r', visible: true },
    ]

    return(
        <div className="all-sum">
            <Row>
                <Col span={6}>
                    <Row>
                        <Col span={16} className="data green padding-left">
                            Общее
                        </Col>
                        <Col span={8} className="data green center">
                            Итог за месяц
                        </Col>
                        {list.map((el) => {
                            return report.pipeline === 5084302 && el.visible ?
                                <>

                                </>
                                :
                                <>
                                    <Col span={16} className="data line_bottom line_right padding-left">
                                        {el.title}
                                    </Col>
                                    <Col span={8} className="data line_bottom line_right center">
                                        {format(el.type ? el.type : '', report.general[el.key])}
                                    </Col>
                                </>
                        })}
                    </Row>
                </Col>
                <Col span={18}>

                </Col>
            </Row>
        </div>
    )
}

const AllSumMonth = () => {

    const list = [
        { key: 'one', title: 'Выручка в день МГ Месяц' },
        { key: 'two', title: 'Выручка в день МГ Пакет' },
        { key: 'three', title: 'Общая выручка в день' },
        { key: 'four', title: 'Общая выручка ЕГЭ (месяц)' },
        { key: 'five', title: 'Общая выручка ОГЭ (месяц) ' },
        { key: 'six', title: 'Общая выручка 10 класс (месяц) ' },
        { key: 'seven', title: 'Пакеты ЕГЭ пакет' },
        { key: 'eight', title: 'Пакеты ОГЭ пакет' },
        { key: 'nine', title: 'Пакеты 10 класс пакет' },
    ]

    return(
        <div className="all-sum-month">
            <Row>
                <Col span={6}>
                    <Row>
                        <Col span={16} className="data orange padding-left">
                            Общая выручка
                        </Col>
                        <Col span={8} className="data orange center">
                            Итог за месяц
                        </Col>
                        {list.map((el) =>
                            <>
                                <Col span={16} className="data line_bottom line_right padding-left indigo">
                                    {el.title}
                                </Col>
                                <Col span={8} className="data line_bottom line_right center indigo">
                                    {format('', report.all[el.key])}
                                </Col>
                            </>
                        )}
                    </Row>
                </Col>
                <Col span={18} className="listing">
                    <div className="days">
                        {report.price.map(el => (
                            <div className="">
                                <Row>
                                    <Col span={24} className="data center orange line_right">
                                        {el.title}
                                    </Col>
                                    <Col span={24} className="data center line_bottom line_right indigo">{el.days.one}</Col>
                                    <Col span={24} className="data center line_bottom line_right indigo">{el.days.two}</Col>
                                    <Col span={24} className="data center line_bottom line_right indigo">{el.days.three}</Col>
                                    <Col span={24} className="data center line_bottom line_right indigo">{el.days.four}</Col>
                                    <Col span={24} className="data center line_bottom line_right indigo">{el.days.five}</Col>
                                    <Col span={24} className="data center line_bottom line_right indigo">{el.days.six}</Col>
                                    <Col span={24} className="data center line_bottom line_right indigo">{el.days.seven}</Col>
                                    <Col span={24} className="data center line_bottom line_right indigo">{el.days.eight}</Col>
                                    <Col span={24} className="data center line_bottom line_right indigo">{el.days.nine}</Col>
                                </Row>
                            </div>
                        ))}
                    </div>
                </Col>
            </Row>
        </div>
    )
}

const ManagerDay = ({ day, manager }) => {

    const classAdd = day.week ? 'blue' : '';

    return(
        <div className="">
            <Row>
                <Col span={24} className="data center green line_right">
                    {day.title}
                </Col>
                <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>{format('', day.values.all.value)}</Col>
                <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>{format('r', day.values.monthExam.value)}</Col>
                <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>{format('r', day.values.monthOge.value)}</Col>
                <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>{format('r', day.values.monthTenClass.value)}</Col>
                <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>{format('r', day.values.packageExam.value)}</Col>
                <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>{format('r', day.values.packageOge.value)}</Col>
                <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>{format('r', day.values.packageTenClass.value)}</Col>
                <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>{format('', day.values.countPackagesExam.value)}</Col>
                <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>{format('', day.values.countPackagesOge.value)}</Col>
                <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>{format('', day.values.countPackagesTenClass.value)}</Col>
                <Col span={24} className={`data center line_bottom line_right blue`}>{format('r', day.values.countPriceMonth.value)}</Col>
                <Col span={24} className={`data center line_bottom line_right blue`}>{format('r', day.values.countPricePackage.value)}</Col>
                <Col span={24} className="data center line_bottom line_right yellow">{format('', day.values.countMonth.value)}</Col>
                <Col span={24} className="data center line_bottom line_right yellow">{format('', day.values.countPackage.value)}</Col>
                <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>{day.week && format('r', day.values.sumPrice.value)}</Col>
                <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>{day.week && format('', day.values.countBuy.value)}</Col>
                <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>{day.week && format('r', day.values.averageCheck ? day.values.averageCheck.value : '')}</Col>
                <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>{day.week && format('%', day.values.conversion ? day.values.conversion.value : '')}</Col>
                {report.pipeline !== 5084302 && <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>
                    {day.week
                        ?<InputNumber
                            value={day.plan}
                            onBlur={(e) => report.setPlan(e, manager, `week${day.weekNumber}`)}
                        />
                        : ''
                    }

                </Col>}
                {report.pipeline !== 5084302 &&
                    <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>{day.week && format('%', day.values.planPercent.value)}</Col>}
                {report.pipeline !== 5084302 && <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>{day.week && format('r', day.values.planRemainder.value)}</Col>}
                <Col span={24} className={`data center line_bottom line_right ${classAdd}`}>
                    {!day.week ?
                        <Switch
                            size="small"
                            checked={day.values.weekend && day.values.weekend.value > 0}
                            onChange={() => report.setOrRemoveWeekend(manager, day.day)}
                        />
                        : day.values.weekend.value
                    }

                </Col>
            </Row>
        </div>
    )
}

const ManagerTitles = ({ all, manager }) => {

    const list = [
        { key: 'all', title: 'Кол-во лидов' },
        { key: 'monthExam', title: 'МГ Месяц ЕГЭ', type: 'r' },
        { key: 'monthOge', title: 'МГ Месяц ОГЭ', type: 'r' },
        { key: 'monthTenClass', title: 'МГ Месяц 10 класс', type: 'r' },
        { key: 'packageExam', title: 'МГ пакет ЕГЭ', type: 'r' },
        { key: 'packageOge', title: 'МГ пакет ОГЭ', type: 'r' },
        { key: 'packageTenClass', title: 'МГ пакет 10 класс', type: 'r' },
        { key: 'countPackagesExam', title: 'Продано месяцев ЕГЭ' },
        { key: 'countPackagesOge', title: 'Продано месяцев ОГЭ' },
        { key: 'countPackagesTenClass', title: 'Продано месяцев 10 класс' },
        { key: 'countPriceMonth', title: 'Сумма продаж МГ Месяц', className: 'blue', type: 'r' },
        { key: 'countPricePackage', title: 'Сумма продаж МГ пакет', className: 'blue', type: 'r' },
        { key: 'countMonth', title: 'Количество покупок МГ Месяц', className: 'yellow' },
        { key: 'countPackage', title: 'Количество покупок МГ пакет', className: 'yellow' },
        { key: 'sumPrice', title: 'Сумма продаж общая', type: 'r' },
        { key: 'countBuy', title: 'Кол-во продаж общее' },
        { key: 'averageCheck', title: 'Средний чек', type: 'r' },
        { key: 'conversion', title: 'Конверсия', type: '%' },
        { key: 'plan', title: 'План', type: 'input', visible: true },
        { key: 'planPercent', title: '% выполнения плана на текущий день', type: '%', visible: true },
        { key: 'planRemainder', title: 'Остаток по плану', type: 'r', visible: true },
        { key: 'weekends', title: 'Выходные' },
    ]

    return <>
        <Col span={24} className="data green padding-left">
            {manager}
        </Col>
        {list.map(el => report.pipeline === 5084302 && el.visible ? "" : (
            <>
                <Col span={16} className={"data line_bottom line_right padding-left " + el.className}>
                    {el.title}
                </Col>
                <Col span={8} className={"data center line_bottom " + el.className}>
                    {el.type && el.type === 'input'
                        ?   <InputNumber
                            value={all[el.key]}
                            onBlur={(e) => report.setPlan(e, manager, `plan`)}
                        />
                        : format(el.type ? el.type : '', all[el.key])}
                </Col>
            </>
        ))}</>
}

const App = () => {

    useEffect(() => {
        return () => {
            report.get()
        }
    }, [])

    const list = [
        { key: 'one', title: 'Выручка в день МГ Месяц' },
        { key: 'two', title: 'Выручка в день МГ Пакет' },
        { key: 'three', title: 'Общая выручка в день' },
        { key: 'four', title: 'Общая выручка ЕГЭ (месяц)' },
        { key: 'five', title: 'Общая выручка ОГЭ (месяц) ' },
        { key: 'six', title: 'Общая выручка 10 класс (месяц) ' },
        { key: 'seven', title: 'Пакеты ЕГЭ пакет' },
        { key: 'eight', title: 'Пакеты ОГЭ пакет' },
        { key: 'nine', title: 'Пакеты 10 класс пакет' },
    ]

    return (
        <>
            {Loaders.load && <LoaderDiv />}
            <div className="App">
                <Selectable />
                <AllSum />
                {report.list.length > 0 &&
                    <Row className="dashboard">
                        <Col span={6} className="border-right">
                            <Row>
                                <Col span={16} className="data orange padding-left">
                                    Общая выручка
                                </Col>
                                <Col span={8} className="data orange center">
                                    Итог за месяц
                                </Col>
                                {list.map((el) =>
                                    <>
                                        <Col span={16} className="data line_bottom line_right padding-left indigo">
                                            {el.title}
                                        </Col>
                                        <Col span={8} className="data line_bottom line_right center indigo">
                                            {format('r', report.all[el.key])}
                                        </Col>
                                    </>
                                )}
                            </Row>
                            <Row>
                                {report.list.map(el => <ManagerTitles all={el.all} manager={el.name} />)}
                            </Row>
                        </Col>
                        <Col span={18} className="listing">
                            <div className="days">
                                {report.price.map(el => (
                                    <div className="">
                                        <Row>
                                            <Col span={24} className="data center orange line_right">
                                                {el.title}
                                            </Col>
                                            <Col span={24} className="data center line_bottom line_right indigo">{format('r', el.days.one)}</Col>
                                            <Col span={24} className="data center line_bottom line_right indigo">{format('r', el.days.two)}</Col>
                                            <Col span={24} className="data center line_bottom line_right indigo">{format('r', el.days.three)}</Col>
                                            <Col span={24} className="data center line_bottom line_right indigo">{format('r', el.days.four)}</Col>
                                            <Col span={24} className="data center line_bottom line_right indigo">{format('r', el.days.five)}</Col>
                                            <Col span={24} className="data center line_bottom line_right indigo">{format('r', el.days.six)}</Col>
                                            <Col span={24} className="data center line_bottom line_right indigo">{format('r', el.days.seven)}</Col>
                                            <Col span={24} className="data center line_bottom line_right indigo">{format('r', el.days.eight)}</Col>
                                            <Col span={24} className="data center line_bottom line_right indigo">{format('r', el.days.nine)}</Col>
                                        </Row>
                                    </div>
                                ))}
                            </div>
                            {report.list.map(el => (
                                <div className="days">
                                    {el.days.map((item) => (
                                        <ManagerDay day={item} manager={el.name} />
                                    ))}
                                </div>
                            ))}
                        </Col>
                    </Row>
                }
            </div>
        </>
    )
}

ReactDOM.render(
    <App />,
    document.getElementById("title-page")
)
