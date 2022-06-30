<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomApiException;
use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AmoCrmController;

class SenlerController extends Controller {

    public function __construct() {}

    public function getSubscriptions() {

    }

    protected static function getApiKeyByGroupId($groupId) {
        return config("app.services.senler.groups.{$groupId}.key") ?? false;
    }

    public static function post(string $uri, int $groupId, array $params = []) {
        $params['vk_group_id'] = $groupId;
        $params['access_token'] = self::getApiKeyByGroupId($groupId);
        $params['v'] = 2;
        return Http::asForm()->post("https://senler.ru/api/{$uri}", $params);
    }

}
