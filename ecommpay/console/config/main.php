<?php

use common\modules\google\adapters\GoogleDriveAdvancedAdapter;
use common\modules\google\GoogleDriveAdvancedFilesystem;
use common\modules\partnerAnalytics\interfaces\AnalyticParserInterface;
use common\modules\partnerAnalytics\interfaces\GoogleQueryBuilderInterface;
use common\modules\partnerAnalytics\interfaces\PartnerAnalyticsImportInterface;
use common\modules\partnerAnalytics\services\GoogleDrivePartnerAnalyticsImportService;
use common\modules\partnerAnalytics\services\GoogleQueryBuilder;
use common\modules\partnerAnalytics\services\parsers\CsvAnalyticParser;
use common\modules\partnerAnalytics\services\parsers\SpreadsheetAnalyticParser;
use lhs\Yii2FlysystemGoogleDrive\GoogleDriveFilesystem;
use yii\console\controllers\MigrateController;
use yii\di\Container;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'partnerAnalytics' => console\modules\partnerAnalytics\Module::class,
        'partner' => console\modules\partner\Module::class,
    ],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
        ],
        'migrate' => [
            'class' => MigrateController::class,
            'migrationNamespaces' => [
                'console\modules\partner\migrations',
                'console\modules\partnerAnalytics\migrations',
            ],
        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'container' => [
        'definitions' => [
            GoogleDriveFilesystem::class => [
                'class' => GoogleDriveAdvancedFilesystem::class,
                'clientId' => '',
                'clientSecret' => '',
                'refreshToken' => '',
                'rootFolderId' => '',
            ],
            PartnerAnalyticsImportInterface::class => GoogleDrivePartnerAnalyticsImportService::class,
            GoogleQueryBuilderInterface::class => GoogleQueryBuilder::class,
            AnalyticParserInterface::class => static function (
                Container $container,
                array $params,
                array $config
            ) {
                $mimeType = $config['mimeType'] ?? null;
                $filename = $config['filename'] ?? null;
                if ($mimeType === null) {
                    throw new Exception('Отсутствует обязательный параметр type');
                }
                if ($filename === null) {
                    throw new Exception('Отсутствует обязательный параметр filename');
                }

                switch ($mimeType) {
                    case GoogleDriveAdvancedAdapter::MIME_TYPE_CSV:
                        return new CsvAnalyticParser($filename);
                    case GoogleDriveAdvancedAdapter::MIME_TYPE_GOOGLE_SPREADSHEET:
                        return new SpreadsheetAnalyticParser($filename);
                }

                throw new Exception('Неподдерживаемый mime-тип');
            },
        ],
    ],
    'params' => $params,
];
