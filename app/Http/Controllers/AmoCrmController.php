<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomApiException;
use App\Models\Lead;
use App\Models\LeadCount;
use App\Models\LeadCustom;
use App\Models\Message;
use App\Models\Report;
use App\Models\ReportCustom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Telegram\Bot\Laravel\Facades\Telegram;

class AmoCrmController extends Controller {

    protected AccessController $__access;
    protected static $weekends = [];
    protected static $plans = [];

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

            Telegram::sendMessage([
                'chat_id' => '228519769',
                'text' => json_encode($request->input('talk'))
            ]);

            if($message = Message::where('chatId', $chatId)->first()) {
                $time = time() - 2;

                if($time > $message['time']) {
                    $close = $this->closeTalk($talkId);
                        Telegram::sendMessage([
                            'chat_id' => '228519769',
                            'text' => "Закрыт: {$talkId}"
                        ]);
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
        $data = [];
        foreach($list as $user) {
            if($user['rights']['is_active'] && isset($user['rights']) && isset($user['rights']['group_id']) && $user['rights']['group_id'] == $userGroupId) {

                $data[] = [
                    'userId' => $user['id'],
                    'name' => $user['name']
                ];
            }
        }

        return User::insert($data);
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

    public function clearLeadsCountTables() {
        LeadCount::truncate();
        return "Ok";
    }

    public function getAndSetLeadsCount(array $date) {
        $pipelines = [
            3493222, // Первичные КЦ
            5084302 // Продление КЦ
        ];

        $array = [];

        foreach($pipelines as $pipeline) {
            $list = $this->getAllListByFilter('leads', "&filter[pipeline_id]={$pipeline}&filter[created_at][from]={$date[0]}&filter[created_at][to]={$date[1]}");
            $array = array_merge($array, $list);
        }

        $data = [];
        foreach($array as $lead) {
            $data[] = [
                'userId' => $lead['responsible_user_id'],
                'pipelineId' => $lead['pipeline_id']
            ];
        }

        LeadCount::insert($data);

        return $array;
    }

    public function getAndSetLeads(array $date) {
        $pipelines = [
            3493222, // Первичные КЦ
            5084302 // Продление КЦ
        ];

        $array = [];

        foreach($pipelines as $pipeline) {
            $list = $this->getAllListByFilter('leads', "&filter[statuses][0][pipeline_id]={$pipeline}&filter[statuses][0][status_id]=142&filter[closed_at][from]={$date[0]}&filter[closed_at][to]={$date[1]}");
            $array = array_merge($array, $list);
        }

        $data = [];
        foreach($array as $lead) {
            $data[] = [
                'leadId' => $lead['id'],
                'price' => $lead['price'],
                'userId' => $lead['responsible_user_id'],
                'statusId' => $lead['status_id'],
                'pipelineId' => $lead['pipeline_id'],
                'createdAt' => $lead['created_at']
            ];

            $facts = [];
            foreach($this->getIsSetListCustomFields($lead) as $custom) {
                if(
                    $custom['field_id'] == 709405 || // Продукт
                    $custom['field_id'] == 708651 // Пакет
                ) {
                    $facts[] = [
                        'leadId' => $lead['id'],
                        'fieldId' => $custom['field_id'],
                        'value' => $custom['values'][0]['value'],
                        'enum' => $custom['values'][0]['enum_id']
                    ];
                }
            }

            LeadCustom::insert($facts);
        }

        Lead::insert($data);

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
//            '08.05.2022',
//            '09.05.2022',
//            '10.05.2022',
//            '11.05.2022',
//            '12.05.2022',
//            '13.05.2022',
//            '14.05.2022',
//            '15.05.2022',
//            '16.05.2022',
//            '17.05.2022',
//            '18.05.2022',
//            '19.05.2022',
//            '20.05.2022',
//            '21.05.2022',
//            '22.05.2022',
//            '23.05.2022',
//            '24.05.2022',
//            '25.05.2022',
//            '26.05.2022',
//            '27.05.2022',
//            '28.05.2022',
//            '29.05.2022',
//            '30.05.2022',
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

            $this->getAndSetLeadsCount([$yesterdayStart, $yesterdayEnd]);
            $this->getAndSetLeads([$yesterdayStart, $yesterdayEnd]);
            $this->getInfoByManager($dateArray);

            $this->clearLeadsTables();
            $this->clearLeadsCountTables();
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

            $allFromUsers = LeadCount::where('pipelineId', $pipeline)->get();
            $fromCompleted = Lead::where('pipelineId', $pipeline)->get();

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
                    'countTen' => 0, // Количество покупок 10 класс
                    'averageCheck' => 0, // Средний чек
                    'conversion' => 0, // Конверсия
                ];

                foreach($allFromUsers as $f)
                    if($f['userId'] == $user['userId']) $item['all'] = $item['all'] + 1;

                $completed = [];
                foreach($fromCompleted as $f)
                    if($f['userId'] == $user['userId']) $completed[] = $f;

                foreach($completed as $complete) {
                    $status = false;
                    $package = 0;

                    $customPackage = LeadCustom::where('leadId', $complete['leadId'])->where('fieldId', 708651)->first();

                    if($customPackage) {
                        if($customPackage['enum'] == 430903) {
                            // Это значит пакет 1
                            $package = 1;
                        } else {
                            // Пакет больше 1
                            $num = $customPackage['value'];
                            $num = str_replace(',', '.', $num);
                            $package = floatval($num);
                        }

                        $customProduct = LeadCustom::where('leadId', $complete['leadId'])->where('fieldId', 709405)->first();

                        if($customProduct) {
                            if($package > 1) {
                                if(
                                    $customProduct['enum']
                                ) {
                                    $status = true;

                                    if(
                                        $customProduct['enum'] == 431967 || // МГ - ЕГЭ
                                        $customProduct['enum'] == 431973 || // Предбанник - ЕГЭ
                                        $customProduct['enum'] == 431979 || // Курс ФЛЕШ - ЕГЭ
                                        $customProduct['enum'] == 431985 || // Игра "Рулетка" - ЕГЭ
                                        $customProduct['enum'] == 432689 || // Индивидуальные занятия с репетитором - ЕГЭ
                                        $customProduct['enum'] == 432701 || // Курс по основам - ЕГЭ
                                        $customProduct['enum'] == 432709 // База заданий - ЕГЭ
                                    ) {
                                        $item['packageExam'] = $item['packageExam'] + $complete['price'];
                                        $item['countPackagesExam'] = $item['countPackagesExam'] + $package;
                                    } else if(
                                        $customProduct['enum'] == 431969 || // МГ - ОГЭ
                                        $customProduct['enum'] == 431975 || // Предбанник - ОГЭ
                                        $customProduct['enum'] == 431981 || // Курс ФЛЕШ - ОГЭ
                                        $customProduct['enum'] == 431983 || // Игра "Рулетка" - ОГЭ
                                        $customProduct['enum'] == 432691 || // Индивидуальные занятия с репетитором - ОГЭ
                                        $customProduct['enum'] == 432703 || // Курс по основам - ОГЭ
                                        $customProduct['enum'] == 432707 // База заданий - ОГЭ
                                    ) {
                                        $item['packageOge'] = $item['packageOge'] + $complete['price'];
                                        $item['countPackagesOge'] = $item['countPackagesOge'] + $package;
                                    } else if(
                                        $customProduct['enum'] == 431971 || // МГ - 10 класс
                                        $customProduct['enum'] == 431977 || // Курс ФЛЕШ - 10 класс
                                        $customProduct['enum'] == 432693 // Индивидуальные занятия с репетитором - 10 класс
                                    ) {
                                        $item['packageTenClass'] = $item['packageTenClass'] + $complete['price'];
                                        $item['countPackagesTenClass'] = $item['countPackagesTenClass'] + $package;
                                    }
                                    $item['countPackage'] = $item['countPackage'] + 1;
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
                                        $item['countTen'] = $item['countTen'] + 1;
                                        $item['countMonth'] = $item['countMonth'] + 1;
                                        $item['monthTenClass'] = $item['monthTenClass'] + $complete['price'];
                                    }
                                }
                            }
                        }
                    }
                }

                $item['countPriceMonth'] = $item['monthExam'] + $item['monthOge'] + $item['monthTenClass'];
                $item['countPricePackage'] = $item['packageExam'] + $item['packageOge'] + $item['packageTenClass'];

                $countBye = $item['countMonth'] + $item['countPackage'] + $item['countTen'];

                if($countBye > 0) $item['averageCheck'] = ($item['countPriceMonth'] + $item['countPricePackage']) / $countBye;
                if($countBye > 0) $item['conversion'] = $item['all'] / $countBye;

                $array[] = [
                    'manager' => $user['name'],
                    'items' => $item
                ];
            }

            $data = [];
            foreach($array as $a) {
                $data[] = [
                    'manager' => $a['manager'],
                    'monthName' => $dateArray['monthName'],
                    'day' => $dateArray['day'],
                    'month' => $dateArray['month'],
                    'year' => $dateArray['year'],
                    'pipelineId' => $pipeline,
                    'all' => $a['items']['all'],
                    'monthExam' => $a['items']['monthExam'],
                    'monthOge' => $a['items']['monthOge'],
                    'monthTenClass' => $a['items']['monthTenClass'],
                    'packageExam' => $a['items']['packageExam'],
                    'packageOge' => $a['items']['packageOge'],
                    'packageTenClass' => $a['items']['packageTenClass'],
                    'countPackagesExam' => $a['items']['countPackagesExam'],
                    'countPackagesOge' => $a['items']['countPackagesOge'],
                    'countPackagesTenClass' => $a['items']['countPackagesTenClass'],
                    'countPriceMonth' => $a['items']['countPriceMonth'],
                    'countPricePackage' => $a['items']['countPricePackage'],
                    'countMonth' => $a['items']['countMonth'],
                    'countPackage' => $a['items']['countPackage'],
                    'countTen' => $a['items']['countTen'],
                    'averageCheck' => $a['items']['averageCheck'],
                    'conversion' => $a['items']['conversion'],
                ];
            }

            Report::insert($data);
        }

        return "ok";
    }

    protected static function clearWeek(): array {
        return [
            'all' => [ 'title' => 'Кол-во лидов', 'value' => 0 ],
            'monthExam' => [ 'title' => 'МГ Месяц ЕГЭ', 'value' => 0 ],
            'monthOge' => [ 'title' => 'МГ Месяц ОГЭ', 'value' => 0 ],
            'monthTenClass' => [ 'title' => 'МГ Месяц 10 класс', 'value' => 0 ],
            'packageExam' => [ 'title' => 'МГ пакет ЕГЭ', 'value' => 0 ],
            'packageOge' => [ 'title' => 'МГ пакет ОГЭ', 'value' => 0 ],
            'packageTenClass' => [ 'title' => 'МГ пакет 10 класс', 'value' => 0 ],
            'countPackagesExam' => [ 'title' => 'Продано месяцев ЕГЭ', 'value' => 0 ],
            'countPackagesOge' => [ 'title' => 'Продано месяцев ОГЭ', 'value' => 0 ],
            'countPackagesTenClass' => [ 'title' => 'Продано месяцев 10 класс', 'value' => 0 ],
            'countPriceMonth' => [ 'title' => 'Сумма продаж МГ Месяц', 'value' => 0 ],
            'countPricePackage' => [ 'title' => 'Сумма продаж МГ пакет', 'value' => 0 ],
            'countMonth' => [ 'title' => 'Количество покупок МГ Месяц', 'value' => 0 ],
            'countPackage' => [ 'title' => 'Количество покупок МГ пакет', 'value' => 0 ],
            'countTen' => [ 'title' => 'Количество покупок 10 класс', 'value' => 0 ],
            'sumPrice' => [ 'title' => 'Сумма продаж общая', 'value' => 0 ],
            'countBuy' => [ 'title' => 'Кол-во продаж общее', 'value' => 0 ],
            'averageCheck' => [ 'title' => 'Средний чек', 'value' => 0 ],
            'conversion' => [ 'title' => 'Конверсия', 'value' => 0 ],
            'plan' => [ 'title' => 'План', 'value' => 0, 'type' => 'input' ],
            'planPercent' => [ 'title' => '% выполнения плана на текущий день', 'value' => 0 ],
            'planRemainder' => [ 'title' => 'Остаток по плану', 'value' => 0 ],
            'weekend' => [ 'title' => 'Выходные', 'value' => 0, 'type' => 'switch' ],
        ];

        // return ["all" => 0, "monthExam" => 0, "monthOge" => 0, "monthTenClass" => 0, "packageExam" => 0, "packageOge" => 0, "packageTenClass" => 0, "countPackagesExam" => 0, "countPackagesOge" => 0, "countPackagesTenClass" => 0, "countPriceMonth" => 0, "countPricePackage" => 0, "countMonth" => 0, "countPackage" => 0];
    }

    protected static function clearDay($date): array {
        return ['date' => $date, 'value' => ["all" => 0, "monthExam" => 0, "monthOge" => 0, "monthTenClass" => 0, "packageExam" => 0, "packageOge" => 0, "packageTenClass" => 0, "countPackagesExam" => 0, "countPackagesOge" => 0, "countPackagesTenClass" => 0, "countPriceMonth" => 0, "countPricePackage" => 0, "countMonth" => 0, "countPackage" => 0, "countTen" => 0]];
    }

    protected static function clearSumByManager(): array {
        return ["all" => 0, "monthExam" => 0, "monthOge" => 0, "monthTenClass" => 0, "packageExam" => 0, "packageOge" => 0, "packageTenClass" => 0, "countPackagesExam" => 0, "countPackagesOge" => 0, "countPackagesTenClass" => 0, "countPriceMonth" => 0, "countPricePackage" => 0, "countMonth" => 0, "countPackage" => 0, "countTen" => 0];
    }

    protected static function getWeekendDay($data, $day) {
        foreach($data as $d) {
            if($d['day'] == $day)
                return 1;
        }
        return $day;
    }

    protected static function getWeekendsByMonthAndYear($month, $year) {
        self::$weekends = ReportCustom::where('type', 'weekend')->where('year', $year)->where('month', $month)->where('value', '>', 0)->get();
    }

    protected static function searchWeekendDayByUser(string $user, int $day): int {
        foreach(self::$weekends as $weekend) {
            if($weekend['manager'] == $user && $weekend['day'] == $day)
                return 1;
        }
        return 0;
    }

    protected static function getCountWeekendsMonth(string $user): int {
        $count = 0;
        foreach(self::$weekends as $weekend) {
            if($weekend['manager'] == $user)
                $count++;
        }
        return $count;
    }

    protected static function getPlanMonth($manager) {
        foreach(self::$plans as $plan)
            if($plan['manager'] == $manager && $plan['type'] == 'plan') return $plan['value'];
        return 0;
    }

    protected static function getSumByManager(array $days, array $date, string $manager) {
        $sum = self::clearSumByManager();

        foreach($days as $dayNumber => $day) {
            if(isset($day['week'])) {
                $sum = [
                    "all" => $sum['all'] + $day['values']['all']['value'],
                    "monthExam" => $sum['monthExam'] + $day['values']['monthExam']['value'],
                    "monthOge" => $sum['monthOge'] + $day['values']['monthOge']['value'],
                    "monthTenClass" => $sum['monthTenClass'] + $day['values']['monthTenClass']['value'],
                    "packageExam" => $sum['packageExam'] + $day['values']['packageExam']['value'],
                    "packageOge" => $sum['packageOge'] + $day['values']['packageOge']['value'],
                    "packageTenClass" => $sum['packageTenClass'] + $day['values']['packageTenClass']['value'],
                    "countPackagesExam" => $sum['countPackagesExam'] + $day['values']['countPackagesExam']['value'],
                    "countPackagesOge" => $sum['countPackagesOge'] + $day['values']['countPackagesOge']['value'],
                    "countPackagesTenClass" => $sum['countPackagesTenClass'] + $day['values']['countPackagesTenClass']['value'],
                    "countPriceMonth" => $sum['countPriceMonth'] + $day['values']['countPriceMonth']['value'],
                    "countPricePackage" => $sum['countPricePackage'] + $day['values']['countPricePackage']['value'],
                    "countMonth" => $sum['countMonth'] + $day['values']['countMonth']['value'],
                    "countPackage" => $sum['countPackage'] + $day['values']['countPackage']['value'],
                    "countTen" => $sum['countTen'] + $day['values']['countTen']['value'],
                ];

                $sum['sumPrice'] = $sum['countPriceMonth'] + $sum['countPricePackage'];
                $sum['countBuy'] = $sum['countMonth'] + $sum['countPackage'];
                $sum['averageCheck'] = $sum['countBuy'] > 0 ? round($sum['sumPrice'] / $sum['countBuy'], 1) : 0;
                $sum['conversion'] = $sum['countBuy'] > 0 ? (round($sum['countBuy'] / $sum['all'], 1) * 100) : 0;
            }
        }

        $sum['weekends'] = self::getCountWeekendsMonth($manager);
        $sum['plan'] = self::getPlanMonth($manager);
        $sum['planPercent'] = $sum['plan'] > 0 ? round(($sum['sumPrice'] / $sum['plan']) * 100, 1) : 0;
        $sum['planRemainder'] = $sum['plan'] - $sum['sumPrice'];

        return $sum;
    }

    protected static function addToWeek(array $week, array $new) {
        foreach($new as $key => $value)
            if(isset($week[$key])) $week[$key]['value'] = $week[$key]['value'] + $value['value'];
        return $week;
    }

    protected static function getWeek(array $week, $plan): array {
        $numberOfPurchases = $week['countMonth']['value'] + $week['countPackage']['value'] + $week['countTen']['value']; // Кол-во покупок
        $week['conversion']['value'] = self::getConversion($numberOfPurchases, $week['all']['value']);
        $week['averageCheck']['value'] = self::getAverageCheck($week['countPriceMonth']['value'] + $week['countPricePackage']['value'], $numberOfPurchases);

        $week['sumPrice']['value'] = $week['countPriceMonth']['value'] + $week['countPricePackage']['value'];
        $week['countBuy']['value'] = $week['countMonth']['value'] + $week['countPackage']['value'];
        $week['planPercent']['value'] = $plan > 0 ? round(($week['sumPrice']['value'] / $plan) * 100, 1) : 0;
        $week['planRemainder']['value'] = $plan - $week['sumPrice']['value'];



        return $week;
    }

    protected static function getConversion($countOne, $countTwo): float|int {
        return $countTwo > 0 ? (round($countOne / $countTwo, 1)) * 100 : 0;
    }

    protected static function getAverageCheck($countOne, $countTwo): float|int {
        return $countTwo > 0 ? round($countOne / $countTwo, 1) : 0;
    }

    protected static function getSumAllManagers(array $managers) {
        $sum = [ 'dayMonth' => 0, 'dayPackage' => 0, 'allDay' => 0, 'allEge' => 0, 'allOge' => 0, 'allTen' => 0, 'packageEge' => 0, 'packageOge' => 0 ];
    }

    protected static function groupDaysToManagers($managers) {
        $users = [];
        foreach($managers as $manager) $users[$manager['manager']][] = $manager;
        return $users;
    }

    protected static function formatDayByUser($number, $date) {
        $day = [
            'title' => "{$number}.{$date[0]}.{$date[1]}",
            'values' => [
                'all' => [ 'title' => 'Кол-во лидов', 'value' => 0 ],
                'monthExam' => [ 'title' => 'МГ Месяц ЕГЭ', 'value' => 0 ],
                'monthOge' => [ 'title' => 'МГ Месяц ОГЭ', 'value' => 0 ],
                'monthTenClass' => [ 'title' => 'МГ Месяц 10 класс', 'value' => 0 ],
                'packageExam' => [ 'title' => 'МГ пакет ЕГЭ', 'value' => 0 ],
                'packageOge' => [ 'title' => 'МГ пакет ОГЭ', 'value' => 0 ],
                'packageTenClass' => [ 'title' => 'МГ пакет 10 класс', 'value' => 0 ],
                'countPackagesExam' => [ 'title' => 'Продано месяцев ЕГЭ', 'value' => 0 ],
                'countPackagesOge' => [ 'title' => 'Продано месяцев ОГЭ', 'value' => 0 ],
                'countPackagesTenClass' => [ 'title' => 'Продано месяцев 10 класс', 'value' => 0 ],
                'countPriceMonth' => [ 'title' => 'Сумма продаж МГ Месяц', 'value' => 0 ],
                'countPricePackage' => [ 'title' => 'Сумма продаж МГ пакет', 'value' => 0 ],
                'countMonth' => [ 'title' => 'Количество покупок МГ Месяц', 'value' => 0 ],
                'countPackage' => [ 'title' => 'Количество покупок МГ пакет', 'value' => 0 ],
                'countTen' => [ 'title' => 'Количество покупок 10 класс', 'value' => 0 ],
                'sumPrice' => [ 'title' => 'Сумма продаж общая', 'value' => 0 ],
                'countBuy' => [ 'title' => 'Кол-во продаж общее', 'value' => 0 ],
                'averageCheck' => [ 'title' => 'Средний чек', 'value' => 0 ],
                'conversion' => [ 'title' => 'Конверсия', 'value' => 0 ],
                'plan' => [ 'title' => 'План', 'value' => 0, 'type' => 'input' ],
                'planPercent' => [ 'title' => '% выполнения плана на текущий день', 'value' => 0 ],
                'planRemainder' => [ 'title' => 'Остаток по плану', 'value' => 0 ],
            ]
        ];

        return $day;
    }

    protected static function formatDaysByUser($countDays, $date) {
        $days = [];
        for($i=1;$i<=$countDays;$i++) $days[$i] = self::formatDayByUser($i, $date);
        return $days;
    }

    protected static function formatByUser($countDays, $date, $name) {
        return [
            'name' => $name,
            'all' => [],
            'days' => self::formatDaysByUser($countDays, $date)
        ];
    }

    public function setOrRemoveWeekend(Request $request) {
        $manager = $request->input('manager');
        $month = $request->input('month');
        $year = $request->input('year');
        $day = $request->input('day');
        if($check = ReportCustom::where('manager', $manager)->where('type', 'weekend')->where('month', $month)->where('year', $year)->where('day', $day)->first()) {
            if($check['value'] > 0) {
                $check->__set('value', 0);
            } else {
                $check->__set('value', 1);
            }
        } else {
            $check = new ReportCustom();
            $check->__set('manager', $manager);
            $check->__set('type', 'weekend');
            $check->__set('day', $day);
            $check->__set('month', $month);
            $check->__set('year', $year);
            $check->__set('value', 1);
        }

        $check->save();
        return $check;
    }

    public function setPlan(Request $request) {
        $manager = $request->input('manager');
        $month = $request->input('month');
        $year = $request->input('year');
        $type = $request->input('type');
        $value = $request->input('value');

        if($check = ReportCustom::where('manager', $manager)->where('type', $type)->where('month', $month)->where('year', $year)->first()) {
            $check->__set('value', $value);
        } else {
            $check = new ReportCustom();
            $check->__set('manager', $manager);
            $check->__set('type', $type);
            $check->__set('month', $month);
            $check->__set('year', $year);
            $check->__set('value', $value);
        }

        $check->save();
        return $check;
    }

    protected static function getPlansByMonthAndYear($month, $year) {
        self::$plans = ReportCustom::where('type', '!=' , "weekend")->where('year', $year)->where('month', $month)->get();
    }

    protected static function getPlanWeek($manager, $week) {
        foreach(self::$plans as $plan)
            if($plan['manager'] == $manager && $plan['type'] == "week{$week}") return $plan['value'];
        return 0;
    }

    public function getToDesktop(Request $request) {

        if($request->has('pipeline'))
            $pipelineId = $request->input('pipeline');
        else
            $pipelineId = 3493222;

        if($request->has('month'))
            $month = $request->input('month');
        else
            $month = date('m');

        if($request->has('year'))
            $year = $request->input('year');
        else
            $year = date('Y');

        $date = [$month, $year];
        unset($month);
        unset($year);

        $fromDate = [
            'month' => $date[0],
            'year' => $date[1],
        ];

        self::getWeekendsByMonthAndYear($fromDate['month'], $fromDate['year']);
        self::getPlansByMonthAndYear($fromDate['month'], $fromDate['year']);

        $countDays = date('t', mktime(0, 0, 0, $date[0], 1, $date[1])); // Кол-во дней в месяце
        $users = self::groupDaysToManagers(Report::where('month', $date[0])->where('year', $date[1]) ->where('pipelineId', $pipelineId)->get());

        $format = [];
        foreach($users as $user) {
            $element = self::formatByUser($countDays, $date, $user[0]['manager']);
            foreach($user as $value) {
                $weekendDay = $this->searchWeekendDayByUser($user[0]['manager'], $value['day']);

                foreach($element['days'][$value['day']]['values'] as $key => $v) {
                    $element['days'][$value['day']]['values'][$key]['value'] = $value[$key];
                }

                $element['days'][$value['day']]['values']["weekend"]["value"] = $weekendDay;

            }
            $format[] = $element;
        }

        $arr = [];
        foreach($format as $r) {
            $days = [];
            $week = self::clearWeek();

            for($i=1;$i<=sizeof($r['days']);$i++) {
                $week = self::addToWeek($week, $r['days'][$i]['values']);

                if($i == 8 || $i == 15 || $i == 22) {
                    if($i == 8) $weekNumber = 1;
                    else if($i == 15) $weekNumber = 2;
                    else if($i == 22) $weekNumber = 3;

                    $days[] = [
                        'title' => 'Недельный план',
                        'week' => true,
                        'weekNumber' => $weekNumber,
                        'plan' => self::getPlanWeek($r['name'], $weekNumber),
                        'values' => self::getWeek($week, self::getPlanWeek($r['name'], $weekNumber)),
                    ];
                    $week = self::clearWeek();
                }

                $days[] = [ 'title' => "{$r['days'][$i]['title']}", 'day' => $i, 'values' => $r['days'][$i]['values'] ];

                if($i == sizeof($r['days'])) {
                    $days[] = [
                        'title' => 'Недельный план',
                        'week' => true,
                        'weekNumber' => 4,
                        'plan' => self::getPlanWeek($r['name'], 4),
                        'values' => self::getWeek($week, self::getPlanWeek($r['name'], 4))
                    ];
                }
            }

            $r['days'] = $days;
            $r['all'] = self::getSumByManager($days, $date, $r['name']);
            $arr[] = $r;
        }

        $price = self::getAllPrice($arr, $countDays);

        $items = [
            'general' => self::getGeneral($arr),
            'date' => $fromDate,
            'allPrice' => self::getAllPriceMonth($price),
            'price' => $price,
            'list' => $arr,
            'vars' => self::getMonthAndYears(),
            'pipeline' => $pipelineId
        ];

        return $items;
    }

    protected static function formatAllPrice($array) {
        return [
            "one" => number_format($array['one'], 2, ',', ' ') . " ₽",
            "two" => number_format($array['two'], 2, ',', ' ') . " ₽",
            "three" => number_format($array['three'], 2, ',', ' ') . " ₽",
            "four" => number_format($array['four'], 2, ',', ' ') . " ₽",
            "five" => number_format($array['five'], 2, ',', ' ') . " ₽",
            "six" => number_format($array['six'], 2, ',', ' ') . " ₽",
            "seven" => number_format($array['seven'], 2, ',', ' ') . " ₽",
            "eight" => number_format($array['eight'], 2, ',', ' ') . " ₽",
            "nine" => number_format($array['nine'], 2, ',', ' ') . " ₽",
        ];
    }

    protected static function formatPrice($array) {
        $return = [];
        foreach($array as $a) {
            $el = [];
            foreach($a['days'] as $key => $value)
                $el[$key] = number_format($value, 2, ',', ' ') . " ₽";
            $a['days'] = $el;
            $return[] = $a;
        }
        return $return;
    }

    protected static function formatList($array) {
        $return = [];
        foreach($array as $a) {
            foreach($a['all'] as $allKey => $allValue) {
                $a['all'][$allKey] = number_format($allValue, 2, ',', ' ');
                if($allKey == 'conversion' || $allKey == 'planPercent') {
                    $a['all'][$allKey] .= " %";
                } else if($allKey == 'plan' || $allKey == 'weekends' || $allKey == 'countMonth' || $allKey == 'countPackage') {

                } else {
                    $a['all'][$allKey] .= " ₽";
                }
            }
            foreach($a['days'] as $day) {
                //$set = [];
                $i=0;
                foreach($day['values'] as $key => $val) {
                    $a['days']['values'][$i][$key]['value'] = number_format($val['value'], 2, ',', ' ');
//                    if($key != 'plan' && $key != 'weekend' && $key != 'countMonth' && $key != 'countPackage') {
//                        if($key == 'conversion' || $key == 'planPercent') {
//                            $val['value'] .= " %";
//                        } else {
//                            $val['value'] .= " ₽";
//                        }
//                    }
//                    $set[$key] = $val;
                    $i++;
                    $a['days']['values'] = array_values($a['days']['values']);
                }
                $a['days'] = array_values($a['days']);
            }
            $return[] = $a;
        }
        return $return;
    }

    protected static function getGeneral($array) {
        $return = [
            'count' => 0,
            'priceGeneral' => 0,
            'priceMonth' => 0,
            'pricePackage' => 0,
            'countBuy' => 0,
            'conversion' => 0,
            'percent' => 0,
            'generalPlan' => 0,
        ];

        foreach($array as $a) {
            $return['count'] = $return['count'] + $a['all']['all'];
            $return['priceGeneral'] = $return['priceGeneral'] + ($a['all']['countPriceMonth'] + $a['all']['countPricePackage']);
            $return['priceMonth'] = $return['priceMonth'] + $a['all']['countPriceMonth'];
            $return['pricePackage'] = $return['pricePackage'] + $a['all']['countPricePackage'];
            $return['countBuy'] = $return['countBuy'] + ($a['all']['countMonth'] + $a['all']['countPackage']);
            $return['generalPlan'] = $return['generalPlan'] + $a['all']['plan'];
        }

        $return['conversion'] = $return['count'] > 0 ? (round($return['countBuy'] / $return['count'], 1)) * 100 : 0;
        $return['percent'] = $return['generalPlan'] > 0 ? (round($return['priceGeneral'] / $return['generalPlan'], 1) * 100) : 0;

        return $return;
    }

    protected static function getAllPriceMonth($array) {
        $items = self::clearTemplate();

        $arr = [ 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine' ];

        foreach($array as $value) {
            if(!isset($value['day']))
                foreach($arr as $a) $items[$a] = $items[$a] + $value['days'][$a];
        }

        return $items;
    }

    protected static function clearTemplate() {
        return [
            'one' => 0,
            'two' => 0,
            'three' => 0,
            'four' => 0,
            'five' => 0,
            'six' => 0,
            'seven' => 0,
            'eight' => 0,
            'nine' => 0,
        ];
    }

    protected static function getAllPrice($list, $countDays) {
        $return = [];

        for($i=1;$i<=$countDays+4;$i++)
            $return[$i] = [
                'title' => '',
                'days' => self::clearTemplate(),
            ];

        foreach($list as $el) {
            $i = 1;
            foreach($el['days'] as $day) {

                $return[$i]['title'] = $day['title'];

                if(isset($day['day'])) $return[$i]['day'] = $day['day'];

                $return[$i]['days']['one'] = $return[$i]['days']['one'] + $day['values']['countPriceMonth']['value'];
                $return[$i]['days']['two'] = $return[$i]['days']['two'] + $day['values']['countPricePackage']['value'];
                $return[$i]['days']['three'] = $return[$i]['days']['one'] + $return[$i]['days']['two'];
                $return[$i]['days']['four'] = $return[$i]['days']['four'] + $day['values']['monthExam']['value'];
                $return[$i]['days']['five'] = $return[$i]['days']['five'] + $day['values']['monthOge']['value'];
                $return[$i]['days']['six'] = $return[$i]['days']['six'] + ($day['values']['monthTenClass']['value'] + $day['values']['packageTenClass']['value']);
                $return[$i]['days']['seven'] = $return[$i]['days']['seven'] + $day['values']['packageExam']['value'];
                $return[$i]['days']['eight'] = $return[$i]['days']['eight'] + $day['values']['packageOge']['value'];
                $return[$i]['days']['nine'] = $return[$i]['days']['nine'] + $day['values']['packageTenClass']['value'];

                $i++;
            }
        }

        return array_values($return);
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
        return $this->getAllListByFilter('events', "&filter[type]=");
    }

    public function pactNewMessage(Request $request) {
        if($request->has('event') && $request->input('event') == 'new' && $request->has('type') && $request->input('type') == 'conversation') {

//            if(isset($request->input('data')['event']) && $request->input('data')['event'] == 'new') {
//                Telegram::sendMessage([
//                    'chat_id' => '228519769',
//                    'text' => json_encode($request->input('data'))
//                ]);
//            }
        }

        if($request->has('event') && $request->input('event') == 'new' && $request->has('type') && $request->input('type') == 'message') {
            Telegram::sendMessage([
                'chat_id' => '228519769',
                'text' => json_encode($request->input('data'))
            ]);
        }

        return "Ok";
    }
}
