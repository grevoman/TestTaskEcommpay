<?php

namespace common\modules\partnerAnalytics\services\parsers;

use common\modules\partnerAnalytics\interfaces\AnalyticParserInterface;
use Exception;
use Generator;

abstract class AbstractAnalyticParser implements AnalyticParserInterface
{
    protected string $filename;

    /**
     * @throws \Exception
     */
    public function __construct(string $fileName)
    {
        if (is_readable($fileName)) {
            $this->filename = $fileName;
        } else {
            throw new Exception('Файл не существует или недоступен для чтения');
        }
    }

    /**
     * @return Generator
     */
    public abstract function getData(): Generator;
}