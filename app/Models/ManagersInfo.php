<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ManagersInfo extends Model {
    use HasFactory;

    protected $table = 'managers_info';

    protected int $id;

    protected int $manager_id;
    protected string $manager_name;
    protected int $day;
    protected int $month;
    protected string $month_name;
    protected int $year;
    protected int $pipeline_id;

    protected int $leads_count;
    protected int $sum_month;
    protected int $sum_package;
    protected int $sum_pro;
    protected int $count;
    protected int $count_month;
    protected int $count_package;
    protected int $count_pro;
    protected int $count_clients_month;
    protected int $count_clients_package;
    protected int $count_clients_pro;
    protected int $children_ege;
    protected int $children_oge;
    protected int $children_10;
    protected int $parents_ege;
    protected int $parents_oge;
    protected int $parents_10;
    protected int $children_month_ege;
    protected int $children_month_oge;
    protected int $children_month_10;
    protected int $parents_month_ege;
    protected int $parents_month_oge;
    protected int $parents_month_10;
    protected int $children_package_ege;
    protected int $children_package_oge;
    protected int $children_package_10;
    protected int $parents_package_ege;
    protected int $parents_package_oge;
    protected int $parents_package_10;
    protected int $count_children_none;
    protected int $count_children_ege;
    protected int $count_children_oge;
    protected int $count_children_10;
    protected int $count_parents_none;
    protected int $count_parents_ege;
    protected int $count_parents_oge;
    protected int $count_parents_10;
    protected int $count_sale_children_ege;
    protected int $count_sale_children_oge;
    protected int $count_sale_children_10;
    protected int $count_sale_parents_ege;
    protected int $count_sale_parents_oge;
    protected int $count_sale_parents_10;
    protected int $unique_children_ege;
    protected int $unique_children_oge;
    protected int $unique_children_10;
    protected int $unique_parents_ege;
    protected int $unique_parents_oge;
    protected int $unique_parents_10;
    protected int $average_check;
    protected int $average_check_children_ege;
    protected int $average_check_children_oge;
    protected int $average_check_children_10;
    protected int $average_check_parents_ege;
    protected int $average_check_parents_oge;
    protected int $average_check_parents_10;
    protected int $substandard_leads;

}
