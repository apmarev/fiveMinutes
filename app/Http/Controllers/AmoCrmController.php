<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomApiException;
use App\Models\Lead;
use App\Models\LeadCustom;
use App\Models\Message;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Telegram\Bot\Laravel\Facades\Telegram;

class AmoCrmController extends Controller {

    protected $__access;

    public function __construct(AccessController $__access) {
        $this->__access = $__access;
    }

    public function createLead($title, $pipelineID, $statusID, $custom) {
        return $this->amoPost('/leads', [
            [
                'name' => $title,
                'pipeline_id' => $pipelineID,
                'status_id' => $statusID,
                'custom_fields_values' => $custom,
            ]
        ]);
    }

    public function getOutgoingMessages() {
        $chats = Message::all();

        foreach($chats as $chat) {
            if($chat['timeUpdate'] - 5 > $chat['time']) {
                // Значит было именно исходящее
                $this->closeTalk($chat['talkId']);
            } else {
                // Значит исходящего не было
            }
            $chat->delete();
        }

        return "Ok";
    }

    public function closeTalk($talkId) {
        return $this->amoPost("/talks/{$talkId}/close", [
            "force_close" => true
        ]);
    }

    public function updateChat(Request $request) {

        if($request->has('talk') && isset($request->input('talk')['update'])) {
            $chatId = $request->input('talk')['update'][0]['chat_id'];
            $talkId = $request->input('talk')['update'][0]['talk_id'];

            if($message = Message::where('chatId', $chatId)->first()) {
                if(time() - 5 > $message['time']) {
                    $this->closeTalk($talkId);
                    $message->delete();
                }
//                $message->__set('timeUpdate', time());
//                $message->save();
            }
        }

        return "Ok";
    }

    public function incoming(Request $request) {

        if($request->has('message') && isset($request->input('message')['add'])) {
            $chatId = $request->input('message')['add'][0]['chat_id'];
            $talkId = $request->input('message')['add'][0]['talk_id'];

            if($message = Message::where('chatId', $chatId)->first()) {
                $message->__set('talkId', $talkId);
                $message->__set('time', time());
            } else {
                $message = new Message();
                $message->__set('talkId', $talkId);
                $message->__set('chatId', $chatId);
                $message->__set('time', time());
            }

            $message->save();

//            Telegram::sendMessage([
//                'chat_id' => '228519769',
//                'text' => json_encode($message)
//            ]);

        }

        return "Ok";

//        Telegram::sendMessage([
//            'chat_id' => '228519769',
//            'text' => json_encode($request->all())
//        ]);
//
//        return "Ok";
    }

    public function changeDialog(Request $request) {

        Telegram::sendMessage([
            'chat_id' => '228519769',
            'text' => json_encode($request->all())
        ]);

        return "Ok";
    }

    public static function getIsSetList($data, string $type): array {
        if(isset($data['_embedded']) && isset($data['_embedded'][$type]) && is_array($data['_embedded'][$type]) && sizeof($data['_embedded'][$type]) > 0) {
            return $data['_embedded'][$type];
        }
        return [];
    }

    public static function getIsSetListCustomFields($data): array {
        if(isset($data['custom_fields_values']) && is_array($data['custom_fields_values']) && sizeof($data['custom_fields_values']) > 0) {
            return $data['custom_fields_values'];
        }
        return [];
    }

    public function getAllNotesByType() {
        $result = [];

        for($i=1;;$i++) {
            $query = "/companies/notes?page={$i}&limit=250&filter[note_type]=common&filter[updated_at][from]=1649980861";
            $res = $this->amoGet($query);
            $list = self::getIsSetList($res, 'notes');

            if(sizeof($list) > 0) {
                $result = array_merge($result, $list);
                unset($list);
            } else
                break;
        }

        return $result;
    }

    public function getAllListByFilter(string $type, string $filter) {
        $result = [];

        for($i=1;;$i++) {
            $query = "/{$type}?page={$i}&limit=250{$filter}";
            $res = $this->amoGet($query);
            $list = self::getIsSetList($res, $type);
            if(sizeof($list) > 0) {
                $result = array_merge($result, $list);
                unset($list);
            } else
                break;
        }

        return $result;
    }

    public function getLeadByID($leadID) {
        $path = "/leads/{$leadID}?with=contacts,companies";
        return $this->amoGet($path);
    }

    public function getCompanyByID($companyID) {
        $path = "/companies/{$companyID}";
        return $this->amoGet($path);
    }

    /**
     * Добавление текстового примечания к сделке
     *
     * @param $leadID
     * @param $description
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response|\Illuminate\Http\JsonResponse
     */
    public function setDescriptionToLead($leadID, $description) {
        $path = "/leads/{$leadID}/notes";
        return $this->amoPost($path, [[
            'note_type' => 'common',
            'params' => [ 'text' => $description ]
        ]]);
    }

    /**
     * Добавление текстового примечания к компании
     *
     * @param $leadID
     * @param $description
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response|\Illuminate\Http\JsonResponse
     */
    public function setDescriptionToCompany($companyID, $description) {
        $path = "/companies/{$companyID}/notes";
        return $this->amoPost($path, [[
            'note_type' => 'common',
            'params' => [ 'text' => $description ]
        ]]);
    }

    /**
     * Связь контакта с лидом
     *
     * @param $contactID
     * @param $leadID
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response|\Illuminate\Http\JsonResponse
     */
    public function associateLeadWithContact($contactID, $leadID) {
        return $this->amoPost("/leads/{$leadID}/link", [[
            'to_entity_id' => $contactID,
            'to_entity_type' => 'contacts'
        ]]);
    }

    /**
     * Связь компании с лидом
     *
     * @param $companyID
     * @param $leadID
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response|\Illuminate\Http\JsonResponse
     */
    public function associateLeadWithCompany($companyID, $leadID) {
        return $this->amoPost("/leads/{$leadID}/link", [[
            'to_entity_id' => $companyID,
            'to_entity_type' => 'companies'
        ]]);
    }

    public function amoGet($path) {
        try {
            $amo = $this->amoGetStatusAccess();
            return Http::withHeaders([
                "Authorization" => "Bearer {$amo['access']}",
                "Content-Type" => "application/json",
            ])->get("https://" . config('app.services.amo.subdomain') . ".amocrm.ru/api/v4{$path}");
        } catch (\Exception $e) {
            return CustomApiException::error(500, $e->getMessage());
        }
    }

    public function amoPost($path, $data) {
        try {
            $amo = $this->amoGetStatusAccess();
            return Http::withHeaders([
                "Authorization" => "Bearer {$amo['access']}",
                "Content-Type" => "application/json",
            ])->post("https://" . config('app.services.amo.subdomain') . ".amocrm.ru/api/v4{$path}", $data);
        } catch (\Exception $e) {
            return CustomApiException::error(500, $e->getMessage());
        }
    }

    public function amoPut($path, $data) {
        try {
            $amo = $this->amoGetStatusAccess();
            return Http::withHeaders([
                "Authorization" => "Bearer {$amo['access']}",
                "Content-Type" => "application/json",
            ])->patch("https://" . config('app.services.amo.subdomain') . ".amocrm.ru/api/v4{$path}", $data);
        } catch (\Exception $e) {
            return CustomApiException::error(500, $e->getMessage());
        }
    }

    public function amoNewAccess(Request $request) {
        if(
            !$request->input('key') ||
            !$request->input('description') ||
            !$request->input('secret') ||
            !$request->input('client')
        )
            return CustomApiException::error(400);

        try {
            $response = $this->amoPostNewAccessAndRefresh($request->input('key'), $request->input('client'), $request->input('secret'));

            return $this->__access->create([
                'name'          => 'amo',
                'description'   => $request->input('description'),
                'secret'        => $request->input('secret'),
                'client'        => $request->input('client'),
                'access'        => $response['access_token'],
                'refresh'       => $response['refresh_token'],
                'expires'       => time() + $response['expires_in'],
            ]);
        } catch (\Exception $e) {
            return CustomApiException::error(500, $e->getMessage());
        }
    }

    public function amoPostNewAccessAndRefresh($code, $client, $secret) {
        $link = 'https://' . config('app.services.amo.subdomain') . '.amocrm.ru/oauth2/access_token';

        try {
            return Http::post($link, [
                'client_id' => $client,
                'client_secret' => $secret,
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('app.services.amo.domain'),
            ]);
        } catch (\Exception $e) {
            return CustomApiException::error(500, $e->getMessage());
        }
    }

    public function amoGetStatusAccess() {
        $amoID = 1;

        try {
            $access = $this->__access->getAccessByID($amoID);
            if(time() >= $access->expires) {
                return $this->newAccessTokenByRefreshToken($access);
            } else {
                return $access;
            }
        } catch (\Exception $e) {
            return CustomApiException::error(404);
        }
    }

    public function newAccessTokenByRefreshToken($service) {
        try {
            $result = Http::asForm()->post("https://" . config('app.services.amo.subdomain') . ".amocrm.ru/oauth2/access_token", [
                'client_id' => $service['client'],
                'client_secret' => $service['secret'],
                'grant_type' => 'refresh_token',
                'refresh_token' => $service['refresh'],
                'redirect_uri' => config('app.services.amo.domain'),
            ]);

            if(isset($result['access_token'])) {
                try {
                    $access = $this->__access->getAccessByID($service['id']);
                    $access->__set('access', $result['access_token']);
                    $access->__set('refresh', $result['refresh_token']);
                    $access->__set('expires', time() + $result['expires_in']);
                    $access->save();

                    return $access;
                } catch (\Exception $e) {
                    return CustomApiException::error(500, $e->getMessage());
                }
            } else {
                return CustomApiException::error(500);
            }
        } catch (\Exception $e) {
            return CustomApiException::error(500, $e->getMessage());
        }
    }

    public function getOrCreateContact($name = 'test', $phone = '71231231231') {
        $search = $this->searchContactByPhone($phone);
        if($search > 0) return $search;
        else {
            $path = "/contacts";
            $result = $this->amoPost($path, [
                [
                    'name' => $name,
                    'custom_fields_values' => [
                        [
                            'field_id' => 741295,
                            'values' => [
                                [ 'value' => $phone ]
                            ],
                        ]
                    ]
                ]
            ]);
            $list = $this->getIsSetList($result, 'contacts');
            if($list[0]['id'] > 0) {
                return $list[0]['id'];
            } else {
                return 0;
            }
        }
    }

    public function searchContactByPhone($phone) {
        $path = "/contacts?query={$phone}";
        $result = $this->amoGet($path);
        $list = $this->getIsSetList($result, 'contacts');

        foreach($list as $el) {
            $custom = $this->getIsSetListCustomFields($el);
            foreach($custom as $c) {
                if($c['field_id'] == 741295) {
                    foreach($c['values'] as $value) {
                        if(strripos($value['value'], strval($phone))) {
                            return $el['id'];
                        }
                    }
                }
            }
        }

        return 0;
    }

    /**
     * Получение пользователей в БД
     */
    public function getAndSetUsers() {
        $userGroupId = 385195; // Группа пользователей "Колл-центр"
        $list = $this->getAllListByFilter('users', "&with=group");
        foreach($list as $user) {
            if($user['rights']['is_active'] && isset($user['rights']) && isset($user['rights']['group_id']) && $user['rights']['group_id'] == $userGroupId) {
                $el = new User();
                $el->__set('userId', $user['id']);
                $el->__set('name', $user['name']);
                $el->save();
            }
        }
        return "Ok";
    }

    /**
     * Очистка таблицы пользователей
     */
    public function clearUserTable() {
        User::truncate();
        return "Ok";
    }

    public function clearLeadsTables() {
        Lead::truncate();
        LeadCustom::truncate();
        return "Ok";
    }

    public function getAndSetLeads(array $date) {
        $pipelines = [
            3493222, // Первичные КЦ
            5084302 // Продление КЦ
        ];

        $array = [];

        foreach($pipelines as $pipeline) {
            $list = $this->getAllListByFilter('leads', "&filter[pipeline_id]={$pipeline}&filter[created_at][from]={$date[0]}&filter[created_at][to]={$date[1]}");
            $array = array_merge($array, $list);
        }

        foreach($array as $lead) {
            $el = new Lead();
            $el->__set('leadId', $lead['id']);
            $el->__set('price', $lead['price']);
            $el->__set('userId', $lead['responsible_user_id']);
            $el->__set('statusId', $lead['status_id']);
            $el->__set('pipelineId', $lead['pipeline_id']);
            $el->__set('createdAt', $lead['created_at']);
            $el->save();

            foreach($this->getIsSetListCustomFields($lead) as $custom) {
                if(
                    $custom['field_id'] == 709405 || // Продукт
                    $custom['field_id'] == 708651 // Пакет
                ) {
                    $element = new LeadCustom();
                    $element->__set('leadId', $lead['id']);
                    $element->__set('fieldId', $custom['field_id']);
                    $element->__set('value', $custom['values'][0]['value']);
                    $element->__set('enum', $custom['values'][0]['enum_id']);
                    $element->save();
                }
            }
        }

        return $array;
    }

    public function start(Request $request) {
        $yesterday = strtotime("-1 day");
        $yesterdayStart = strtotime(date('d.m.Y', $yesterday) . " 00:00:01");
        $yesterdayEnd = strtotime(date('d.m.Y', $yesterday) . " 23:59:59");

        if($request->has('date')) {
            $yesterdayStart = strtotime("{$request->input('date')} 00:00:01");
            $yesterdayEnd = strtotime("{$request->input('date')} 23:59:59");
        }

        $arrayDates = [
            '01.05.2022',
            '02.05.2022',
            '03.05.2022',
            '04.05.2022',
            '05.05.2022',
            '06.05.2022',
            '07.05.2022',
            '08.05.2022',
            '09.05.2022',
            '10.05.2022',
            '11.05.2022',
            '12.05.2022',
            '13.05.2022',
            '14.05.2022',
            '15.05.2022',
            '16.05.2022',
            '17.05.2022',
            '18.05.2022',
            '19.05.2022',
            '20.05.2022',
            '21.05.2022',
            '22.05.2022',
            '23.05.2022',
            '24.05.2022',
        ];

        $this->getAndSetUsers();

        foreach($arrayDates as $d) {
            $yesterdayStart = strtotime("{$d} 00:00:01");
            $yesterdayEnd = strtotime("{$d} 23:59:59");

            $day = date('j', $yesterdayStart);
            $month = date('m', $yesterdayStart);
            $year = date('Y', $yesterdayStart);

            $monthName = "";

            if($month == '01') $monthName = 'Январь';
            else if($month == '02') $monthName = 'Февраль';
            else if($month == '03') $monthName = 'Март';
            else if($month == '04') $monthName = 'Апрель';
            else if($month == '05') $monthName = 'Май';
            else if($month == '06') $monthName = 'Июнь';
            else if($month == '07') $monthName = 'Июль';
            else if($month == '08') $monthName = 'Август';
            else if($month == '09') $monthName = 'Сентябрь';
            else if($month == '10') $monthName = 'Октябрь';
            else if($month == '11') $monthName = 'Ноябрь';
            else if($month == '12') $monthName = 'Декабрь';

            $dateArray = [
                'day' => $day,
                'month' => $month,
                'monthName' => $monthName,
                'year' => $year,
            ];


            $this->getAndSetLeads([$yesterdayStart, $yesterdayEnd]);
            $this->getInfoByManager($dateArray);

            $this->clearLeadsTables();
        }

        $this->clearUserTable();


        return "ok";
    }

    public function getInfoByManager($dateArray) {
        $pipelines = [
            3493222, // Первичные КЦ
            5084302 // Продление КЦ
        ];
        $users = User::all();

        foreach($pipelines as $pipeline) {
            $array = [];

            foreach($users as $user) {
                $item = [
                    'all' => 0, // Все новые
                    'monthExam' => 0, // МГ Месяц ЕГЭ
                    'monthOge' => 0, // МГ Месяц ОГЭ
                    'monthTenClass' => 0, // МГ Месяц 10 класс
                    'packageExam' => 0, // МГ пакет ЕГЭ
                    'packageOge' => 0, // МГ пакет ОГЭ
                    'packageTenClass' => 0, // МГ пакет 10 класс
                    'countPackagesExam' => 0, // Продано месяцев ЕГЭ
                    'countPackagesOge' => 0, // Продано месяцев ОГЭ
                    'countPackagesTenClass' => 0, // Продано месяцев 10 класс,
                    'countPriceMonth' => 0, // Сумма продаж МГ Месяц
                    'countPricePackage' => 0, // Сумма продаж МГ пакет
                    'countMonth' => 0, // Количество покупок МГ Месяц
                    'countPackage' => 0, // Количество покупок МГ пакет
                    'averageCheck' => 0, // Средний чек
                    'conversion' => 0, // Конверсия
                ];

                $item['all'] = Lead::where('userId', $user['userId'])->where('pipelineId', $pipeline)->count();

                $completed = Lead::where('userId', $user['userId'])->where('pipelineId', $pipeline)->where('statusId', 142)->get();
                foreach($completed as $complete) {
                    $status = false;
                    $package = 0;

                    $customPackage = LeadCustom::where('leadId', $complete['leadId'])->where('fieldId', 708651)->first();
                    if($customPackage['enum'] == 430903) {
                        // Это значит пакет 1
                        $package = 1;
                    } else {
                        // Пакет больше 1
                        $package = intval($customPackage['value']);
                    }

                    $customProduct = LeadCustom::where('leadId', $complete['leadId'])->where('fieldId', 709405)->first();

                    if($customProduct) {
                        if($package > 1) {
                            if(
                                $customProduct['enum'] == 431967 || // МГ - ЕГЭ
                                $customProduct['enum'] == 431969 || // МГ - ОГЭ
                                $customProduct['enum'] == 431971 // МГ - 10 класс
                            ) {
                                $status = true;

                                if($customProduct['enum'] == 431967) {
                                    $item['packageExam'] = $item['packageExam'] + $complete['price'];
                                    $item['countPackagesExam'] = $item['countPackagesExam'] + $package;
                                } else if($customProduct['enum'] == 431969) {
                                    $item['packageOge'] = $item['packageOge'] + $complete['price'];
                                    $item['countPackagesOge'] = $item['countPackagesOge'] + $package;
                                } else if($customProduct['enum'] == 431971) {
                                    $item['packageTenClass'] = $item['packageTenClass'] + $complete['price'];
                                    $item['countPackagesTenClass'] = $item['countPackagesTenClass'] + $package;
                                }
                            }
                        } else if($package == 1) {
                            if($customProduct['enum']) {
                                if(
                                    $customProduct['enum'] == 431967 || // МГ - ЕГЭ
                                    $customProduct['enum'] == 431973 || // Предбанник - ЕГЭ
                                    $customProduct['enum'] == 431979 || // Курс ФЛЕШ - ЕГЭ
                                    $customProduct['enum'] == 431985 || // Игра "Рулетка" - ЕГЭ
                                    $customProduct['enum'] == 432689 || // Индивидуальные занятия с репетитором - ЕГЭ
                                    $customProduct['enum'] == 432701 || // Курс по основам - ЕГЭ
                                    $customProduct['enum'] == 432709 // База заданий - ЕГЭ
                                ) {
                                    $item['countMonth'] = $item['countMonth'] + 1;
                                    $item['monthExam'] = $item['monthExam'] + $complete['price'];
                                } else if(
                                    $customProduct['enum'] == 431969 || // МГ - ОГЭ
                                    $customProduct['enum'] == 431975 || // Предбанник - ОГЭ
                                    $customProduct['enum'] == 431981 || // Курс ФЛЕШ - ОГЭ
                                    $customProduct['enum'] == 431983 || // Игра "Рулетка" - ОГЭ
                                    $customProduct['enum'] == 432691 || // Индивидуальные занятия с репетитором - ОГЭ
                                    $customProduct['enum'] == 432703 || // Курс по основам - ОГЭ
                                    $customProduct['enum'] == 432707 // База заданий - ОГЭ
                                ) {
                                    $item['countMonth'] = $item['countMonth'] + 1;
                                    $item['monthOge'] = $item['monthOge'] + $complete['price'];
                                } else if(
                                    $customProduct['enum'] == 431971 || // МГ - 10 класс
                                    $customProduct['enum'] == 431977 || // Курс ФЛЕШ - 10 класс
                                    $customProduct['enum'] == 432693 // Индивидуальные занятия с репетитором - 10 класс
                                ) {
                                    $item['monthTenClass'] = $item['monthTenClass'] + $complete['price'];
                                }
                            }
                        }
                    }
                }

                $item['countPriceMonth'] = $item['monthExam'] + $item['monthOge'];
                $item['countPricePackage'] = $item['packageExam'] + $item['packageOge'];

                $countBye = $item['countMonth'] + $item['countPackage'];

                if($countBye > 0) $item['averageCheck'] = ($item['countPriceMonth'] + $item['countPricePackage']) / $countBye;
                if($countBye > 0) $item['conversion'] = $item['all'] / $countBye;

                $array[] = [
                    'manager' => $user['name'],
                    'items' => $item
                ];
            }

            foreach($array as $a) {
                $report = new Report();

                $report->__set('manager', $a['manager']);
                $report->__set('monthName', $dateArray['monthName']);
                $report->__set('day', $dateArray['day']);
                $report->__set('month', $dateArray['month']);
                $report->__set('year', $dateArray['year']);
                $report->__set('pipelineId', $pipeline);
                $report->__set('all', $a['items']['all']);
                $report->__set('monthExam', $a['items']['monthExam']);
                $report->__set('monthOge', $a['items']['monthOge']);
                $report->__set('monthTenClass', $a['items']['monthTenClass']);
                $report->__set('packageExam', $a['items']['packageExam']);
                $report->__set('packageOge', $a['items']['packageOge']);
                $report->__set('packageTenClass', $a['items']['packageTenClass']);
                $report->__set('countPackagesExam', $a['items']['countPackagesExam']);
                $report->__set('countPackagesOge', $a['items']['countPackagesOge']);
                $report->__set('countPackagesTenClass', $a['items']['countPackagesTenClass']);
                $report->__set('countPriceMonth', $a['items']['countPriceMonth']);
                $report->__set('countPricePackage', $a['items']['countPricePackage']);
                $report->__set('countMonth', $a['items']['countMonth']);
                $report->__set('countPackage', $a['items']['countPackage']);
                $report->__set('averageCheck', $a['items']['averageCheck']);
                $report->__set('conversion', $a['items']['conversion']);
                $report->save();
            }
        }

        return "ok";
    }

    public static function getToDesktop($pipelineId = 3493222, $date = []) {
        if(sizeof($date) == 0) $date = ['05', 2022];

        $users = Report::where('month', $date[0])
            ->where('year', $date[1])
            ->where('pipelineId', $pipelineId)
            ->get();

        $array = [];

        $countDays = date('t', mktime(0, 0, 0, $date[0], 1, $date[1]));

        foreach($users as $user) {
            $array[$user['manager']][] = $user;
        }

        $return = [];
        foreach($array as $k => $v) {
            $element = [
                'name' => $k,
                'days' => []
            ];
            for($i=1;$i<=$countDays;$i++) {
                $element['days'][$i] = [
                    "all" => 0,
                    "monthExam" => 0,
                    "monthOge" => 0,
                    "monthTenClass" => 0,
                    "packageExam" => 0,
                    "packageOge" => 0,
                    "packageTenClass" => 0,
                    "countPackagesExam" => 0,
                    "countPackagesOge" => 0,
                    "countPackagesTenClass" => 0,
                    "countPriceMonth" => 0,
                    "countPricePackage" => 0,
                    "countMonth" => 0,
                    "countPackage" => 0,
                    "averageCheck" => 0,
                    "conversion" => 0,
                ];
            }

            foreach($v as $value) {
                $element['days'][$value['day']] = [
                    "all" => $value['all'],
                    "monthExam" => $value['monthExam'],
                    "monthOge" => $value['monthOge'],
                    "monthTenClass" => $value['monthTenClass'],
                    "packageExam" => $value['packageExam'],
                    "packageOge" => $value['packageOge'],
                    "packageTenClass" => $value['packageTenClass'],
                    "countPackagesExam" => $value['countPackagesExam'],
                    "countPackagesOge" => $value['countPackagesOge'],
                    "countPackagesTenClass" => $value['countPackagesTenClass'],
                    "countPriceMonth" => $value['countPriceMonth'],
                    "countPricePackage" => $value['countPricePackage'],
                    "countMonth" => $value['countMonth'],
                    "countPackage" => $value['countPackage'],
                    "averageCheck" => $value['averageCheck'],
                    "conversion" => $value['conversion'],
                ];
            }
            $return[] = $element;
        }

        return $return;
    }

    public static function getMonths() {
        return DB::table('reports')
            ->select(['month', 'monthName'])
            ->groupBy(['month', 'monthName'])
            ->get();
    }

    public static function getYears() {
        return DB::table('reports')
            ->select(['year'])
            ->groupBy(['year'])
            ->get();
    }

    public static function getMonthAndYears() {
        return [
            'years' => self::getYears(),
            'months' => self::getMonths()
        ];
    }

    public function test() {
        return $this->amoGet('/leads?limit=1');
    }
}
