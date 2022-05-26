<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class LeadCustom extends Model {
    use HasFactory;

    protected $table = 'leads_custom';

    protected $id;
    protected $leadId;
    protected $fieldId;
    protected $value;
    protected $enum;

}
