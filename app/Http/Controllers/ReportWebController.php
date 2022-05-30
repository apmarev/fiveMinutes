<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomApiException;
use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AmoCrmController;

class ReportWebController extends Controller {

    public function report() {

        $footer = AmoCrmController::getMonthAndYears();
        $footer['year'] = date('Y');
        $footer['month'] = date('m');

        $top = [
            'one' => [
                [
                    'title' => 'Кол-во лидов',
                    'value' => ''
                ],
                [
                    'title' => 'Сумма продаж (всего)',
                    'value' => ''
                ],
                [
                    'title' => 'Сумма продаж (месяц)',
                    'value' => ''
                ],
                [
                    'title' => 'Сумма продаж (пакет)',
                    'value' => ''
                ],
                [
                    'title' => 'Количество покупок',
                    'value' => ''
                ],
                [
                    'title' => 'Конверсия',
                    'value' => ''
                ],
                [
                    'title' => 'Процент выполнения',
                    'value' => ''
                ],
                [
                    'title' => 'Общий план',
                    'value' => ''
                ],
            ],
            'two' => [
                'count' => [
                    [
                        'title' => 'Выручка в день МГ Месяц',
                        'value' => ''
                    ],
                    [
                        'title' => 'Выручка в день МГ Пакет',
                        'value' => ''
                    ],
                    [
                        'title' => 'Общая выручка в день',
                        'value' => ''
                    ],
                    [
                        'title' => 'Общая выручка ЕГЭ (месяц)',
                        'value' => ''
                    ],
                    [
                        'title' => 'Общая выручка ОГЭ (месяц) ',
                        'value' => ''
                    ],
                    [
                        'title' => 'Общая выручка 10 класс (месяц) ',
                        'value' => ''
                    ],
                    [
                        'title' => 'Пакеты ЕГЭ пакет',
                        'value' => ''
                    ],
                    [
                        'title' => 'Пакеты ОГЭ пакет',
                        'value' => ''
                    ],
                    [
                        'title' => 'Пакеты 10 класс пакет',
                        'value' => ''
                    ],
                ],
                'days' => []
            ]
        ];

        return view('report', [
            'pipeline' => '',
            'footer' => $footer,
            'managers' => AmoCrmController::getToDesktop(),
            'top' => $top,
        ]);
    }

}
