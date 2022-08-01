<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
$this->setFrameMode(true);
global $APPLICATION;
?>
<div class="feedback send">
    <div class="block-title">ОСТАВЬТЕ СВОЙ ОТЗЫВ</div>
    <div class="feedback-product__card feedback-product">
        <div class="feedback-product__image"><img src="<?=$arResult['PRODUCT']['DETAIL_PICTURE']?>" alt=""></div>
        <div class="feedback-product__content"><b><?=$arResult['PRODUCT']['NAME']?></b><?/*<span>100 мл</span>*/?></div>
    </div>
    <input type="hidden" id="product_id" value="<?=$arResult['PRODUCT']['ID']?>">
    <input type="hidden" id="ajax_url" value="<?=$this->GetFolder().'/ajax.php'?>">
    <div class="feedback-product__card feedback-rating">
        <div class="block-title">ОЦЕНИТЕ ПРОДУКТ</div>
        <div class="feedback-rating__content">
            <div class="feedback-rating__row common">
                <div class="feedback-rating__name">Общий рейтинг</div>
                <div class="rating-stars">
                    <div class="rating-star" data-value="1">
                        <div class="icon icon-star"></div>
                    </div>
                    <div class="rating-star" data-value="2">
                        <div class="icon icon-star"></div>
                    </div>
                    <div class="rating-star" data-value="3">
                        <div class="icon icon-star"></div>
                    </div>
                    <div class="rating-star" data-value="4">
                        <div class="icon icon-star"></div>
                    </div>
                    <div class="rating-star" data-value="5">
                        <div class="icon icon-star"></div>
                    </div>
                </div>
            </div>
            <div class="feedback-rating__row quality">
                <div class="feedback-rating__name">Качество</div>
                <div class="rating-stars">
                    <div class="rating-star" data-value="1">
                        <div class="icon icon-star"></div>
                    </div>
                    <div class="rating-star" data-value="2">
                        <div class="icon icon-star"></div>
                    </div>
                    <div class="rating-star" data-value="3">
                        <div class="icon icon-star"></div>
                    </div>
                    <div class="rating-star" data-value="4">
                        <div class="icon icon-star"></div>
                    </div>
                    <div class="rating-star" data-value="5">
                        <div class="icon icon-star"></div>
                    </div>
                </div>
            </div>
            <div class="feedback-rating__row easily">
                <div class="feedback-rating__name">Простота применения</div>
                <div class="rating-stars">
                    <div class="rating-star" data-value="1">
                        <div class="icon icon-star"></div>
                    </div>
                    <div class="rating-star" data-value="2">
                        <div class="icon icon-star"></div>
                    </div>
                    <div class="rating-star" data-value="3">
                        <div class="icon icon-star"></div>
                    </div>
                    <div class="rating-star" data-value="4">
                        <div class="icon icon-star"></div>
                    </div>
                    <div class="rating-star" data-value="5">
                        <div class="icon icon-star"></div>
                    </div>
                </div>
            </div>
            <div class="feedback-rating__row benefit">
                <div class="feedback-rating__name">Польза</div>
                <div class="rating-stars">
                    <div class="rating-star" data-value="1">
                        <div class="icon icon-star"></div>
                    </div>
                    <div class="rating-star" data-value="2">
                        <div class="icon icon-star"></div>
                    </div>
                    <div class="rating-star" data-value="3">
                        <div class="icon icon-star"></div>
                    </div>
                    <div class="rating-star" data-value="4">
                        <div class="icon icon-star"></div>
                    </div>
                    <div class="rating-star" data-value="5">
                        <div class="icon icon-star"></div>
                    </div>
                </div>
            </div>
            <div class="feedback-rating__row">
                <div class="feedback-rating__name">Я рекомендую</div>
                <div class="feedback-rating__row-req">
                    <label class="radio" for="req-1">
                        <input class="radio__input" type="radio" value="1" id="req-1" name="req" checked="checked">
                        <div class="radio__container">
                            <div class="radio__icon"></div>
                            <div class="radio__label">Да</div>
                        </div>
                    </label>
                    <label class="radio" for="req-2">
                        <input class="radio__input" type="radio" id="req-2" name="req" value="0">
                        <div class="radio__container">
                            <div class="radio__icon"></div>
                            <div class="radio__label">Нет</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="feedback-product__card">
        <div class="block-title">ВАШ ОТЗЫВ</div>
        <div class="feedback-product__int">
            <div class="feedback-product__int-label">Заголовок</div>
            <div class="feedback-product__int-input">
                <input name="review_title">
                <span>Например: Очень приятный вкус!</span>
            </div>
        </div>
        <div class="feedback-product__int">
            <div class="feedback-product__int-label">Текст отзыва</div>
            <div class="feedback-product__int-textarea">
                <textarea name="review_text"></textarea>
            </div>
        </div>
        <div class="feedback-product__int">
            <div class="feedback-product__int-label"></div>
            <div class="feedback-product__int-group">
                <div class="input">
                    <label>Плюсы
                    </label>
                    <input name="review_pluses">
                </div>
                <div class="input">
                    <label>Минусы
                    </label>
                    <input name="review_minuses">
                </div>
            </div>
        </div>
    </div>
    <div class="feedback-product__card">
        <div class="block-title">Личная информация</div>
        <div class="feedback-product__int">
            <div class="feedback-product__int-label">Никнейм</div>
            <div class="feedback-product__int-input">
                <input name="review_nickname"><span>Например: Алина759. Не указывайте полные ФИО или e-mail в качестве никнейма из соображений конфиденциальности</span>
            </div>
        </div>
        <div class="feedback-product__int">
            <div class="feedback-product__int-label">Местоположение</div>
            <div class="feedback-product__int-input">
                <input name="review_place"><span>Например: Екатеринбург</span>
            </div>
        </div>
    </div>
    <div class="button button_primary send_request">Отправить отзыв</div>
</div>


