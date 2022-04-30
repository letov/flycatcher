<?php

namespace Letov\Flycatcher\GoogleSheet;

use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;

class GoogleSheet implements GoogleSheetInterface
{
    private Google_Service_Sheets $service;
    private string $spreadsheetId;

    function __construct(string $sheetsJsonFilePath, array $scope, string $spreadsheetId)
    {
        $client = new Google_Client();
        $client->setAuthConfig($sheetsJsonFilePath);
        $client->addScope($scope);
        $this->service = new Google_Service_Sheets($client);
        $this->spreadsheetId = $spreadsheetId;
    }

    function getListData(string $listName): array
    {
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $listName);
        return $response->getValues();
    }

    function setListData(string $listName, array $data)
    {
        $valueRange = new Google_Service_Sheets_ValueRange();
        $valueRange->setValues($data);
        $options = ['valueInputOption' => 'USER_ENTERED'];
        $this->service->spreadsheets_values->update($this->spreadsheetId, $listName, $valueRange, $options);
    }
}