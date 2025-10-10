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
    Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Iblock\Elements\ElementDoctorsTable as Doctors;
use Bitrix\Iblock\Elements\ElementProceduresTable as Procedures;
use Otus\ORM\PatientTable as Patient;

class DoctorsProceduresTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'doctorsprocedures';
    }

    public static function getMap()
    {
        return [
            'ID' => (new IntegerField('ID',
                []
            ))->configureTitle('Идентификатор')
                ->configurePrimary(true)
                ->configureAutocomplete(true),

            'DOCTOR_ID' => (new IntegerField('DOCTOR_ID',
                []
            ))->configureTitle('Идентификатор doctor'),

            'PROCEDURE_ID' => (new IntegerField('PROCEDURE_ID',
                []
            ))->configureTitle('Идентификатор procedure'),

            (new Reference(
                'DOCTOR',
                Doctors::class,
                Join::on('this.DOCTOR_ID', 'ref.ID')
            ))
                ->configureJoinType('inner'),

            (new Reference(
                'PROCEDURE',
                Procedures::class,
                Join::on('this.PROCEDURE_ID', 'ref.ID')
            ))
                ->configureJoinType('inner'),
            
            (new ManyToMany('PATIENT', Patient::class))
                ->configureTableName('visits')
                ->configureLocalPrimary('ID', 'DOCPROC_ID')
                ->configureRemotePrimary('ID', 'PATIENT_ID'),
        ];
    }
}
