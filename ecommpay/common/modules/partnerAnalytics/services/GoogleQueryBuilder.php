<?php

namespace common\modules\partnerAnalytics\services;

use common\modules\partnerAnalytics\dto\GoogleDriveSearchQueryDto;
use common\modules\partnerAnalytics\interfaces\GoogleQueryBuilderInterface;
use DateTime;

/**
 * Формирование строки запроса поиска файлов к Google Drive API
 */
class GoogleQueryBuilder implements GoogleQueryBuilderInterface
{
    private string $query = '';

    public function getQuery(GoogleDriveSearchQueryDto $filter): string
    {
        $this->query = '';
        $this->mimeTypeQuery($filter->mimeTypes);
        $this->createdTimeFromQuery($filter->createdTimeFrom);

        return $this->query;
    }

    /**
     * Формирование части строки запроса по MIME-типам
     *
     * @param string[] $mimeTypes
     *
     * @return void
     */
    protected function mimeTypeQuery(array $mimeTypes)
    {
        $mimeTypeQuery = '';

        foreach ($mimeTypes as $index => $mimeType) {
            if ($index === 0) {
                $mimeTypeQuery .= sprintf("mimeType='%s'", $mimeType);
            } else {
                $mimeTypeQuery .= sprintf(" or mimeType='%s'", $mimeType);
            }
        }

        $this->query .= sprintf("(%s)", $mimeTypeQuery);
    }

    /**
     * Формирование части строки запроса по дате создания
     *
     * @param \DateTime|null $createdTimeFrom
     *
     * @return void
     */
    protected function createdTimeFromQuery(?DateTime $createdTimeFrom)
    {
        if ($createdTimeFrom === null) {
            return;
        }

        $formattedDate = $createdTimeFrom->format('Y-m-d\TH:i:s.v');
        $this->query .= $this->query ? sprintf("and createdTime > '%s'", $formattedDate) : sprintf(
            "createdTime >= '%s'",
            $formattedDate
        );
    }
}