<?php

namespace Diler\Salon\Orm;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

class CarTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'car';
    }

    /**
     * @return array
     */
    public static function getMap(): array
    {
        Loader::includeModule("crm");
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete()
                ->configureTitle(Loc::getMessage('DILER_CAR_TABLE_ID')),

            (new StringField('MARKA'))
                ->configureRequired()
                ->configureSize(30),

            (new StringField('MODEL'))
                ->configureRequired()
                ->configureSize(30),

            (new StringField('NUMBER'))
                ->configureSize(20),

            (new IntegerField('YEAR')),

            (new StringField('COLOR')),

            (new IntegerField('KM')),

            (new IntegerField('CONTACT_ID')),

            (new Reference('CONTACT', \Bitrix\CRM\ContactTable::class, Join::on('this.CONTACT_ID', 'ref.ID')))
                ->configureJoinType('inner'),
        ];
    }
}
