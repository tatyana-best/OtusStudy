<?php

namespace Otus\ORM;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\StringField,
    Bitrix\Main\ORM\Fields\Validators\LengthValidator,
    Bitrix\Main\ORM\Fields\Relations\Reference,
    Bitrix\Main\ORM\Fields\Relations\OneToMany,
    Bitrix\Main\ORM\Fields\Relations\ManyToMany,
    Bitrix\Main\Entity\Query\Join,
    Bitrix\Main\Entity;
use Otus\ORM\ApplicantsExtTable as Applicants;

class SubjectsTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'subjects';
    }

    public static function getMap()
    {
        return [
            'ID' => (new IntegerField('ID',
                []
            ))->configureTitle('Идентификатор')
                ->configurePrimary(true)
                ->configureAutocomplete(true),

            'NAME' => (new StringField('FULL_NAME',
                ['validation' => [__CLASS__, 'validateName']]
            ))->configureTitle('ФИО пациента'),

            "APPSUB" => (new ManyToMany('APPSUB', Applicants::class))
                ->configureTableName('appsub')
                ->configureLocalPrimary('ID', 'SUB_ID')
                ->configureRemotePrimary('IBLOCK_ELEMENT_ID', 'APP_ID'),
        ];
    }

    /**
     * Returns validators for name field.
     *
     * @return array
     */
    public static function validateName()
    {
        return [
            new LengthValidator(5, 50),
        ];
    }
}
