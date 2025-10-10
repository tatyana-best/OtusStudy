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
    Bitrix\Main\Entity,
    Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Iblock\Elements\ElementPlotsTable as Plots;
use Otus\ORM\DoctorsProceduresTable as DoctorsProcedures;

class PatientTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'patient';
    }

    public static function getMap()
    {
        return [
            'ID' => (new IntegerField('ID',
                []
            ))->configureTitle('Идентификатор')
                ->configurePrimary(true)
                ->configureAutocomplete(true),

            'FULL_NAME' => (new StringField('FULL_NAME',
                ['validation' => [__CLASS__, 'validateName']]
            ))->configureTitle('ФИО пациента'),

            'BIRTH_DATE' => (new DateField('BIRTH_DATE',
                []
            ))->configureTitle('Дата рождения пациента'),

            'FLAT' => (new IntegerField('FLAT',
                []
            ))->configureTitle('Идентификатор'),

            'PLOT_ID' => (new IntegerField('PLOT_ID',
                []
            ))->configureTitle('Связь с таблицей участки'),
            
            (new Reference(
                'PLOT',
                Plots::class,
                Join::on('this.PLOT_ID', 'ref.ID')
            ))
                ->configureJoinType('inner'),

            (new ManyToMany('DOCPROC', DoctorsProcedures::class))
                ->configureTableName('visits')
                ->configureLocalPrimary('ID', 'PATIENT_ID')
                ->configureRemotePrimary('ID', 'DOCPROC_ID'),

            new Entity\ExpressionField('AGE',
                'FLOOR(DATEDIFF(NOW(), %s) / 365)', array('BIRTH_DATE')
            )
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
