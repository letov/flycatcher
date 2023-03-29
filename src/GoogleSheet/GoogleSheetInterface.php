<?php

declare(strict_types=1);

namespace Letov\Flycatcher\GoogleSheet;

interface GoogleSheetInterface
{
    public function __construct(string $sheetsJsonFilePath, array $scope, string $spreadsheetId);

    public function getListData(string $listName): array;

    public function setListData(string $listName, array $data);
}
