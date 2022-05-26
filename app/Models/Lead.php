<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Lead extends Model {
    use HasFactory;

    protected $table = 'leads';

    protected $id;
    protected $leadId;
    protected $price;
    protected $userId;
    protected $statusId;
    protected $pipelineId;
    protected $createdAt;

}
