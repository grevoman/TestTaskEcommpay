<?php

namespace common\modules\google;

use common\modules\google\adapters\GoogleDriveAdvancedAdapter;
use Google_Client;
use Google_Service_Drive;
use League\Flysystem\AdapterInterface;
use lhs\Yii2FlysystemGoogleDrive\GoogleDriveFilesystem;


/**
 * @method array getFilesList(string $directory = '', boolean $recursive = false)
 */
class GoogleDriveAdvancedFilesystem extends GoogleDriveFilesystem
{
    /**
     * @return AdapterInterface
     */
    protected function prepareAdapter()
    {
        $client = new Google_Client();
        $client->setClientId($this->clientId);
        $client->setClientSecret($this->clientSecret);
        $client->refreshToken($this->refreshToken);
        $service = new Google_Service_Drive($client);

        return new GoogleDriveAdvancedAdapter(
            $service,
            $this->rootFolderId,
            ['defaultParams' => ['files.list' => ['orderBy' => 'createdTime']], 'additionalFetchField' => 'createdTime']
        );
    }
}
