<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ManagersLeads extends Model {
    use HasFactory;

    protected $table = 'managers_leads';

    protected int $id;
    protected int $manager;
    protected int $pipeline_id;
    protected bool $target;
    protected int $contact;
    protected string $type;
    protected float $package;
    protected string $course;

    protected $fillable = ['name'];
}
