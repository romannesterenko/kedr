<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;
use \Bitrix\Main\Loader;

class ReviewSendComponent extends CBitrixComponent
{
        /**
     * подключает языковые файлы
     */
    public function onIncludeComponentLang()
    {
        $this->includeComponentLang(basename(__FILE__));
        Loc::loadMessages(__FILE__);
    }

    /**
     * подготавливает входные параметры
     * @param array $arParams
     * @return array
     */
    public function onPrepareComponentParams($params)
    {
        $result = array(
            'PRODUCT_ID' => $params['PRODUCT_ID'],
            'PRODUCT_IBLOCK_ID' => $params['PRODUCT_IBLOCK_ID'],
        );
        return $result;
    }
    /**
     * проверяет подключение необходиимых модулей
     * @throws LoaderException
     */
    protected function checkModules()
    {
        if (!Main\Loader::includeModule('iblock'))
            throw new Main\LoaderException("Ошибка подключения модуля инфоблоков");
    }
    /**
     * получение результатов
     */
    protected function getResult()
    {
        $this->arResult = [
            'PRODUCT' => $this->prepareProductFields()
        ];
    }
    /**
     * выполняет логику работы компонента
     */
    public function executeComponent()
    {
        global $APPLICATION;
        try
        {
            $this->checkModules();
            $this->getResult();
            $this->includeComponentTemplate();

            return $this->returned;
        }
        catch (Exception $e)
        {
            ShowError($e->getMessage());
        }
    }

    private function prepareProductFields()
    {
        \CModule::IncludeModule('iblock');
        $arSelect = Array("ID", "NAME", "DETAIL_PICTURE");
        $arFilter = Array("IBLOCK_ID"=>$this->arParams['PRODUCT_IBLOCK_ID'], "ID"=>$this->arParams['PRODUCT_ID']);
        $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
        if($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            $arFields['DETAIL_PICTURE'] = \CFile::GetPath($arFields['DETAIL_PICTURE']);
            return $arFields;
        }

    }
}
?>