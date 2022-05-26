<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Message extends Model {
    use HasFactory;

    protected $table = 'messages';

    protected $id;
    protected $talkId;
    protected $chatId;
    protected $time;
    protected $timeUpdate;

}
