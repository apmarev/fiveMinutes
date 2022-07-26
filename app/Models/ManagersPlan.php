<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ManagersPlan extends Model {
    use HasFactory;

    protected $table = 'managers_plan';

    protected int $id;

    protected int $manager_id;
    protected int $year;
    protected int $month;
    protected int $week;
    protected int $pipeline_id;

    protected int $month_sum;
    protected int $package_sum;
    protected int $month_count;
    protected int $package_count;
    protected int $pro_count;
    protected int $count;
}
