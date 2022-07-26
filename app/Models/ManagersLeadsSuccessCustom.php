<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ManagersLeadsSuccessCustom extends Model {
    use HasFactory;

    protected $table = 'managers_leads_success_custom';

    protected $id;
    protected $lead_id;
    protected $field_id;
    protected $value;
    protected $enum;
}
