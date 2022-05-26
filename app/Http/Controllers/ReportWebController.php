<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomApiException;
use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AmoCrmController;

class ReportWebController extends Controller {

    public function report() {

        $footer = AmoCrmController::getMonthAndYears();
        $footer['year'] = date('Y');
        $footer['month'] = date('m');

        return view('report', [
            'pipeline' => '',
            'footer' => $footer,
            'managers' => AmoCrmController::getToDesktop()
        ]);
    }

}
