<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Talks extends Model {
    use HasFactory;

    protected $table = 'talks';

    protected $id;
    protected $contactId;
    protected $talkId;
    protected $vk;
}
