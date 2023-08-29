<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Pregunta;
use App\Models\Respuesta;
use App\Models\Evaluado;
use App\Services\EncuestaService;

use Google\Client;
use Revolution\Google\Sheets\Facades\Sheets;
use Illuminate\Support\Facades\Log;

class GoogleSheetSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:google-sheet-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Google Sheet Sync';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Inicia comando');

        $pending = EncuestaService::listPending();

        Log::info(count($pending).' registros');

        // echo json_encode($pending).' registros';
        foreach($pending as $p){
            $result = [];
            
            Log::info('Sheet: '.$p->google_sheet);
            
            $sheets = $this->getSheets($p->google_sheet);
            if($p->google_sheet_min){
                $sheets_min = $this->getSheets($p->google_sheet);
            }

            $dataEvaluado = Evaluado::where('encuesta_id', $p->encuesta_id)->where('anulada', 0)->get();
            $dataPreguntaCategoria = Pregunta::where('encuesta_id', $p->encuesta_id)->whereNotNull('categoria_id')->orderBy('orden', 'asc')->get();
            $dataPreguntaInfo = Pregunta::where('encuesta_id', $p->encuesta_id)->whereNull('categoria_id')->orderBy('orden', 'asc')->get();
            $preguntaIds = [];

            foreach($dataPreguntaInfo as $pr){
                $result[0][] = $pr->nombre;
                $preguntaIds[] = $pr->id;
            }
            $result[0][] = 'Fecha';
            $result[0][] = 'Fecha Humano';
            $result[0][] = 'Usuario';

            foreach($dataEvaluado as $d){
                // Evaluado
                $resultPregunta = [];
                
                $respuesta = Respuesta::whereIn('pregunta_id', $preguntaIds)
                    ->where('evaluado_id', $d->id)
                    ->where('encuesta_id', $d->encuesta_id)
                    ->get();

                foreach($dataPreguntaInfo as $pr){
                    foreach($respuesta as $r){
                        if($pr->id == $r->pregunta_id){
                            $resultPregunta[] = $r->respuesta;
                        }
                    }
                }
                $resultPregunta[] = $d->fecha;
                $resultPregunta[] = date('d/m/Y h:iA', strtotime($d->fecha));
                $resultPregunta[] = $d->user->user;

                if($resultPregunta) $result[] = $resultPregunta;
            }

            $this->clearSheet($p->google_sheet, $sheets[0]->properties->title, $sheets[0]->properties->sheetId);
            $this->updateSheet($p->google_sheet, $sheets[0]->properties->title, $result);

            if($p->google_sheet_min != ''){
                $this->clearSheet($p->google_sheet_min, $sheets_min[0]->properties->title, $sheets_min[0]->properties->sheetId);
                $this->updateSheet($p->google_sheet_min, $sheets_min[0]->properties->title, $result);
            }

            /* Hoja de Preguntas */

            $sheetPreguntasId = NULL;
            $sheetPreguntasName = 'PREGUNTAS';
            foreach($sheets as $s){
                if($s->properties->title == $sheetPreguntasName) { $sheetPreguntasId = $s->properties->sheetId; }
            }

            if(!$sheetPreguntasId){
                $sheet = $this->createSheet($p->google_sheet, $sheetPreguntasName);
                $sheetPreguntasId = $sheet->replies[0]->addSheet->properties->sheetId;
            }

            $dataSheet = [];
            $dataSheet[] = ['Codigo', 'Pregunta', 'Etiqueta', 'Categoria', 'Orden'];
            foreach($dataPreguntaCategoria as $dc){
                $dataSheet[] = [
                    $dc->codigo_externo,
                    $dc->nombre,
                    ($dc->etiqueta ? $dc->etiqueta : ''),
                    ($dc->categoria ? $dc->categoria->codigo : ''),
                    ($dc->orden ? $dc->orden : '') 
                ];
            }

            $this->clearSheet($p->google_sheet, $sheetPreguntasName, $sheetPreguntasId);
            $this->updateSheet($p->google_sheet, $sheetPreguntasName, $dataSheet);

            /* Hoja de respuestas */

            $sheetId = null;
            $sheetName = 'RESPUESTAS';
            foreach($sheets as $s){
                if($s->properties->title == $sheetName) { $sheetId = $s->properties->sheetId; }
            }
            if(!$sheetId){
                $sheet = $this->createSheet($p->google_sheet, $sheetName);
                $sheetId = $sheet->replies[0]->addSheet->properties->sheetId;
            }
            
            $dataCategoria = EncuestaService::listPreguntaRespuesta($p->encuesta_id, 0);

            $dataSheet = [];
            $dataSheet[] = ['Identificacion', 'Pregunta', 'Respuesta'];
            foreach($dataCategoria as $dc){
                $dataSheet[] = [
                    $dc->nro_identificacion,
                    $dc->codigo_externo,
                    $dc->respuesta 
                ];
            }
            $this->clearSheet($p->google_sheet, $sheetName, $sheetId);
            $this->updateSheet($p->google_sheet, $sheetName, $dataSheet);

            /* Hoja de Categorias */
            /*
            $categorias = EncuestaService::listCategorias($p->encuesta_id);

            foreach($categorias as $c){
                $sheetId = null;
                $sheetName = $c->codigo;
                foreach($sheets as $s){
                    if($s->properties->title == $c->codigo) { $sheetId = $s->properties->sheetId; }
                }
                if(!$sheetId){
                    $sheet = $this->createSheet($p->google_sheet, $c->codigo);
                    $sheetId = $sheet->replies[0]->addSheet->properties->sheetId;
                }

                $dataCategoria = EncuestaService::listPreguntaRespuestaCategoria($c->categoria_id, $p->encuesta_id);

                $dataSheet = [];
                $dataSheet[] = ['Identificacion', 'Pregunta', 'Respuesta'];
                foreach($dataCategoria as $dc){
                    $dataSheet[] = [
                        $dc->nro_identificacion,
                        $dc->codigo_externo,
                        $dc->respuesta 
                    ];
                }
                $this->clearSheet($p->google_sheet, $sheetName, $sheetId);
                $this->updateSheet($p->google_sheet, $sheetName, $dataSheet);
            }
            */
            Evaluado::where('encuesta_id', $p->encuesta_id)->whereNull('fecha_sincronizacion')->update(['fecha_sincronizacion' => date('Y-m-d H:i:s')]);
        }
        Log::info('Fin comando');
    }

    private function updateSheet($spreadsheetId, $sheetName, $data)
    {
        $client = $this->getClient();
        $service = new \Google\Service\Sheets($client);

        $range = $sheetName.'!A1'; /// the first cell on the first sheet.. (it's really titleOfSheet! but it's called Sheet1 by default)
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

    private function clearSheet($spreadsheetId, $sheetName, $sheetId)
    {
        $client = $this->getClient();
        $service = new \Google\Service\Sheets($client);

        $range = $sheetName.'!A:Z';

        $response = $service->spreadsheets_values->get($spreadsheetId, $range);

        $values = $response->getValues();

        if($values){
            
            if(count($values) > 1){
                $batchUpdateRequest = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest(array(
                    'requests' => array(
                    'deleteDimension' => array(
                        'range' => array(
                            'sheetId' => $sheetId, // the ID of the sheet/tab shown after 'gid=' in the URL
                            'dimension' => "ROWS",
                            'startIndex' => 2, // row number to delete
                            'endIndex' => 50000
                        )
                    )    
                    )
                ));
                $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
            }
        }
        
    }

    public function createSheet($spreadsheetId, $title)
    {
        $client = $this->getClient();
        $service = new \Google\Service\Sheets($client);

        $body = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
           'requests' => ['addSheet' => ['properties' => ['title' => $title ]]]
        ]);
        
        return $service->spreadsheets->batchUpdate($spreadsheetId,$body);
    }

    public function getSheets($spreadsheetId)
    {
        $client = $this->getClient();
        $service = new \Google\Service\Sheets($client);

        return $service->spreadsheets->get($spreadsheetId)->getSheets();
    }

    function getClient()
    {
        $tokenPath = storage_path('tokenGoogle.json');
        $credentialPath = storage_path('credentials.json');

        $client = new \Google\Client();
        $guzzleClient = new \GuzzleHttp\Client(["curl" => [
            CURLOPT_SSL_VERIFYPEER => false
        ]]);    
        $client->setHttpClient($guzzleClient);
        $client->setApplicationName('Google Sheets API PHP Quickstart');
        // $client->setScopes(\Google\Service\Sheets::SPREADSHEETS_READONLY);
        $client->setScopes(\Google\Service\Sheets::SPREADSHEETS);
        $client->setAuthConfig($credentialPath);
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
