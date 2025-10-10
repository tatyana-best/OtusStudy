<?php

namespace Otus\Model\Lists;

use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\EventManager;
use \Bitrix\Iblock\PropertyEnumerationTable as PropEnumTable;
use Otus\ORM\PatientTable as Patient;
use Otus\ORM\DoctorsProceduresTable as DoctorsProcedures;
use Otus\ORM\SubjectsTable as Subjects;
use Otus\ORM\ApplicantsExtTable as Applicants;
use Bitrix\Iblock\Elements\ElementPlotsTable as Plots;
use Bitrix\Iblock\Elements\ElementApplicantsTable as Applicant;

class CustomTablesList
{
    public static function PatientsAndPlotsGetList(): array
    {
        $collection = Patient::getList([
            'select' => [
                'ID',
                'FULL_NAME',
                'BIRTH_DATE',
                'AGE',
                'FLAT',
                'PLOT.*'
            ],
            'limit' => 20
        ])->fetchCollection();

        $arResult = [];
        foreach ($collection as $record) {
            $id = $record->getId();
            $arResult[$id]['PATIENT_NAME'] = $record->get('FULL_NAME');
            $arResult[$id]['PATIENT_BIRTH'] = $record->getBirthDate()->format('d.m.Y');
            $arResult[$id]['PATIENT_AGE'] = $record->get('AGE');
            $arResult[$id]['PLOT'] = $record->getPlot()->getName();
            $arResult[$id]['ADDRESS'] = $record->getPlot()->get('PREVIEW_TEXT') . ", кв." . $record->get('FLAT');
        }

        return $arResult;
    }

    public static function VisitsByIdGetList(int $id): array
    {
        $patient = Patient::getByPrimary($id, [
            'select' => [
                '*',
                'DOCPROC',
                'PLOT'
            ]
        ])->fetchObject();

        $arResult = [];
        $arResult['PATIENT_NAME'] = $patient->get('FULL_NAME');
        $arResult['PATIENT_BIRTH'] = $patient->get('BIRTH_DATE')->format('d.m.Y');
        $arResult['PLOT'] = $patient->getPlot()->getName();
        $arResult['ADDRESS'] = $patient->getPlot()->get('PREVIEW_TEXT') . ", кв." .  $patient->get('FLAT');
        foreach ($patient->get('DOCPROC') as $key => $procdoc){
            $docproc = DoctorsProcedures::getByPrimary($procdoc->get('ID'), [
                'select' => [
                    '*',
                    'DOCTOR.*',
                    'PROCEDURE.*'
                ]
            ])->fetchObject();
            $arResult['DOCPROC'][$key]['DOCTOR_ID'] = $procdoc->get('DOCTOR_ID');
            $arResult['DOCPROC'][$key]['DOCTOR_NAME'] = $docproc->getDoctor()->getName();
            $arResult['DOCPROC'][$key]['PROCEDURE_ID'] = $procdoc->get('PROCEDURE_ID');
            $arResult['DOCPROC'][$key]['PROCEDURE_NAME'] = $docproc->getProcedure()->getName();
            $arResult['DOCPROC'][$key]['PROCEDURE_PRICE'] = $docproc->getProcedure()->get('PRICE');
            $query = Application::getConnection()->query("SELECT * FROM visits WHERE DOCPROC_ID=" . $procdoc->get('ID') . " AND PATIENT_ID=" . $id);
            if ($record = $query->fetch())
            {
                $arResult['DOCPROC'][$key]['DIAGNOSIS'] = $record['DIAGNOSIS'];
                $arResult['DOCPROC'][$key]['RECIPE'] = $record['RECIPE'];
                $arResult['DOCPROC'][$key]['DATE_VISIT'] = $record['DATE_VISIT']->format('d.m.Y');
            }
        }

        return $arResult;
    }

    public static function VisitsGetList(): array
    {
        $patients = Patient::getList([
            'select' => [
                '*',
                'DOCPROC',
                'PLOT'
            ]
        ])->fetchCollection();

        $arResult = [];
        foreach ($patients as $key => $patient) {
            $arResult[$key] = self::VisitsByIdGetList($key);
        }

        return $arResult;
    }

    public static function createTables(): void
    {
        if (!Application::getConnection(Patient::getConnectionName())->isTableExists(Base::getInstance("Otus\ORM\PatientTable")->getDBTableName())) {
            Base::getInstance("Otus\ORM\PatientTable")->createDbTable();
        }

        if (!Application::getConnection(DoctorsProcedures::getConnectionName())->isTableExists(Base::getInstance("Otus\ORM\DoctorsProceduresTable")->getDBTableName())) {
            Base::getInstance("Otus\ORM\DoctorsProceduresTable")->createDbTable();
        }

        if (!Application::getConnection()->isTableExists('visits')) {
            Application::getConnection()->queryExecute("
                CREATE TABLE visits (
                    ID int NOT NULL auto_increment,
                    PATIENT_ID int NOT NULL,
                    DOCPROC_ID int NOT NULL,
                    DIAGNOSIS VARCHAR(30),
                    RECIPE VARCHAR(30),
                    DATE_VISIT DATE,
                    PRIMARY KEY (ID)
                )
            ");
        }

        if (!Application::getConnection(Subjects::getConnectionName())->isTableExists(Base::getInstance("Otus\ORM\SubjectsTable")->getDBTableName())) {
            Base::getInstance("Otus\ORM\SubjectsTable")->createDbTable();
        }

        if (!Application::getConnection()->isTableExists('appsub')) {
            Application::getConnection()->queryExecute("
                CREATE TABLE appsub (
                    ID int NOT NULL auto_increment,
                    APP_ID int NOT NULL,
                    SUB_ID int NOT NULL,
                    RANG int,
                    PRIMARY KEY (ID)
                )
            ");
        }
    }

    public static function deleteTables(): void
    {
        Application::getConnection(Patient::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("Otus\ORM\PatientTable")->getDBTableName());
        Application::getConnection(DoctorsProcedures::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("Otus\ORM\DoctorsProceduresTable")->getDBTableName());
        Application::getConnection()->queryExecute("DROP TABLE visits");
        Application::getConnection(Subjects::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("Otus\ORM\SubjectsTable")->getDBTableName());
        Application::getConnection()->queryExecute("DROP TABLE appsub");
    }

    public static function doctorsProceduresList(): array
    {
        $collection = DoctorsProcedures::getList([
            'select' => [
                'ID',
                'DOCTOR',
                'PROCEDURE'
            ]
        ])->fetchCollection();

        $arResult = [];
        foreach ($collection as $key => $doctor) {
            $arResult[$doctor->getDoctor()->getId()]['DOCTOR_NAME'] = $doctor->getDoctor()->getName();
            $arResult[$doctor->getDoctor()->getId()]['PROCEDURES'][$doctor->getProcedure()->getId()] = $doctor->getProcedure()->getName();
        }

        return $arResult;
    }
    
    public static function applicantsByIDList(int $id): array
    {
        $arResult = [];
        $app = Applicants::getByPrimary($id, [
            'select' => [
                '*',
                'APPSUB'
            ]
        ])
            ->fetchObject();

        $arResult['SCHOOL'] = $app->getSchool();

        $rsEnum = PropEnumTable::getList(array(
            'filter' => ['ID' => $app->getCity()],
        ));

        if ($arEnum = $rsEnum->fetch())
        {
            $arResult['CITY'] = $arEnum["VALUE"];
        }

        $rsEnum = Applicant::getList(array(
            'select' => ['ID', 'NAME'],
            'filter' => ['ID' => $id],
        ));

        if ($arEnum = $rsEnum->fetch())
        {
            $arResult['APPLICANT'] = $arEnum["NAME"];
        }

        foreach ($app->getAppsub() as $sub){
            $arResult['SUBJECTS'][$sub->getId()]['NAME'] = $sub->getName();
            $query = Application::getConnection()->query("SELECT * FROM appsub WHERE APP_ID=" . $id . " AND SUB_ID=" . $sub->getId());
            if ($record = $query->fetch())
            {
                $arResult['SUBJECTS'][$sub->getId()]['RANG'] = $record['RANG'];
            }
        }

        return $arResult;
    }

    public static function applicantsList(): array
    {
        $arResult = [];
        $app = Applicants::getList([
            'select' => [
                '*',
                'APPSUB'
            ]
        ])->fetchCollection();

        foreach ($app as $id => $subjects) {
            $arResult[$id] = self::applicantsByIDList($id);
        }

        return $arResult;
    }
}
