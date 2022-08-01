<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

//echo "<pre>"; print_r($arResult); echo "</pre>";

$this->setFrameMode(true);
//$this->addExternalCss('/bitrix/css/main/bootstrap.css');


$templateLibrary = array('popup', 'fx');
$currencyList = '';

if (!empty($arResult['CURRENCIES'])) {
	$templateLibrary[] = 'currency';
	$currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}

$templateData = array(
	'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
	'TEMPLATE_LIBRARY' => $templateLibrary,
	'CURRENCIES' => $currencyList,
	'ITEM' => array(
		'ID' => $arResult['ID'],
		'IBLOCK_ID' => $arResult['IBLOCK_ID'],
		'OFFERS_SELECTED' => $arResult['OFFERS_SELECTED'],
		'JS_OFFERS' => $arResult['JS_OFFERS']
	)
);
unset($currencyList, $templateLibrary);

$mainId = $this->GetEditAreaId($arResult['ID']);
$itemIds = array(
	'ID' => $mainId,
	'DISCOUNT_PERCENT_ID' => $mainId . '_dsc_pict',
	'STICKER_ID' => $mainId . '_sticker',
	'BIG_SLIDER_ID' => $mainId . '_big_slider',
	'BIG_IMG_CONT_ID' => $mainId . '_bigimg_cont',
	'SLIDER_CONT_ID' => $mainId . '_slider_cont',
	'OLD_PRICE_ID' => $mainId . '_old_price',
	'PRICE_ID' => $mainId . '_price',
	'DESCRIPTION_ID' => $mainId . '_description',
	'DISCOUNT_PRICE_ID' => $mainId . '_price_discount',
	'PRICE_TOTAL' => $mainId . '_price_total',
	'SLIDER_CONT_OF_ID' => $mainId . '_slider_cont_',
	'QUANTITY_ID' => $mainId . '_quantity',
	'QUANTITY_DOWN_ID' => $mainId . '_quant_down',
	'QUANTITY_UP_ID' => $mainId . '_quant_up',
	'QUANTITY_MEASURE' => $mainId . '_quant_measure',
	'QUANTITY_LIMIT' => $mainId . '_quant_limit',
	'BUY_LINK' => $mainId . '_buy_link',
	'ADD_BASKET_LINK' => $mainId . '_add_basket_link',
	'BASKET_ACTIONS_ID' => $mainId . '_basket_actions',
	'NOT_AVAILABLE_MESS' => $mainId . '_not_avail',
	'COMPARE_LINK' => $mainId . '_compare_link',
	'TREE_ID' => $mainId . '_skudiv',
	'DISPLAY_PROP_DIV' => $mainId . '_sku_prop',
	'DISPLAY_MAIN_PROP_DIV' => $mainId . '_main_sku_prop',
	'OFFER_GROUP' => $mainId . '_set_group_',
	'BASKET_PROP_DIV' => $mainId . '_basket_prop',
	'SUBSCRIBE_LINK' => $mainId . '_subscribe',
	'TABS_ID' => $mainId . '_tabs',
	'TAB_CONTAINERS_ID' => $mainId . '_tab_containers',
	'SMALL_CARD_PANEL_ID' => $mainId . '_small_card_panel',
	'TABS_PANEL_ID' => $mainId . '_tabs_panel'
);
$obName = $templateData['JS_OBJ'] = 'ob' . preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);
$name = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
	: $arResult['NAME'];
$title = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
	: $arResult['NAME'];
$alt = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
	: $arResult['NAME'];

$haveOffers = !empty($arResult['OFFERS']);
if ($haveOffers) {
	$actualItem = $arResult['OFFERS'][$arResult['OFFERS_SELECTED']] ?? reset($arResult['OFFERS']);
	$showSliderControls = false;

	foreach ($arResult['OFFERS'] as $offer) {
		if ($offer['MORE_PHOTO_COUNT'] > 1) {
			$showSliderControls = true;
			break;
		}
	}
} else {
	$actualItem = $arResult;
	$showSliderControls = $arResult['MORE_PHOTO_COUNT'] > 1;
}

$skuProps = array();
$price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
$measureRatio = $actualItem['ITEM_MEASURE_RATIOS'][$actualItem['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
$showDiscount = $price['PERCENT'] > 0;

if ($arParams['SHOW_SKU_DESCRIPTION'] === 'Y') {
	$skuDescription = false;
	foreach ($arResult['OFFERS'] as $offer) {
		if ($offer['DETAIL_TEXT'] != '' || $offer['PREVIEW_TEXT'] != '') {
			$skuDescription = true;
			break;
		}
	}
	$showDescription = $skuDescription || !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
} else {
	$showDescription = !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
}

$showBuyBtn = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']);
$buyButtonClassName = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showAddBtn = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']);
$showButtonClassName = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && ($arResult['PRODUCT']['SUBSCRIBE'] === 'Y' || $haveOffers);

$arParams['MESS_BTN_BUY'] = $arParams['MESS_BTN_BUY'] ?: Loc::getMessage('CT_BCE_CATALOG_BUY');
$arParams['MESS_BTN_ADD_TO_BASKET'] = $arParams['MESS_BTN_ADD_TO_BASKET'] ?: Loc::getMessage('CT_BCE_CATALOG_ADD');
$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE'] ?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE');
$arParams['MESS_BTN_COMPARE'] = $arParams['MESS_BTN_COMPARE'] ?: Loc::getMessage('CT_BCE_CATALOG_COMPARE');
$arParams['MESS_PRICE_RANGES_TITLE'] = $arParams['MESS_PRICE_RANGES_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_PRICE_RANGES_TITLE');
$arParams['MESS_DESCRIPTION_TAB'] = $arParams['MESS_DESCRIPTION_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_DESCRIPTION_TAB');
$arParams['MESS_PROPERTIES_TAB'] = $arParams['MESS_PROPERTIES_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_PROPERTIES_TAB');
$arParams['MESS_COMMENTS_TAB'] = $arParams['MESS_COMMENTS_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_COMMENTS_TAB');
$arParams['MESS_SHOW_MAX_QUANTITY'] = $arParams['MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_BCE_CATALOG_SHOW_MAX_QUANTITY');
$arParams['MESS_RELATIVE_QUANTITY_MANY'] = $arParams['MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['MESS_RELATIVE_QUANTITY_FEW'] = $arParams['MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_FEW');

$positionClassMap = array(
	'left' => 'product-item-label-left',
	'center' => 'product-item-label-center',
	'right' => 'product-item-label-right',
	'bottom' => 'product-item-label-bottom',
	'middle' => 'product-item-label-middle',
	'top' => 'product-item-label-top'
);

$discountPositionClass = 'product-item-label-big';
if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION'])) {
	foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos) {
		$discountPositionClass .= isset($positionClassMap[$pos]) ? ' ' . $positionClassMap[$pos] : '';
	}
}

$labelPositionClass = 'product-item-label-big';
if (!empty($arParams['LABEL_PROP_POSITION'])) {
	foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos) {
		$labelPositionClass .= isset($positionClassMap[$pos]) ? ' ' . $positionClassMap[$pos] : '';
	}
}

/*if ($USER->IsAdmin()) {
	echo "<pre>";
	print_r($arResult["JS_OFFERS"]);
	//print_r($arResult);
	echo "</pre>";
}*/
?>
	<style>
        .header-top-wrapper .container,.header-content-wrapper .container{align-items:center;display:flex;justify-content:space-between}.header-top-wrapper{background-color:#c2d3b2;color:#fff}@media (max-width:999px){.header-nav-wrapper{display:none}}@media (max-width:767px){.header{position:sticky;top:0;z-index:11;background:#fff}.header-top-wrapper{display:none}}.header-content-wrapper{border-bottom:1px solid #b19e86}.header-content-wrapper .container{height:100px}.header-top-nav{display:flex;font-family:"Cera Regular","Tahoma","sans-serif"}.header-top-nav a{color:#fff;display:inline-block;margin:0 20px;padding:9px 0;text-decoration:none}.header-top-nav a:first-child{margin-left:0}.header-top-nav a:last-child{margin-right:0}.header-top-nav a span{border-bottom:1.3px solid transparent}@media (max-width:767px){.header-content-wrapper .container{height:72px;display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px}}.main{margin:0;position:relative}.footer-content-wrapper{background-color:#c2d3b2;color:#fff;line-height:1.3;padding:95px 0}.footer-content-wrapper h3{color:#fff}.footer-content-wrapper a{border-bottom:1.3px solid transparent;color:#fff;text-decoration:none}.footer-content-wrapper li{margin-bottom:8px}.footer-content-wrapper li:last-child{margin-bottom:0}.footer-content-wrapper .container{display:flex;justify-content:space-between}.footer-about-wrapper{width:245px}.footer-catalog-wrapper{width:102px}.footer-cooperation-wrapper{margin-right:33px;max-width:205px;width:100%}.footer-copyright-wrapper{padding-left:100px;padding-top:100px}@media (max-width:999px){.footer-content-wrapper{display:none}}@media (min-width:999px){.footer-content-wrapper_mobile{display:none!important}}.footer-content-wrapper_mobile{display:block;padding-top:50px;padding-bottom:70px;background:#C2D3B2;font-size:14px;font-weight:500}.footer-content-wrapper_mobile .container{flex-direction:column}.footer-content-container{display:grid;grid-template-columns:1fr 1fr;gap:15px}.footer-content-container+.footer-content-container{margin-top:28px}.footer-content-wrapper_mobile h3{font-size:18px}.footer-content-wrapper_mobile ul a{width:auto!important}.footer-content-top{display:flex;flex-direction:column}.footer-content-wrapper_mobile .footer-social-links a{width:42px;height:42px}.footer-content-wrapper_mobile .footer-social-links svg{width:22px;height:22px}.footer-content-wrapper_mobile .footer-social-links a:nth-child(2),.footer-content-wrapper_mobile .footer-social-links a:nth-child(5){margin-left:7px;margin-right:7px}.footer-content-wrapper_mobile .footer-logo a{display:flex;flex-direction:column}.footer-content-wrapper_mobile .footer-logo svg{width:80px;height:80px;margin:0 0 10px}.footer-content-wrapper_mobile .footer-logo span{font-size:16px}.footer-content-container li b{font-weight:700}.footer-content-wrapper_mobile .footer-about-wrapper{width:auto;margin-top:45px;margin-bottom:33px}.footer-content-wrapper_mobile .footer-menu{column-count:2;column-gap:15px}.footer-content-wrapper_mobile .footer-terms-container{margin-bottom:40px;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center}.footer-content-wrapper_mobile .footer-cooperation-wrapper .footer-menu{column-count:1}.footer-content-wrapper_mobile .footer-copyright-wrapper{font-size:14px;margin-top:45px;padding:0;text-align:center}.footer-shops-wrapper .footer-menu{column-count:1}.error-modal-text{color:red}@media (max-width:767px){.footer-cooperation-wrapper{margin-right:0;gap:15px}}@media (min-width:1400px){.container{max-width:1195px}}ul{list-style-type:none;margin:0;padding:0}a{color:#81b751}a[href^="tel:"],a[href^="tel:"] span{padding-right:8.5px}p{color:#898989}i.heart-filled{background-image:url("/local/templates/megre/images/icons/heart.svg");background-size:cover;display:inline-block;height:20px;width:21px}i.heart-filled{background-image:url("/local/templates/megre/images/icons/heart-filled.svg")}i.cross,i.cross-white{background-image:url("/local/templates/megre/images/icons/cross.svg");background-size:cover;display:inline-block;height:16px;width:16px}i.cross-white{background-image:url("/local/templates/megre/images/icons/cross-white.svg")}i.map-marker{background-image:url("/local/templates/megre/images/icons/map-marker.svg");background-size:cover;display:inline-block;height:14px;width:13px}div[data-popup-code]{display:none}h3{color:#636363;font-size:22px;line-height:1.3;margin:0;padding:0;text-decoration:none;text-transform:uppercase}@media (max-width:767px){h3{font-size:18px}}.page-title{color:#636363;font-size:28px;line-height:1.3;margin:40px 0;padding:0;text-transform:uppercase}.product-about__action-add input{width:30px;border:none;text-align:center}.footer-logo{display:flex;margin-top:10px}.footer-logo a{border-bottom:none!important;display:flex;text-decoration:none}.footer-logo svg{height:80px;margin-right:15px;width:80px}.footer-logo svg path{fill:#fff}.footer-logo span{color:#fff;font-size:30px;line-height:1.2;margin-top:5px}@media (max-width:767px){.footer-logo{display:flex;justify-content:center}}.footer-social-links{display:flex;flex-wrap:wrap;max-width:176px}.footer-social-links a{align-items:center;background-color:#fff;border-bottom:none!important;border-radius:50%;display:inline-flex;height:48px;justify-content:center;text-decoration:none!important;width:48px}.footer-social-links a:nth-child(2){margin-bottom:15px}.footer-social-links a:nth-child(2),.footer-social-links a:nth-child(5){margin-left:15px;margin-right:15px}@media (max-width:767px){.footer-social-links{justify-content:center}}.search-wrapper{background-color:#fff;border-bottom:1px solid #b19e86;display:none;left:0;position:absolute;right:0;top:0;z-index:2}.search-content{align-items:center;display:flex;justify-content:space-between}.search-content form{width:100%}.search-content input{-webkit-appearance:none;border:none;box-shadow:none;font:inherit;line-height:1;outline:none;padding:17px 10px 16px 0;width:100%}.search-close{display:flex;line-height:1;padding:12.5px 10px}.search-close span{align-items:center;border-radius:3px;display:flex;height:25px;justify-content:center;line-height:1;padding:2px;width:25px}.header-user-bar-wrapper{position:relative}.header-user-bar{display:flex}.header-user-bar a{align-items:center;background-color:transparent;border-radius:10px;display:flex;height:36px;justify-content:center;margin:0 6px;position:relative;text-decoration:none;width:36px}.header-user-bar-count{align-items:center;background-color:#003e2a;border-radius:50%;bottom:4px;color:#fff;display:flex;font-size:8px;justify-content:center;line-height:1;min-height:16px;min-width:16px;position:absolute;right:0}.header-user-bar-count span{transform:translateY(.5px)}.user-bar-popup{background-color:#fff;border:1px solid #b19e86;border-radius:0 0 5px 5px;display:none;margin-top:32px;position:absolute;right:0;width:370px;z-index:85}.user-bar-popup-header{align-items:center;background-color:#81b751;color:#fff;display:flex;font-size:16px;font-weight:bold;height:56px;justify-content:space-between;line-height:1;padding:15px;text-transform:uppercase}.user-bar-popup-header-close{padding:5px}.user-bar-popup-content{max-height:670px;overflow-y:scroll}.header-user-auth{position:absolute;top:68px;right:0;background:#FFFFFF;border:1px solid #B19E86;z-index:9;padding:20px 30px;width:301px;display:none}.header-user-auth-title{color:#898989;font-size:14px;line-height:21px;margin-bottom:19px}.header-user-auth .button{width:100%}@media (max-width:767px){.header-user-bar{justify-content:flex-end}.header-user-bar a[data-code="user-account"]{display:none}.header-user-bar a>img{filter:grayscale(1) brightness(1.2) contrast(0.6)}.user-bar-popup{position:fixed;margin:0;top:73px;width:100%;left:0;right:0;bottom:68px;border:none;z-index:50;border-radius:0}.user-bar-popup-header{background:#fff;color:#636363}.user-bar-popup-header-close .cross-white{background-image:url('/local/templates/megre/images/icons/cross.svg')}.user-bar-popup-content{height:calc(100% - 56px)}}@media (min-width:767px){.mobile-user-bar{display:none!important}}.mobile-user-bar{position:fixed;left:0;right:0;bottom:0;border-top:1px solid #CACACA;background:#fff;z-index:99;padding:10px 20px;gap:15px;display:grid;grid-template-columns:repeat(auto-fit,minmax(50px,1fr))}.mobile-user-bar__card{display:flex;flex-direction:column;color:#898989;font-size:12px;align-items:center;justify-content:center}.mobile-user-bar__card img{width:24px;height:24px;margin-bottom:7px}.header-menu{align-items:center;display:flex;font-family:"Cera Regular","Tahoma","sans-serif";justify-content:center;position:relative;z-index:107}.header-menu-item{margin:0 8px;position:relative;transform:translateY(6px)}.header-menu .header-menu-item:first-child{margin-left:0}.header-menu .header-menu-item:last-child{margin-right:0}.header-menu .header-menu-item>a{color:#636363;display:inline-block;line-height:1;padding:10px;position:relative;text-decoration:none;text-transform:uppercase}.header-menu .header-menu-item>a:after{background-color:transparent;border-radius:5px;content:"";display:block;height:3px;margin-top:3px;width:100%}.header-menu-data{background:#fff;border:1px solid #b19e86;border-radius:0 0 5px 5px;box-sizing:border-box;display:none;height:330px;margin-top:23px;padding:30px;position:absolute;width:440px;z-index:99}.header-menu-item .header-menu-data:before{background-color:transparent;content:"";display:block;height:23px;left:0;position:absolute;right:0;top:-24px;width:100%;z-index:99}.header-menu .header-menu-item:first-child .header-menu-data{left:-180px}.header-menu .header-menu-item:nth-child(2) .header-menu-data{left:-174px}.header-menu .header-menu-item:nth-child(3) .header-menu-data{left:-185px}.header-menu .header-menu-item:nth-child(4) .header-menu-data{left:-190px}.header-menu .header-menu-item:nth-child(5) .header-menu-data{left:-180px}.header-menu .header-menu-item:nth-child(6) .header-menu-data{left:-170px}.header-menu .header-menu-item:last-child .header-menu-data{left:-190px}.header-menu-data-right{border-left:1px solid #e6e6e6;display:flex;flex-direction:column;justify-content:space-between;padding-left:26px}.header-menu-data-left,.header-menu-data-right{width:50%}.header-menu-data ul{margin:0;padding:0}.header-menu-data ul li{line-height:1;margin-bottom:20px}.header-menu-data ul li:last-child{margin-bottom:0}.header-menu-data ul a{color:#636363;font-size:16px;font-style:normal;font-weight:normal;line-height:1;text-decoration:none}.header-menu-data-single{align-items:center;display:flex;flex-direction:column;justify-content:center}.header-menu-data-single>a{display:block;padding-bottom:20px;text-align:center;text-decoration:none;width:100%}.header-menu-data-single-footer{align-items:center;display:flex;flex-direction:row;justify-content:space-between}.header-menu-data-single-footer>div{width:50%}.header-menu-data-single-description{color:#898989;font-size:14px;font-style:normal;font-weight:normal;line-height:1.3}.header-menu-data-single-detail{padding-left:15px;text-align:center}a.header-menu-item-detail-url{color:#81b751;display:inline-block;font-size:16px;font-style:normal;font-weight:bold;line-height:1;padding-bottom:5px;position:relative;text-decoration:none;text-transform:uppercase}a.header-menu-item-detail-url:after{background-color:#81b751;border-radius:5px;content:"";display:block;height:3px;margin-top:5px;position:absolute;width:100%}.header-menu-data-left a.header-menu-item-detail-url{color:#898989;font-size:13px;font-style:normal;font-weight:normal;line-height:1.2;text-transform:none}.header-menu-data-left a.header-menu-item-detail-url:after{bottom:-3px;height:2px}.header-menu-data-full-image{background-size:cover;height:100%;width:100%}.header-menu-data-right-image{position:relative;text-align:center}.header-menu-data-right-image img{max-height:170px;max-width:170px;width:100%}.header-menu-data-right-description{color:#898989;font-size:14px;font-style:normal;font-weight:normal;line-height:1.3;margin:14px 0}.header-logo{display:flex}.header-logo a{display:flex;text-decoration:none}.header-logo svg{height:55px;margin-right:5px;width:55px}.header-logo svg path{fill:#00662c}.header-logo span{color:#00662c;font-family:"Agco Regular",serif;font-size:21px;font-weight:bold;line-height:1.1;margin-top:4px;text-transform:uppercase}@media (max-width:767px){.header-logo{align-items:center}.header-logo svg{width:30px;height:30px}.header-logo span{font-size:14px}}.user-region-wrapper{align-items:center;display:flex;justify-content:space-between}.user-region{padding:9px 7px 9px 0}.user-region i{margin-right:8px;transform:translateY(2px)}.user-region span{border-bottom:1.3px solid #fff}.user-region-country-list a{border-right:1px solid #b19e86;color:#636363;font-family:"Cera Regular","Tahoma","sans-serif";font-size:14px;line-height:1;padding:0 20px;text-decoration:none;text-transform:uppercase}.user-region-country-list a:first-child{padding-left:0}.user-region-country-list a:last-child{border-right:0;padding-right:0}.user-region-country-list a span{padding-bottom:7px;position:relative}.user-region-country-list a span:after{background-color:transparent;border-radius:5px;bottom:0;content:"";display:inline-block;height:3px;left:0;position:absolute;width:100%}.user-region-country-list a.current span:after{background-color:#81b751}.user-region-country-city-list{margin-top:25px;position:relative}.user-region-country-city-list [data-country-city-list]{display:none;flex-direction:column}.user-region-country-city-list [data-country-city-list].current{display:flex}.user-region-country-city-list a{color:#636363;display:block;font-family:"Cera Regular","Tahoma","sans-serif";font-size:16px;line-height:1.3;padding:10px 0;text-decoration:none}.user-region-country-city-list a:first-child{padding-top:0}.user-region-country-city-list a:last-child{padding-bottom:0}.user-region-search{margin-top:32px}.user-region-search input{-webkit-appearance:none;border:1px solid #b6b6b6;border-radius:3px;box-sizing:border-box;display:block;font-family:"Cera Regular","Tahoma","sans-serif";font-size:12px;line-height:1;outline:0;padding:12px 16px 11px;width:100%}.user-region-search-result-wrapper{background-color:#fff;bottom:0;color:#636363;display:none;flex-direction:column;font-size:16px;left:0;position:absolute;right:0;top:0;z-index:1}.user-region-search-result-content{height:100%;overflow-y:scroll}.user-region-search-result-close-wrapper{padding:15px 10px 0;text-align:center}.user-region-search-result-close{background-color:#81b751;border-radius:5px;color:#fff!important;display:inline-block!important;line-height:1!important;margin:0;max-width:120px;padding:10px 5px!important;text-decoration:none!important;width:100%}html{scroll-behavior:smooth}body{margin:0;padding:0;min-height:100vh;font-family:'Cera Pro',sans-serif;font-size:16px;line-height:1.35;font-weight:400;color:#898989;background:#ffffff}@media (max-width:767px){body{padding-bottom:30px}}@font-face{font-family:'Cera Pro';src:url('/local/templates/megre/fonts/CeraPro-Light.eot');src:local('Cera Pro Light'),local('CeraPro-Light'),url('/local/templates/megre/fonts/CeraPro-Light.eot?#iefix') format('embedded-opentype'),url('/local/templates/megre/fonts/CeraPro-Light.woff2') format('woff2'),url('/local/templates/megre/fonts/CeraPro-Light.woff') format('woff'),url('/local/templates/megre/fonts/CeraPro-Light.ttf') format('truetype');font-weight:300;font-style:normal}@font-face{font-family:'Cera Pro';src:url('/local/templates/megre/fonts/CeraPro-Regular.eot');src:local('Cera Pro Regular'),local('CeraPro-Regular'),url('/local/templates/megre/fonts/CeraPro-Regular.eot?#iefix') format('embedded-opentype'),url('/local/templates/megre/fonts/CeraPro-Regular.woff2') format('woff2'),url('/local/templates/megre/fonts/CeraPro-Regular.woff') format('woff'),url('/local/templates/megre/fonts/CeraPro-Regular.ttf') format('truetype');font-weight:normal;font-style:normal}@font-face{font-family:'Cera Pro';src:url('/local/templates/megre/fonts/CeraPro-Medium.eot');src:local('Cera Pro Medium'),local('CeraPro-Medium'),url('/local/templates/megre/fonts/CeraPro-Medium.eot?#iefix') format('embedded-opentype'),url('/local/templates/megre/fonts/CeraPro-Medium.woff2') format('woff2'),url('/local/templates/megre/fonts/CeraPro-Medium.woff') format('woff'),url('/local/templates/megre/fonts/CeraPro-Medium.ttf') format('truetype');font-weight:500;font-style:normal}@font-face{font-family:'Cera Pro';src:url('/local/templates/megre/fonts/CeraPro-Bold.eot');src:local('Cera Pro Bold'),local('CeraPro-Bold'),url('/local/templates/megre/fonts/CeraPro-Bold.eot?#iefix') format('embedded-opentype'),url('/local/templates/megre/fonts/CeraPro-Bold.woff2') format('woff2'),url('/local/templates/megre/fonts/CeraPro-Bold.woff') format('woff'),url('/local/templates/megre/fonts/CeraPro-Bold.ttf') format('truetype');font-weight:600;font-style:normal}b{font-weight:500}.container{margin-right:auto;margin-left:auto;padding-right:12px;padding-left:12px;width:100%;max-width:1200px}.container:before,.container:after{display:none}.icon{flex-shrink:0;background-color:#81b751;-webkit-mask-position:center;-webkit-mask-repeat:no-repeat;-webkit-mask-size:contain}.icon-pine-cone{-webkit-mask-image:url('/local/templates/megre/images/icons/pine-cone.svg')}.icon-plus{-webkit-mask-image:url('/local/templates/megre/images/icons/add.svg')}.icon-minus{-webkit-mask-image:url('/local/templates/megre/images/icons/remove.svg')}.icon-delivery{-webkit-mask-image:url('/local/templates/megre/images/icons/delivery.svg')}.icon-close{-webkit-mask-image:url('/local/templates/megre/images/icons/cancel.svg')}.radio__input{display:none}.input{width:100%;display:flex;flex-direction:column}.input input{padding:8px 16px;width:100%;height:40px;font-size:16px;color:#898989;background:#ffffff;border:1px solid #b19e86;border-radius:3px}.button{padding:13px 20px;height:49px;display:flex;justify-content:center;align-items:center;font-size:16px;line-height:1.1;font-weight:500;color:#000000;background:transparent;border:1px solid #81b751;border-radius:5px;outline:none}.button_primary{color:#ffffff;background:#81b751}.button_outline{color:#81b751;border-color:#81b751}.breadcrumbs{padding:17px 0;border-bottom:1px solid #b19e86}.breadcrumbs-container{position:relative;margin:0;padding:0;display:flex;flex-wrap:wrap;list-style:none}.breadcrumbs__item{display:flex;gap:6px 0;align-items:center;font-size:16px;line-height:1;font-weight:400}.breadcrumbs__item a{text-decoration:none;color:#898989}.breadcrumbs__item:not(:last-child):after{content:'/';margin:0 5px;display:block;color:#898989}@media (max-width:767px){.breadcrumbs{display:none}}*,:before,:after{box-sizing:border-box}form{margin:0}section{padding:50px 0}@media (max-width:767px){section{padding:30px 0}}input{font-family:'Cera Pro',serif;outline:none}input::-webkit-outer-spin-button,input::-webkit-inner-spin-button{margin:0;-webkit-appearance:none}input[type='number']{-moz-appearance:textfield}input::-webkit-datetime-edit{color:#a3afbc}input::-webkit-calendar-picker-indicator{filter:opacity(.3)}img{max-width:100%;height:auto;display:block}a{text-decoration:none}p{margin:0}section .page-title{margin:0 0 45px;padding:0}@media (max-width:767px){section .page-title{margin-bottom:25px}}h3{margin:0;padding:0;font-size:22px;line-height:1.2;font-weight:700;text-transform:uppercase;color:#636363}@media (max-width:767px){h3{font-size:18px}}.link{position:relative;color:#81b751}.link:before{content:'';position:absolute;left:0;right:0;bottom:-2px;height:2px;background:#81b751}.product-mini-menu{position:absolute;top:0;left:0;right:0;z-index:5;display:flex;justify-content:space-between}.product-mini-menu__type{display:grid;gap:7px;justify-items: flex-start;}.button-like{width:20px;height:20px;background-color:#b19e86;-webkit-mask-position:center;-webkit-mask-repeat:no-repeat;-webkit-mask-size:contain;-webkit-mask-image:url('/local/templates/megre/images/icons/heart.svg')}.page-title{font-size:28px;line-height:1.3;font-weight:700;text-transform:uppercase;color:#636363}@media (max-width:999px){.page-title{font-size:25px!important}}@media (max-width:767px){.page-title{font-size:22px!important}}.block-title{font-size:22px;font-weight:700;text-transform:uppercase;color:#636363}@media (max-width:767px){.block-title{font-size:18px}}.swiper-arrow-prev,.swiper-arrow-next{position:absolute;top:50%;z-index:3;width:42px;height:42px;background:url('/local/templates/megre/images/icons/chevron-left.svg') center/contain no-repeat;transform:translateY(-50%)}.swiper-arrow-prev{left:0}.swiper-arrow-next{right:0;transform:scale(-1,1) translateY(-50%)}.swiper:not(.swiper-grid) .swiper-wrapper,.swiper:not(.swiper-grid) .swiper-slide{height:auto!important}.product-more .block-title{display:flex;justify-content:center;align-items:center;white-space:pre}.product-more .block-title:not(:empty):before,.product-more .block-title:not(:empty):after{content:'';width:100%;height:1px;background:#b19e86}.product-more .block-title:before{margin-right:20px}.product-more .block-title:after{margin-left:20px}.product-more__nav{margin-bottom:35px;display:flex;justify-content:flex-end}.product-more .swiper-arrow-prev,.product-more .swiper-arrow-next{position:initial;width:24px;height:24px;background:url('/local/templates/megre/images/icons/chevron-left.svg') center/contain no-repeat;transform:none}.product-more .swiper-arrow-prev{margin-right:15px}.product-more .swiper-arrow-next{transform:rotate(180deg)}@media (max-width:767px){.product-more .block-title{margin-bottom:45px;justify-content:flex-start;font-size:22px}.product-more .block-title:before,.product-more .block-title:after{display:none}.product-more__nav{display:none}}.block-share{display:flex;flex-direction:column}.block-share__label{margin-bottom:16px;color:#898989}.product-preview-wrapper a{text-decoration:none}.product{color:#898989}.product-header{margin-bottom:100px;display:flex;gap:30px;align-items:flex-start}.product-header>div{width:calc(50% - 30px);flex-shrink:0;flex-grow:1}.product-fixed-menu{position:fixed;top:0;left:0;right:0;z-index:15;padding:5px;opacity:0;background:#ffffff;border-bottom:1px solid #b19e86}.product-fixed-menu .container{display:flex;align-items:center}.product-fixed-menu__image{margin-right:15px;width:105px;height:105px;flex-shrink:0}.product-fixed-menu__image img{width:100%;height:100%;object-fit:cover;object-position:center}.product-fixed-menu__name{max-width:350px;font-size:22px;line-height:1.33;font-weight:700}.product-fixed-menu__value{margin:0 50px;display:flex}.product-fixed-menu__buttons{margin-left:auto;display:flex;gap:15px 10px}.product-fixed-menu__buttons span{margin-right:30px;display:block}.product-fixed-menu__buttons .button_primary{min-width:220px}@media (max-width:999px){.product-fixed-menu__buttons{flex-direction:column}}@media (max-width:767px){.product-fixed-menu{top:initial;bottom:0;padding:10px 0 20px;border-top:1px solid #b19e86;border-bottom:none}.product-fixed-menu .container{flex-direction:column}.product-fixed-menu__name{margin-bottom:10px;font-size:14px}.product-fixed-menu__value{display:flex;flex-wrap:wrap;justify-content:center}.product-fixed-menu__buttons{margin:10px 0 0}.product-fixed-menu__buttons .button{width:100%;font-size:14px}.product-fixed-menu__image{display:none}.product-fixed-menu__buttons .button_outline{display:none}.product-header{margin-bottom:60px;gap:0;flex-direction:column}.product-header>div{width:100%}}.product-slider{position:relative;display:flex;flex-direction:row-reverse}.product-slider .block-share{position:absolute;left:0;bottom:-100px}@media (max-width:767px){.product-slider .block-share{display:none}}.product-slider .product-mini-menu{top:20px;left:20px;right:20px}.product-slider .product-mini-menu .button-like{width:30px;height:30px}.product-slider .swiper-thumb{position:relative;padding-bottom:83%;width:100%}.product-slider .swiper-thumb .swiper-wrapper{position:absolute;top:0;left:0;width:100%;height:100%!important}.product-slider .swiper-thumb-nav{margin-right:10px;width:90px;flex-shrink:0}.product-slider .swiper-thumb-nav .swiper-slide{width:90px;height:90px!important;overflow:hidden;opacity:.6;border:1px solid #b19e86;border-radius:5px}.product-slider .swiper-thumb-nav .swiper-slide img{width:100%;height:100%;object-position:center;object-fit:cover}.product-slider__large{position:relative;padding:5px;width:100%;height:100%}.product-slider__large img{width:100%;height:100%;object-fit:contain;object-position:center}@media (max-width:999px){.product-slider .swiper-thumb-nav{display:none}}@media (max-width:767px){.product-slider .swiper-thumb{padding-bottom:100%}}.product-about__header{margin-bottom:50px}.product-about .page-title{margin-bottom:5px}.product-about__ml{font-size:22px;font-weight:300}.product-about__value-buttons{margin:-5px;display:flex;flex-wrap:wrap}.product-about__price{margin-bottom:20px;display:flex;flex-wrap:wrap;align-items:center}.product-about__price-current{margin-right:20px;font-size:28px;line-height:1.2;font-weight:700;color:#636363}.product-about__price-bonus{margin-top:5px;display:flex;align-items:center;font-size:14px;font-weight:500;color:#81b751}.product-about__price-bonus .icon{margin-left:5px;width:16px;height:16px;background-color:#81b751}.product-about__action{margin:-5px;display:flex;flex-wrap:wrap}.product-about__action>*{margin:5px}.product-about__action .button_primary{width:254px}.product-about__action .button_outline{width:200px}.product-about__action-add{padding:10px;width:120px;height:45px;display:flex;justify-content:space-between;align-items:center;font-size:20px;line-height:1;color:#b19e86;border:2px solid #b19e86;border-radius:5px}.product-about__action-add .icon{width:25px;height:25px;background-color:#b19e86}.product-about__delivery-info{margin-top:50px;display:flex;align-items:center}.product-about__delivery-info .icon{margin-right:25px;width:60px;height:60px}.product-about__delivery{margin-top:50px;display:flex;flex-direction:column}.product-about__delivery b{margin-bottom:15px;display:block;font-weight:500}.product-about__delivery b span{text-decoration:underline}.product-about__delivery p{margin:0}@media (max-width:767px){.product-about{margin-top:20px;display:flex;flex-direction:column}.product-about__header{margin-bottom:20px;order:-1}.product-about__price{margin-bottom:25px;order:-1}.product-about__action .button_primary{width:calc(100% - 140px);order:1}.product-about__action .button_outline{width:100%;order:3}.product-about__action-add{height:49px;order:2}.product-about__delivery-info{margin-top:35px}.product-about__delivery{margin-top:25px}}.menu-toggle{margin-right:20px;width:31px;height:37px;background:url('/local/templates/megre/images/icons/menu.svg') center / contain no-repeat}.header-content-wrapper-left{display:none}@media (max-width:767px){.header-content-wrapper-left{display:flex;align-items:center}.header-content-wrapper a[data-code='search']{display:block;filter:grayscale(1) brightness(1.2) contrast(.6)}}.product-family{max-width:100px;font-size:16px!important;color:#B6B6B6!important;background:transparent!important;font-weight:500}.modal{position:fixed;top:0;left:0;z-index:1050;width:100%;height:100%;display:none}.modal-inner{position:absolute;top:0;left:0;right:0;bottom:0;padding:15px;overflow:auto;text-align:center}.modal-inner:before{content:'';width:0;height:100%;display:inline-block;font-size:0;vertical-align:middle}.modal-container{position:relative;padding:64px 100px;width:100%;max-width:850px;display:inline-block;text-align:left;vertical-align:middle;background:#ffffff}.modal-container_m{max-width:570px}.modal-header{margin-bottom:34px;padding:0;width:100%;display:flex;justify-content:space-between;font-size:22px;line-height:29px;font-weight:700;text-transform:uppercase;color:#636363;border:none}.modal-body{position:initial;padding:0;display:grid;gap:10px}.modal-text{margin-bottom:20px}.modal-recode{margin-top:10px;display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;font-size:12px}.modal-recode__send{text-decoration:underline;color:#a7a7a7}.modal-recode__help{position:absolute;top:0;left:0;right:0;bottom:0;padding:40px;display:none;background:rgba(0,0,0,.35)}.modal-recode__help-container{position:relative;z-index:1;padding:40px 45px;height:100%;font-size:14px;background:#ffffff}.modal-recode__help-close{position:absolute;top:25px;right:25px;display:flex;align-items:center;color:#81b751}.modal-recode__help-close:after{content:'';position:absolute;left:0;right:0;bottom:-2px;height:1px;background:#81b751}.modal-recode__help-close .icon{margin-left:6px;width:11px!important;height:11px!important;background-color:#81b751}.modal-recode__help-header{margin-bottom:11px;font-size:20px;font-weight:700;color:#898989}.modal-recode__help-body{font-size:14px;line-height:20px}.modal-recode__help-body ul{margin:0;padding:0;list-style:none}.modal-recode__help-body ul li{position:relative}.modal-recode__help-body ul li:before{content:'';position:absolute;top:8px;left:-15px;width:7px;height:7px;background:#81b751;border-radius:50%}.modal-footer{margin-top:34px;padding:0;display:flex;border:none}.modal-footer>*{margin:0}.modal-footer .button{width:100%}.modal-close{position:absolute;top:25px;right:25px}.modal .icon-close{width:18px;height:18px;background-color:#81b751}@media (max-width:899px){.modal-inner:before{height:auto}.modal .icon-close{right:0}}@media (max-width:767px){.modal-inner{padding:0}.modal-header{flex-direction:column-reverse;align-items:flex-start;font-size:18px}.modal-close{position:initial;margin-left:auto;margin-bottom:25px}.modal-container{margin-top:auto;padding:15px;width:100%;max-width:100%;border-bottom-right-radius:0;border-bottom-left-radius:0;transform:translateY(110%)}.modal .icon-close{top:-20px;width:20px;height:20px}.modal-recode__help{padding:20px}.modal-recode__help-container{padding:20px 35px;height:auto}}.product-added-wrapper{justify-content:center;align-items:center;background-color:#81b751}.product-added-wrapper{height:49px;display:none;font-size:16px;line-height:49px;font-weight:400;font-style:normal;color:#ffffff;border-radius:5px}.product-added-wrapper>div:first-child{margin:0 12px}.product-added-wrapper a{padding:3px;width:25px;height:25px;display:inline-flex;justify-content:center;align-items:center;text-decoration:none;color:#ffffff!important;background:0 0;border-radius:50%}.product-added-wrapper span{display:inline-block}.product-added-wrapper span{width:20px;font-size:18px;text-align:center}.product-add-to-favorites-button-wrapper i.heart-filled{display:none;opacity:1}@media (max-width:767px){.product-added-wrapper{font-size:14px!important}.product-added-wrapper a{width:10px!important}}
	</style>
<?php $APPLICATION->IncludeComponent(
	'bitrix:breadcrumb',
	'',
	[
		'COMPONENT_TEMPLATE' => '',
		'START_FROM'         => '0',
	],
	false,
	['HIDE_ICONS' => 'Y']
) ?>

	<section class="product">
		<div class="container">
			<div class="product-header">
				<div class="product-slider">
					<div class="swiper swiper-thumb">
						<div class="product-mini-menu">

							<div class="product-mini-menu__type">
								<?/* if ($arResult["BADGES"]["HIT"]["CODE"]) { */?><!--
                                    <div class="product-mini-menu__label">Хит</div>
                                --><?/* } */?>
								<?foreach($arResult["BADGES"] as $BADGE){?><div class="product-mini-menu__label"><?=$BADGE["TITLE"]?></div><?}?>
								<?if($arResult["FAMILY"]){?>
									<div class="product-family"><?echo $arResult["FAMILY"];?></div>
								<?}?>
							</div>

							<div class="button-like"
								 data-controller="addToFavorites"
								 data-id="<?= $arResult["ID"] ?>"></div>
						</div>
						<div class="swiper-wrapper">
							<? foreach ($arResult["MORE_PHOTO"] as $MORE_PHOTO) { ?>
								<div class="swiper-slide">
									<div class="product-slider__large">
										<img src="<?= $MORE_PHOTO["SRC"] ?>" alt="">
									</div>
								</div>
							<? } ?>
						</div>
					</div>
					<div class="swiper swiper-thumb-nav" thumbsSlider>
						<div class="swiper-wrapper">
							<? foreach ($arResult["MORE_PHOTO"] as $MORE_PHOTO) { ?>
								<div class="swiper-slide"><img src="<?= $MORE_PHOTO["SRC"] ?>" alt=""></div><? } ?>

						</div>
					</div>
					<div class="block-share">
						<div class="block-share__label">Поделиться</div>
						<script src="https://yastatic.net/share2/share.js"></script>
						<div class="ya-share2" data-curtain data-shape="round"
							 data-services="vkontakte,instagram,odnoklassniki,twitter,pinterest"></div>
					</div>
				</div>
				<div class="product-about">
					<div class="product-about__header">
						<div class="page-title"><?= $arResult["NAME"] ?></div>
						<div class="product-about__ml"><? /*?>100 мл<?*/ ?></div>
					</div>
					<?if($arResult["JS_OFFERS"]){?>
						<div class="product-about__value">
							<div class="product-about__value-label">Выберите <?echo toLower($arResult["JS_OFFERS"][0]["DISPLAY_PROPERTIES"][1]["NAME"]);?></div>
							<div class="product-about__value-buttons">
								<?$KEY = 0;?>
								<?foreach($arResult["JS_OFFERS"] as $JS_OFFER){
									if(!$JS_OFFER["ITEM_PRICES"][0]["RATIO_PRICE"])continue;?>
									<div class="product-about__value-button <?if(!$KEY){?>active<?}?>"
										 data-id="<?=$JS_OFFER["ID"]?>"
										 data-price="<?=$JS_OFFER["ITEM_PRICES"][0]["RATIO_PRICE"]?>"
										 data-price_print="<?=$JS_OFFER["ITEM_PRICES"][0]["PRINT_PRICE"]?>"
										 data-picture="<?=$JS_OFFER["PREVIEW_PICTURE"]["SRC"]?>"
									><?echo $JS_OFFER["DISPLAY_PROPERTIES"][1]["VALUE"]?></div>
									<?$KEY++;?>
								<?}?>
							</div>
						</div>
					<?}?>
					<div class="product-about__price">
						<div class="product-about__price-current"><? echo $arResult["ITEM_PRICES"]["PRINT_RATIO_PRICE"] ?></div>
						<? if ($arResult['PROPERTIES']['NUMBER_BONUSES']['VALUE']) { ?>
							<div class="product-about__price-bonus">+<? echo $arResult['PROPERTIES']['NUMBER_BONUSES']['VALUE']; ?>
								зкр на счёт
								<div class="icon icon-pine-cone"></div>
							</div>
						<? } ?>
					</div>
					<div class="product-about__action">
						<div class="button button_primary" data-controller="addToBasket"
							 data-id="<?= $arResult["ID"] ?>" data-url="<?= $arResult["DETAIL_PAGE_URL"] ?>"
							 data-url-to-basket="?action=ADD2BASKET&id=<?= $arResult["ID"] ?>" id="add_to_basket"><?=$arResult["ADD_TO_CART_BTN"]?></div>
						<?php $productName = str_replace(['&quot;', '"'], '"', $arResult['NAME']) ?>

						<script id="bx24_form_link" data-skip-moving="true">
                            (function (w, d, u, b) {
                                w['Bitrix24FormObject'] = b;
                                w[b] = w[b] || function () {
                                    arguments[0].ref = u;
                                    (w[b].forms = w[b].forms || []).push(arguments[0])
                                };
                                if (w[b]['forms']) return;
                                var s = d.createElement('script');
                                s.async = 1;
                                s.src = u + '?' + (1 * new Date());
                                var h = d.getElementsByTagName('script')[0];
                                h.parentNode.insertBefore(s, h);
                            })(window, document, 'https://megre.bitrix24.ru/bitrix/js/crm/form_loader.js', 'b24form');
						</script>
						<script data-skip-moving="true">
                            b24form({
                                'id': '25',
                                'lang': 'ru',
                                'sec': '1ei7jf',
                                'type': 'link',
                                'click': document.querySelector('div.btn-one-click-buy'),
                                'fields': {
                                    'values': {
                                        'LEAD_NAME': '<?=$USER->GetFullName()?>',
                                        'LEAD_EMAIL': '<?=$USER->GetEmail()?>',
                                        'LEAD_COMMENTS': 'Я хочу приобрести товар: <?=$productName?>',
                                    }
                                }
                            });
						</script>
						<div class="button button_outline btn-one-click-buy">Заказ в один клик</div>
						<div class="product-about__action-add">
							<div class="icon icon-minus"></div>
							<span><input type="number" name="QUANTITY" value="<?=$arResult["ADD_TO_CART_QUANTITY"]?>"></span>
							<div class="icon icon-plus"></div>
						</div>
					</div>
					<div class="product-about__delivery-info">
						<div class="icon icon-delivery"></div>
						<span><?

							$basket = Bitrix\Sale\Basket::loadItemsForFUser(Bitrix\Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
							$price = $basket->getPrice();
							if($price<5000){
								echo "Закажите еще на <b>".(5000 - $price)." руб.</b><br/> и получите бесплатную доставку!";
							}
							else {
								echo "<b>У вас бесплатная доставка</b>";
							}

							?></span>
					</div>
					<?/*?>
                    <div class="product-about__delivery"><b>Доставка в <span>г. Москва:</span></b>
                        <p>
                            Постаматы PickPoint — от 99 руб., 1 дн.<br/>ПВЗ Boxberry — от 99 руб., 1 дн.<br/>
                            Курьерская доставка — от 249 руб., 1 дн.<br/>
                            Почта России — от 282 руб.
                        </p>
                    </div>
                    <?*/?>
				</div>
			</div>
			<div class="product-fixed-menu">
				<div class="container">
					<div class="product-fixed-menu__image">
						<img src="<?= $MORE_PHOTO["SRC"] ?>" alt="">
					</div>
					<div class="product-fixed-menu__name"><?= $arResult["NAME"] ?></div>
					<div class="product-fixed-menu__value">
						<div class="product-about__value-buttons">
							<?$KEY = 0;?>
							<?foreach($arResult["JS_OFFERS"] as $JS_OFFER){
								if(!$JS_OFFER["ITEM_PRICES"][0]["RATIO_PRICE"])continue;?>
								<div class="product-about__value-button <?if(!$KEY){?>active<?}?>"
									 data-id="<?=$JS_OFFER["ID"]?>"
									 data-price="<?=$JS_OFFER["ITEM_PRICES"][0]["RATIO_PRICE"]?>"
									 data-price_print="<?=$JS_OFFER["ITEM_PRICES"][0]["PRINT_PRICE"]?>"
									 data-picture="<?=$JS_OFFER["PREVIEW_PICTURE"]["SRC"]?>"
								><?echo $JS_OFFER["DISPLAY_PROPERTIES"][1]["VALUE"]?></div>
								<?$KEY++;?>
							<?}?>
						</div>
					</div>
					<div class="product-fixed-menu__buttons">
						<div class="button button_primary" data-controller="addToBasket"
							 data-id="<?= $arResult["ID"] ?>" data-url="<?= $arResult["DETAIL_PAGE_URL"] ?>"
							 data-url-to-basket="?action=ADD2BASKET&id=<?= $arResult["ID"] ?>">
							<span><? if($arResult["ADD_TO_CART_BTN"] != "Добавлено")echo $arResult["ITEM_PRICES"]["PRINT_RATIO_PRICE"] ?></span><?=$arResult["ADD_TO_CART_BTN"]?>
						</div>
						<div class="button button_outline btn-one-click-buy">Заказ в один клик</div>
					</div>
				</div>
			</div>
			<? if ($arResult["PROPERTIES"]["DETAIL_DESCRIPTION"]["~VALUE"]["TEXT"] || $arResult["PROPERTIES"]["DETAIL_INGRIDIENT"]["~VALUE"]["TEXT"]) { ?>
				<div class="product-info">
					<? if ($arResult["PROPERTIES"]["DETAIL_DESCRIPTION"]["~VALUE"]["TEXT"]) { ?>
						<div class="product-description">
							<div class="block-title">Описание</div>
							<? echo $arResult["PROPERTIES"]["DETAIL_DESCRIPTION"]["~VALUE"]["TEXT"] ?>
						</div>
					<? } ?>
					<? if ($arResult["PROPERTIES"]["DETAIL_INGRIDIENT"]["~VALUE"]["TEXT"]) { ?>
						<div class="product-composition">
							<div class="block-title">СОСТАВ</div>
							<div class="product-composition__list">
								<? echo $arResult["PROPERTIES"]["DETAIL_INGRIDIENT"]["~VALUE"]["TEXT"] ?>
							</div>
							<div class="product-composition__all hidden">
								<div class="link-more">ВЕСЬ СОСТАВ</div>
							</div>
						</div>
					<? } ?>
				</div>
			<? } ?>
			<? if ($arResult["PROPERTIES"]["DETAIL_HOW_USE"]["~VALUE"]["TEXT"]) { ?>
				<div class="product-info product-use">
					<div class="block-title">КАК ИСПОЛЬЗОВАТЬ</div>
					<div class="product-use__content">
						<? echo $arResult["PROPERTIES"]["DETAIL_HOW_USE"]["~VALUE"]["TEXT"] ?>
					</div>
				</div>
			<? } ?>
		</div>
		<style>.breadcrumbs {display: block;}</style>
	</section>

<? if ($arResult["PROPERTIES"]["SIMILAR"]["VALUE"]) { ?>
	<div class="product-more">
		<div class="container">
			<div class="block-title">С ЭТИМ ТОВАРОМ ПОКУПАЮТ</div>
			<?
			$GLOBALS["arrFilter"] = array("ID" => $arResult["PROPERTIES"]["SIMILAR"]["VALUE"]);
			?>
			<?
			$intSectionID = $APPLICATION->IncludeComponent(
				"bitrix:catalog.section",
				"slider",
				array(
					"IBLOCK_TYPE" => "1c_catalog",
					"IBLOCK_ID" => "37",
					"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
					"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
					"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
					"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
					"PROPERTY_CODE" => (isset($arParams["LIST_PROPERTY_CODE"]) ? $arParams["LIST_PROPERTY_CODE"] : []),
					"PROPERTY_CODE_MOBILE" => array(),
					"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
					"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
					"BROWSER_TITLE" => "-",
					"SET_LAST_MODIFIED" => "N",
					"INCLUDE_SUBSECTIONS" => "Y",
					"BASKET_URL" => $arParams["BASKET_URL"],
					"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
					"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
					"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
					"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
					"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
					"FILTER_NAME" => "arrFilter",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_FILTER" => "N",
					"CACHE_GROUPS" => "N",
					"SET_TITLE" => "N",
					"MESSAGE_404" => $arParams["~MESSAGE_404"],
					"SET_STATUS_404" => "N",
					"SHOW_404" => "N",
					"FILE_404" => $arParams["FILE_404"],
					"DISPLAY_COMPARE" => "N",
					"PAGE_ELEMENT_COUNT" => "24",
					"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
					"PRICE_CODE" => array(
						0 => "BASE",
					),
					"USE_PRICE_COUNT" => "N",
					"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
					"PRICE_VAT_INCLUDE" => "N",
					"USE_PRODUCT_QUANTITY" => "N",
					"ADD_PROPERTIES_TO_BASKET" => "N",
					"PARTIAL_PRODUCT_PROPERTIES" => "N",
					"PRODUCT_PROPERTIES" => (isset($arParams["PRODUCT_PROPERTIES"]) ? $arParams["PRODUCT_PROPERTIES"] : []),
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "N",
					"PAGER_TITLE" => $arParams["PAGER_TITLE"],
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
					"PAGER_SHOW_ALL" => "N",
					"PAGER_BASE_LINK_ENABLE" => "N",
					"PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
					"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
					"LAZY_LOAD" => "N",
					"MESS_BTN_LAZY_LOAD" => $arParams["~MESS_BTN_LAZY_LOAD"],
					"LOAD_ON_SCROLL" => "N",
					"OFFERS_CART_PROPERTIES" => (isset($arParams["OFFERS_CART_PROPERTIES"]) ? $arParams["OFFERS_CART_PROPERTIES"] : []),
					"OFFERS_FIELD_CODE" => array(
						0 => "",
						1 => $arParams["LIST_OFFERS_FIELD_CODE"],
						2 => "",
					),
					"OFFERS_PROPERTY_CODE" => (isset($arParams["LIST_OFFERS_PROPERTY_CODE"]) ? $arParams["LIST_OFFERS_PROPERTY_CODE"] : []),
					"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
					"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
					"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
					"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
					"OFFERS_LIMIT" => (isset($arParams["LIST_OFFERS_LIMIT"]) ? $arParams["LIST_OFFERS_LIMIT"] : 0),
					"SECTION_ID" => "",
					"SECTION_CODE" => "",
					"SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
					"DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
					"USE_MAIN_ELEMENT_SECTION" => "N",
					"CONVERT_CURRENCY" => "N",
					"CURRENCY_ID" => $arParams["CURRENCY_ID"],
					"HIDE_NOT_AVAILABLE" => "N",
					"HIDE_NOT_AVAILABLE_OFFERS" => "N",
					"LABEL_PROP" => array(),
					"LABEL_PROP_MOBILE" => $arParams["LABEL_PROP_MOBILE"],
					"LABEL_PROP_POSITION" => $arParams["LABEL_PROP_POSITION"],
					"ADD_PICT_PROP" => "-",
					"PRODUCT_DISPLAY_MODE" => "N",
					"PRODUCT_BLOCKS_ORDER" => $arParams["LIST_PRODUCT_BLOCKS_ORDER"],
					"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'6','BIG_DATA':false},{'VARIANT':'6','BIG_DATA':false},{'VARIANT':'6','BIG_DATA':false},{'VARIANT':'6','BIG_DATA':false}]",
					"ENLARGE_PRODUCT" => "STRICT",
					"ENLARGE_PROP" => isset($arParams["LIST_ENLARGE_PROP"]) ? $arParams["LIST_ENLARGE_PROP"] : "",
					"SHOW_SLIDER" => "N",
					"SLIDER_INTERVAL" => isset($arParams["LIST_SLIDER_INTERVAL"]) ? $arParams["LIST_SLIDER_INTERVAL"] : "",
					"SLIDER_PROGRESS" => isset($arParams["LIST_SLIDER_PROGRESS"]) ? $arParams["LIST_SLIDER_PROGRESS"] : "",
					"OFFER_ADD_PICT_PROP" => $arParams["OFFER_ADD_PICT_PROP"],
					"OFFER_TREE_PROPS" => (isset($arParams["OFFER_TREE_PROPS"]) ? $arParams["OFFER_TREE_PROPS"] : []),
					"PRODUCT_SUBSCRIPTION" => "N",
					"SHOW_DISCOUNT_PERCENT" => "N",
					"DISCOUNT_PERCENT_POSITION" => $arParams["DISCOUNT_PERCENT_POSITION"],
					"SHOW_OLD_PRICE" => "N",
					"SHOW_MAX_QUANTITY" => "N",
					"MESS_SHOW_MAX_QUANTITY" => (isset($arParams["~MESS_SHOW_MAX_QUANTITY"]) ? $arParams["~MESS_SHOW_MAX_QUANTITY"] : ""),
					"RELATIVE_QUANTITY_FACTOR" => (isset($arParams["RELATIVE_QUANTITY_FACTOR"]) ? $arParams["RELATIVE_QUANTITY_FACTOR"] : ""),
					"MESS_RELATIVE_QUANTITY_MANY" => (isset($arParams["~MESS_RELATIVE_QUANTITY_MANY"]) ? $arParams["~MESS_RELATIVE_QUANTITY_MANY"] : ""),
					"MESS_RELATIVE_QUANTITY_FEW" => (isset($arParams["~MESS_RELATIVE_QUANTITY_FEW"]) ? $arParams["~MESS_RELATIVE_QUANTITY_FEW"] : ""),
					"MESS_BTN_BUY" => (isset($arParams["~MESS_BTN_BUY"]) ? $arParams["~MESS_BTN_BUY"] : ""),
					"MESS_BTN_ADD_TO_BASKET" => (isset($arParams["~MESS_BTN_ADD_TO_BASKET"]) ? $arParams["~MESS_BTN_ADD_TO_BASKET"] : ""),
					"MESS_BTN_SUBSCRIBE" => (isset($arParams["~MESS_BTN_SUBSCRIBE"]) ? $arParams["~MESS_BTN_SUBSCRIBE"] : ""),
					"MESS_BTN_DETAIL" => (isset($arParams["~MESS_BTN_DETAIL"]) ? $arParams["~MESS_BTN_DETAIL"] : ""),
					"MESS_NOT_AVAILABLE" => (isset($arParams["~MESS_NOT_AVAILABLE"]) ? $arParams["~MESS_NOT_AVAILABLE"] : ""),
					"MESS_BTN_COMPARE" => (isset($arParams["~MESS_BTN_COMPARE"]) ? $arParams["~MESS_BTN_COMPARE"] : ""),
					"USE_ENHANCED_ECOMMERCE" => "N",
					"DATA_LAYER_NAME" => (isset($arParams["DATA_LAYER_NAME"]) ? $arParams["DATA_LAYER_NAME"] : ""),
					"BRAND_PROPERTY" => (isset($arParams["BRAND_PROPERTY"]) ? $arParams["BRAND_PROPERTY"] : ""),
					"TEMPLATE_THEME" => (isset($arParams["TEMPLATE_THEME"]) ? $arParams["TEMPLATE_THEME"] : ""),
					"ADD_SECTIONS_CHAIN" => "N",
					"ADD_TO_BASKET_ACTION" => "ADD",
					"SHOW_CLOSE_POPUP" => "N",
					"COMPARE_PATH" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["compare"],
					"COMPARE_NAME" => $arParams["COMPARE_NAME"],
					"USE_COMPARE_LIST" => "Y",
					"BACKGROUND_IMAGE" => (isset($arParams["SECTION_BACKGROUND_IMAGE"]) ? $arParams["SECTION_BACKGROUND_IMAGE"] : ""),
					"COMPATIBLE_MODE" => "N",
					"DISABLE_INIT_JS_IN_COMPONENT" => "N",
					"COMPONENT_TEMPLATE" => "slider",
					"SECTION_USER_FIELDS" => array(
						0 => "",
						1 => "",
					),
					"SHOW_ALL_WO_SECTION" => "Y",
					"CUSTOM_FILTER" => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[]}",
					"RCM_TYPE" => "personal",
					"RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
					"SHOW_FROM_SECTION" => "N",
					"SEF_MODE" => "N",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "N",
					"AJAX_OPTION_HISTORY" => "N",
					"AJAX_OPTION_ADDITIONAL" => "",
					"SET_BROWSER_TITLE" => "N",
					"SET_META_KEYWORDS" => "N",
					"SET_META_DESCRIPTION" => "N"
				),
				false
			);
			?>

		</div>
	</div>
	<?
}
?>
	<section class="product-review">
		<div class="container">
			<div class="tabs-container">
				<div class="tabs-header">
					<div class="tabs-name">Отзывы покупателей</div>
					<div class="tabs-name s-none">Оставить отзыв</div>
				</div>
				<div class="tabs-content">
					<div class="tabs-item">
						<? $GLOBALS["arrFilter"] = array("PROPERTY_ITEM_ID" => $arResult["ID"]); ?>
						<?php $APPLICATION->IncludeComponent(
							"bitrix:news.list",
							"detail_review_list",
							array(
								"ACTIVE_DATE_FORMAT" => "d.m.Y",
								"ADD_SECTIONS_CHAIN" => "N",
								"AJAX_MODE" => "N",
								"AJAX_OPTION_ADDITIONAL" => "",
								"AJAX_OPTION_HISTORY" => "N",
								"AJAX_OPTION_JUMP" => "N",
								"AJAX_OPTION_STYLE" => "N",
								"CACHE_FILTER" => "N",
								"CACHE_GROUPS" => "N",
								"CACHE_TIME" => "86400000",
								"CACHE_TYPE" => "A",
								"CHECK_DATES" => "Y",
								"COMPOSITE_FRAME_MODE" => "A",
								"COMPOSITE_FRAME_TYPE" => "AUTO",
								"DETAIL_URL" => "",
								"DISPLAY_BOTTOM_PAGER" => "N",
								"DISPLAY_DATE" => "N",
								"DISPLAY_NAME" => "N",
								"DISPLAY_PICTURE" => "N",
								"DISPLAY_PREVIEW_TEXT" => "N",
								"DISPLAY_TOP_PAGER" => "N",
								"FIELD_CODE" => array(
									0 => "",
									1 => "",
								),
								"FILTER_NAME" => "arrFilter",
								"HIDE_LINK_WHEN_NO_DETAIL" => "N",
								"IBLOCK_ID" => "69",
								"IBLOCK_TYPE" => "review",
								"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
								"INCLUDE_SUBSECTIONS" => "Y",
								"MESSAGE_404" => "",
								"NEWS_COUNT" => "999",
								"PAGER_BASE_LINK_ENABLE" => "N",
								"PAGER_DESC_NUMBERING" => "N",
								"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
								"PAGER_SHOW_ALL" => "N",
								"PAGER_SHOW_ALWAYS" => "N",
								"PAGER_TEMPLATE" => "",
								"PAGER_TITLE" => "Новости",
								"PARENT_SECTION" => "",
								"PARENT_SECTION_CODE" => "",
								"PREVIEW_TRUNCATE_LEN" => "",
								"PROPERTY_CODE" => array(
									0 => "REVIEW_TITLE",
									1 => "STAR_QUALITY",
									2 => "REVIEW_CITY",
									3 => "REVIEW_MINUS",
									4 => "REVIEW_NAME",
									5 => "STAR_RATE",
									6 => "REVIEW_PLUS",
									7 => "STAR_BENEFIT",
									8 => "STAR_USE",
									9 => "STAR_RECOMENDED",
									10 => "ITEM_ID",
									11 => "",
								),
								"SET_BROWSER_TITLE" => "N",
								"SET_LAST_MODIFIED" => "N",
								"SET_META_DESCRIPTION" => "N",
								"SET_META_KEYWORDS" => "N",
								"SET_STATUS_404" => "N",
								"SET_TITLE" => "N",
								"SHOW_404" => "N",
								"SORT_BY1" => "ACTIVE_FROM",
								"SORT_BY2" => "NAME",
								"SORT_ORDER1" => "DESC",
								"SORT_ORDER2" => "ASC",
								"STRICT_SECTION_CHECK" => "N",
								"COMPONENT_TEMPLATE" => "detail_review_list",
								"PRODUCT_PICTURE_SRC" => $arResult["DETAIL_PICTURE"]["SRC"],
								"PRODUCT_PICTURE_ALT" => $arResult["DETAIL_PICTURE"]["ALT"]
							),
							false
						); ?>

					</div>
                    <div class="tabs-item">
                        <?$APPLICATION->IncludeComponent(
                            "roman:review.send",
                            "",
                            Array(
                               'PRODUCT_ID' => $arResult['ID'],
                               'PRODUCT_IBLOCK_ID' => $arResult['IBLOCK_ID'],
                            ),
                            false
                        );?>
                    </div>



					</div>

<br>

				</div>
			</div>
		</div>
	</section>
	<div class="block-subscribe" style="background-image: url('/local/html/images/subscribe.jpg')">
		<div class="container">
			<form class="block-subscribe__inner">
				<div class="page-title">Узнайте первыми о наших акциях и новинках!</div>
				<div class="input">
					<label>Ваш e-mail
					</label>
					<input required="required" name="email" id="email"/>
				</div>
				<button class="button button_primary">Подписаться</button>
				<div class="text-small">Нажимая на кнопку, вы даете согласие на обработку ваших
					персональных данных в соответствии с <a href='#'>политикой конфиденциальности</a></div>
			</form>
		</div>
	</div>


	<div class="product-more">
		<div class="container">
			<?php $APPLICATION->IncludeComponent(
				"bitrix:catalog.products.viewed",
				"products.viewed",
				[
					"PAGE_ELEMENT_COUNT" => "12",
					"ACTION_VARIABLE" => "action_cpv",
					"ADDITIONAL_PICT_PROP_37" => "",
					"ADDITIONAL_PICT_PROP_38" => "",
					"ADD_PROPERTIES_TO_BASKET" => "N",
					"ADD_TO_BASKET_ACTION" => "ADD",
					"BASKET_URL" => "/cart/",
					"CACHE_GROUPS" => "Y",
					"CACHE_TIME" => "3600",
					"CACHE_TYPE" => "A",
					"COMPOSITE_FRAME_MODE" => "A",
					"COMPOSITE_FRAME_TYPE" => "AUTO",
					"CONVERT_CURRENCY" => "N",
					"DATA_LAYER_NAME" => "dataLayer",
					"DEPTH" => "2",
					"DISCOUNT_PERCENT_POSITION" => "bottom-right",
					"DISPLAY_COMPARE" => "N",
					"ENLARGE_PRODUCT" => "STRICT",
					"HIDE_NOT_AVAILABLE" => "N",
					"HIDE_NOT_AVAILABLE_OFFERS" => "N",
					"IBLOCK_ID" => "37",
					"IBLOCK_MODE" => "single",
					"IBLOCK_TYPE" => "1c_catalog",
					"LABEL_PROP_37" => ["NEWPRODUCT", "SPECIALOFFER", "RECOMMENDED", "SALELEADER", "OFFER_WEEK"],
					"LABEL_PROP_MOBILE_37" => ["NEWPRODUCT", "SPECIALOFFER", "RECOMMENDED", "SALELEADER", "OFFER_WEEK"],
					"LABEL_PROP_POSITION" => "top-left",
					"MESS_BTN_ADD_TO_BASKET" => "В корзину",
					"MESS_BTN_BUY" => "Купить",
					"MESS_BTN_DETAIL" => "Подробнее",
					"MESS_BTN_SUBSCRIBE" => "Подписаться",
					"MESS_NOT_AVAILABLE" => "Нет в наличии",
					"PARTIAL_PRODUCT_PROPERTIES" => "N",
					"PRICE_CODE" => ["Розница"],
					"PRICE_VAT_INCLUDE" => "Y",
					"PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
					"PRODUCT_ID_VARIABLE" => "id",
					"PRODUCT_PROPS_VARIABLE" => "prop",
					"PRODUCT_QUANTITY_VARIABLE" => "quantity",
					"PRODUCT_ROW_VARIANTS" => "",
					"PRODUCT_SUBSCRIPTION" => "Y",
					"SECTION_CODE" => "",
					"SECTION_ELEMENT_CODE" => "",
					"SECTION_ELEMENT_ID" => $GLOBALS["CATALOG_CURRENT_ELEMENT_ID"],
					"SECTION_ID" => $GLOBALS["CATALOG_CURRENT_SECTION_ID"],
					"SHOW_CLOSE_POPUP" => "N",
					"SHOW_DISCOUNT_PERCENT" => "Y",
					"SHOW_FROM_SECTION" => "N",
					"SHOW_MAX_QUANTITY" => "N",
					"SHOW_OLD_PRICE" => "Y",
					"SHOW_PRICE_COUNT" => "1",
					"SHOW_SLIDER" => "Y",
					"SLIDER_INTERVAL" => "3000",
					"SLIDER_PROGRESS" => "Y",
					"TEMPLATE_THEME" => "",
					"USE_ENHANCED_ECOMMERCE" => "Y",
					"USE_PRICE_COUNT" => "N",
					"USE_PRODUCT_QUANTITY" => "N",
				]
			) ?>
		</div>
	</div>
<? /*?>
<div class="bx-catalog-element bx-<?=$arParams['TEMPLATE_THEME']?>" id="<?=$itemIds['ID']?>"
	itemscope itemtype="http://schema.org/Product">
	<div class="container-fluid">
		<?php
		if ($arParams['DISPLAY_NAME'] === 'Y')
		{
			?>
			<div class="row">
				<div class="col-xs-12">
					<h1 class="bx-title"><?=$name?></h1>
				</div>
			</div>
			<?php
		}
		?>
		<div class="row">
			<div class="col-md-6 col-sm-12">
				<div class="product-item-detail-slider-container" id="<?=$itemIds['BIG_SLIDER_ID']?>">
					<span class="product-item-detail-slider-close" data-entity="close-popup"></span>
					<div class="product-item-detail-slider-block
						<?=($arParams['IMAGE_RESOLUTION'] === '1by1' ? 'product-item-detail-slider-block-square' : '')?>"
						data-entity="images-slider-block">
						<span class="product-item-detail-slider-left" data-entity="slider-control-left" style="display: none;"></span>
						<span class="product-item-detail-slider-right" data-entity="slider-control-right" style="display: none;"></span>
						<div class="product-item-label-text <?=$labelPositionClass?>" id="<?=$itemIds['STICKER_ID']?>"
							<?=(!$arResult['LABEL'] ? 'style="display: none;"' : '' )?>>
							<?php
							if ($arResult['LABEL'] && !empty($arResult['LABEL_ARRAY_VALUE']))
							{
								foreach ($arResult['LABEL_ARRAY_VALUE'] as $code => $value)
								{
									?>
									<div<?=(!isset($arParams['LABEL_PROP_MOBILE'][$code]) ? ' class="hidden-xs"' : '')?>>
										<span title="<?=$value?>"><?=$value?></span>
									</div>
									<?php
								}
							}
							?>
						</div>
						<?php
						if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y')
						{
							if ($haveOffers)
							{
								?>
								<div class="product-item-label-ring <?=$discountPositionClass?>" id="<?=$itemIds['DISCOUNT_PERCENT_ID']?>"
									style="display: none;">
								</div>
								<?php
							}
							else
							{
								if ($price['DISCOUNT'] > 0)
								{
									?>
									<div class="product-item-label-ring <?=$discountPositionClass?>" id="<?=$itemIds['DISCOUNT_PERCENT_ID']?>"
										title="<?=-$price['PERCENT']?>%">
										<span><?=-$price['PERCENT']?>%</span>
									</div>
									<?php
								}
							}
						}
						?>
						<div class="product-item-detail-slider-images-container" data-entity="images-container">
							<?php
							if (!empty($actualItem['MORE_PHOTO']))
							{
								foreach ($actualItem['MORE_PHOTO'] as $key => $photo)
								{
									?>
									<div class="product-item-detail-slider-image<?=($key == 0 ? ' active' : '')?>" data-entity="image" data-id="<?=$photo['ID']?>">
										<img src="<?=$photo['SRC']?>" alt="<?=$alt?>" title="<?=$title?>"<?=($key == 0 ? ' itemprop="image"' : '')?>>
									</div>
									<?php
								}
							}

							if ($arParams['SLIDER_PROGRESS'] === 'Y')
							{
								?>
								<div class="product-item-detail-slider-progress-bar" data-entity="slider-progress-bar" style="width: 0;"></div>
								<?php
							}
							?>
						</div>
					</div>
					<?php
					if ($showSliderControls)
					{
						if ($haveOffers)
						{
							foreach ($arResult['OFFERS'] as $keyOffer => $offer)
							{
								if (!isset($offer['MORE_PHOTO_COUNT']) || $offer['MORE_PHOTO_COUNT'] <= 0)
									continue;

								$strVisible = $arResult['OFFERS_SELECTED'] == $keyOffer ? '' : 'none';
								?>
								<div class="product-item-detail-slider-controls-block" id="<?=$itemIds['SLIDER_CONT_OF_ID'].$offer['ID']?>" style="display: <?=$strVisible?>;">
									<?php
									foreach ($offer['MORE_PHOTO'] as $keyPhoto => $photo)
									{
										?>
										<div class="product-item-detail-slider-controls-image<?=($keyPhoto == 0 ? ' active' : '')?>"
											data-entity="slider-control" data-value="<?=$offer['ID'].'_'.$photo['ID']?>">
											<img src="<?=$photo['SRC']?>">
										</div>
										<?php
									}
									?>
								</div>
								<?php
							}
						}
						else
						{
							?>
							<div class="product-item-detail-slider-controls-block" id="<?=$itemIds['SLIDER_CONT_ID']?>">
								<?php
								if (!empty($actualItem['MORE_PHOTO']))
								{
									foreach ($actualItem['MORE_PHOTO'] as $key => $photo)
									{
										?>
										<div class="product-item-detail-slider-controls-image<?=($key == 0 ? ' active' : '')?>"
											data-entity="slider-control" data-value="<?=$photo['ID']?>">
											<img src="<?=$photo['SRC']?>">
										</div>
										<?php
									}
								}
								?>
							</div>
							<?php
						}
					}
					?>
				</div>
			</div>
			<div class="col-md-6 col-sm-12">
				<div class="row">
					<div class="col-sm-6">
						<div class="product-item-detail-info-section">
							<?php
							foreach ($arParams['PRODUCT_INFO_BLOCK_ORDER'] as $blockName)
							{
								switch ($blockName)
								{
									case 'sku':
										if ($haveOffers && !empty($arResult['OFFERS_PROP']))
										{
											?>
											<div id="<?=$itemIds['TREE_ID']?>">
												<?php
												foreach ($arResult['SKU_PROPS'] as $skuProperty)
												{
													if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']]))
														continue;

													$propertyId = $skuProperty['ID'];
													$skuProps[] = array(
														'ID' => $propertyId,
														'SHOW_MODE' => $skuProperty['SHOW_MODE'],
														'VALUES' => $skuProperty['VALUES'],
														'VALUES_COUNT' => $skuProperty['VALUES_COUNT']
													);
													?>
													<div class="product-item-detail-info-container" data-entity="sku-line-block">
														<div class="product-item-detail-info-container-title"><?=htmlspecialcharsEx($skuProperty['NAME'])?></div>
														<div class="product-item-scu-container">
															<div class="product-item-scu-block">
																<div class="product-item-scu-list">
																	<ul class="product-item-scu-item-list">
																		<?php
																		foreach ($skuProperty['VALUES'] as &$value)
																		{
																			$value['NAME'] = htmlspecialcharsbx($value['NAME']);

																			if ($skuProperty['SHOW_MODE'] === 'PICT')
																			{
																				?>
																				<li class="product-item-scu-item-color-container" title="<?=$value['NAME']?>"
																					data-treevalue="<?=$propertyId?>_<?=$value['ID']?>"
																					data-onevalue="<?=$value['ID']?>">
																					<div class="product-item-scu-item-color-block">
																						<div class="product-item-scu-item-color" title="<?=$value['NAME']?>"
																							style="background-image: url('<?=$value['PICT']['SRC']?>');">
																						</div>
																					</div>
																				</li>
																				<?php
																			}
																			else
																			{
																				?>
																				<li class="product-item-scu-item-text-container" title="<?=$value['NAME']?>"
																					data-treevalue="<?=$propertyId?>_<?=$value['ID']?>"
																					data-onevalue="<?=$value['ID']?>">
																					<div class="product-item-scu-item-text-block">
																						<div class="product-item-scu-item-text"><?=$value['NAME']?></div>
																					</div>
																				</li>
																				<?php
																			}
																		}
																		?>
																	</ul>
																	<div style="clear: both;"></div>
																</div>
															</div>
														</div>
													</div>
													<?php
												}
												?>
											</div>
											<?php
										}

										break;

									case 'props':
										if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS'])
										{
											?>
											<div class="product-item-detail-info-container">
												<?php
												if (!empty($arResult['DISPLAY_PROPERTIES']))
												{
													?>
													<dl class="product-item-detail-properties">
														<?php
														foreach ($arResult['DISPLAY_PROPERTIES'] as $property)
														{
															if (isset($arParams['MAIN_BLOCK_PROPERTY_CODE'][$property['CODE']]))
															{
																?>
																<dt><?=$property['NAME']?></dt>
																<dd><?=(is_array($property['DISPLAY_VALUE'])
																		? implode(' / ', $property['DISPLAY_VALUE'])
																		: $property['DISPLAY_VALUE'])?>
																</dd>
																<?php
															}
														}
														unset($property);
														?>
													</dl>
													<?php
												}

												if ($arResult['SHOW_OFFERS_PROPS'])
												{
													?>
													<dl class="product-item-detail-properties" id="<?=$itemIds['DISPLAY_MAIN_PROP_DIV']?>"></dl>
													<?php
												}
												?>
											</div>
											<?php
										}

										break;
								}
							}
							?>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="product-item-detail-pay-block">
							<?php
							foreach ($arParams['PRODUCT_PAY_BLOCK_ORDER'] as $blockName)
							{
								switch ($blockName)
								{
									case 'rating':
										if ($arParams['USE_VOTE_RATING'] === 'Y')
										{
											?>
											<div class="product-item-detail-info-container">
												<?php
												$APPLICATION->IncludeComponent(
													'bitrix:iblock.vote',
													'stars',
													array(
														'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
														'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
														'IBLOCK_ID' => $arParams['IBLOCK_ID'],
														'ELEMENT_ID' => $arResult['ID'],
														'ELEMENT_CODE' => '',
														'MAX_VOTE' => '5',
														'VOTE_NAMES' => array('1', '2', '3', '4', '5'),
														'SET_STATUS_404' => 'N',
														'DISPLAY_AS_RATING' => $arParams['VOTE_DISPLAY_AS_RATING'],
														'CACHE_TYPE' => $arParams['CACHE_TYPE'],
														'CACHE_TIME' => $arParams['CACHE_TIME']
													),
													$component,
													array('HIDE_ICONS' => 'Y')
												);
												?>
											</div>
											<?php
										}

										break;

									case 'price':
										?>
										<div class="product-item-detail-info-container">
											<?php
											if ($arParams['SHOW_OLD_PRICE'] === 'Y')
											{
												?>
												<div class="product-item-detail-price-old" id="<?=$itemIds['OLD_PRICE_ID']?>"
													style="display: <?=($showDiscount ? '' : 'none')?>;">
													<?=($showDiscount ? $price['PRINT_RATIO_BASE_PRICE'] : '')?>
												</div>
												<?php
											}
											?>
											<div class="product-item-detail-price-current" id="<?=$itemIds['PRICE_ID']?>">
												<?=$price['PRINT_RATIO_PRICE']?>
											</div>
											<?php
											if ($arParams['SHOW_OLD_PRICE'] === 'Y')
											{
												?>
												<div class="item_economy_price" id="<?=$itemIds['DISCOUNT_PRICE_ID']?>"
													style="display: <?=($showDiscount ? '' : 'none')?>;">
													<?php
													if ($showDiscount)
													{
														echo Loc::getMessage('CT_BCE_CATALOG_ECONOMY_INFO2', array('#ECONOMY#' => $price['PRINT_RATIO_DISCOUNT']));
													}
													?>
												</div>
												<?php
											}
											?>
										</div>
										<?php
										break;

									case 'priceRanges':
										if ($arParams['USE_PRICE_COUNT'])
										{
											$showRanges = !$haveOffers && count($actualItem['ITEM_QUANTITY_RANGES']) > 1;
											$useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';
											?>
											<div class="product-item-detail-info-container"
												<?=$showRanges ? '' : 'style="display: none;"'?>
												data-entity="price-ranges-block">
												<div class="product-item-detail-info-container-title">
													<?=$arParams['MESS_PRICE_RANGES_TITLE']?>
													<span data-entity="price-ranges-ratio-header">
														(<?=(Loc::getMessage(
															'CT_BCE_CATALOG_RATIO_PRICE',
															array('#RATIO#' => ($useRatio ? $measureRatio : '1').' '.$actualItem['ITEM_MEASURE']['TITLE'])
														))?>)
													</span>
												</div>
												<dl class="product-item-detail-properties" data-entity="price-ranges-body">
													<?php
													if ($showRanges)
													{
														foreach ($actualItem['ITEM_QUANTITY_RANGES'] as $range)
														{
															if ($range['HASH'] !== 'ZERO-INF')
															{
																$itemPrice = false;

																foreach ($arResult['ITEM_PRICES'] as $itemPrice)
																{
																	if ($itemPrice['QUANTITY_HASH'] === $range['HASH'])
																	{
																		break;
																	}
																}

																if ($itemPrice)
																{
																	?>
																	<dt>
																		<?php
																		echo Loc::getMessage(
																				'CT_BCE_CATALOG_RANGE_FROM',
																				array('#FROM#' => $range['SORT_FROM'].' '.$actualItem['ITEM_MEASURE']['TITLE'])
																			).' ';

																		if (is_infinite($range['SORT_TO']))
																		{
																			echo Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
																		}
																		else
																		{
																			echo Loc::getMessage(
																				'CT_BCE_CATALOG_RANGE_TO',
																				array('#TO#' => $range['SORT_TO'].' '.$actualItem['ITEM_MEASURE']['TITLE'])
																			);
																		}
																		?>
																	</dt>
																	<dd><?=($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE'])?></dd>
																	<?php
																}
															}
														}
													}
													?>
												</dl>
											</div>
											<?php
											unset($showRanges, $useRatio, $itemPrice, $range);
										}

										break;

									case 'quantityLimit':
										if ($arParams['SHOW_MAX_QUANTITY'] !== 'N')
										{
											if ($haveOffers)
											{
												?>
												<div class="product-item-detail-info-container" id="<?=$itemIds['QUANTITY_LIMIT']?>" style="display: none;">
													<div class="product-item-detail-info-container-title">
														<?=$arParams['MESS_SHOW_MAX_QUANTITY']?>:
														<span class="product-item-quantity" data-entity="quantity-limit-value"></span>
													</div>
												</div>
												<?php
											}
											else
											{
												if (
													$measureRatio
													&& (float)$actualItem['PRODUCT']['QUANTITY'] > 0
													&& $actualItem['CHECK_QUANTITY']
												)
												{
													?>
													<div class="product-item-detail-info-container" id="<?=$itemIds['QUANTITY_LIMIT']?>">
														<div class="product-item-detail-info-container-title">
															<?=$arParams['MESS_SHOW_MAX_QUANTITY']?>:
															<span class="product-item-quantity" data-entity="quantity-limit-value">
																<?php
																if ($arParams['SHOW_MAX_QUANTITY'] === 'M')
																{
																	if ((float)$actualItem['PRODUCT']['QUANTITY'] / $measureRatio >= $arParams['RELATIVE_QUANTITY_FACTOR'])
																	{
																		echo $arParams['MESS_RELATIVE_QUANTITY_MANY'];
																	}
																	else
																	{
																		echo $arParams['MESS_RELATIVE_QUANTITY_FEW'];
																	}
																}
																else
																{
																	echo $actualItem['PRODUCT']['QUANTITY'].' '.$actualItem['ITEM_MEASURE']['TITLE'];
																}
																?>
															</span>
														</div>
													</div>
													<?php
												}
											}
										}

										break;

									case 'quantity':
										if ($arParams['USE_PRODUCT_QUANTITY'])
										{
											?>
											<div class="product-item-detail-info-container" style="<?=(!$actualItem['CAN_BUY'] ? 'display: none;' : '')?>"
												data-entity="quantity-block">
												<div class="product-item-detail-info-container-title"><?=Loc::getMessage('CATALOG_QUANTITY')?></div>
												<div class="product-item-amount">
													<div class="product-item-amount-field-container">
														<span class="product-item-amount-field-btn-minus no-select" id="<?=$itemIds['QUANTITY_DOWN_ID']?>"></span>
														<input class="product-item-amount-field" id="<?=$itemIds['QUANTITY_ID']?>" type="number"
															value="<?=$price['MIN_QUANTITY']?>">
														<span class="product-item-amount-field-btn-plus no-select" id="<?=$itemIds['QUANTITY_UP_ID']?>"></span>
														<span class="product-item-amount-description-container">
															<span id="<?=$itemIds['QUANTITY_MEASURE']?>">
																<?=$actualItem['ITEM_MEASURE']['TITLE']?>
															</span>
															<span id="<?=$itemIds['PRICE_TOTAL']?>"></span>
														</span>
													</div>
												</div>
											</div>
											<?php
										}

										break;

									case 'buttons':
										?>
										<div data-entity="main-button-container">
											<div id="<?=$itemIds['BASKET_ACTIONS_ID']?>" style="display: <?=($actualItem['CAN_BUY'] ? '' : 'none')?>;">
												<?php
												if ($showAddBtn)
												{
													?>
													<div class="product-item-detail-info-container">
														<a class="btn <?=$showButtonClassName?> product-item-detail-buy-button" id="<?=$itemIds['ADD_BASKET_LINK']?>"
															href="javascript:void(0);">
															<span><?=$arParams['MESS_BTN_ADD_TO_BASKET']?></span>
														</a>
													</div>
													<?php
												}

												if ($showBuyBtn)
												{
													?>
													<div class="product-item-detail-info-container">
														<a class="btn <?=$buyButtonClassName?> product-item-detail-buy-button" id="<?=$itemIds['BUY_LINK']?>"
															href="javascript:void(0);">
															<span><?=$arParams['MESS_BTN_BUY']?></span>
														</a>
													</div>
													<?php
												}
												?>
											</div>
											<?php
											if ($showSubscribe)
											{
												?>
												<div class="product-item-detail-info-container">
													<?php
													$APPLICATION->IncludeComponent(
														'bitrix:catalog.product.subscribe',
														'',
														array(
															'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
															'PRODUCT_ID' => $arResult['ID'],
															'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
															'BUTTON_CLASS' => 'btn btn-default product-item-detail-buy-button',
															'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'],
															'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
														),
														$component,
														array('HIDE_ICONS' => 'Y')
													);
													?>
												</div>
												<?php
											}
											?>
											<div class="product-item-detail-info-container">
												<a class="btn btn-link product-item-detail-buy-button" id="<?=$itemIds['NOT_AVAILABLE_MESS']?>"
													href="javascript:void(0)"
													rel="nofollow" style="display: <?=(!$actualItem['CAN_BUY'] ? '' : 'none')?>;">
													<?=$arParams['MESS_NOT_AVAILABLE']?>
												</a>
											</div>
										</div>
										<?php
										break;
								}
							}

							if ($arParams['DISPLAY_COMPARE'])
							{
								?>
								<div class="product-item-detail-compare-container">
									<div class="product-item-detail-compare">
										<div class="checkbox">
											<label id="<?=$itemIds['COMPARE_LINK']?>">
												<input type="checkbox" data-entity="compare-checkbox">
												<span data-entity="compare-title"><?=$arParams['MESS_BTN_COMPARE']?></span>
											</label>
										</div>
									</div>
								</div>
								<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<?php
				if ($haveOffers)
				{
					if ($arResult['OFFER_GROUP'])
					{
						foreach ($arResult['OFFER_GROUP_VALUES'] as $offerId)
						{
							?>
							<span id="<?=$itemIds['OFFER_GROUP'].$offerId?>" style="display: none;">
								<?php
								$APPLICATION->IncludeComponent(
									'bitrix:catalog.set.constructor',
									'.default',
									array(
										'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
										'IBLOCK_ID' => $arResult['OFFERS_IBLOCK'],
										'ELEMENT_ID' => $offerId,
										'PRICE_CODE' => $arParams['PRICE_CODE'],
										'BASKET_URL' => $arParams['BASKET_URL'],
										'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
										'CACHE_TYPE' => $arParams['CACHE_TYPE'],
										'CACHE_TIME' => $arParams['CACHE_TIME'],
										'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
										'TEMPLATE_THEME' => $arParams['~TEMPLATE_THEME'],
										'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
										'CURRENCY_ID' => $arParams['CURRENCY_ID']
									),
									$component,
									array('HIDE_ICONS' => 'Y')
								);
								?>
							</span>
							<?php
						}
					}
				}
				else
				{
					if ($arResult['MODULES']['catalog'] && $arResult['OFFER_GROUP'])
					{
						$APPLICATION->IncludeComponent(
							'bitrix:catalog.set.constructor',
							'.default',
							array(
								'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
								'IBLOCK_ID' => $arParams['IBLOCK_ID'],
								'ELEMENT_ID' => $arResult['ID'],
								'PRICE_CODE' => $arParams['PRICE_CODE'],
								'BASKET_URL' => $arParams['BASKET_URL'],
								'CACHE_TYPE' => $arParams['CACHE_TYPE'],
								'CACHE_TIME' => $arParams['CACHE_TIME'],
								'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
								'TEMPLATE_THEME' => $arParams['~TEMPLATE_THEME'],
								'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
								'CURRENCY_ID' => $arParams['CURRENCY_ID']
							),
							$component,
							array('HIDE_ICONS' => 'Y')
						);
					}
				}
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-8 col-md-9">
				<div class="row" id="<?=$itemIds['TABS_ID']?>">
					<div class="col-xs-12">
						<div class="product-item-detail-tabs-container">
							<ul class="product-item-detail-tabs-list">
								<?php
								if ($showDescription)
								{
									?>
									<li class="product-item-detail-tab active" data-entity="tab" data-value="description">
										<a href="javascript:void(0);" class="product-item-detail-tab-link">
											<span><?=$arParams['MESS_DESCRIPTION_TAB']?></span>
										</a>
									</li>
									<?php
								}

								if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS'])
								{
									?>
									<li class="product-item-detail-tab" data-entity="tab" data-value="properties">
										<a href="javascript:void(0);" class="product-item-detail-tab-link">
											<span><?=$arParams['MESS_PROPERTIES_TAB']?></span>
										</a>
									</li>
									<?php
								}

								if ($arParams['USE_COMMENTS'] === 'Y')
								{
									?>
									<li class="product-item-detail-tab" data-entity="tab" data-value="comments">
										<a href="javascript:void(0);" class="product-item-detail-tab-link">
											<span><?=$arParams['MESS_COMMENTS_TAB']?></span>
										</a>
									</li>
									<?php
								}
								?>
							</ul>
						</div>
					</div>
				</div>
				<div class="row" id="<?=$itemIds['TAB_CONTAINERS_ID']?>">
					<div class="col-xs-12">
						<?php
						if ($showDescription)
						{
							?>
							<div class="product-item-detail-tab-content active" data-entity="tab-container" data-value="description"
								itemprop="description" id="<?=$itemIds['DESCRIPTION_ID']?>">
								<?php
								if (
									$arResult['PREVIEW_TEXT'] != ''
									&& (
										$arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'S'
										|| ($arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'E' && $arResult['DETAIL_TEXT'] == '')
									)
								)
								{
									echo $arResult['PREVIEW_TEXT_TYPE'] === 'html' ? $arResult['PREVIEW_TEXT'] : '<p>'.$arResult['PREVIEW_TEXT'].'</p>';
								}

								if ($arResult['DETAIL_TEXT'] != '')
								{
									echo $arResult['DETAIL_TEXT_TYPE'] === 'html' ? $arResult['DETAIL_TEXT'] : '<p>'.$arResult['DETAIL_TEXT'].'</p>';
								}
								?>
							</div>
							<?php
						}

						if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS'])
						{
							?>
							<div class="product-item-detail-tab-content" data-entity="tab-container" data-value="properties">
								<?php
								if (!empty($arResult['DISPLAY_PROPERTIES']))
								{
									?>
									<dl class="product-item-detail-properties">
										<?php
										foreach ($arResult['DISPLAY_PROPERTIES'] as $property)
										{
											?>
											<dt><?=$property['NAME']?></dt>
											<dd><?=(
												is_array($property['DISPLAY_VALUE'])
													? implode(' / ', $property['DISPLAY_VALUE'])
													: $property['DISPLAY_VALUE']
												)?>
											</dd>
											<?php
										}
										unset($property);
										?>
									</dl>
									<?php
								}

								if ($arResult['SHOW_OFFERS_PROPS'])
								{
									?>
									<dl class="product-item-detail-properties" id="<?=$itemIds['DISPLAY_PROP_DIV']?>"></dl>
									<?php
								}
								?>
							</div>
							<?php
						}

						if ($arParams['USE_COMMENTS'] === 'Y')
						{
							?>
							<div class="product-item-detail-tab-content" data-entity="tab-container" data-value="comments" style="display: none;">
								<?php
								$componentCommentsParams = array(
									'ELEMENT_ID' => $arResult['ID'],
									'ELEMENT_CODE' => '',
									'IBLOCK_ID' => $arParams['IBLOCK_ID'],
									'SHOW_DEACTIVATED' => $arParams['SHOW_DEACTIVATED'],
									'URL_TO_COMMENT' => '',
									'WIDTH' => '',
									'COMMENTS_COUNT' => '5',
									'BLOG_USE' => $arParams['BLOG_USE'],
									'FB_USE' => $arParams['FB_USE'],
									'FB_APP_ID' => $arParams['FB_APP_ID'],
									'VK_USE' => $arParams['VK_USE'],
									'VK_API_ID' => $arParams['VK_API_ID'],
									'CACHE_TYPE' => $arParams['CACHE_TYPE'],
									'CACHE_TIME' => $arParams['CACHE_TIME'],
									'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
									'BLOG_TITLE' => '',
									'BLOG_URL' => $arParams['BLOG_URL'],
									'PATH_TO_SMILE' => '',
									'EMAIL_NOTIFY' => $arParams['BLOG_EMAIL_NOTIFY'],
									'AJAX_POST' => 'Y',
									'SHOW_SPAM' => 'Y',
									'SHOW_RATING' => 'N',
									'FB_TITLE' => '',
									'FB_USER_ADMIN_ID' => '',
									'FB_COLORSCHEME' => 'light',
									'FB_ORDER_BY' => 'reverse_time',
									'VK_TITLE' => '',
									'TEMPLATE_THEME' => $arParams['~TEMPLATE_THEME']
								);
								if(isset($arParams["USER_CONSENT"]))
									$componentCommentsParams["USER_CONSENT"] = $arParams["USER_CONSENT"];
								if(isset($arParams["USER_CONSENT_ID"]))
									$componentCommentsParams["USER_CONSENT_ID"] = $arParams["USER_CONSENT_ID"];
								if(isset($arParams["USER_CONSENT_IS_CHECKED"]))
									$componentCommentsParams["USER_CONSENT_IS_CHECKED"] = $arParams["USER_CONSENT_IS_CHECKED"];
								if(isset($arParams["USER_CONSENT_IS_LOADED"]))
									$componentCommentsParams["USER_CONSENT_IS_LOADED"] = $arParams["USER_CONSENT_IS_LOADED"];
								$APPLICATION->IncludeComponent(
									'bitrix:catalog.comments',
									'',
									$componentCommentsParams,
									$component,
									array('HIDE_ICONS' => 'Y')
								);
								?>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
			<div class="col-sm-4 col-md-3">
				<div>
					<?php
					if ($arParams['BRAND_USE'] === 'Y')
					{
						$APPLICATION->IncludeComponent(
							'bitrix:catalog.brandblock',
							'.default',
							array(
								'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
								'IBLOCK_ID' => $arParams['IBLOCK_ID'],
								'ELEMENT_ID' => $arResult['ID'],
								'ELEMENT_CODE' => '',
								'PROP_CODE' => $arParams['BRAND_PROP_CODE'],
								'CACHE_TYPE' => $arParams['CACHE_TYPE'],
								'CACHE_TIME' => $arParams['CACHE_TIME'],
								'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
								'WIDTH' => '',
								'HEIGHT' => ''
							),
							$component,
							array('HIDE_ICONS' => 'Y')
						);
					}
					?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<?php
				if ($arResult['CATALOG'] && $actualItem['CAN_BUY'] && \Bitrix\Main\ModuleManager::isModuleInstalled('sale'))
				{
					$APPLICATION->IncludeComponent(
						'bitrix:sale.prediction.product.detail',
						'.default',
						array(
							'BUTTON_ID' => $showBuyBtn ? $itemIds['BUY_LINK'] : $itemIds['ADD_BASKET_LINK'],
							'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
							'POTENTIAL_PRODUCT_TO_BUY' => array(
								'ID' => $arResult['ID'] ?? null,
								'MODULE' => $arResult['MODULE'] ?? 'catalog',
								'PRODUCT_PROVIDER_CLASS' => $arResult['~PRODUCT_PROVIDER_CLASS'] ?? \Bitrix\Catalog\Product\Basket::getDefaultProviderName(),
								'QUANTITY' => $arResult['QUANTITY'] ?? null,
								'IBLOCK_ID' => $arResult['IBLOCK_ID'] ?? null,

								'PRIMARY_OFFER_ID' => $arResult['OFFERS'][0]['ID'] ?? null,
								'SECTION' => array(
									'ID' => $arResult['SECTION']['ID'] ?? null,
									'IBLOCK_ID' => $arResult['SECTION']['IBLOCK_ID'] ?? null,
									'LEFT_MARGIN' => $arResult['SECTION']['LEFT_MARGIN'] ?? null,
									'RIGHT_MARGIN' => $arResult['SECTION']['RIGHT_MARGIN'] ?? null,
								),
							)
						),
						$component,
						array('HIDE_ICONS' => 'Y')
					);
				}

				if ($arResult['CATALOG'] && $arParams['USE_GIFTS_DETAIL'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled('sale'))
				{
					?>
					<div data-entity="parent-container">
						<?php
						if (!isset($arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE']) || $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'] !== 'Y')
						{
							?>
							<div class="catalog-block-header" data-entity="header" data-showed="false" style="display: none; opacity: 0;">
								<?=($arParams['GIFTS_DETAIL_BLOCK_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_GIFT_BLOCK_TITLE_DEFAULT'))?>
							</div>
							<?php
						}

						CBitrixComponent::includeComponentClass('bitrix:sale.products.gift');
						$APPLICATION->IncludeComponent(
							'bitrix:sale.products.gift',
							'.default',
							array(
								'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
								'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
								'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],

								'PRODUCT_ROW_VARIANTS' => "",
								'PAGE_ELEMENT_COUNT' => 0,
								'DEFERRED_PRODUCT_ROW_VARIANTS' => \Bitrix\Main\Web\Json::encode(
									SaleProductsGiftComponent::predictRowVariants(
										$arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
										$arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT']
									)
								),
								'DEFERRED_PAGE_ELEMENT_COUNT' => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],

								'SHOW_DISCOUNT_PERCENT' => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
								'DISCOUNT_PERCENT_POSITION' => $arParams['DISCOUNT_PERCENT_POSITION'],
								'SHOW_OLD_PRICE' => $arParams['GIFTS_SHOW_OLD_PRICE'],
								'PRODUCT_DISPLAY_MODE' => 'Y',
								'PRODUCT_BLOCKS_ORDER' => $arParams['GIFTS_PRODUCT_BLOCKS_ORDER'],
								'SHOW_SLIDER' => $arParams['GIFTS_SHOW_SLIDER'],
								'SLIDER_INTERVAL' => $arParams['GIFTS_SLIDER_INTERVAL'] ?? '',
								'SLIDER_PROGRESS' => $arParams['GIFTS_SLIDER_PROGRESS'] ?? '',

								'TEXT_LABEL_GIFT' => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],

								'LABEL_PROP_'.$arParams['IBLOCK_ID'] => array(),
								'LABEL_PROP_MOBILE_'.$arParams['IBLOCK_ID'] => array(),
								'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'],

								'ADD_TO_BASKET_ACTION' => ($arParams['ADD_TO_BASKET_ACTION'] ?? ''),
								'MESS_BTN_BUY' => $arParams['~GIFTS_MESS_BTN_BUY'],
								'MESS_BTN_ADD_TO_BASKET' => $arParams['~GIFTS_MESS_BTN_BUY'],
								'MESS_BTN_DETAIL' => $arParams['~MESS_BTN_DETAIL'],
								'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],

								'SHOW_PRODUCTS_'.$arParams['IBLOCK_ID'] => 'Y',
								'PROPERTY_CODE_'.$arParams['IBLOCK_ID'] => $arParams['LIST_PROPERTY_CODE'],
								'PROPERTY_CODE_MOBILE'.$arParams['IBLOCK_ID'] => $arParams['LIST_PROPERTY_CODE_MOBILE'],
								'PROPERTY_CODE_'.$arResult['OFFERS_IBLOCK'] => $arParams['OFFER_TREE_PROPS'],
								'OFFER_TREE_PROPS_'.$arResult['OFFERS_IBLOCK'] => $arParams['OFFER_TREE_PROPS'],
								'CART_PROPERTIES_'.$arResult['OFFERS_IBLOCK'] => $arParams['OFFERS_CART_PROPERTIES'],
								'ADDITIONAL_PICT_PROP_'.$arParams['IBLOCK_ID'] => ($arParams['ADD_PICT_PROP'] ?? ''),
								'ADDITIONAL_PICT_PROP_'.$arResult['OFFERS_IBLOCK'] => ($arParams['OFFER_ADD_PICT_PROP'] ?? ''),

								'HIDE_NOT_AVAILABLE' => 'Y',
								'HIDE_NOT_AVAILABLE_OFFERS' => 'Y',
								'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
								'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
								'PRICE_CODE' => $arParams['PRICE_CODE'],
								'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],
								'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
								'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
								'BASKET_URL' => $arParams['BASKET_URL'],
								'ADD_PROPERTIES_TO_BASKET' => $arParams['ADD_PROPERTIES_TO_BASKET'],
								'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
								'PARTIAL_PRODUCT_PROPERTIES' => $arParams['PARTIAL_PRODUCT_PROPERTIES'],
								'USE_PRODUCT_QUANTITY' => 'N',
								'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
								'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
								'POTENTIAL_PRODUCT_TO_BUY' => array(
									'ID' => $arResult['ID'] ?? null,
									'MODULE' => $arResult['MODULE'] ?? 'catalog',
									'PRODUCT_PROVIDER_CLASS' => $arResult['~PRODUCT_PROVIDER_CLASS'] ?? \Bitrix\Catalog\Product\Basket::getDefaultProviderName(),
									'QUANTITY' => $arResult['QUANTITY'] ?? null,
									'IBLOCK_ID' => $arResult['IBLOCK_ID'] ?? null,

									'PRIMARY_OFFER_ID' => $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'] ?? null,
									'SECTION' => array(
										'ID' => $arResult['SECTION']['ID'] ?? null,
										'IBLOCK_ID' => $arResult['SECTION']['IBLOCK_ID'] ?? null,
										'LEFT_MARGIN' => $arResult['SECTION']['LEFT_MARGIN'] ?? null,
										'RIGHT_MARGIN' => $arResult['SECTION']['RIGHT_MARGIN'] ?? null,
									),
								),

								'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
								'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
								'BRAND_PROPERTY' => $arParams['BRAND_PROPERTY']
							),
							$component,
							array('HIDE_ICONS' => 'Y')
						);
						?>
					</div>
					<?php
				}

				if ($arResult['CATALOG'] && $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled('sale'))
				{
					?>
					<div data-entity="parent-container">
						<?php
						if (!isset($arParams['GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE']) || $arParams['GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE'] !== 'Y')
						{
							?>
							<div class="catalog-block-header" data-entity="header" data-showed="false" style="display: none; opacity: 0;">
								<?=($arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_GIFTS_MAIN_BLOCK_TITLE_DEFAULT'))?>
							</div>
							<?php
						}

						$APPLICATION->IncludeComponent(
							'bitrix:sale.gift.main.products',
							'.default',
							array(
								'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
								'PAGE_ELEMENT_COUNT' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
								'LINE_ELEMENT_COUNT' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
								'HIDE_BLOCK_TITLE' => 'Y',
								'BLOCK_TITLE' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],

								'OFFERS_FIELD_CODE' => $arParams['OFFERS_FIELD_CODE'],
								'OFFERS_PROPERTY_CODE' => $arParams['OFFERS_PROPERTY_CODE'],

								'AJAX_MODE' => $arParams['AJAX_MODE'],
								'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
								'IBLOCK_ID' => $arParams['IBLOCK_ID'],

								'ELEMENT_SORT_FIELD' => 'ID',
								'ELEMENT_SORT_ORDER' => 'DESC',
								'FILTER_NAME' => 'searchFilter',
								'SECTION_URL' => $arParams['SECTION_URL'],
								'DETAIL_URL' => $arParams['DETAIL_URL'],
								'BASKET_URL' => $arParams['BASKET_URL'],
								'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
								'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
								'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],

								'CACHE_TYPE' => $arParams['CACHE_TYPE'],
								'CACHE_TIME' => $arParams['CACHE_TIME'],

								'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
								'SET_TITLE' => $arParams['SET_TITLE'],
								'PROPERTY_CODE' => $arParams['PROPERTY_CODE'],
								'PRICE_CODE' => $arParams['PRICE_CODE'],
								'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
								'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],

								'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
								'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
								'CURRENCY_ID' => $arParams['CURRENCY_ID'],
								'HIDE_NOT_AVAILABLE' => 'Y',
								'HIDE_NOT_AVAILABLE_OFFERS' => 'Y',
								'TEMPLATE_THEME' => ($arParams['TEMPLATE_THEME'] ?? ''),
								'PRODUCT_BLOCKS_ORDER' => $arParams['GIFTS_PRODUCT_BLOCKS_ORDER'],

								'SHOW_SLIDER' => $arParams['GIFTS_SHOW_SLIDER'],
								'SLIDER_INTERVAL' => $arParams['GIFTS_SLIDER_INTERVAL'] ?? '',
								'SLIDER_PROGRESS' => $arParams['GIFTS_SLIDER_PROGRESS'] ?? '',

								'ADD_PICT_PROP' => ($arParams['ADD_PICT_PROP'] ?? ''),
								'LABEL_PROP' => ($arParams['LABEL_PROP'] ?? ''),
								'LABEL_PROP_MOBILE' => ($arParams['LABEL_PROP_MOBILE'] ?? ''),
								'LABEL_PROP_POSITION' => ($arParams['LABEL_PROP_POSITION'] ?? ''),
								'OFFER_ADD_PICT_PROP' => ($arParams['OFFER_ADD_PICT_PROP'] ?? ''),
								'OFFER_TREE_PROPS' => ($arParams['OFFER_TREE_PROPS'] ?? ''),
								'SHOW_DISCOUNT_PERCENT' => ($arParams['SHOW_DISCOUNT_PERCENT'] ?? ''),
								'DISCOUNT_PERCENT_POSITION' => ($arParams['DISCOUNT_PERCENT_POSITION'] ?? ''),
								'SHOW_OLD_PRICE' => ($arParams['SHOW_OLD_PRICE'] ?? ''),
								'MESS_BTN_BUY' => ($arParams['~MESS_BTN_BUY'] ?? ''),
								'MESS_BTN_ADD_TO_BASKET' => ($arParams['~MESS_BTN_ADD_TO_BASKET'] ?? ''),
								'MESS_BTN_DETAIL' => ($arParams['~MESS_BTN_DETAIL'] ?? ''),
								'MESS_NOT_AVAILABLE' => ($arParams['~MESS_NOT_AVAILABLE'] ?? ''),
								'ADD_TO_BASKET_ACTION' => ($arParams['ADD_TO_BASKET_ACTION'] ?? ''),
								'SHOW_CLOSE_POPUP' => ($arParams['SHOW_CLOSE_POPUP'] ?? ''),
								'DISPLAY_COMPARE' => ($arParams['DISPLAY_COMPARE'] ?? ''),
								'COMPARE_PATH' => ($arParams['COMPARE_PATH'] ?? ''),
							)
							+ array(
								'OFFER_ID' => empty($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'])
									? $arResult['ID']
									: $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'],
								'SECTION_ID' => $arResult['SECTION']['ID'],
								'ELEMENT_ID' => $arResult['ID'],

								'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
								'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
								'BRAND_PROPERTY' => $arParams['BRAND_PROPERTY']
							),
							$component,
							array('HIDE_ICONS' => 'Y')
						);
						?>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
	<!--Small Card-->
	<div class="product-item-detail-short-card-fixed hidden-xs" id="<?=$itemIds['SMALL_CARD_PANEL_ID']?>">
		<div class="product-item-detail-short-card-content-container">
			<table>
				<tr>
					<td rowspan="2" class="product-item-detail-short-card-image">
						<img src="" data-entity="panel-picture">
					</td>
					<td class="product-item-detail-short-title-container" data-entity="panel-title">
						<span class="product-item-detail-short-title-text"><?=$name?></span>
					</td>
					<td rowspan="2" class="product-item-detail-short-card-price">
						<?php
						if ($arParams['SHOW_OLD_PRICE'] === 'Y')
						{
							?>
							<div class="product-item-detail-price-old" style="display: <?=($showDiscount ? '' : 'none')?>;"
								data-entity="panel-old-price">
								<?=($showDiscount ? $price['PRINT_RATIO_BASE_PRICE'] : '')?>
							</div>
							<?php
						}
						?>
						<div class="product-item-detail-price-current" data-entity="panel-price">
							<?=$price['PRINT_RATIO_PRICE']?>
						</div>
					</td>
					<?php
					if ($showAddBtn)
					{
						?>
						<td rowspan="2" class="product-item-detail-short-card-btn"
							style="display: <?=($actualItem['CAN_BUY'] ? '' : 'none')?>;"
							data-entity="panel-add-button">
							<a class="btn <?=$showButtonClassName?> product-item-detail-buy-button"
								id="<?=$itemIds['ADD_BASKET_LINK']?>"
								href="javascript:void(0);">
								<span><?=$arParams['MESS_BTN_ADD_TO_BASKET']?></span>
							</a>
						</td>
						<?php
					}

					if ($showBuyBtn)
					{
						?>
						<td rowspan="2" class="product-item-detail-short-card-btn"
							style="display: <?=($actualItem['CAN_BUY'] ? '' : 'none')?>;"
							data-entity="panel-buy-button">
							<a class="btn <?=$buyButtonClassName?> product-item-detail-buy-button" id="<?=$itemIds['BUY_LINK']?>"
								href="javascript:void(0);">
								<span><?=$arParams['MESS_BTN_BUY']?></span>
							</a>
						</td>
						<?php
					}
					?>
					<td rowspan="2" class="product-item-detail-short-card-btn"
						style="display: <?=(!$actualItem['CAN_BUY'] ? '' : 'none')?>;"
						data-entity="panel-not-available-button">
						<a class="btn btn-link product-item-detail-buy-button" href="javascript:void(0)"
							rel="nofollow">
							<?=$arParams['MESS_NOT_AVAILABLE']?>
						</a>
					</td>
				</tr>
				<?php
				if ($haveOffers)
				{
					?>
					<tr>
						<td>
							<div class="product-item-selected-scu-container" data-entity="panel-sku-container">
								<?php
								$i = 0;

								foreach ($arResult['SKU_PROPS'] as $skuProperty)
								{
									if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']]))
									{
										continue;
									}

									$propertyId = $skuProperty['ID'];

									foreach ($skuProperty['VALUES'] as $value)
									{
										$value['NAME'] = htmlspecialcharsbx($value['NAME']);
										if ($skuProperty['SHOW_MODE'] === 'PICT')
										{
											?>
											<div class="product-item-selected-scu product-item-selected-scu-color selected"
												title="<?=$value['NAME']?>"
												style="background-image: url('<?=$value['PICT']['SRC']?>'); display: none;"
												data-sku-line="<?=$i?>"
												data-treevalue="<?=$propertyId?>_<?=$value['ID']?>"
												data-onevalue="<?=$value['ID']?>">
											</div>
											<?php
										}
										else
										{
											?>
											<div class="product-item-selected-scu product-item-selected-scu-text selected"
												title="<?=$value['NAME']?>"
												style="display: none;"
												data-sku-line="<?=$i?>"
												data-treevalue="<?=$propertyId?>_<?=$value['ID']?>"
												data-onevalue="<?=$value['ID']?>">
												<?=$value['NAME']?>
											</div>
											<?php
										}
									}

									$i++;
								}
								?>
							</div>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
		</div>
	</div>
	<!--Top tabs-->
	<div class="product-item-detail-tabs-container-fixed hidden-xs" id="<?=$itemIds['TABS_PANEL_ID']?>">
		<ul class="product-item-detail-tabs-list">
			<?php
			if ($showDescription)
			{
				?>
				<li class="product-item-detail-tab active" data-entity="tab" data-value="description">
					<a href="javascript:void(0);" class="product-item-detail-tab-link">
						<span><?=$arParams['MESS_DESCRIPTION_TAB']?></span>
					</a>
				</li>
				<?php
			}

			if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS'])
			{
				?>
				<li class="product-item-detail-tab" data-entity="tab" data-value="properties">
					<a href="javascript:void(0);" class="product-item-detail-tab-link">
						<span><?=$arParams['MESS_PROPERTIES_TAB']?></span>
					</a>
				</li>
				<?php
			}

			if ($arParams['USE_COMMENTS'] === 'Y')
			{
				?>
				<li class="product-item-detail-tab" data-entity="tab" data-value="comments">
					<a href="javascript:void(0);" class="product-item-detail-tab-link">
						<span><?=$arParams['MESS_COMMENTS_TAB']?></span>
					</a>
				</li>
				<?php
			}
			?>
		</ul>
	</div>

	<meta itemprop="name" content="<?=$name?>" />
	<meta itemprop="category" content="<?=$arResult['CATEGORY_PATH']?>" />
	<?php
	if ($haveOffers)
	{
		foreach ($arResult['JS_OFFERS'] as $offer)
		{
			$currentOffersList = array();

			if (!empty($offer['TREE']) && is_array($offer['TREE']))
			{
				foreach ($offer['TREE'] as $propName => $skuId)
				{
					$propId = (int)mb_substr($propName, 5);

					foreach ($skuProps as $prop)
					{
						if ($prop['ID'] == $propId)
						{
							foreach ($prop['VALUES'] as $propId => $propValue)
							{
								if ($propId == $skuId)
								{
									$currentOffersList[] = $propValue['NAME'];
									break;
								}
							}
						}
					}
				}
			}

			$offerPrice = $offer['ITEM_PRICES'][$offer['ITEM_PRICE_SELECTED']];
			?>
			<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				<meta itemprop="sku" content="<?=htmlspecialcharsbx(implode('/', $currentOffersList))?>" />
				<meta itemprop="price" content="<?=$offerPrice['RATIO_PRICE']?>" />
				<meta itemprop="priceCurrency" content="<?=$offerPrice['CURRENCY']?>" />
				<link itemprop="availability" href="http://schema.org/<?=($offer['CAN_BUY'] ? 'InStock' : 'OutOfStock')?>" />
			</span>
			<?php
		}

		unset($offerPrice, $currentOffersList);
	}
	else
	{
		?>
		<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<meta itemprop="price" content="<?=$price['RATIO_PRICE']?>" />
			<meta itemprop="priceCurrency" content="<?=$price['CURRENCY']?>" />
			<link itemprop="availability" href="http://schema.org/<?=($actualItem['CAN_BUY'] ? 'InStock' : 'OutOfStock')?>" />
		</span>
		<?php
	}
	?>
</div>
<?php
if ($haveOffers)
{
	$offerIds = array();
	$offerCodes = array();

	$useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';

	foreach ($arResult['JS_OFFERS'] as $ind => &$jsOffer)
	{
		$offerIds[] = (int)$jsOffer['ID'];
		$offerCodes[] = $jsOffer['CODE'];

		$fullOffer = $arResult['OFFERS'][$ind];
		$measureName = $fullOffer['ITEM_MEASURE']['TITLE'];

		$strAllProps = '';
		$strMainProps = '';
		$strPriceRangesRatio = '';
		$strPriceRanges = '';

		if ($arResult['SHOW_OFFERS_PROPS'])
		{
			if (!empty($jsOffer['DISPLAY_PROPERTIES']))
			{
				foreach ($jsOffer['DISPLAY_PROPERTIES'] as $property)
				{
					$current = '<dt>'.$property['NAME'].'</dt><dd>'.(
						is_array($property['VALUE'])
							? implode(' / ', $property['VALUE'])
							: $property['VALUE']
						).'</dd>';
					$strAllProps .= $current;

					if (isset($arParams['MAIN_BLOCK_OFFERS_PROPERTY_CODE'][$property['CODE']]))
					{
						$strMainProps .= $current;
					}
				}

				unset($current);
			}
		}

		if ($arParams['USE_PRICE_COUNT'] && count($jsOffer['ITEM_QUANTITY_RANGES']) > 1)
		{
			$strPriceRangesRatio = '('.Loc::getMessage(
					'CT_BCE_CATALOG_RATIO_PRICE',
					array('#RATIO#' => ($useRatio
							? $fullOffer['ITEM_MEASURE_RATIOS'][$fullOffer['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']
							: '1'
						).' '.$measureName)
				).')';

			foreach ($jsOffer['ITEM_QUANTITY_RANGES'] as $range)
			{
				if ($range['HASH'] !== 'ZERO-INF')
				{
					$itemPrice = false;

					foreach ($jsOffer['ITEM_PRICES'] as $itemPrice)
					{
						if ($itemPrice['QUANTITY_HASH'] === $range['HASH'])
						{
							break;
						}
					}

					if ($itemPrice)
					{
						$strPriceRanges .= '<dt>'.Loc::getMessage(
								'CT_BCE_CATALOG_RANGE_FROM',
								array('#FROM#' => $range['SORT_FROM'].' '.$measureName)
							).' ';

						if (is_infinite($range['SORT_TO']))
						{
							$strPriceRanges .= Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
						}
						else
						{
							$strPriceRanges .= Loc::getMessage(
								'CT_BCE_CATALOG_RANGE_TO',
								array('#TO#' => $range['SORT_TO'].' '.$measureName)
							);
						}

						$strPriceRanges .= '</dt><dd>'.($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE']).'</dd>';
					}
				}
			}

			unset($range, $itemPrice);
		}

		$jsOffer['DISPLAY_PROPERTIES'] = $strAllProps;
		$jsOffer['DISPLAY_PROPERTIES_MAIN_BLOCK'] = $strMainProps;
		$jsOffer['PRICE_RANGES_RATIO_HTML'] = $strPriceRangesRatio;
		$jsOffer['PRICE_RANGES_HTML'] = $strPriceRanges;
	}

	$templateData['OFFER_IDS'] = $offerIds;
	$templateData['OFFER_CODES'] = $offerCodes;
	unset($jsOffer, $strAllProps, $strMainProps, $strPriceRanges, $strPriceRangesRatio, $useRatio);

	$jsParams = array(
		'CONFIG' => array(
			'USE_CATALOG' => $arResult['CATALOG'],
			'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
			'SHOW_PRICE' => true,
			'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
			'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
			'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
			'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
			'SHOW_SKU_PROPS' => $arResult['SHOW_OFFERS_PROPS'],
			'OFFER_GROUP' => $arResult['OFFER_GROUP'],
			'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
			'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
			'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
			'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
			'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
			'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
			'USE_STICKERS' => true,
			'USE_SUBSCRIBE' => $showSubscribe,
			'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
			'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
			'ALT' => $alt,
			'TITLE' => $title,
			'MAGNIFIER_ZOOM_PERCENT' => 200,
			'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
			'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
			'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
				? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
				: null,
			'SHOW_SKU_DESCRIPTION' => $arParams['SHOW_SKU_DESCRIPTION'],
			'DISPLAY_PREVIEW_TEXT_MODE' => $arParams['DISPLAY_PREVIEW_TEXT_MODE']
		),
		'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
		'VISUAL' => $itemIds,
		'DEFAULT_PICTURE' => array(
			'PREVIEW_PICTURE' => $arResult['DEFAULT_PICTURE'],
			'DETAIL_PICTURE' => $arResult['DEFAULT_PICTURE']
		),
		'PRODUCT' => array(
			'ID' => $arResult['ID'],
			'ACTIVE' => $arResult['ACTIVE'],
			'NAME' => $arResult['~NAME'],
			'CATEGORY' => $arResult['CATEGORY_PATH'],
			'DETAIL_TEXT' => $arResult['DETAIL_TEXT'],
			'DETAIL_TEXT_TYPE' => $arResult['DETAIL_TEXT_TYPE'],
			'PREVIEW_TEXT' => $arResult['PREVIEW_TEXT'],
			'PREVIEW_TEXT_TYPE' => $arResult['PREVIEW_TEXT_TYPE']
		),
		'BASKET' => array(
			'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
			'BASKET_URL' => $arParams['BASKET_URL'],
			'SKU_PROPS' => $arResult['OFFERS_PROP_CODES'],
			'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
			'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
		),
		'OFFERS' => $arResult['JS_OFFERS'],
		'OFFER_SELECTED' => $arResult['OFFERS_SELECTED'],
		'TREE_PROPS' => $skuProps
	);
}
else
{
	$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
	if ($arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !$emptyProductProperties)
	{
		?>
		<div id="<?=$itemIds['BASKET_PROP_DIV']?>" style="display: none;">
			<?php
			if (!empty($arResult['PRODUCT_PROPERTIES_FILL']))
			{
				foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propId => $propInfo)
				{
					?>
					<input type="hidden" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]" value="<?=htmlspecialcharsbx($propInfo['ID'])?>">
					<?php
					unset($arResult['PRODUCT_PROPERTIES'][$propId]);
				}
			}

			$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
			if (!$emptyProductProperties)
			{
				?>
				<table>
					<?php
					foreach ($arResult['PRODUCT_PROPERTIES'] as $propId => $propInfo)
					{
						?>
						<tr>
							<td><?=$arResult['PROPERTIES'][$propId]['NAME']?></td>
							<td>
								<?php
								if (
									$arResult['PROPERTIES'][$propId]['PROPERTY_TYPE'] === 'L'
									&& $arResult['PROPERTIES'][$propId]['LIST_TYPE'] === 'C'
								)
								{
									foreach ($propInfo['VALUES'] as $valueId => $value)
									{
										?>
										<label>
											<input type="radio" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]"
												value="<?=$valueId?>" <?=($valueId == $propInfo['SELECTED'] ? '"checked"' : '')?>>
											<?=$value?>
										</label>
										<br>
										<?php
									}
								}
								else
								{
									?>
									<select name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]">
										<?php
										foreach ($propInfo['VALUES'] as $valueId => $value)
										{
											?>
											<option value="<?=$valueId?>" <?=($valueId == $propInfo['SELECTED'] ? '"selected"' : '')?>>
												<?=$value?>
											</option>
											<?php
										}
										?>
									</select>
									<?php
								}
								?>
							</td>
						</tr>
						<?php
					}
					?>
				</table>
				<?php
			}
			?>
		</div>
		<?php
	}

	$jsParams = array(
		'CONFIG' => array(
			'USE_CATALOG' => $arResult['CATALOG'],
			'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
			'SHOW_PRICE' => !empty($arResult['ITEM_PRICES']),
			'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
			'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
			'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
			'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
			'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
			'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
			'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
			'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
			'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
			'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
			'USE_STICKERS' => true,
			'USE_SUBSCRIBE' => $showSubscribe,
			'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
			'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
			'ALT' => $alt,
			'TITLE' => $title,
			'MAGNIFIER_ZOOM_PERCENT' => 200,
			'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
			'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
			'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
				? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
				: null
		),
		'VISUAL' => $itemIds,
		'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
		'PRODUCT' => array(
			'ID' => $arResult['ID'],
			'ACTIVE' => $arResult['ACTIVE'],
			'PICT' => reset($arResult['MORE_PHOTO']),
			'NAME' => $arResult['~NAME'],
			'SUBSCRIPTION' => true,
			'ITEM_PRICE_MODE' => $arResult['ITEM_PRICE_MODE'],
			'ITEM_PRICES' => $arResult['ITEM_PRICES'],
			'ITEM_PRICE_SELECTED' => $arResult['ITEM_PRICE_SELECTED'],
			'ITEM_QUANTITY_RANGES' => $arResult['ITEM_QUANTITY_RANGES'],
			'ITEM_QUANTITY_RANGE_SELECTED' => $arResult['ITEM_QUANTITY_RANGE_SELECTED'],
			'ITEM_MEASURE_RATIOS' => $arResult['ITEM_MEASURE_RATIOS'],
			'ITEM_MEASURE_RATIO_SELECTED' => $arResult['ITEM_MEASURE_RATIO_SELECTED'],
			'SLIDER_COUNT' => $arResult['MORE_PHOTO_COUNT'],
			'SLIDER' => $arResult['MORE_PHOTO'],
			'CAN_BUY' => $arResult['CAN_BUY'],
			'CHECK_QUANTITY' => $arResult['CHECK_QUANTITY'],
			'QUANTITY_FLOAT' => is_float($arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']),
			'MAX_QUANTITY' => $arResult['PRODUCT']['QUANTITY'],
			'STEP_QUANTITY' => $arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'],
			'CATEGORY' => $arResult['CATEGORY_PATH']
		),
		'BASKET' => array(
			'ADD_PROPS' => $arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y',
			'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
			'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
			'EMPTY_PROPS' => $emptyProductProperties,
			'BASKET_URL' => $arParams['BASKET_URL'],
			'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
			'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
		)
	);
	unset($emptyProductProperties);
}

if ($arParams['DISPLAY_COMPARE'])
{
	$jsParams['COMPARE'] = array(
		'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
		'COMPARE_DELETE_URL_TEMPLATE' => $arResult['~COMPARE_DELETE_URL_TEMPLATE'],
		'COMPARE_PATH' => $arParams['COMPARE_PATH']
	);
}

$jsParams["IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED"] =
	$arResult["IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED"]
;

?>
<script>
	BX.message({
		ECONOMY_INFO_MESSAGE: '<?=GetMessageJS('CT_BCE_CATALOG_ECONOMY_INFO2')?>',
		TITLE_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_ERROR')?>',
		TITLE_BASKET_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_BASKET_PROPS')?>',
		BASKET_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_BASKET_UNKNOWN_ERROR')?>',
		BTN_SEND_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_SEND_PROPS')?>',
		BTN_MESSAGE_DETAIL_BASKET_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_BASKET_REDIRECT')?>',
		BTN_MESSAGE_CLOSE: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE')?>',
		BTN_MESSAGE_DETAIL_CLOSE_POPUP: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE_POPUP')?>',
		TITLE_SUCCESSFUL: '<?=GetMessageJS('CT_BCE_CATALOG_ADD_TO_BASKET_OK')?>',
		COMPARE_MESSAGE_OK: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_OK')?>',
		COMPARE_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_UNKNOWN_ERROR')?>',
		COMPARE_TITLE: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_TITLE')?>',
		BTN_MESSAGE_COMPARE_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT')?>',
		PRODUCT_GIFT_LABEL: '<?=GetMessageJS('CT_BCE_CATALOG_PRODUCT_GIFT_LABEL')?>',
		PRICE_TOTAL_PREFIX: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_PRICE_TOTAL_PREFIX')?>',
		RELATIVE_QUANTITY_MANY: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY'])?>',
		RELATIVE_QUANTITY_FEW: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW'])?>',
		SITE_ID: '<?=CUtil::JSEscape($component->getSiteId())?>'
	});

	var <?=$obName?> = new JCCatalogElement(<?=CUtil::PhpToJSObject($jsParams, false, true)?>);
</script>
<?php
unset($actualItem, $itemIds, $jsParams);
