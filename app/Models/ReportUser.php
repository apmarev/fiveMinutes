<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ReportUser extends Model {
    use HasFactory;

    protected $table = 'report_users';

    protected int $id;
    protected string $login;
    protected string $password;
    protected bool $super;
    protected string $token;
}
