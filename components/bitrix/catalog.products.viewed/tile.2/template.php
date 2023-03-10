<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];
$iCounter = 1;

/**
 * @var Closure $vCounter
 * @var Closure $dData
 * @var Closure $vButtons
 * @var Closure $vImage
 * @var Closure $vPrice
 * @var Closure $vPurchase
 * @var Closure $vQuantity
 * @var Closure $vSku
 * @var Closure $vSkuExtended
 * @var Closure $vQuickView
 */

include(__DIR__.'/parts/counter.php');
include(__DIR__.'/parts/data.php');
include(__DIR__.'/parts/buttons.php');
include(__DIR__.'/parts/image.php');
include(__DIR__.'/parts/price.php');
include(__DIR__.'/parts/purchase.php');
include(__DIR__.'/parts/quantity.php');
include(__DIR__.'/parts/sku.php');

$arSkuExtended = [];

if ($arVisual['OFFERS']['USE'] && $arVisual['OFFERS']['VIEW'] === 'extended')
    foreach ($arVisual['OFFERS']['EXTENDED'] as $sKeySide => $sCode)
        if (!empty($sCode))
            foreach ($arResult['SKU_PROPS'] as $sKeySku => $arSku)
                if ($arSku['code'] === "P_$sCode")
                    $arSkuExtended[$sKeySide] = $arSku;

?>

<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-products-viewed',
        'c-catalog-products-viewed-tile-2'
    ],
    'data' => [
        'borders' => $arVisual['BORDERS']['USE'] ? 'true' : 'false',
        'columns-desktop' => $arVisual['COLUMNS']['DESKTOP'],
        'columns-mobile' => $arVisual['COLUMNS']['MOBILE'],
        'properties' => !empty($arResult['SKU_PROPS']) ? Json::encode($arResult['SKU_PROPS'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : '',
        'button' => $arResult['ACTION'] !== 'none' ? 'true' : 'false'
    ]
]) ?>
    <div class="intec-content intec-content-visible catalog-products-viewed-wrapper">
        <div class="intec-content-wrapper catalog-products-viewed-wrapper-2">
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-products-viewed-items',
                    'intec-grid' => [
                        '',
                        'wrap',
                        'a-v-stretch',
                        'a-h-start'
                    ]
                ]
            ]) ?>
                <?php foreach ($arResult['ITEMS'] as $arItem) {

                $sId = $sTemplateId.'_'.$arItem['ID'];
                $sAreaId = $this->GetEditAreaId($sId);
                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                $sData = Json::encode($dData($arItem), JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true);
                $sLink = Html::decode($arItem['DETAIL_PAGE_URL']);
                $arPrice = null;

                $bSkuExtended = !empty($arItem['OFFERS']) && $arVisual['OFFERS']['VIEW'] === 'extended' && !empty($arSkuExtended);

                if (!empty($arItem['ITEM_PRICES']))
                    $arPrice = ArrayHelper::getFirstValue($arItem['ITEM_PRICES']);

                $arSkuProps = [];

                if (!empty($arResult['SKU_PROPS']))
                    $arSkuProps = $arResult['SKU_PROPS'];
                else if (!empty($arItem['SKU_PROPS']))
                    $arSkuProps = $arItem['SKU_PROPS'];

                ?>
                    <?= Html::beginTag('div', [
                        'id' => $sAreaId,
                        'class' => Html::cssClassFromArray([
                            'catalog-products-viewed-item' => true,
                            'intec-grid-item' => [
                                $arVisual['COLUMNS']['DESKTOP'] => true,
                                '500-1' => $arVisual['COLUMNS']['DESKTOP'] <= 4 && $arVisual['COLUMNS']['MOBILE'] == 1,
                                '800-2' => $arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 2,
                                '1000-3' => $arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 3,
                                '700-2' => !$arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 2,
                                '720-3' => !$arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 2,
                                '950-2' => !$arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 2,
                                '1200-3' => !$arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 3
                            ]
                        ],  true),
                        'data' => [
                            'id' => $arItem['ID'],
                            'role' => 'item',
                            'products' => 'main',
                            'order' => $iCounter <= $arVisual['COLUMNS']['DESKTOP'] ? 0 : 2,
                            'data' => $sData,
                            'expanded' => 'false',
                            'available' => $arItem['CAN_BUY'] ? 'true' : 'false',
                            'entity' => 'items-row'
                        ]
                    ]) ?>
                        <div class="catalog-products-viewed-item-wrapper" data-borders-style="<?= $arVisual['BORDERS']['STYLE'] ?>">
                            <div class="catalog-products-viewed-item-base">
                                <?php if ($arVisual['NAME']['POSITION'] == 'top') { ?>
                                    <div class="catalog-products-viewed-item-name" data-align="<?= $arVisual['NAME']['ALIGN'] ?>">
                                        <?= Html::tag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', $arItem['NAME'], [
                                            'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $sLink : null,
                                            'class' => [
                                                'intec-cl-text-hover',
                                            ],
                                            'data' => [
                                                'role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                                            ]
                                        ]) ?>
                                    </div>
                                <?php } ?>
                                <div class="catalog-products-viewed-item-image-container">
                                    <?php $vImage($arItem) ?>
                                    <?php if ($arResult['QUICK_VIEW']['USE'] && !$arResult['QUICK_VIEW']['DETAIL']) { ?>
                                        <div class="catalog-products-viewed-item-quick-view intec-ui-align">
                                            <!--noindex-->
                                            <div class="catalog-products-viewed-item-quick-view-button" data-role="quick.view">
                                                <div class="catalog-products-viewed-item-quick-view-button-icon">
                                                    <i class="intec-ui-icon intec-ui-icon-eye-1"></i>
                                                </div>
                                                <div class="catalog-products-viewed-item-quick-view-button-text">
                                                    <?= Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_TEMPLATE_QUICK_VIEW') ?>
                                                </div>
                                            </div>
                                            <!--/noindex-->
                                        </div>
                                    <?php } ?>
                                    <!--noindex-->
                                    <?php if ($arItem['MARKS']['SHOW']) { ?>
                                        <div class="catalog-products-viewed-item-marks">
                                            <?php $APPLICATION->includeComponent(
                                                'intec.universe:main.markers',
                                                'template.1', [
                                                'HIT' => $arItem['MARKS']['VALUES']['HIT'] ? 'Y' : 'N',
                                                'NEW' => $arItem['MARKS']['VALUES']['NEW'] ? 'Y' : 'N',
                                                'RECOMMEND' => $arItem['MARKS']['VALUES']['RECOMMEND'] ? 'Y' : 'N',
                                                'ORIENTATION' => $arVisual['MARKS']['ORIENTATION']
                                            ],
                                                $component,
                                                ['HIDE_ICONS' => 'Y']
                                            ) ?>
                                        </div>
                                    <?php } ?>
                                    <?php $vButtons($arItem) ?>
                                    <?php if ($bSkuExtended) {
                                        $vSkuExtended($arSkuExtended);
                                    } ?>
                                    <!--/noindex-->
                                </div>
                                <!--noindex-->
                                <?php if ($arVisual['VOTE']['SHOW']) { ?>
                                    <div class="catalog-products-viewed-item-vote" data-align="<?= $arVisual['VOTE']['ALIGN'] ?>">
                                        <?php $APPLICATION->IncludeComponent(
                                            'bitrix:iblock.vote',
                                            'template.1',
                                            array(
                                                'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                                'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                                'ELEMENT_ID' => $arItem['ID'],
                                                'ELEMENT_CODE' => $arItem['CODE'],
                                                'MAX_VOTE' => '5',
                                                'VOTE_NAMES' => array(
                                                    0 => '1',
                                                    1 => '2',
                                                    2 => '3',
                                                    3 => '4',
                                                    4 => '5',
                                                ),
                                                'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                                'CACHE_TIME' => $arParams['CACHE_TIME'],
                                                'DISPLAY_AS_RATING' => $arVisual['VOTE']['MODE'] === 'rating' ? 'rating' : 'vote_avg',
                                                'SHOW_RATING' => 'N'
                                            ),
                                            $component,
                                            ['HIDE_ICONS' => 'Y']
                                        ) ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['QUANTITY']['SHOW']) { ?>
                                    <div class="catalog-products-viewed-item-quantity-wrap">
                                        <?php $vQuantity($arItem) ?>
                                    </div>
                                <?php } ?>
                                <!--/noindex-->
                                <div class="catalog-products-viewed-item-article" data-align="<?= $arVisual['NAME']['ALIGN'] ?>">
                                    <?php if ($arItem['ARTICLE']['SHOW']) { ?>
                                        <?= Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_TEMPLATE_ARTICLE', [
                                            '#ARTICLE#' => $arItem['ARTICLE']['VALUE']
                                        ]) ?>
                                    <?php } ?>
                                </div>
                                <?php if ($arVisual['NAME']['POSITION'] == 'middle') { ?>
                                    <div class="catalog-products-viewed-item-name" data-align="<?= $arVisual['NAME']['ALIGN'] ?>">
                                        <?= Html::tag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', $arItem['NAME'], [
                                            'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $sLink : null,
                                            'class' => [
                                                'intec-cl-text-hover',
                                            ],
                                            'data' => [
                                                'role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                                            ]
                                        ]) ?>
                                    </div>
                                <?php } ?>
                                <?php $vPrice($arPrice) ?>
                            </div>
                            <!--noindex-->
                            <div class="catalog-products-viewed-item-advanced">
                                <?php if ($arVisual['OFFERS']['USE'] && !empty($arItem['OFFERS']) && !empty($arSkuProps)) {
                                    $vSku($arSkuProps);
                                } ?>
                                <?php if ($arItem['ACTION'] !== 'none') { ?>
                                    <div class="catalog-products-viewed-item-purchase-container intec-grid intec-grid-a-v-center">
                                        <?php if ($arVisual['COUNTER']['SHOW'] && $arItem['ACTION'] === 'buy') {
                                            $vCounter();
                                        } ?>
                                        <div class="catalog-products-viewed-item-purchase intec-grid-item intec-grid-item-shrink-1">
                                            <?php $vPurchase($arItem) ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <!--/noindex-->
                        </div>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            <?= Html::endTag('div') ?>
        </div>
    </div>
<?= Html::endTag('div') ?>
<?php include(__DIR__.'/parts/script.php') ?>
