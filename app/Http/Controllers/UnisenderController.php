<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Illuminate\Support\Facades\Http;

class UnisenderController extends Controller {

    public $key = '61dqbaqcca5kb6uwkj9om7cnxi1pyib5hyt331qe';

    public function getWebVersion($campaign_id) {
        $res = Http::get("https://api.unisender.com/ru/api/getWebVersion?format=json&api_key={$this->key}&campaign_id={$campaign_id}");
        return $res['result'];
    }

    public function getCampaignCommonStats($campaign_id) {
        $res = Http::get("https://api.unisender.com/ru/api/getCampaignCommonStats?format=json&api_key={$this->key}&campaign_id={$campaign_id}");
        return $res['result'];
    }

    public function get() {
        $return = [];

        $res = Http::get("https://api.unisender.com/ru/api/getLists?format=json&api_key={$this->key}");
        $list = $res['result'];

        $dateFrom = "2000-01-01 00:00:01";
        $dateNow = date('Y-m-d h:i:s');
        $res = Http::get("https://api.unisender.com/ru/api/getCampaigns?format=json&api_key={$this->key}&from={$dateFrom}&to={$dateNow}");
        $items = $res['result'];


        foreach($items as $item) {
            $index = array_search($item['list_id'], array_column($list, 'id'));
            $getCampaignCommonStats = $this->getCampaignCommonStats($item['id']);
            $web = $this->getWebVersion($item['id']);

//            $return[] = [
//                'start_time' => $item['start_time'],
//                'status' => $item['status'],
//                'list' => $list[$index]['title'],
//                'subject' => $item['subject'],
//                'stats_url' => $item['stats_url'],
//                'sent' => $getCampaignCommonStats['sent'],
//                'delivered' => $getCampaignCommonStats['delivered'],
//                'read_unique' => $getCampaignCommonStats['read_unique'],
//                'clicked_unique' => $getCampaignCommonStats['clicked_unique'],
//                'WebLetterUrl' => $web['web_letter_link'],
//            ];

            $return[] = [
                $item['start_time'],
                $item['status'],
                $list[$index]['title'],
                $item['subject'],
                $item['stats_url'],
                $getCampaignCommonStats['sent'],
                $getCampaignCommonStats['delivered'],
                $getCampaignCommonStats['read_unique'],
                $getCampaignCommonStats['clicked_unique'],
                $web['web_letter_link'],
            ];
        }

        return $return;
    }

    public function getSheet() {
        $googleAccountKeyFilePath = __DIR__ . '/sheet.json';
        putenv( 'GOOGLE_APPLICATION_CREDENTIALS=' . $googleAccountKeyFilePath );
        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();

        $client->addScope( 'https://www.googleapis.com/auth/spreadsheets' );

        $service = new Google_Service_Sheets( $client );
        $spreadsheetId = '12kWr4cA3U_1jbSim8iP6wkgQIPLxTeCz6dho7OAimGU';
//        $response = $service->spreadsheets->get($spreadsheetId);
//        $spreadsheetProperties = $response->getProperties();
//        $range = 'Лист1!A1:J1000';
//        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
//
//        $line = sizeof($response) + 1;

        $body = new Google_Service_Sheets_ValueRange( [ 'values' => $this->get() ] );
        $options = array( 'valueInputOption' => 'RAW' );
        $service->spreadsheets_values->update( $spreadsheetId, 'Лист1!A2', $body, $options );

        return "Ok";
    }

}
