<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pregunta;
use App\Models\Respuesta;
use App\Models\Evaluado;
use App\Services\EncuestaService;

use Google\Client;
use Revolution\Google\Sheets\Facades\Sheets;

class GoogleController extends Controller
{
    public function index(Request $request)
    {
        $this->test();
        exit;

        // $sheets = Sheets::spreadsheet('1Ad6_LOODaJsh_g0hXgIWqhjW0DZcajybfTJ1oBEVZcw')
            // ->all();
        // $sheets = Sheets::spreadsheet(env('sheets.post_spreadsheet_id'))

        //                 ->sheetById(config('sheets.post_sheet_id'))

        //                 ->all();


        // $header = $sheets->pull(0);


        // $posts = Sheets::collection($header, $sheets);

        // $posts = $posts->reverse()->take(10);

        // $this->getClient();


        // Get the API client and construct the service object.
        $client = $this->getClient();
        $service = new \Google\Service\Sheets($client);

        // Prints the names and majors of students in a sample spreadsheet:
        // https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
        // $spreadsheetId = '1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms';
        // $range = 'Class Data!A2:E';
        $spreadsheetId = '1Ad6_LOODaJsh_g0hXgIWqhjW0DZcajybfTJ1oBEVZcw';
        $range = 'Data!A:Z';
        
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        
        $sheets = $service->spreadsheets->get($spreadsheetId)->getSheets();
        print_r(json_encode($sheets));
        $values = $response->getValues();

        if (empty($values)) {
            print "No data found.\n";
        } else {
            print "Name, Major:\n";
            foreach ($values as $row) {
            //     // Print columns A and E, which correspond to indices 0 and 4.
                print_r($row);
            //     print '<br>';
            }
        }

        
        // $batchUpdateRequest = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest(array(
        //     'requests' => array(
        //       'deleteDimension' => array(
        //           'range' => array(
        //               'sheetId' => 0, // the ID of the sheet/tab shown after 'gid=' in the URL
        //               'dimension' => "ROWS",
        //               'startIndex' => 2, // row number to delete
        //               'endIndex' => 1000
        //           )
        //       )    
        //     )
        // ));
        
        // $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);

        $range = "Data!A1"; /// the first cell on the first sheet.. (it's really titleOfSheet! but it's called Sheet1 by default)
        $myValue = "Hola mundo";
        $updateBody = new \Google\Service\Sheets\ValueRange([
            'range' => $range,
            'majorDimension' => 'ROWS',
            'values' => [
              ['a','b','c'],
              ['a','b','c']
            ]
        ]);

        $service->spreadsheets_values->update(
            $spreadsheetId,
            $range,
            $updateBody,
            ['valueInputOption' => "RAW"]
        );
    }

    public function test()
    {
        $pending = EncuestaService::listPending();
        foreach($pending as $p){
            $result = [];
            echo $p->google_sheet.'<br>';
            // echo $p->encuesta_id.'<br>';

            $data = Evaluado::where('encuesta_id', $p->encuesta_id)->where('anulada', 0)->get();
            $dataPregunta = Pregunta::where('encuesta_id', $p->encuesta_id)->whereNull('categoria_id')->orderBy('orden', 'asc')->get();
            $preguntaIds = [];

            foreach($dataPregunta as $pr){
                $result[0][] = $pr->nombre;
                $preguntaIds[] = $pr->id;
            }

            foreach($data as $d){
                // Evaluado
                $resultPregunta = [];
                
                $respuesta = Respuesta::whereIn('pregunta_id', $preguntaIds)
                    ->where('evaluado_id', $d->id)
                    ->where('encuesta_id', $d->encuesta_id)
                    ->get();

                foreach($dataPregunta as $pr){
                    foreach($respuesta as $r){
                        if($pr->id == $r->pregunta_id){
                            $resultPregunta[] = $r->respuesta;
                        }
                    }
                }

                if($resultPregunta) $result[] = $resultPregunta;
            }
            // Evaluado::where('encuesta_id', $p->encuesta_id)->whereNull('fecha_sincronizacion')->update(['fecha_sincronizacion' => date('Y-m-d H:i:s')]);
            $this->clearSheet($p->google_sheet, 0);
            $this->updateSheet($p->google_sheet, 0, $result);
            echo '<pre>';
            print_r($result);
        }
    }

    private function updateSheet($spreadsheetId, $sheedId, $data)
    {
        $client = $this->getClient();
        $service = new \Google\Service\Sheets($client);

        $range = "Data!A1"; /// the first cell on the first sheet.. (it's really titleOfSheet! but it's called Sheet1 by default)
        $updateBody = new \Google\Service\Sheets\ValueRange([
            'range' => $range,
            'majorDimension' => 'ROWS',
            'values' => $data
        ]);

        $service->spreadsheets_values->update(
            $spreadsheetId,
            $range,
            $updateBody,
            ['valueInputOption' => "RAW"]
        );
    }

    private function clearSheet($spreadsheetId, $sheedId)
    {
        $client = $this->getClient();
        $service = new \Google\Service\Sheets($client);

        $range = 'Data!A:Z';

        $response = $service->spreadsheets_values->get($spreadsheetId, $range);

        $values = $response->getValues();

        if(count($values) > 1){
            $batchUpdateRequest = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest(array(
                'requests' => array(
                  'deleteDimension' => array(
                      'range' => array(
                          'sheetId' => $sheedId, // the ID of the sheet/tab shown after 'gid=' in the URL
                          'dimension' => "ROWS",
                          'startIndex' => 2, // row number to delete
                          'endIndex' => 1000
                      )
                  )    
                )
            ));
        }
        
        $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
    }

    public function callback()
    {
        # code...
        $this->getClient();
    }

    function getClient()
    {
        $client = new \Google\Client();
        $client->setApplicationName('Google Sheets API PHP Quickstart');
        // $client->setScopes(\Google\Service\Sheets::SPREADSHEETS_READONLY);
        $client->setScopes(\Google\Service\Sheets::SPREADSHEETS);
        $client->setAuthConfig('../storage/credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        // $client->setRedirectUri('https://enel.datanet.pe/google/callback');
        // $client->setRedirectUri('https://' . $_SERVER['HTTP_HOST'] . '/google');
        $client->setRedirectUri(url('google/callback'));
        // echo url('google/callback');
        // echo 'https://' . $_SERVER['HTTP_HOST'] . '/google/callback';

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.

        $tokenPath = '../storage/tokenGoogle.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                // print 'Enter verification code: ';
                // $authCode = trim(fgets(STDIN));
                $authCode = $_GET['code'];

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new \Exception(join(', ', $accessToken));
                }
            }
            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }
        return $client;
    }
}
