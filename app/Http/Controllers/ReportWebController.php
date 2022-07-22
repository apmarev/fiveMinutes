<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomApiException;
use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AmoCrmController;

class ReportWebController extends Controller {

    public function getViewReport(Request $request) {
        return view('reports');
    }

    public function report(Request $request) {

        $listOne = [
            [ 'key' => 'one', 'title' => 'Выручка в день МГ Месяц' ],
            [ 'key' => 'two', 'title' => 'Выручка в день МГ Пакет' ],
            [ 'key' => 'three', 'title' => 'Общая выручка в день' ],
            [ 'key' => 'four', 'title' => 'Общая выручка ЕГЭ (месяц)' ],
            [ 'key' => 'five', 'title' => 'Общая выручка ОГЭ (месяц) ' ],
            [ 'key' => 'six', 'title' => 'Общая выручка 10 класс (месяц) ' ],
            [ 'key' => 'seven', 'title' => 'Пакеты ЕГЭ пакет' ],
            [ 'key' => 'eight', 'title' => 'Пакеты ОГЭ пакет' ],
            [ 'key' => 'nine', 'title' => 'Пакеты 10 класс пакет' ],
        ];

        $listTwo = [
            [ 'key' => 'all', 'title' => 'Кол-во лидов' ],
            [ 'key' => 'monthExam', 'title' => 'МГ Месяц ЕГЭ', 'type' => '₽' ],
            [ 'key' => 'monthOge', 'title' => 'МГ Месяц ОГЭ', 'type' => '₽' ],
            [ 'key' => 'monthTenClass', 'title' => 'МГ Месяц 10 класс', 'type' => '₽' ],
            [ 'key' => 'packageExam', 'title' => 'МГ пакет ЕГЭ', 'type' => '₽' ],
            [ 'key' => 'packageOge', 'title' => 'МГ пакет ОГЭ', 'type' => '₽' ],
            [ 'key' => 'packageTenClass', 'title' => 'МГ пакет 10 класс', 'type' => '₽' ],
            [ 'key' => 'countPackagesExam', 'title' => 'Продано месяцев ЕГЭ' ],
            [ 'key' => 'countPackagesOge', 'title' => 'Продано месяцев ОГЭ' ],
            [ 'key' => 'countPackagesTenClass', 'title' => 'Продано месяцев 10 класс' ],
            [ 'key' => 'countPriceMonth', 'title' => 'Сумма продаж МГ Месяц', 'className' => 'blue', 'type' => '₽' ],
            [ 'key' => 'countPricePackage', 'title' => 'Сумма продаж МГ пакет', 'className' => 'blue', 'type' => '₽' ],
            [ 'key' => 'countMonth', 'title' => 'Количество покупок МГ Месяц', 'className' => 'yellow' ],
            [ 'key' => 'countPackage', 'title' => 'Количество покупок МГ пакет', 'className' => 'yellow' ],
            [ 'key' => 'sumPrice', 'title' => 'Сумма продаж общая', 'type' => '₽' ],
            [ 'key' => 'countBuy', 'title' => 'Кол-во продаж общее' ],
            [ 'key' => 'averageCheck', 'title' => 'Средний чек', 'type' => '₽' ],
            [ 'key' => 'conversion', 'title' => 'Конверсия', 'type' => '%' ],
            [ 'key' => 'plan', 'title' => 'План', 'type' => 'input', 'visible' => true ],
            [ 'key' => 'planPercent', 'title' => '% выполнения плана на текущий день', 'type' => '%', 'visible' => true ],
            [ 'key' => 'planRemainder', 'title' => 'Остаток по плану', 'type' => '₽', 'visible' => true ],
            [ 'key' => 'weekends', 'title' => 'Выходные' ],
        ];

        $listThree = [
            [ 'key' => 'count', 'title' => 'Кол-во лидов' ],
            [ 'key' => 'priceGeneral', 'title' => 'Сумма продаж (всего)', 'type' => '₽' ],
            [ 'key' => 'priceMonth', 'title' => 'Сумма продаж (месяц)', 'type' => '₽' ],
            [ 'key' => 'pricePackage', 'title' => 'Сумма продаж (пакет)', 'type' => '₽' ],
            [ 'key' => 'countBuy', 'title' => 'Количество покупок' ],
            [ 'key' => 'conversion', 'title' => 'Конверсия', 'type' => '%' ],
            [ 'key' => 'percent', 'title' => 'Процент выполнения', 'type' => '%', 'visible' => true ],
            [ 'key' => 'generalPlan', 'title' => 'Общий план', 'type' => '₽', 'visible' => true ],
        ];

        $pipeline = $request->has('pipeline') ? $request->input('pipeline') : 3493222;
        $month = $request->has('month') ? $request->input('month') : date('m');
        $year = $request->has('year') ? $request->input('year') : date('Y');

        $amo = new AmoCrmController(new AccessController());
        $data = $amo->getToDesktop($pipeline, $month, $year);

        return view('report', [
            'data' => $data,
            'listOne' => $listOne,
            'listTwo' => $listTwo,
            'pipeline' => 3493222,
            'listThree' => $listThree
        ]);
    }

}
