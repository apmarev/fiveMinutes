<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class LeadCount extends Model {
    use HasFactory;

    protected $table = 'leads_count';

    protected $id;
    protected $userId;
    protected $pipelineId;

}
