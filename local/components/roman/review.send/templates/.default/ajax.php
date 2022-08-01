<?php
define("NO_KEEP_STATISTIC", true);
define('NO_AGENT_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$response['success'] = true;
$response['request'] = $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getValues();
if( $request['data']['benefit']==0||
    $request['data']['common']==0||
    $request['data']['easily']==0||
    $request['data']['quality']==0
){
    $response['success'] = false;
    $response['message'] = 'Проставьте все оценки, пожалуйста';
}
if($request['data']['review_title']=='') {
    $response['success'] = false;
    $response['message'] = 'Заполните поле "Заголовок"';
}elseif($request['data']['review_text']=='') {
    $response['success'] = false;
    $response['message'] = 'Заполните поле "Текст отзыва"';
}elseif($request['data']['review_nickname']=='') {
    $response['success'] = false;
    $response['message'] = 'Заполните поле "Никнейм"';
}
if ($response['success']){
    \CModule::IncludeModule('iblock');
    $el = new CIBlockElement;
    $PROP['ITEM_ID'] = $request['data']['product_id'];
    $PROP['STAR_RATE'] = $request['data']['common'];
    $PROP['STAR_QUALITY'] = $request['data']['quality'];
    $PROP['STAR_USE'] = $request['data']['easily'];
    $PROP['STAR_BENEFIT'] = $request['data']['benefit'];
    $PROP['STAR_RECOMENDED'] = $request['data']['recommend']==0?2650:2649;
    $PROP['REVIEW_PLUS'] = $request['data']['review_pluses'];
    $PROP['REVIEW_MINUS'] = $request['data']['review_minuses'];
    $PROP['REVIEW_NAME'] = $request['data']['review_nickname'];
    $PROP['REVIEW_CITY'] = $request['data']['review_place'];
    $PROP['REVIEW_TITLE'] = $request['data']['review_title'];


    $arLoadProductArray = Array(
        "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
        "IBLOCK_ID"      => 69,
        "PROPERTY_VALUES"=> $PROP,
        "NAME"           => $request['data']['review_text'],
        "ACTIVE"         => "N",            // активен
        "PREVIEW_TEXT"   => $request['data']['review_text'],
    );
    $response['add_array'] = $arLoadProductArray;
    if($PRODUCT_ID = $el->Add($arLoadProductArray)) {
        $response['message'] = 'Отзыв успешно добавлен';
        $response['review_id'] = $PRODUCT_ID;
    }else {
        $response['success'] = false;
        $response['message'] = $el->LAST_ERROR;
    }
}
echo json_encode($response);

