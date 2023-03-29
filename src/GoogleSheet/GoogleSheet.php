<?php

declare(strict_types=1);

namespace Letov\Flycatcher\GoogleSheet;

class GoogleSheet implements GoogleSheetInterface
{
    private \Google_Service_Sheets $service;
    private string $spreadsheetId;

    public function __construct(string $sheetsJsonFilePath, array $scope, string $spreadsheetId)
    {
        $client = new \Google_Client();
        $client->setAuthConfig($sheetsJsonFilePath);
        $client->addScope($scope);
        $this->service = new \Google_Service_Sheets($client);
        $this->spreadsheetId = $spreadsheetId;
    }

    public function getListData(string $listName): array
    {
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $listName);

        return $response->getValues();
    }

    public function setListData(string $listName, array $data): void
    {
        $valueRange = new \Google_Service_Sheets_ValueRange();
        $valueRange->setValues($data);
        $options = ['valueInputOption' => 'USER_ENTERED'];
        $this->service->spreadsheets_values->update($this->spreadsheetId, $listName, $valueRange, $options);
    }
}
