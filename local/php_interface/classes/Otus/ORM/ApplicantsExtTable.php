<?php

namespace Otus\ORM;

use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Iblock\ElementTable;
use Otus\Model\AbstractIblocksModel;
use Bitrix\Main\Entity\ReferenceField;
use Otus\ORM\SubjectsTable as Subjects;

class ApplicantsExtTable extends AbstractIblocksModel
{
    public const IBLOCK_ID = 19;

    public static function getMap(): array {
        $map = [
            "APPSUB" => (new ManyToMany('APPSUB', Subjects::class))
                ->configureTableName('appsub')
                ->configureLocalPrimary('IBLOCK_ELEMENT_ID', 'APP_ID')
                ->configureRemotePrimary('ID', 'SUB_ID'),
        ];

        return parent::getMap() + $map;
    }
}
