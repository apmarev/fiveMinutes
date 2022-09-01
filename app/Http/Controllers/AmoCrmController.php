<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomApiException;
use App\Models\Lead;
use App\Models\LeadCount;
use App\Models\LeadCustom;
use App\Models\Manager;
use App\Models\ManagersInfo;
use App\Models\ManagersLeads;
use App\Models\ManagersLeadsSuccess;
use App\Models\ManagersLeadsSuccessCustom;
use App\Models\ManagersPlan;
use App\Models\Message;
use App\Models\Report;
use App\Models\ReportCustom;
use App\Models\Senler;
use App\Models\Talks;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PHPUnit\Util\Exception;
use Telegram\Bot\Laravel\Facades\Telegram;

class AmoCrmController extends Controller {

    protected AccessController $__access;
    protected static $weekends = [];
    protected static $plans = [];

    public static $pipelines = [
        3493222, // Первичные КЦ
        5084302, // Продление КЦ
        5322871 // Чатики
    ];

    public function __construct(AccessController $__access) {
        $this->__access = $__access;
    }

    public function getPipelines() {
        return [
            [ 'name' => 'Продление КЦ', 'id' => 5084302 ],
            [ 'name' => 'Первичные КЦ', 'id' => 3493222 ],
            [ 'name' => 'Чатики', 'id' => 5322871 ],
        ];
    }

    public function getManagersPlan(Request $request) {
        if(!$request->has('year') || !$request->has('month') || !$request->has('pipeline_id'))
            return CustomApiException::error(400);

        $ret = [];

        foreach($this->getWebManagers() as $manager) {
            $ret[] = [
                'manager_id' => $manager['id'],
                'manager_name' => $manager['name'],
                'plan' => ManagersPlan::where('manager_id', $manager['id'])->where('year', $request->input('year'))->where('month', $request->input('month'))->where('pipeline_id', $request->input('pipeline_id'))->get(),
            ];
        }

        return $ret;
    }

    public function getFilterPlan() {
        return [
            'month' => [
                [ 'name' => 'Январь', 'id' => 1 ],
                [ 'name' => 'Февраль', 'id' => 2 ],
                [ 'name' => 'Март', 'id' => 3 ],
                [ 'name' => 'Апрель', 'id' => 4 ],
                [ 'name' => 'Май', 'id' => 5 ],
                [ 'name' => 'Июнь', 'id' => 6 ],
                [ 'name' => 'Июль', 'id' => 7 ],
                [ 'name' => 'Август', 'id' => 8 ],
                [ 'name' => 'Сентябрь', 'id' => 9 ],
                [ 'name' => 'Октябрь', 'id' => 10 ],
                [ 'name' => 'Ноябрь', 'id' => 11 ],
                [ 'name' => 'Декабрь', 'id' => 12 ],
            ],
            'years' => [
                2019, 2020, 2021, 2022, 2023, 2024
            ],
            'pipelines' => $this->getPipelines()
        ];
    }

    public function setManagersPlan(Request $request) {

        foreach($request->input('managers') as $manager) {
            $manager = json_decode($manager, true);
            if(
                $entity = ManagersPlan::where('manager_id', $manager['id'])
                ->where('pipeline_id', $request->input('pipeline_id'))
                    ->where('year', $request->input('year'))
                    ->where('month', $request->input('month'))
                    ->where('week', $manager['week'])
                ->first()
            ) {

            } else {
                $entity = new ManagersPlan();
            }

            $entity->__set('manager_id', $manager['id']);
            $entity->__set('year', $request->input('year'));
            $entity->__set('month', $request->input('month'));
            $entity->__set('week', $manager['week']);
            $entity->__set('pipeline_id', $request->input('pipeline_id'));
            $entity->__set('month_sum', $manager['month_sum'] ?? 0);
            $entity->__set('package_sum', $manager['package_sum'] ?? 0);
            $entity->__set('month_count', $manager['month_count'] ?? 0);
            $entity->__set('package_count', $manager['package_count'] ?? 0);
            $entity->__set('pro_count', $manager['pro_count'] ?? 0);
            $entity->__set('count', $manager['count'] ?? 0);

            $entity->save();
        }

        return "Ok";
    }

    public function getWebManagers() {
        return Manager::all();
    }

    public function getWebYears() {
        return ManagersInfo::distinct()->get(['year']);
    }

    public function getWebMonthByYear($year) {
        return ManagersInfo::where('year', $year)->distinct()->get(['month_name', 'month']);
    }

    protected static function format($day, $month) {
        return mb_substr("0{$day}", -2) . "." . mb_substr("0{$month}", -2);
    }

    protected static function getWeeksPlansByManagers(array $ids, $month, $year, $pipeline = null) {
        $ret = [
            'week1' => [
                'plan_month' => 0,
                'plan_package' => 0,
                'plan_pro' => 0,
                'plan_count' => 0,
            ],
            'week2' => [
                'plan_month' => 0,
                'plan_package' => 0,
                'plan_pro' => 0,
                'plan_count' => 0,
            ],
            'week3' => [
                'plan_month' => 0,
                'plan_package' => 0,
                'plan_pro' => 0,
                'plan_count' => 0,
            ],
            'week4' => [
                'plan_month' => 0,
                'plan_package' => 0,
                'plan_pro' => 0,
                'plan_count' => 0,
            ],
        ];

        foreach($ids as $id) {
            $user = ManagersPlan::where('manager_id', $id)
                ->where('month', $month)
                ->where('year', $year);

            if($pipeline) $user = $user->where('pipeline_id', $pipeline);

            $user = $user->get()->toArray();

            for($i=1;$i<=4;$i++) {
                $index = array_search($i, array_column($user, 'week'));
                if($index > -1) {
                    $ret["week{$i}"]['plan_month'] = $ret["week{$i}"]['plan_month'] + $user[$index]['month_sum'];
                    $ret["week{$i}"]['plan_package'] = $ret["week{$i}"]['plan_package'] + $user[$index]['package_sum'];
                    $ret["week{$i}"]['plan_count'] = $ret["week{$i}"]['plan_count'] + $user[$index]['count'];
                    $ret["week{$i}"]['plan_pro'] = $ret["week{$i}"]['plan_pro'] + $user[$index]['pro_count'];
                }
            }

        }

        return $ret;
    }

    protected static function getPercent($from, $to) {
        return $to > 0 ? round(($from / $to) * 100, 1) : 0;
    }

    protected static function getRemainder($plan, $facts) {
        return $plan - $facts;
    }

    protected static function sum_calc($data, $key, array $keys_two, $data_to) {
        $sum = 0;

        foreach($keys_two as $key)
            $sum = $sum + $data_to[$key];

        if(isset($data[$key]))
            return $data[$key] + $sum;
        else
            return $sum;

    }

    protected static function calculateManagers($data, $month, $year, $pipeline = null) {
        $ret = [];

        $count_days = date('t', mktime(0, 0, 0, $month, 1, $year));

        for($i=1;$i<=$count_days;$i++) {
            $ret['days'][$i] = [];
        }

        $all = [];

        $managers_ids = [];

        foreach($data as $d) {
            $key = $d['day'];
            foreach($d as $k => $v) {
                if($k == 'manager_id') $managers_ids[] = $v;
                if($k != 'id' && $k != 'manager_id' && $k != 'manager_name' && $k != 'day' && $k != 'month' && $k != 'month_name' && $k != 'year') {
                    if(isset($ret['days'][$key][$k]))
                        $ret['days'][$key][$k] = $ret['days'][$key][$k] + $v;
                    else
                        $ret['days'][$key][$k] = $v;

                    if(isset($all[$k]))
                        $all[$k] = $all[$k] + $v;
                    else
                        $all[$k] = $v;
                }
            }

            $all['sum_sale_month_ege'] = self::sum_calc($all, 'sum_sale_month_ege', [ 'children_month_ege', 'parents_month_ege' ], $d);
            $all['sum_sale_month_oge'] = self::sum_calc($all, 'sum_sale_month_oge', [ 'children_month_oge', 'parents_month_oge' ], $d);
            $all['sum_sale_month_ten'] = self::sum_calc($all, 'sum_sale_month_ten', [ 'children_month_10', 'parents_month_10' ], $d);

            $all['sum_sale_package_ege'] = self::sum_calc($all, 'sum_sale_package_ege', [ 'children_package_ege', 'parents_package_ege' ], $d);
            $all['sum_sale_package_oge'] = self::sum_calc($all, 'sum_sale_package_oge', [ 'children_package_oge', 'parents_package_oge' ], $d);
            $all['sum_sale_package_ten'] = self::sum_calc($all, 'sum_sale_package_ten', [ 'children_package_10', 'parents_package_10' ], $d);

        }

        $all['sum_sale_ege'] = $all['children_ege'] + $all['parents_ege'];
        $all['sum_sale_oge'] = $all['children_oge'] + $all['parents_oge'];
        $all['sum_sale_ten'] = $all['children_10'] + $all['parents_10'];

        $all['sum_sale'] = $all['sum_sale_ege'] + $all['sum_sale_oge'] + $all['sum_sale_ten'];

        $plans = self::getWeeksPlansByManagers(array_unique($managers_ids), $month, $year, $pipeline);

        $week1 = [];
        $week2 = [];
        $week3 = [];
        $week4 = [];
        $size = [];

        $monthPlan = [
            'month' => 0,
            'package' => 0,
            'pro' => 0,
            'count' => 0
        ];

        for($i=1;$i<=$count_days;$i++) {

            foreach($ret['days'][$i] as $k => $v) {
                if(isset($size[$k]))
                    $size[$k] = $size[$k] + $v;
                else
                    $size[$k] = $v;

                $ret['days'][$i]['sum_sale_month_ege'] = self::sum_calc($ret['days'][$i], 'sum_sale_month_ege', [ 'children_month_ege', 'parents_month_ege' ], $ret['days'][$i]);
                $ret['days'][$i]['sum_sale_month_oge'] = self::sum_calc($ret['days'][$i], 'sum_sale_month_oge', [ 'children_month_oge', 'parents_month_oge' ], $ret['days'][$i]);
                $ret['days'][$i]['sum_sale_month_ten'] = self::sum_calc($ret['days'][$i], 'sum_sale_month_ten', [ 'children_month_10', 'parents_month_10' ], $ret['days'][$i]);

                $ret['days'][$i]['sum_sale_package_ege'] = self::sum_calc($ret['days'][$i], 'sum_sale_package_ege', [ 'children_package_ege', 'parents_package_ege' ], $ret['days'][$i]);
                $ret['days'][$i]['sum_sale_package_oge'] = self::sum_calc($ret['days'][$i], 'sum_sale_package_oge', [ 'children_package_oge', 'parents_package_oge' ], $ret['days'][$i]);
                $ret['days'][$i]['sum_sale_package_ten'] = self::sum_calc($ret['days'][$i], 'sum_sale_package_ten', [ 'children_package_10', 'parents_package_10' ], $ret['days'][$i]);

                $ret['days'][$i]['sum_sale_ege'] = $ret['days'][$i]['children_ege'] + $ret['days'][$i]['parents_ege'];
                $ret['days'][$i]['sum_sale_oge'] = $ret['days'][$i]['children_oge'] + $ret['days'][$i]['parents_oge'];
                $ret['days'][$i]['sum_sale_ten'] = $ret['days'][$i]['children_10'] + $ret['days'][$i]['parents_10'];

                $ret['days'][$i]['sum_sale'] = $ret['days'][$i]['sum_sale_ege'] + $ret['days'][$i]['sum_sale_oge'] + $ret['days'][$i]['sum_sale_ten'];

                $ret['days'][$i]['average_check'] = $ret['days'][$i]['count'] > 0 ? ($ret['days'][$i]['sum_month'] + $ret['days'][$i]['sum_package'] + $ret['days'][$i]['sum_pro']) / $ret['days'][$i]['count'] : 0;

                $ret['days'][$i]['average_check_children_ege'] = $ret['days'][$i]['count_children_ege'] > 0 ? $ret['days'][$i]['children_ege'] / $ret['days'][$i]['count_children_ege'] : 0;
                $ret['days'][$i]['average_check_children_oge'] = $ret['days'][$i]['count_children_oge'] > 0 ? $ret['days'][$i]['children_oge'] / $ret['days'][$i]['count_children_oge'] : 0;
                $ret['days'][$i]['average_check_children_10'] = $ret['days'][$i]['count_children_10'] > 0 ? $ret['days'][$i]['children_10'] / $ret['days'][$i]['count_children_10'] : 0;
                $ret['days'][$i]['average_check_parents_ege'] = $ret['days'][$i]['count_parents_ege'] > 0 ? $ret['days'][$i]['parents_ege'] / $ret['days'][$i]['count_parents_ege'] : 0;
                $ret['days'][$i]['average_check_parents_oge'] = $ret['days'][$i]['count_parents_oge'] > 0 ? $ret['days'][$i]['parents_oge'] / $ret['days'][$i]['count_parents_oge'] : 0;
                $ret['days'][$i]['average_check_parents_10'] = $ret['days'][$i]['count_parents_10'] > 0 ? $ret['days'][$i]['parents_10'] / $ret['days'][$i]['count_parents_10'] : 0;

                $children_sum = $ret['days'][$i]['children_ege'] + $ret['days'][$i]['children_oge'] + $ret['days'][$i]['children_10'];
                $children_count = $ret['days'][$i]['count_children_ege'] + $ret['days'][$i]['count_children_oge'] + $ret['days'][$i]['count_children_10'];

                $parents_sum = $ret['days'][$i]['children_ege'] + $ret['days'][$i]['children_oge'] + $ret['days'][$i]['children_10'];
                $parents_count = $ret['days'][$i]['count_children_ege'] + $ret['days'][$i]['count_children_oge'] + $ret['days'][$i]['count_children_10'];

                $ret['days'][$i]['average_check_children'] = $children_count > 0 ? $children_sum / $children_count : 0;
                $ret['days'][$i]['average_check_parents'] = $parents_count > 0 ? $parents_sum / $parents_count : 0;

                $ege_sum = $ret['days'][$i]['children_month_ege'] + $ret['days'][$i]['parents_month_ege'] + $ret['days'][$i]['children_package_ege'] + $ret['days'][$i]['parents_package_ege'];
                $oge_sum = $ret['days'][$i]['children_month_oge'] + $ret['days'][$i]['parents_month_oge'] + $ret['days'][$i]['children_package_oge'] + $ret['days'][$i]['parents_package_oge'];
                $ten_sum = $ret['days'][$i]['children_month_10'] + $ret['days'][$i]['parents_month_10'] + $ret['days'][$i]['children_package_10'] + $ret['days'][$i]['parents_package_10'];

                $ege_count = $ret['days'][$i]['count_sale_children_ege'] + $ret['days'][$i]['count_sale_parents_ege'];
                $oge_count = $ret['days'][$i]['count_sale_children_oge'] + $ret['days'][$i]['count_sale_parents_oge'];
                $ten_count = $ret['days'][$i]['count_sale_children_10'] + $ret['days'][$i]['count_sale_parents_10'];

                $ret['days'][$i]['average_check_ege'] = $ege_count > 0 ? $ege_sum / $ege_count : 0;
                $ret['days'][$i]['average_check_oge'] = $oge_count > 0 ? $oge_sum / $oge_count : 0;
                $ret['days'][$i]['average_check_10'] = $ten_count > 0 ? $ten_sum / $ten_count : 0;
            }



            if($i == 7) {
                $size['plan'] = $plans['week1'];
                $size = self::getPlanWeekPlan($size);
                $week1 = $size;
                $size = [];
            } else if($i == 14) {
                $size['plan'] = $plans['week2'];
                $size = self::getPlanWeekPlan($size);
                $week2 = $size;
                $size = [];
            } else if($i == 21) {
                $size['plan'] = $plans['week3'];
                $size = self::getPlanWeekPlan($size);
                $week3 = $size;
                $size = [];
            }
            if($i == $count_days) {
                $size['plan'] = $plans['week4'];
                $size = self::getPlanWeekPlan($size);
                $week4 = $size;
                $size = [];
            }
        }

        $report = [];

        $i = 1;
        foreach($ret['days'] as $key => $day) {
            $el = $day;
            $el['date'] = self::format($key, $month);
            $report[] = $el;

            if($i == 7) {
                $weeks = $week1;
                $weeks['date'] = 'Недельный план';

                $monthPlan['month'] = $monthPlan['month'] + $weeks['plan']['plan_month'];
                $monthPlan['package'] = $monthPlan['package'] + $weeks['plan']['plan_package'];
                $monthPlan['pro'] = $monthPlan['pro'] + $weeks['plan']['plan_pro'];
                $monthPlan['count'] = $monthPlan['count'] + $weeks['plan']['plan_count'];

                $weeks['average_check'] = $weeks['count'] > 0 ?($weeks['sum_month'] + $weeks['sum_package'] + $weeks['sum_pro']) / $weeks['count'] : 0;

                if(!$pipeline)
                    $report[] = $weeks;
            } else if($i == 14) {
                $weeks = $week2;
                $weeks['date'] = 'Недельный план';

                $monthPlan['month'] = $monthPlan['month'] + $weeks['plan']['plan_month'];
                $monthPlan['package'] = $monthPlan['package'] + $weeks['plan']['plan_package'];
                $monthPlan['pro'] = $monthPlan['pro'] + $weeks['plan']['plan_pro'];
                $monthPlan['count'] = $monthPlan['count'] + $weeks['plan']['plan_count'];

                $weeks['average_check'] = $weeks['count'] > 0 ?($weeks['sum_month'] + $weeks['sum_package'] + $weeks['sum_pro']) / $weeks['count'] : 0;

                if(!$pipeline)
                    $report[] = $weeks;
            } else if($i == 21) {
                $weeks = $week3;
                $weeks['date'] = 'Недельный план';

                $monthPlan['month'] = $monthPlan['month'] + $weeks['plan']['plan_month'];
                $monthPlan['package'] = $monthPlan['package'] + $weeks['plan']['plan_package'];
                $monthPlan['pro'] = $monthPlan['pro'] + $weeks['plan']['plan_pro'];
                $monthPlan['count'] = $monthPlan['count'] + $weeks['plan']['plan_count'];

                $weeks['average_check'] = $weeks['count'] > 0 ?($weeks['sum_month'] + $weeks['sum_package'] + $weeks['sum_pro']) / $weeks['count'] : 0;

                if(!$pipeline)
                    $report[] = $weeks;
            }

            if($i == $count_days) {
                $weeks = $week4;
                $weeks['date'] = 'Недельный план';

                $monthPlan['month'] = $monthPlan['month'] + $weeks['plan']['plan_month'];
                $monthPlan['package'] = $monthPlan['package'] + $weeks['plan']['plan_package'];
                $monthPlan['pro'] = $monthPlan['pro'] + $weeks['plan']['plan_pro'];
                $monthPlan['count'] = $monthPlan['count'] + $weeks['plan']['plan_count'];

                $weeks['average_check'] = $weeks['count'] > 0 ?($weeks['sum_month'] + $weeks['sum_package'] + $weeks['sum_pro']) / $weeks['count'] : 0;

                if(!$pipeline)
                    $report[] = $weeks;
            }

            $i++;
        }

        $all['plan'] = $monthPlan;

        $all_month_sum = $all['children_month_ege'] + $all['children_month_oge'] +$all['children_month_10'] +$all['parents_month_ege'] +$all['parents_month_oge'] +$all['parents_month_10'];
        $all['plan']['month_percent'] = $all['plan']['month'] > 0 ? round($all_month_sum / $all['plan']['month'] * 100, 1) : 0;
        $all['plan']['month_remainder'] = $all['plan']['month'] - $all_month_sum;

        $all_package_sum = $all['children_package_ege'] + $all['children_package_oge'] +$all['children_package_10'] +$all['parents_package_ege'] +$all['parents_package_oge'] +$all['parents_package_10'];
        $all['plan']['package_percent'] = $all['plan']['package'] > 0 ? round($all_package_sum / $all['plan']['package'] * 100, 1) : 0;
        $all['plan']['package_remainder'] = $all['plan']['package'] - $all_package_sum;

        $all['plan']['pro_percent'] = $all['plan']['pro'] > 0 ? round($all['count_pro'] / $all['plan']['pro'] * 100, 1) : 0;
        $all['plan']['pro_remainder'] = $all['plan']['pro'] - $all['count_pro'];

        $all_count_sale = $all['count_sale_children_ege'] + $all['count_sale_children_oge'] +$all['count_sale_children_10'] +$all['count_sale_parents_ege'] +$all['count_sale_parents_oge'] +$all['count_sale_parents_10'];
        $all['plan']['count_percent'] = $all['plan']['count'] > 0 ? round($all_count_sale / $all['plan']['count'] * 100, 1) : 0;
        $all['plan']['count_remainder'] = $all['plan']['count'] - $all_count_sale;

        $plan_month = $all['plan']['month'];
        $plan_package = $all['plan']['package'];
        $plan_pro = $all['plan']['pro'];
        $plan_count = $all['plan']['count'];

        $week_plan_month = 0;
        $week_plan_package = 0;
        $week_plan_pro = 0;
        $week_plan_count = 0;

        foreach($report as $k => $v) {
            if(isset($v['date']) && $v['date'] == "Недельный план") {

                $report[$k]['plan']['month'] = $v['plan']['plan_month'];
                $report[$k]['plan']['month_percent'] = $v['plan']['plan_month'] > 0 ? ($week_plan_month / $v['plan']['plan_month']) *  100 : 0;
                $report[$k]['plan']['month_remainder'] = $v['plan']['plan_month'] - $week_plan_month;

                $report[$k]['plan']['package'] = $v['plan']['plan_package'];
                $report[$k]['plan']['package_percent'] = $v['plan']['plan_package'] > 0 ? ($week_plan_package / $v['plan']['plan_package']) *  100 : 0;
                $report[$k]['plan']['package_remainder'] = $v['plan']['plan_package'] - $week_plan_package;

                $report[$k]['plan']['pro'] = $v['plan']['plan_pro'];
                $report[$k]['plan']['pro_percent'] = $v['plan']['plan_pro'] > 0 ? ($week_plan_pro / $v['plan']['plan_pro']) *  100 : 0;
                $report[$k]['plan']['pro_remainder'] = $v['plan']['plan_pro'] - $week_plan_pro;

                $report[$k]['plan']['count'] = $v['plan']['plan_count'];
                $report[$k]['plan']['count_percent'] = $v['plan']['plan_count'] > 0 ? ($week_plan_count / $v['plan']['plan_count']) *  100 : 0;
                $report[$k]['plan']['count_remainder'] = $v['plan']['plan_count'] - $week_plan_count;

                $week_plan_month = 0;
                $week_plan_package = 0;
                $week_plan_pro = 0;
                $week_plan_count = 0;
            } else {
                if(isset($v['sum_month']) && $v['sum_month'] > 0) {
                    $report[$k]['plan']['month_percent'] = $plan_month > 0 ? ($v['sum_month'] / $plan_month) * 100 : 0;
                    $plan_month = $plan_month - $v['sum_month'];
                    $report[$k]['plan']['month_remainder'] = $plan_month;

                    $report[$k]['plan']['package_percent'] = $plan_package > 0 ? ($v['sum_package'] / $plan_package) * 100 : 0;
                    $plan_package = $plan_package - $v['sum_package'];
                    $report[$k]['plan']['package_remainder'] = $plan_package;

                    $report[$k]['plan']['pro_percent'] = $plan_pro > 0 ? ($v['count_pro'] / $plan_pro) * 100 : 0;
                    $plan_pro = $plan_pro - $v['count_pro'];
                    $report[$k]['plan']['pro_remainder'] = $plan_pro;

                    $su = $v['count_sale_children_ege'] + $v['count_sale_children_oge'] + $v['count_sale_children_10'] + $v['count_sale_parents_ege'] + $v['count_sale_parents_oge'] + $v['count_sale_parents_10'];
                    $report[$k]['plan']['count_percent'] = $plan_count > 0 ? ($su / $plan_count) * 100 : 0;
                    $plan_count = $plan_count - $su;
                    $report[$k]['plan']['count_remainder'] = $plan_count;

                    $week_plan_month = $week_plan_month + $v['sum_month'];
                    $week_plan_package = $week_plan_package + $v['sum_package'];
                    $week_plan_pro = $week_plan_pro + $v['count_pro'];
                    $week_plan_count = $week_plan_count + $su;

                    //count_percent
                }
            }
        }

        $all['average_check'] = $all['count'] > 0 ? ($all['sum_month'] + $all['sum_package'] + $all['sum_pro']) / $all['count'] : 0;

        $all['average_check_children_ege'] = $all['count_children_ege'] > 0 ? $all['children_ege'] / $all['count_children_ege'] : 0;
        $all['average_check_children_oge'] = $all['count_children_oge'] > 0 ? $all['children_oge'] / $all['count_children_oge'] : 0;
        $all['average_check_children_10'] = $all['count_children_10'] > 0 ? $all['children_10'] / $all['count_children_10'] : 0;
        $all['average_check_parents_ege'] = $all['count_parents_ege'] > 0 ? $all['parents_ege'] / $all['count_parents_ege'] : 0;
        $all['average_check_parents_oge'] = $all['count_parents_oge'] > 0 ? $all['parents_oge'] / $all['count_parents_oge'] : 0;
        $all['average_check_parents_10'] = $all['count_parents_10'] > 0 ? $all['parents_10'] / $all['count_parents_10'] : 0;

        $children_sum = $all['children_ege'] + $all['children_oge'] + $all['children_10'];
        $children_count = $all['count_children_ege'] + $all['count_children_oge'] + $all['count_children_10'];

        $parents_sum = $all['children_ege'] + $all['children_oge'] + $all['children_10'];
        $parents_count = $all['count_children_ege'] + $all['count_children_oge'] + $all['count_children_10'];

        $all['average_check_children'] = $children_count > 0 ? $children_sum / $children_count : 0;
        $all['average_check_parents'] = $parents_count > 0 ? $parents_sum / $parents_count : 0;

        $ege_sum = $all['children_month_ege'] + $all['parents_month_ege'] + $all['children_package_ege'] + $all['parents_package_ege'];
        $oge_sum = $all['children_month_oge'] + $all['parents_month_oge'] + $all['children_package_oge'] + $all['parents_package_oge'];
        $ten_sum = $all['children_month_10'] + $all['parents_month_10'] + $all['children_package_10'] + $all['parents_package_10'];

        $ege_count = $all['count_sale_children_ege'] + $all['count_sale_parents_ege'];
        $oge_count = $all['count_sale_children_oge'] + $all['count_sale_parents_oge'];
        $ten_count = $all['count_sale_children_10'] + $all['count_sale_parents_10'];

        $all['average_check_ege'] = $ege_count > 0 ? $ege_sum / $ege_count : 0;
        $all['average_check_oge'] = $oge_count > 0 ? $oge_sum / $oge_count : 0;
        $all['average_check_10'] = $ten_count > 0 ? $ten_sum / $ten_count : 0;

        return ['all' => $all, 'days' => $report];
    }

    public function getWebInfoMain(Request $request) {

        if(!$request->has('month') || !$request->has('pipeline_id') || !$request->has('year'))
            return CustomApiException::error(400);

        $data = ManagersInfo::where('pipeline_id', $request->input('pipeline_id'))
            ->where('month', $request->input('month'))
            ->where('year', $request->input('year'))
            ->get()->toArray();

        return self::calculateManagers($data, $request->input('month'), $request->input('year'), $request->input('pipeline_id'));
    }

    public function getWebInfoManager(Request $request) {

        if(!$request->has('month') || !$request->has('managers') || !$request->has('year'))
            return CustomApiException::error(400);

        $ret = [];

        foreach($request->input('managers') as $manager) {
            $item = ManagersInfo::where('month', $request->input('month'))->where('year', $request->input('year'))->where('manager_id', $manager)->get()->toArray();
            $ret = array_merge($ret, $item);
        }

        return self::calculateManagers($ret, $request->input('month'), $request->input('year'));
    }

    protected static function getTypeCourse(int $enum): string {
        $ret = 'none';

        $ege = [
            431967, // МГ - ЕГЭ
            431973, // Предбанник - ЕГЭ
            431979, // Курс ФЛЕШ - ЕГЭ
            431985, // Игра "Рулетка" - ЕГЭ
            432689, // Индивидуальные занятия с репетитором - ЕГЭ
            432701, // Курс по основам - ЕГЭ
            432709 // База заданий - ЕГЭ
        ];

        $oge = [
            431969, // МГ - ОГЭ
            431975, // Предбанник - ОГЭ
            431981, // Курс ФЛЕШ - ОГЭ
            431983, // Игра "Рулетка" - ОГЭ
            432691, // Индивидуальные занятия с репетитором - ОГЭ
            432703, // Курс по основам - ОГЭ
            432707 // База заданий - ОГЭ
        ];

        $ten = [
            431971, // МГ - 10 класс
            431977, // Курс ФЛЕШ - 10 класс
            432693 // Индивидуальные занятия с репетитором - 10 класс
        ];

        if(array_search($enum, $ege) > -1) {
            $ret = 'ege';
        } else if(array_search($enum, $oge) > -1) {
            $ret = 'oge';
        } else if(array_search($enum, $ten) > -1) {
            $ret = 'ten';
        }

        return $ret;
    }

    public function getProductByType($lead_id, $package, $type_user) {
        $ret_type = 'none';
        $ret_course = 'none';
        $ret_package = 'none';
        if($product = ManagersLeadsSuccessCustom::where('lead_id', $lead_id)->where('field_id', 709405)->first()) {
            if($product['enum']) {

                if($type_user == 'Ученик')
                    $ret_type = 'children';
                else if($type_user == 'Родитель')
                    $ret_type = 'parents';

                $type = self::getTypeCourse($product['enum']);

                if($package > 1) { // Пакет
                    $ret_package = 'package';

                    if($type == 'ege') {
                        $ret_course = 'ege';
                    } else if($type == 'oge') {
                        $ret_course = 'oge';
                    } else if($type == 'ten') {
                        $ret_course = '10';
                    }
                } else if($package == 1) { // Месяц
                    $ret_package = 'month';

                    if($type == 'ege') {
                        $ret_course = 'ege';
                    } else if($type == 'oge') {
                        $ret_course = 'oge';
                    } else if($type == 'ten') {
                        $ret_course = '10';
                    }
                }
            }
        }

        return [
            'type' => $ret_type,
            'course' => $ret_course,
            'package' => $ret_package,
        ];
    }

    public static function getProduct(int $lead_id, int|float $package) {
        if($product = ManagersLeadsSuccessCustom::where('lead_id', $lead_id)->where('field_id', 709405)->first()) {
            if($product['enum']) {
                $type = self::getTypeCourse($product['enum']);

                if($package > 1) { // Пакет
                    $status = true;

                    if($type == 'ege') {

                    } else if($type == 'oge') {

                    } else if($type == 'ten') {

                    }
                } else if($package == 1) { // Месяц

                    if($type == 'ege') {

                    } else if($type == 'oge') {

                    } else if($type == 'ten') {

                    }
                }
            }
        }
    }

    public static function getPackage(int $lead_id): int|float {
        if($package = ManagersLeadsSuccessCustom::where('lead_id', $lead_id)->where('field_id', 708651)->first()) {
            if($package['enum'] == 430903)
                return 1; // Это значит пакет 1
            else
                return floatval(str_replace(',', '.', $package['value'])); // Пакет больше 1
        } else return 0;
    }

    protected static function getMonthNameByMonthNumber(int $number): string {
        $monthName = '';
        if($number == 1) $monthName = 'Январь';
        else if($number == 2) $monthName = 'Февраль';
        else if($number == 3) $monthName = 'Март';
        else if($number == 4) $monthName = 'Апрель';
        else if($number == 5) $monthName = 'Май';
        else if($number == 6) $monthName = 'Июнь';
        else if($number == 7) $monthName = 'Июль';
        else if($number == 8) $monthName = 'Август';
        else if($number == 9) $monthName = 'Сентябрь';
        else if($number == 10) $monthName = 'Октябрь';
        else if($number == 11) $monthName = 'Ноябрь';
        else if($number == 12) $monthName = 'Декабрь';

        return $monthName;
    }

    public function generate_data() {

        $yesterday = strtotime("-1 day");

        $now_year = date('o', $yesterday);
        $now_month = date('n', $yesterday);
        $now_day = date('j', $yesterday);

        ManagersLeads::truncate();
        ManagersLeadsSuccess::truncate();
        ManagersLeadsSuccessCustom::truncate();

        ManagersInfo::where('year', $now_year)->where('month', $now_month)->delete();

        if($now_month - 1 < 1) {
            ManagersInfo::where('year', $now_year - 1)->where('month', 12)->delete();
            $array = [
                [
                    'month' => 12,
                    'year' => $now_year - 1,
                    'days' => date('t', mktime(0, 0, 0, 12, 1, $now_year - 1)),
                ],
                [
                    'month' => $now_month,
                    'year' => $now_year,
                    'days' => $now_day,
                ],
            ];
        } else {
            ManagersInfo::where('year', $now_year)->where('month', $now_month - 1)->delete();
            $array = [
//                [
//                    'month' => $now_month - 1,
//                    'year' => $now_year,
//                    'days' => date('t', mktime(0, 0, 0, $now_month - 1, 1, $now_year)),
//                ],
                [
                    'month' => $now_month,
                    'year' => $now_year,
                    'days' => $now_day,
                ],
            ];
        }

        foreach($array as $a) {
            $this->generate($a['days'], $a['month'], $a['year']);
        }

        // $this->generate(1, 8, 2022);

        return "Ok";

    }

    public function generate($days, $month, $year) {

        $month = substr("0{$month}", -2);

        for($day=1;$day<=$days;$day++) {
            $day_now = substr("0{$day}", -2);
            $date_from = strtotime("{$day_now}.{$month}.{$year} 00:00:01");
            $date_to = strtotime("{$day_now}.{$month}.{$year} 23:59:59");

            $this->managers();
            $monthName = self::getMonthNameByMonthNumber($month);

            $dateArray = [
                'day' => $day,
                'month' => $month,
                'month_name' => $monthName,
                'year' => $year,
            ];

            try {
                $this->getCountLeadsByManagers($date_from, $date_to);
                $this->getLeadsSuccessByManagers($date_from, $date_to);

                $this->getManagersInfo($dateArray);

                $this->clearManagersLeads();
            } catch (\Exception $e) {
                return CustomApiException::error(501, $e->getMessage());
            }
        }

        return "Ok";
    }

    public function getCountLeadsByManagers($from, $to) {
        try {
            $array = [];
            $contacts = [];

            foreach(self::$pipelines as $pipeline) {
                $list = $this->getAllListByFilter('leads', "&filter[pipeline_id]={$pipeline}&filter[created_at][from]={$from}&filter[created_at][to]={$to}&with=contacts");

                foreach($list as $el) {
                    $target = false;
                    $package = 0;
                    $course = null;

                    foreach($this->getIsSetListCustomFields($el) as $c) {
                        if($c['field_id'] == 704241 && $c['values'][0]['value'] == true) {
                            $target = true;
                        }
                        if($c['field_id'] == 708651) {
                            $package = floatval($c['values'][0]['value']);
                        }
                        if($c['field_id'] == 709405) {
                            if($c['values'][0]['enum_id'] > 0) {
                                $course = self::getTypeCourse($c['values'][0]['enum_id']);
                            }
                        }
                    }

                    $contact = null;

                    if(isset($el['_embedded']) && isset($el['_embedded']['contacts']) && isset($el['_embedded']['contacts'][0])) {
                        $contact = $el['_embedded']['contacts'][0]['id'];
                        $contacts[] = $contact;
                    }

                    $array[] = [
                        'manager' => $el['responsible_user_id'],
                        'pipeline_id' => $el['pipeline_id'],
                        'target' => $target,
                        'contact' => $contact,
                        'type' => null,
                        'package' => $package,
                        'course' => $course,
                    ];
                }
            }

            $contacts = array_chunk($contacts, 60);

            foreach($contacts as $elems) {
                $filter = "";
                $i=0;
                foreach($elems as $e) {
                    $filter .= "&filter[id][{$i}]=$e";
                    $i++;
                }
                $list = $this->getAllListByFilter('contacts', $filter);
                foreach($list as $l) {
                    $type = null;
                    foreach($this->getIsSetListCustomFields($l) as $c) {
                        if($c['field_id'] == 707467) {
                            $type = $c['values'][0]['value'];
                        }
                    }

                    if($type != null) {
                        $index = array_search($l['id'], array_column($array, 'contact'));

                        if($index > -1) {
                            $times = $array[$index];
                            $times['type'] = $type;
                            $array[$index] = $times;
                        }

                    }
                }
            }

            $array = array_chunk($array, 50);

            foreach($array as $a)
                ManagersLeads::insert($a);
        } catch (\Exception $e) {
            throw new Exception($e);
        }

    }

    protected function getContactsByIds(array $contacts) {


        $chunk = array_chunk($contacts, 30);

        $result = [];

        foreach($chunk as $c) {
            $filter = "";
            $i = 0;
            foreach($c as $contact) {
                $filter .= "&filter[id][{$i}]={$contact}";
                $i++;
            }

            $list = $this->getAllListByFilter('contacts', $filter);
            $result = array_merge($result, $list);
        }

        $ret = [];

        foreach($result as $c) {
            $type = 'none';
            foreach($this->getIsSetListCustomFields($c) as $custom) {
                if($custom['field_id'] == 707467) {
                    $type = $custom['values'][0]['value'];
                }
            }

            $ret[] = [
                'id' => $c['id'],
                'type' => $type
            ];
        }

        return $ret;
    }

    protected function clearManagersLeads() {
        ManagersLeads::truncate();
        ManagersLeadsSuccess::truncate();
        ManagersLeadsSuccessCustom::truncate();
    }

    public function getLeadsSuccessByManagers($from, $to) {
        try {
            $array = [];
            $customs = [];
            $contacts = [];

            foreach(self::$pipelines as $pipeline) {
                $list = $this->getAllListByFilter('leads', "&filter[statuses][0][pipeline_id]={$pipeline}&filter[statuses][0][status_id]=142&filter[closed_at][from]={$from}&filter[closed_at][to]={$to}&with=contacts");

                foreach($list as $lead) {

                    $target = false;

                    foreach($this->getIsSetListCustomFields($lead) as $c) {
                        if($c['field_id'] == 704241 && $c['values'][0]['value'] == true) {
                            $target = true;
                        }
                    }

                    if(isset($lead['_embedded']) && isset($lead['_embedded']['contacts']) && isset($lead['_embedded']['contacts'][0]))
                        $contacts[] = $lead['_embedded']['contacts'][0]['id'];


                    $array[] = [
                        'lead_id' => $lead['id'],
                        'price' => $lead['price'],
                        'manager' => $lead['responsible_user_id'],
                        'pipeline_id' => $lead['pipeline_id'],
                        'status_id' => $lead['status_id'],
                        'created' => $lead['created_at'],
                        'contact' => isset($lead['_embedded']) && isset($lead['_embedded']['contacts']) && isset($lead['_embedded']['contacts'][0]) ? $lead['_embedded']['contacts'][0]['id'] : null,
                        'target' => $target,
                        'type' => 'none'
                    ];

                    foreach($this->getIsSetListCustomFields($lead) as $custom) {
                        if(
                            $custom['field_id'] == 709405 || // Продукт
                            $custom['field_id'] == 708651 || // Пакет
                            $custom['field_id'] == 702873 // Тариф
                        ) {
                            $customs[] = [
                                'lead_id' => $lead['id'],
                                'field_id' => $custom['field_id'],
                                'value' => $custom['values'][0]['value'],
                                'enum' => $custom['values'][0]['enum_id']
                            ];
                        }
                    }
                }
            }

            $types = $this->getContactsByIds($contacts);

            $i = 0;
            foreach($array as $lead) {
                $result = array_search($lead['contact'], array_column($types, 'id'));
                if($result >= 0) {
                    $lead['type'] = $types[$result]['type'];
                }
                $array[$i] = $lead;
                $i++;
            }

            ManagersLeadsSuccess::insert($array);
            ManagersLeadsSuccessCustom::insert($customs);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }

    }

    protected static function getTemplateInfo(): array {
        return [
            'leads_count' => 0, // Новых сделок

            'sum_month' => 0, // Выбран пакет 1
            'sum_package' => 0, // Выбран пакет от 1 до 2
            'sum_pro' => 0, // Выбран тариф PRO

            'count' => 0,
            'count_month' => 0, // Выбран пакет 1
            'count_package' => 0, // Выбран пакет от 1 до 2
            'count_pro' => 0, // Выбран тариф PRO

            'count_clients_month' => 0, // Выбран пакет 1
            'count_clients_package' => 0, // Выбран пакет от 1 до 2
            'count_clients_pro' => 0, // Выбран тариф PRO

            'children_ege' => 0, // Дети ЕГЭ (сумма бюджетов)
            'children_oge' => 0, // Дети ОГЭ (сумма бюджетов)
            'children_10' => 0, // Дети 10 класс (сумма бюджетов)
            'parents_ege' => 0, // Родители ЕГЭ (сумма бюджетов)
            'parents_oge' => 0, // Родители ОГЭ (сумма бюджетов)
            'parents_10' => 0, // Родители 10 класс (сумма бюджетов)

            'children_month_ege' => 0, // Дети ЕГЭ пакет 1 (сумма бюджетов)
            'children_month_oge' => 0, // Дети ОГЭ пакет 1 (сумма бюджетов)
            'children_month_10' => 0, // Дети 10 класс пакет 1 (сумма бюджетов)
            'parents_month_ege' => 0, // Родители ЕГЭ пакет 1 (сумма бюджетов)
            'parents_month_oge' => 0, // Родители ОГЭ пакет 1 (сумма бюджетов)
            'parents_month_10' => 0, // Родители 10 класс пакет 1 (сумма бюджетов)

            'children_package_ege' => 0, // Дети ЕГЭ пакет больше 1 (сумма бюджетов)
            'children_package_oge' => 0, // Дети ОГЭ пакет больше 1 (сумма бюджетов)
            'children_package_10' => 0, // Дети 10 класс пакет больше 1 (сумма бюджетов)
            'parents_package_ege' => 0, // Родители ЕГЭ пакет больше 1 (сумма бюджетов)
            'parents_package_oge' => 0, // Родители ОГЭ пакет больше 1 (сумма бюджетов)
            'parents_package_10' => 0, // Родители 10 класс пакет больше 1 (сумма бюджетов)

            'count_children_none' => 0, // Дети неизвестно (кол-во лидов)
            'count_children_ege' => 0, // Дети ЕГЭ (кол-во лидов)
            'count_children_oge' => 0, // Дети ОГЭ (кол-во лидов)
            'count_children_10' => 0, // Дети 10 класс (кол-во лидов)
            'count_parents_none' => 0, // Родители неизвестно (кол-во лидов)
            'count_parents_ege' => 0, // Родители ЕГЭ (кол-во лидов)
            'count_parents_oge' => 0, // Родители ОГЭ (кол-во лидов)
            'count_parents_10' => 0, // Родители 10 класс (кол-во лидов)

            'count_none_ege' => 0,
            'count_none_oge' => 0,
            'count_none_10' => 0,
            'count_none_none' => 0,

            'count_sale_children_ege' => 0, // Дети ЕГЭ (кол-во продаж)
            'count_sale_children_oge' => 0, // Дети ОГЭ (кол-во продаж)
            'count_sale_children_10' => 0, // Дети 10 класс (кол-во продаж)
            'count_sale_parents_ege' => 0, // Родители ЕГЭ (кол-во продаж)
            'count_sale_parents_oge' => 0, // Родители ОГЭ (кол-во продаж)
            'count_sale_parents_10' => 0, // Родители 10 класс (кол-во продаж)

            'unique_children_ege' => 0, // Уникальных ЕГЭ (Дети)
            'unique_children_oge' => 0, // Уникальных ОГЭ (Дети)
            'unique_children_10' => 0, // Уникальных 10 класс (Дети)
            'unique_parents_ege' => 0, // Уникальных ЕГЭ (Родители)
            'unique_parents_oge' => 0, // Уникальных ОГЭ (Родители)
            'unique_parents_10' => 0, // Уникальных 10 класс (Родители)

            'average_check' => 0, // Средний чек
            'average_check_children_ege' => 0, // Средний чек ЕГЭ (Дети)
            'average_check_children_oge' => 0, // Средний чек ОГЭ (Дети)
            'average_check_children_10' => 0, // Средний чек 10 класс (Дети)
            'average_check_parents_ege' => 0, // Средний чек ЕГЭ (Родители)
            'average_check_parents_oge' => 0, // Средний чек ОГЭ (Родители)
            'average_check_parents_10' => 0, // Средний чек 10 класс (Родители)

            'substandard_leads' => 0, // Некачественные клиенты
        ];
    }

    protected static function getPlanWeekPlan($size) {
        $size['plan']['plan_month_percent'] = isset($size['sum_month']) ? self::getPercent($size['sum_month'], $size['plan']['plan_month']) : null;
        $size['plan']['plan_month_remainder'] = isset($size['sum_month']) ? self::getRemainder($size['plan']['plan_month'], $size['sum_month']) : null;

        $size['plan']['plan_package_percent'] = isset($size['sum_package']) ? self::getPercent($size['sum_package'], $size['plan']['plan_package']) : null;
        $size['plan']['plan_package_remainder'] = isset($size['sum_package']) ? self::getRemainder($size['plan']['plan_package'], $size['sum_package']) : null;

        $size['plan']['plan_pro_percent'] = isset($size['count_pro']) ? self::getPercent($size['count_pro'], $size['plan']['plan_pro']) : null;
        $size['plan']['plan_pro_remainder'] = isset($size['count_pro']) ? self::getRemainder($size['plan']['plan_pro'], $size['count_pro']) : null;

        $size['plan']['plan_count_percent'] = isset($size['count']) ? self::getPercent($size['count'], $size['plan']['plan_count']) : null;
        $size['plan']['plan_count_remainder'] = isset($size['count']) ? self::getRemainder($size['plan']['plan_count'], $size['count']) : null;

        return $size;
    }

    public function getManagersInfo($date = []) {
        $managers = Manager::all();

        $pro = ManagersLeadsSuccessCustom::where('field_id', 702873)->where('enum', 425115)->get()->toArray();

        foreach(self::$pipelines as $pipeline) {
            $ret = [];

            $leads = ManagersLeads::where('pipeline_id', $pipeline)->get()->toArray();

            $leadsSuccess = ManagersLeadsSuccess::where('pipeline_id', $pipeline)->get();

            foreach($managers as $manager) {
                $el = self::getTemplateInfo();

                foreach($leads as $lead) {
                    if($lead['manager'] == $manager['id']) {
                        $el['leads_count'] = $el['leads_count'] + 1;

                        if($lead['type'] != null) {
                            if($lead['type'] == 'Ученик')
                                $lead['type'] = 'children';
                            else if($lead['type'] == 'Родитель')
                                $lead['type'] = 'parents';
                        } else {
                            $lead['type'] = 'none';
                        }

                        if($lead['course'] != null) {
                            if($lead['course'] == 'ege') {
                                $lead['course'] = 'ege';
                            } else if($lead['course'] == 'oge') {
                                $lead['course'] = 'oge';
                            } else if($lead['course'] == 'ten') {
                                $lead['course'] = '10';
                            }
                        } else {
                            $lead['course'] = 'none';
                        }


                        if($lead['type'] != 'none' && $lead['course'] != 'none') {
                            $el["count_{$lead['type']}_{$lead['course']}"] = $el["count_{$lead['type']}_{$lead['course']}"] + 1;

                            $el["count_sale_{$lead['type']}_{$lead['course']}"] = $el["count_sale_{$lead['type']}_{$lead['course']}"] + 1;
                        } else {
                            if($lead['type'] != 'none' && $lead['course'] == 'none') {
                                $el["count_{$lead['type']}_none"] = $el["count_{$lead['type']}_none"] + 1;
                            }
                            if($lead['course'] != 'none' && $lead['type'] == 'none') {
                                $el["count_none_{$lead['course']}"] = $el["count_none_{$lead['course']}"] + 1;
                            }
                            if($lead['type'] == 'none' && $lead['course'] == 'none') {
                                $el['count_none_none'] = $el['count_none_none'] + 1;
                            }
                        }

                        if($lead['target'] == 0)
                            $el['substandard_leads'] = $el['substandard_leads'] + 1;
                    }
                }

                $completed = [];

                $contacts = [
                    'all' => [],
                    'month' => [],
                    'package' => [],
                    'pro' => [],
                    'ege' => [],
                    'oge' => [],
                    'ten' => []
                ];

                foreach($leadsSuccess as $lead) {
                    if($lead['manager'] == $manager['id']) {
                        $completed[] = $lead;
                        $contacts['all'][] = $lead['contact'];
                    }

                }

                $unique = [
                    'children_ege' => [],
                    'children_oge' => [],
                    'children_ten' => [],
                    'parents_ege' => [],
                    'parents_oge' => [],
                    'parents_ten' => [],
                ];

                foreach($completed as $complete) {

                    $el['average_check'] = $el['average_check'] + $complete['price'];

                    $package = self::getPackage($complete['lead_id']);
                    if($package && $package > 0) {

                        $search_pro = array_search($complete['lead_id'], array_column($pro, 'lead_id'));

                        $product = $this->getProductByType($complete['lead_id'], $package, $complete['type']);

                        if($product['type'] != 'none' && $product['course'] != 'none') {
                            $el["{$product['type']}_{$product['course']}"] = $el["{$product['type']}_{$product['course']}"] + $complete['price'];

                            $el["{$product['type']}_{$product['package']}_{$product['course']}"] = $el["{$product['type']}_{$product['package']}_{$product['course']}"] + $complete['price'];

//                            $el["count_{$product['type']}_{$product['course']}"] = $el["count_{$product['type']}_{$product['course']}"] + 1;
//
//                            $el["count_sale_{$product['type']}_{$product['course']}"] = $el["count_sale_{$product['type']}_{$product['course']}"] + 1;

                            $el["average_check_{$product['type']}_{$product['course']}"] = $el["average_check_{$product['type']}_{$product['course']}"] + $complete['price'];

                            $unique["{$product['type']}_{$product['course']}"][] = $complete["contact"];
                        }


                        if($package == 1) {
                            // Месяц
                            $el['count_month'] = $el['count_month'] + 1;
                            $el['sum_month'] = $el['sum_month'] + $complete['price'];

                            $contacts['month'][] = $complete['contact'];
                        } else if($package >= 2) {
                            // Пакет
                            $el['count_package'] = $el['count_package'] + 1;
                            $el['sum_package'] = $el['sum_package'] + $complete['price'];
                            $contacts['package'][] = $complete['contact'];
                        }

                        if($search_pro > -1) {
                            $el['count_pro'] = $el['count_pro'] + 1;
                            $el['sum_pro'] = $el['sum_pro'] + $complete['price'];
                            $contacts['pro'][] = $complete['contact'];
                        }
                    }

                }

                $el['average_check_children_ege'] = $el['count_sale_children_ege'] > 0 ? $el['average_check_children_ege'] / $el['count_sale_children_ege'] : 0;
                $el['average_check_children_oge'] = $el['count_sale_children_oge'] > 0 ? $el['average_check_children_oge'] / $el['count_sale_children_oge'] : 0;
                $el['average_check_children_10'] = $el['count_sale_children_10'] > 0 ? $el['average_check_children_10'] / $el['count_sale_children_10'] : 0;
                $el['average_check_parents_ege'] = $el['count_sale_parents_ege'] > 0 ? $el['average_check_parents_ege'] / $el['count_sale_parents_ege'] : 0;
                $el['average_check_parents_oge'] = $el['count_sale_parents_oge'] > 0 ? $el['average_check_parents_oge'] / $el['count_sale_parents_oge'] : 0;
                $el['average_check_parents_10'] = $el['count_sale_parents_10'] > 0 ? $el['average_check_parents_10'] / $el['count_sale_parents_10'] : 0;

                $unique = [
                    'children_ege' => array_unique($unique['children_ege']),
                    'children_oge' => array_unique($unique['children_oge']),
                    'children_ten' => array_unique($unique['children_ten']),
                    'parents_ege' => array_unique($unique['parents_ege']),
                    'parents_oge' => array_unique($unique['parents_oge']),
                    'parents_ten' => array_unique($unique['parents_ten']),
                ];

                $el['unique_children_ege'] = sizeof($unique['children_ege']);
                $el['unique_children_oge'] = sizeof($unique['children_oge']);
                $el['unique_children_10'] = sizeof($unique['children_ten']);
                $el['unique_parents_ege'] = sizeof($unique['parents_ege']);
                $el['unique_parents_oge'] = sizeof($unique['parents_oge']);
                $el['unique_parents_10'] = sizeof($unique['parents_ten']);

                $contacts = [
                    'all' => array_unique($contacts['all']),
                    'month' => array_unique($contacts['month']),
                    'package' => array_unique($contacts['package']),
                    'pro' => array_unique($contacts['pro']),
                    'ege' => array_unique($contacts['ege']),
                    'oge' => array_unique($contacts['oge']),
                    'ten' => array_unique($contacts['ten']),
                ];

                $el['count_clients_month'] = sizeof($contacts['month']);
                $el['count_clients_package'] = sizeof($contacts['package']);
                $el['count_clients_pro'] = sizeof($contacts['pro']);

                $el['count'] = $el['count_month'] + $el['count_package'] + $el['count_pro'];

                // -----
                $managers_leads = ManagersLeads::where('manager', $manager['id'])->get()->toArray();

                $count_children_ege = sizeof(array_filter($managers_leads, function($el) {
                    if($el['type'] == 'Ученик' && $el['course'] == 'ege') return true; else return false;
                }));
                $count_children_oge = sizeof(array_filter($managers_leads, function($el) {
                    if($el['type'] == 'Ученик' && $el['course'] == 'oge') return true; else return false;
                }));
                $count_children_10 = sizeof(array_filter($managers_leads, function($el) {
                    if($el['type'] == 'Ученик' && $el['course'] == 'ten') return true; else return false;
                }));
                $count_children_none = sizeof(array_filter($managers_leads, function($el) {
                    if($el['type'] == 'Ученик' && $el['course'] == 'none') return true; else return false;
                }));

                $count_parents_ege = sizeof(array_filter($managers_leads, function($el) {
                    if($el['type'] == 'Родитель' && $el['course'] == 'ege') return true; else return false;
                }));
                $count_parents_oge = sizeof(array_filter($managers_leads, function($el) {
                    if($el['type'] == 'Родитель' && $el['course'] == 'oge') return true; else return false;
                }));
                $count_parents_10 = sizeof(array_filter($managers_leads, function($el) {
                    if($el['type'] == 'Родитель' && $el['course'] == 'ten') return true; else return false;
                }));
                $count_parents_none = sizeof(array_filter($managers_leads, function($el) {
                    if($el['type'] == 'Родитель' && $el['course'] == 'none') return true; else return false;
                }));

                $el['count_children_ege'] = $el['count_children_ege'] + $count_children_ege;
                $el['count_children_oge'] = $el['count_children_oge'] + $count_children_oge;
                $el['count_children_10'] = $el['count_children_10'] + $count_children_10;
                $el['count_children_none'] = $el['count_children_none'] + $count_children_none;
                $el['count_parents_ege'] = $el['count_parents_ege'] + $count_parents_ege;
                $el['count_parents_oge'] = $el['count_parents_oge'] + $count_parents_oge;
                $el['count_parents_10'] = $el['count_parents_10'] + $count_parents_10;
                $el['count_parents_none'] = $el['count_parents_none'] + $count_parents_none;


                if($el['count'] > 0)
                    $el['average_check'] = $el['average_check'] / $el['count'];

                $el['day'] = $date['day'];
                $el['month'] = $date['month'];
                $el['month_name'] = $date['month_name'];
                $el['year'] = $date['year'];
                $el['manager_id'] = $manager['id'];
                $el['manager_name'] = $manager['name'];
                $el['pipeline_id'] = $pipeline;

                $ret[] = $el;

            }

            ManagersInfo::insert($ret);
        }
    }

    public function managers() {
        $userGroupId = 385195; // Группа пользователей "Колл-центр"
        $list = $this->getAllListByFilter('users', "&with=group");
        $data = [];
        foreach($list as $user) {
            if($user['rights']['is_active'] && isset($user['rights']) && isset($user['rights']['group_id']) && $user['rights']['group_id'] == $userGroupId) {

                $data[] = [
                    'id' => $user['id'],
                    'name' => $user['name']
                ];
            }
        }

        return Manager::upsert($data, ['id'], ['name']);
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
            5084302, // Продление КЦ
            5322871 // Чатики
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
            5084302, // Продление КЦ
            5322871 // Чатики
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
                    $custom['field_id'] == 708651 || // Пакет
                    $custom['field_id'] == 702873 // Тариф
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

    public function start() {
        $yesterday = strtotime("-1 day");
        $yesterdayStart = strtotime(date('d.m.Y', $yesterday) . " 00:00:01");
        $yesterdayEnd = strtotime(date('d.m.Y', $yesterday) . " 23:59:59");

//        $yesterdayStart = strtotime("28.06.2022 00:00:01");
//        $yesterdayEnd = strtotime("28.06.2022 23:59:59");

//        if($request->has('date')) {
//            $yesterdayStart = strtotime("{$request->input('date')} 00:00:01");
//            $yesterdayEnd = strtotime("{$request->input('date')} 23:59:59");
//        }

        $this->getAndSetUsers();

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

        $this->clearUserTable();


        return "ok";
    }

    public function getInfoByManager($dateArray) {
        $pipelines = [
            3493222, // Первичные КЦ
            5084302, // Продление КЦ
            5322871 // Чатики
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
                $sum['conversion'] = $sum['all'] > 0 ? (round($sum['countBuy'] / $sum['all'], 1) * 100) : 0;
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

    public function getToDesktop($pipeline, $month, $year) {

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
        $users = self::groupDaysToManagers(Report::where('month', $date[0])->where('year', $date[1]) ->where('pipelineId', $pipeline)->get());

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
            'pipeline' => $pipeline
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

    public function getContactVkId($contactId) {
        return $this->amoGet("/contacts/{$contactId}");
    }

    public function newTalk(Request $request) {
//        if($request->has('talk')) {
//            $talk = $request->input('talk');
//            if(isset($talk['add']) && is_array($talk['add']) && sizeof($talk['add']) > 0 && isset($talk['add'][0]['origin']) && $talk['add'][0]['origin'] == "pact.vkgroup") {
//
//                $companyId = $talk['add'][0]['contact_id'];
//
//
//
////                if($el = Talks::where('companyId', $companyId)->first()) {
////
////                } else {
//////                    $vk = 0;
//////
//////                    $contact = $this->getContactVkId($companyId);
//////
//////                    if(isset($contact['custom_fields_values']) && is_array($contact['custom_fields_values']) && sizeof($contact['custom_fields_values']) > 0) {
//////                        foreach($contact['custom_fields_values'] as $custom) {
//////                            if($custom['field_id'] == 708615 && $custom['values'][0]['value'] !== '') {
//////                                $vk = intval(preg_replace("/[^,.0-9]/", '', $custom['values'][0]['value']));
//////                            }
//////                        }
//////                    }
////
////
////                }
//
//                $el = new Talks();
//                $el->__set('companyId', $talk['add'][0]['contact_id']);
//                $el->__set('vk', 0);
//                $el->__set('talkId', $talk['add'][0]['talk_id']);
//                $el->save();
//
//            }
//        } else if($request->has('contacts')) {
//            $contact = $request->input('contacts');
//            if(isset($contact['update']) && isset($contact['update'][0]) && isset($contact['update'][0]['custom_fields'])) {
//
//                if(Talks::where('companyId', $contact['update'][0]['id'])->first()) {
//                    foreach($contact['update'][0]['custom_fields'] as $c) {
//                        if($c['id'] == 708615) {
//                            $vk = intval(preg_replace("/[^,.0-9]/", '', $c['values'][0]['value']));
//                            $el = Talks::where('companyId', $contact['update'][0]['id'])->first();
//                            $el->__set('vk', $vk);
//                            $el->save();
//
////                            Telegram::sendMessage([
////                                'chat_id' => '228519769',
////                                'text' => "Обновили в строке {$el['id']}"
////                            ]);
//                            break;
//                        }
//                    }
//                }
//            }
//
//        }

        return "Ok";
    }

    protected function getPactConversationsById($id) {
        return Http::withHeaders([
            'X-Private-Api-Token' => '6508a1ce76aa7593ba53b55cdba7647e9f58748439c29eb11669eaa525f9e680f1f7b1e93f09e6b79cad9238c2055c25f4d912f3209de95bab1b2df5dfbe026c',
        ])->get("https://api.pact.im/p1/companies/60531/conversations/{$id}");
    }

    public function pactNewMessage(Request $request) {
//        Telegram::sendMessage([
//            'chat_id' => '228519769',
//            'text' => json_encode($request->all())
//        ]);
//        if($request->has('event') && $request->input('event') == 'new' && $request->has('type') && $request->input('type') == 'message') {
//            $message = $request->input('data');
//
//            if(
//                isset($message['income']) &&
//                isset($message['channel_type']) &&
//                $message['channel_type'] == 'vkontakte'
//            ) {
//                if($message['income'] > 0) {
//                    // Входящее
//                } else {
//                    $con = $this->getPactConversationsById($message['conversation_id']);
//
//                    if(isset($con['data']) && isset($con['data']['conversation']) && isset($con['data']['conversation']['contacts']) && isset($con['data']['conversation']['contacts'][0]) && isset($con['data']['conversation']['contacts'][0]['amocrm_contact_id']) && $con['data']['conversation']['contacts'][0]['amocrm_contact_id'] > 0) {
//                        $contact_id = $con['data']['conversation']['contacts'][0]['amocrm_contact_id'];
//
//                        if($el = Talks::where('companyId', $contact_id)->first()) {
//
//                            Telegram::sendMessage([
//                                'chat_id' => '228519769',
//                                'text' => "Закрыта {$el['companyId']} - {$el['talkId']} " . json_encode($request->all())
//                            ]);
//
////                            Telegram::sendMessage([
////                                'chat_id' => '228519769',
////                                'text' => "Закрыта {$el['companyId']} - {$el['talkId']}"
////                            ]);
//                            $this->closeTalk($el['talkId']);
//                            $el->delete();
//                        }
//                    }
//
////                    $contact_id = $message['contact_id'];
////
//
//                }
//            }
//
//        }

        return "Ok";
    }

    public function senler(Request $request): string {

        // Telegram::sendMessage(['chat_id' => '-698970732', 'text' => json_encode($request->all())]);

        if($request->has('vk_user_id') && $request->has('object')) {
            $el = new Senler();
            $el->__set('vkId', $request->input('vk_user_id'));
            $el->__set('vkGroupId', $request->input('vk_group_id'));
            $el->__set('subscriptions', $request->input('subscriptions'));
            $el->__set('utm', json_encode($request->input('object')));
            $el->__set('update', time());
            $item = $el->save();

            // Telegram::sendMessage(['chat_id' => '-698970732', 'text' => 'Добавлен объект ' . json_encode($item)]);
        }

        return "Ok";
    }

    public function getSenlerQueues() {
        $elements = Senler::where('update', '<=', time() - 14400)->get();

        try {
            $leadsSuccess = [];

            if(sizeof($elements) > 0) {
                foreach($elements as $el) {
                    if($contact = $this->getUserByVkId($el['vkId'])) {

                        $el['utm'] = json_decode($el['utm']);

                        $senlerData = SenlerController::post('subscriptions/get', $el['vkGroupId'], [
                            'subscription_id' => [ $el['subscriptions'] ]
                        ]);

                        if(isset($senlerData['items']) && is_array($senlerData['items']) && isset($senlerData['items'][0]) && isset($senlerData['items'][0]['name'])) {
                            $subscriptionName = $senlerData['items'][0]['name'];

                            $customFields = [];

                            foreach($el['utm'] as $k => $v) {
                                if(gettype($v) != 'string')
                                    continue;

                                if($k == 'openstat_source') $customFields[] = [ 'field_id' => 278347, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == 'yclid') $customFields[] = [ 'field_id' => 278355, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == 'utm_campaign') $customFields[] = [ 'field_id' => 278325, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == 'utm_content') $customFields[] = [ 'field_id' => 278329, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == 'utm_source') $customFields[] = [ 'field_id' => 278321, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == 'utm_medium') $customFields[] = [ 'field_id' => 278323, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == 'utm_term') $customFields[] = [ 'field_id' => 278327, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == 'utm_referrer') $customFields[] = [ 'field_id' => 278331, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == 'roistat') $customFields[] = [ 'field_id' => 278337, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == '_ym_counter') $customFields[] = [ 'field_id' => 278335, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == '_ym_uid') $customFields[] = [ 'field_id' => 278333, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == 'openstat_service') $customFields[] = [ 'field_id' => 278341, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == 'referrer') $customFields[] = [ 'field_id' => 278339, 'values' => [ [ 'value' => $v ] ]];
                                else if($k == 'fbclid') $customFields[] = [ 'field_id' => 278357, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == 'gclid') $customFields[] = [ 'field_id' => 278353, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == 'gclientid') $customFields[] = [ 'field_id' => 278351, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == 'from') $customFields[] = [ 'field_id' => 278349, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == 'openstat_ad') $customFields[] = [ 'field_id' => 278345, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == 'openstat_campaign') $customFields[] = [ 'field_id' => 278343, 'values' => [ [ 'value' => $v ] ] ];
                                else if($k == 'ga_utm') $customFields[] = [ 'field_id' => 710919, 'values' => [ [ 'value' => $v ] ] ];
                            }

                            $leads = [];
                            if(isset($contact['_embedded']) && isset($contact['_embedded']['leads']) && is_array($contact['_embedded']['leads'])) {

                                foreach($contact['_embedded']['leads'] as $lead) {
                                    $entity = $this->getLeadByID($lead['id']);
                                    $leads[] = $entity;
                                }
                            }

                            $leadID = 0;
                            $tags = [];
                            foreach($leads as $e) {
                                if(isset($e['_embedded']) && isset($e['_embedded']['tags'])) {
                                    foreach($e['_embedded']['tags'] as $t) {
                                        if($t['name'] == config("app.services.senler.groups.{$el['vkGroupId']}.name")) {
                                            $leadID = $e['id'];
                                            foreach($e['_embedded']['tags'] as $e) {
                                                $tags[] = [
                                                    'id' => $e['id'],
                                                    'name' => $e['name'],
                                                ];
                                            }
                                            break;
                                        }
                                    }
                                }
                            }

                            if($leadID > 0) {
                                $leadsSuccess[] = $leadID;
                                $tags[] = [
                                    'name' => $subscriptionName
                                ];
                                $this->amoPut("/leads", [
                                    [
                                        'id' => $leadID,
                                        'custom_fields_values' => $customFields,
                                        '_embedded' => [
                                            'tags' => $tags,
                                        ],
                                    ]
                                ]);
                                $el->delete();
                            } else {
                                $item = Senler::find($el['id']);
                                $item->__set('update', time());
                                $item->save();
                            }

                        }
                    }
                }
            }

            if(sizeof($leadsSuccess) > 0)
                Telegram::sendMessage(['chat_id' => '-698970732', 'text' => 'Обработаны сделки: ' . json_encode($leadsSuccess)]);
            else
                Telegram::sendMessage(['chat_id' => '-698970732', 'text' => 'Подходящих сделок не найдено']);

            return "Ok";
        } catch(\Exception $e) {
            Telegram::sendMessage(['chat_id' => '-698970732', 'text' => 'Ошибка: ' . $e->getMessage()]);
        }

    }

    protected function getLeadsByTag($userID, string $tag) {
        $result = $this->amoGet("/leads?query={$tag}&filter[]");
        if(isset($result['_embedded']) && isset($result['_embedded']['leads']) && is_array($result['_embedded']['leads'])) {
            return $result['_embedded']['leads'];
        }
        return $result;
    }

    public function getUserByVkId($id) {
        $res = $this->amoGet("/contacts?query={$id}&with=leads");

        if(isset($res['_embedded']) && isset($res['_embedded']['contacts']) && is_array($res['_embedded']['contacts'])) {
            foreach($res['_embedded']['contacts'] as $contact) {
                if(isset($contact['custom_fields_values']) && is_array($contact['custom_fields_values'])) {
                    foreach($contact['custom_fields_values'] as $custom) {
                        if($custom['field_id'] == 708615) {
                            $vk = preg_replace("/[^0-9]/", '', $custom['values'][0]['value']);
                            if((int) $vk == (int) $id) {
                                return $contact;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    public function getTestCSV() {
        $ret = 0;
        for($i=601;$i<=700;$i++) {

            $query = "/leads?page={$i}&limit=250&filter[created_at][from]=1593561601&filter[created_at][to]=1657670399&filter[statuses][0][pipeline_id]=3493222&&filter[statuses][0][status_id]=143&with=contacts";
            $res = $this->amoGet($query);
            $list = self::getIsSetList($res, 'leads');

            if(sizeof($list) > 0) {
                $str = [];


                foreach($list as $lead) {
                    $contact = '';
                    $prichina = '';
                    $oi = '';
                    $napr = '';
                    $predmet = '';

                    if(isset($lead['_embedded']) && isset($lead['_embedded']['contacts']) && isset($lead['_embedded']['contacts'][0]) && isset($lead['_embedded']['contacts'][0]['id']))
                        $contact = $lead['_embedded']['contacts'][0]['id'];

                    if(isset($lead['custom_fields_values']) && is_array($lead['custom_fields_values'])) {
                        foreach($lead['custom_fields_values'] as $custom) {
                            if($custom['field_id'] == 702227) $prichina = $custom['values'][0]['value'];
                            if($custom['field_id'] == 707155) $oi = $custom['values'][0]['value'];
                            if($custom['field_id'] == 702875) $napr = $custom['values'][0]['value'];
                            if($custom['field_id'] == 709399) $predmet = $custom['values'][0]['value'];
                        }
                    }

                    $str[] = [
                        $lead['id'], date('d.m.Y', $lead['closed_at']), $contact, $prichina, $oi, $napr, $predmet
                    ];
                }

                $fp = fopen(__DIR__ . '/test.csv', 'a');
                foreach ($str as $fields) {
                    fputcsv($fp, $fields, ';');
                }
                fclose($fp);
                unset($list);
                unset($fp);
                unset($str);
            } else {
                $ret = $i;
                break;
            }
        }

        return $ret;


    }

    public function getCsv() {
        $csv = [];
        $date = 1561939202;
        $date_to = time();
        $contacts = [];

        for($i=1201;$i<1600;$i++) {
            $query = "/contacts?page={$i}&limit=250";
            $res = $this->amoGet($query);
            $list = self::getIsSetList($res, 'contacts');
            if(sizeof($list) > 0) {
                $arr = [];
                foreach($list as $lead) {
                    $phone = '';
                    $email = '';
                    $leadType = '';
                    $city = '';
                    $class = '';
                    $vk = '';

                    if(isset($lead['custom_fields_values']) && is_array($lead['custom_fields_values']))
                        foreach($lead['custom_fields_values'] as $custom) {
                            if($custom['field_id'] == 176801) {
                                $phone = '';
                                foreach($custom['values'] as $value) {
                                    $phone .= $value['value'] . ',';
                                }
                            }
                            if($custom['field_id'] == 176803) {
                                $email = $custom['values'][0]['value'];
                            }
                            if($custom['field_id'] == 707467) {
                                $leadType = $custom['values'][0]['value'];
                            }
                            if($custom['field_id'] == 708585) {
                                $city = $custom['values'][0]['value'];
                            }
                            if($custom['field_id'] == 709749) {
                                $class = $custom['values'][0]['value'];
                            }
                            if($custom['field_id'] == 708615) {
                                $vk = $custom['values'][0]['value'];
                            }
                        }

                    $arr[] = [
                        $lead['id'],
                        "{$lead['first_name']} {$lead['last_name']}",
                        $email,
                        $phone,
                        $leadType,
                        $city,
                        $class,
                        $vk
                    ];
                }

                if(sizeof($arr) > 0) {
                    try {
                        $fp = fopen(__DIR__ . '/contact.csv', 'a+');
                        foreach ($arr as $fields) {
                            fputcsv($fp, $fields, ';');
                        }
                        fclose($fp);
                    } catch (\Exception $e) {
                        return 'Error';
                    }

                }
                unset($arr);
                unset($list);
            } else
                break;
        }


        return "Ok";
    }
}
