<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ManagersLeadsSuccess extends Model {
    use HasFactory;

    protected $table = 'managers_leads_success';

    protected int $id;
    protected int $lead_id;
    protected int $price;
    protected int $manager;
    protected int $pipeline_id;
    protected int $status_id;
    protected int $created;
    protected bool $target;
    protected string $type;

}
