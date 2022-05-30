<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ReportCustom extends Model {
    use HasFactory;

    protected $table = 'report_custom';

    protected $id;
    protected $type;
    protected $day;
    protected $month;
    protected $year;
    protected $value;

}
