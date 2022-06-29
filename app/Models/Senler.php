<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Senler extends Model {
    use HasFactory;

    protected $table = 'senler';

    protected int $id;
    protected int $vkId;
    protected int $vkGroupId;
    protected int $subscriptions;
    protected string $utm;
    protected int $update;

}
