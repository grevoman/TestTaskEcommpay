<?php

namespace common\modules\google\adapters;

use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;

class GoogleDriveAdvancedAdapter extends GoogleDriveAdapter
{
    public const MIME_TYPE_CSV = 'text/csv';
    public const MIME_TYPE_GOOGLE_SPREADSHEET = 'application/vnd.google-apps.spreadsheet';

    /**
     * Получение списка файлов и информации о них в соответствии со строкой запроса
     *
     * @see https://developers.google.com/drive/api/guides/ref-search-terms
     *
     * @param string $dirname
     * @param bool $recursive
     * @param int $maxResults
     * @param string $query
     *
     * @return array
     */
    public function getFilesList(
        string $dirname = '',
        bool $recursive = false,
        int $maxResults = 0,
        string $query = ''
    ): array {
        return $this->getItems($dirname, $recursive = false, $maxResults, $query);
    }
}