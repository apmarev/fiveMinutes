<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Report extends Model {
    use HasFactory;

    protected $table = 'reports';

    protected $id;
    protected $manager;
    protected $monthName;
    protected $day;
    protected $month;
    protected $year;
    protected $pipelineId;
    protected $all;

    protected $monthExam;
    protected $monthOge;
    protected $monthTenClass;
    protected $packageExam;
    protected $packageOge;
    protected $packageTenClass;
    protected $countPackagesExam;
    protected $countPackagesOge;
    protected $countPackagesTenClass;
    protected $countPriceMonth;
    protected $countPricePackage;
    protected $countMonth;
    protected $countPackage;
    protected $countTen;
    protected $averageCheck;
    protected $conversion;

}
