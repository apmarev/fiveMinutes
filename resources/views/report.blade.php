<x-layout :footer="$footer" :pipeline="$pipeline">


    @foreach($managers as $manager)
        <div class="container-fluid manager">
            <div class="row">
                <div class="col-12 manager-title">
                    {{ $manager['name'] }}
                </div>
                <div class="col-12">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-2">
                                <div class="titles">
                                    <div class="number-report">Кол-во лидов</div class="number-report">
                                    <div class="number-report">МГ Месяц ЕГЭ</div class="number-report">
                                    <div class="number-report">МГ Месяц ОГЭ</div class="number-report">
                                    <div class="number-report">МГ Месяц 10 класс</div class="number-report">
                                    <div class="number-report">МГ пакет ЕГЭ</div class="number-report">
                                    <div class="number-report">МГ пакет ОГЭ</div class="number-report">
                                    <div class="number-report">МГ пакет 10 класс</div class="number-report">
                                    <div class="number-report">Продано месяцев ЕГЭ</div class="number-report">
                                    <div class="number-report">Продано месяцев ОГЭ</div class="number-report">
                                    <div class="number-report">Продано месяцев 10 класс</div class="number-report">
                                    <div class="number-report">Сумма продаж МГ Месяц</div class="number-report">
                                    <div class="number-report">Сумма продаж МГ пакет</div class="number-report">
                                    <div class="number-report">Количество покупок МГ Месяц</div class="number-report">
                                    <div class="number-report">Количество покупок МГ пакет</div class="number-report">
                                    <div class="number-report">Средний чек</div class="number-report">
                                    <div class="number-report">Конверсия</div class="number-report">
                                    <div class="number-report">План</div class="number-report">
                                    <div class="number-report">% выполнения плана на текущий день</div class="number-report">
                                    <div class="number-report">Остаток по плану</div class="number-report">
                                    <div class="number-report">Выходные</div class="number-report">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="titles">
                                    <div class="number-report">все новые на менеджера воронка Первичные</div class="number-report">
                                    <div class="number-report">Все где содержится ЕГЭ + пакет 1</div class="number-report">
                                    <div class="number-report">Все где содержится ОГЭ + пакет 1</div class="number-report">
                                    <div class="number-report">Все где содержится 10 класс + пакет 1</div class="number-report">
                                    <div class="number-report">МГ-ЕГЭ+пакет (1,5-10)</div class="number-report">
                                    <div class="number-report">МГ-OГЭ+пакет (1,5-10)</div class="number-report">
                                    <div class="number-report">МГ-10 класс+пакет (1,5-10)</div class="number-report">
                                    <div class="number-report">Сумма чисел из поля пакет проданных пакетов ЕГЭ</div class="number-report">
                                    <div class="number-report">Сумма чисел из поля пакет проданных пакетов ОГЭ</div class="number-report">
                                    <div class="number-report">Сумма чисел из поля пакет проданных пакетов 10 класс</div class="number-report">
                                    <div class="number-report">МГ Месяц ОГЭ+МГ Месяц ЕГЭ</div class="number-report">
                                    <div class="number-report">МГ пакет ЕГЭ+МГ пакет ОГЭ</div class="number-report">
                                    <div class="number-report">кол-во (МГ Месяц ЕГЭ+МГ месяц ОГЭ)</div class="number-report">
                                    <div class="number-report">кол-во (МГ пакет ЕГЭ+МГ пакет ОГЭ)</div class="number-report">
                                    <div class="number-report">Сумма проданных /Количество покупок</div class="number-report">
                                    <div class="number-report">Кол-во лидов/Количество покупок</div class="number-report">
                                    <div class="number-report">Цели-бюджет менеджера</div class="number-report">
                                </div>
                            </div>
                            <div class="col-7 listing">
                                @foreach($manager['days'] as $day)
                                    <div class="titles count">
                                        <div class="number-report">{{ $day['all'] }}</div class="number-report">
                                        <div class="number-report">{{ $day['monthExam'] }}</div class="number-report">
                                        <div class="number-report">{{ $day['monthOge'] }}</div class="number-report">
                                        <div class="number-report">{{ $day['monthTenClass'] }}</div class="number-report">
                                        <div class="number-report">{{ $day['packageExam'] }}</div class="number-report">
                                        <div class="number-report">{{ $day['packageOge'] }}</div class="number-report">
                                        <div class="number-report">{{ $day['packageTenClass'] }}</div class="number-report">
                                        <div class="number-report">{{ $day['countPackagesExam'] }}</div class="number-report">
                                        <div class="number-report">{{ $day['countPackagesOge'] }}</div class="number-report">
                                        <div class="number-report">{{ $day['countPackagesTenClass'] }}</div class="number-report">
                                        <div class="number-report">{{ $day['countPriceMonth'] }}</div class="number-report">
                                        <div class="number-report">{{ $day['countPricePackage'] }}</div class="number-report">
                                        <div class="number-report">{{ $day['countMonth'] }}</div class="number-report">
                                        <div class="number-report">{{ $day['countPackage'] }}</div class="number-report">
                                        <div class="number-report">{{ $day['averageCheck'] }}</div class="number-report">
                                        <div class="number-report">{{ $day['conversion'] }}</div class="number-report">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</x-layout>
