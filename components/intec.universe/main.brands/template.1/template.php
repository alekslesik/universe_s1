<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

?>
<?= Html::beginTag('div', [
    'id' => 'i-18-intec-universe-main-brands-template-1-ZxlMNuLRZfyp',
    'class' => [
        'widget',
        'c-brands',
        'c-brands-template-1'
    ],
    'data' => [
        'effect' => $arVisual['EFFECT'],
        'slider' => $arVisual['SLIDER']['USE'] ? 'true' : 'false',
        'slider-dots' => $arVisual['SLIDER']['DOTS'] ? 'true' : 'false',
        'slider-navigation' => $arVisual['SLIDER']['NAVIGATION'] ? 'true' : 'false',
        'columns' => $arVisual['COLUMNS']
    ]
]) ?>
    <div class="widget-wrapper intec-content intec-content-visible">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                        <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                            <div class="intec-grid-item">
                                <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                    'class' => [
                                        'widget-title',
                                        'align-'.$arBlocks['HEADER']['POSITION'],
                                        $arBlocks['FOOTER']['BUTTON']['SHOW'] ? 'widget-title-margin' : null
                                    ]
                                ]) ?>
                            </div>
                            <?php if ($arBlocks['FOOTER']['BUTTON']['SHOW']) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'widget-all-container' => true,
                                        'mobile' => $arBlocks['HEADER']['SHOW'],
                                        'intec-grid-item' => [
                                            'auto' => $arBlocks['HEADER']['SHOW'],
                                            '1' => !$arBlocks['HEADER']['SHOW']
                                        ]
                                    ], true)
                                ]) ?>
                                    <?= Html::beginTag('a', [
                                        'class' => [
                                            'widget-all-button',
                                            'intec-cl-text-light-hover',
                                        ],
                                        'href' => $arBlocks['FOOTER']['BUTTON']['LINK']
                                    ])?>
                                        <i class="fal fa-angle-right"></i>
                                    <?= Html::endTag('a')?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                            <div class="intec-grid-item-1">
                                <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                                    <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-items' => true,
                        'intec-grid' => [
                            '' => !$arVisual['SLIDER']['USE'],
                            'wrap' => !$arVisual['SLIDER']['USE'],
                            'a-v-start' => !$arVisual['SLIDER']['USE'],
                            'a-h-'.$arVisual['ALIGNMENT'] => !$arVisual['SLIDER']['USE'],
                            'i-15' => !$arVisual['SLIDER']['USE'],
                            '600-nowrap' => !$arVisual['SLIDER']['USE']
                        ],
                        'owl-carousel' => $arVisual['SLIDER']['USE']
                    ], true),
                    'data' => [
                        'role' => $arVisual['SLIDER']['USE'] ? 'slider' : null
                    ]
                ]) ?>

                    <?php foreach ($arResult['ITEMS'] as $arItem) {
                        
                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';
                        $sPicture = $arItem['PREVIEW_PICTURE'];

                        if (empty($sPicture))
                            $sPicture = $arItem['DETAIL_PICTURE'];

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet($sPicture, [
                                'width' => 400,
                                'height' => 400
                            ], BX_RESIZE_IMAGE_PROPORTIONAL);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => !$arVisual['SLIDER']['USE'],
                                    '1024-4' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] >= 5,
                                    '768-3' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] >= 4,
                                    '600-2' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] >= 3,
                                    '375-1' => !$arVisual['SLIDER']['USE']
                                ]
                            ], true)
                        ]) ?>
                            <?= Html::beginTag($sTag, [
                                'id' => $sAreaId,
                                'class' => 'widget-item-wrapper',
                                'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                'target' => $arVisual['LINK']['USE'] && $arVisual['LINK']['BLANK'] ? '_blank' : null
                            ]) ?>
                                <?= Html::tag('div', null, [
                                    'class' => 'widget-item-picture',
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ],
                                    'style' => [
                                        'opacity' => $arVisual['TRANSPARENCY'],
                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                    ]
                                ]) ?>
                            <?= Html::endTag($sTag) ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
            <?php if ($arBlocks['FOOTER']['SHOW']) { ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-footer' => true,
                        'align-' . $arBlocks['FOOTER']['POSITION'] => true,
                        'mobile' => $arBlocks['HEADER']['SHOW'],
                        'intec-grid-item' => [
                            'auto' => $arBlocks['HEADER']['SHOW'],
                            '1' => !$arBlocks['HEADER']['SHOW']
                        ]
                    ], true)
                ]) ?>
                    <?php if ($arBlocks['FOOTER']['BUTTON']['SHOW']) { ?>
                        <a href="<?= $arBlocks['FOOTER']['BUTTON']['LINK'] ?>" class="<?= Html::cssClassFromArray([
                            'widget-footer-button',
                            'intec-ui' => [
                                '',
                                'control-button',
                                'mod-transparent',
                                'mod-round-half',
                                'scheme-current',
                                'size-5'
                            ]
                        ]) ?>">
                            <?= Html::encode($arBlocks['FOOTER']['BUTTON']['TEXT']) ?>
                        </a>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
        </div>

<!-- MOBILE CAROUSEL whit 8 elements-->

<div class="widget-wrapper-2 intec-content-wrapper-mobile">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center">
                        <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                            <div class="intec-grid-item">
                                <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                    'class' => [
                                        'widget-title',
                                        'align-'.$arBlocks['HEADER']['POSITION'],
                                        $arBlocks['FOOTER']['BUTTON']['SHOW'] ? 'widget-title-margin' : null
                                    ]
                                ]) ?>
                            </div>
                            <?php if ($arBlocks['FOOTER']['BUTTON']['SHOW']) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'widget-all-container' => true,
                                        'mobile' => $arBlocks['HEADER']['SHOW'],
                                        'intec-grid-item' => [
                                            'auto' => $arBlocks['HEADER']['SHOW'],
                                            '1' => !$arBlocks['HEADER']['SHOW']
                                        ]
                                    ], true)
                                ]) ?>
                                    <?= Html::beginTag('a', [
                                        'class' => [
                                            'widget-all-button',
                                            'intec-cl-text-light-hover',
                                        ],
                                        'href' => $arBlocks['FOOTER']['BUTTON']['LINK']
                                    ])?>
                                        <i class="fal fa-angle-right"></i>
                                    <?= Html::endTag('a')?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                            <div class="intec-grid-item-1">
                                <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                                    <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-items' => true,
                        'intec-grid' => [
                            '' => !$arVisual['SLIDER']['USE'],
                            'wrap' => !$arVisual['SLIDER']['USE'],
                            'a-v-start' => !$arVisual['SLIDER']['USE'],
                            'a-h-between' => !$arVisual['SLIDER']['USE'],
                            'i-0' => !$arVisual['SLIDER']['USE'],
                            '600-wrap' => !$arVisual['SLIDER']['USE']
                        ],
                        'owl-carousel' => $arVisual['SLIDER']['USE']
                    ], true),
                    'data' => [
                        'role' => $arVisual['SLIDER']['USE'] ? 'slider' : null
                    ]
                ]) ?>
<!-- Only 8 elements -->
                    <?php 
                    $counter = 0;
                    foreach ($arResult['ITEMS'] as $arItem) {
                        $counter++;

                        if ($counter == 9) break;
                        
                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';
                        $sPicture = $arItem['PREVIEW_PICTURE'];

                        if (empty($sPicture))
                            $sPicture = $arItem['DETAIL_PICTURE'];

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet($sPicture, [
                                'width' => 400,
                                'height' => 400
                            ], BX_RESIZE_IMAGE_PROPORTIONAL);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => !$arVisual['SLIDER']['USE'],
                                    '1024-4' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] >= 5,
                                    '768-3' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] >= 4,
                                    '600-2' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] >= 3,
                                    '375-1' => !$arVisual['SLIDER']['USE']
                                ]
                            ], true)
                        ]) ?>
                            <?= Html::beginTag($sTag, [
                                'id' => $sAreaId,
                                'class' => 'widget-item-wrapper',
                                'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                'target' => $arVisual['LINK']['USE'] && $arVisual['LINK']['BLANK'] ? '_blank' : null
                            ]) ?>
                                <?= Html::tag('div', null, [
                                    'class' => 'widget-item-picture',
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ],
                                    'style' => [
                                        'opacity' => $arVisual['TRANSPARENCY'],
                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                    ]
                                ]) ?>
                            <?= Html::endTag($sTag) ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
            <?php if ($arBlocks['FOOTER']['SHOW']) { ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-footer' => true,
                        'align-' . $arBlocks['FOOTER']['POSITION'] => true,
                        'mobile' => $arBlocks['HEADER']['SHOW'],
                        'intec-grid-item' => [
                            'auto' => $arBlocks['HEADER']['SHOW'],
                            '1' => !$arBlocks['HEADER']['SHOW']
                        ]
                    ], true)
                ]) ?>
                    <?php if ($arBlocks['FOOTER']['BUTTON']['SHOW']) { ?>
                        <a href="<?= $arBlocks['FOOTER']['BUTTON']['LINK'] ?>" class="<?= Html::cssClassFromArray([
                            'widget-footer-button',
                            'intec-ui' => [
                                '',
                                'control-button',
                                'mod-transparent',
                                'mod-round-half',
                                'scheme-current',
                                'size-5'
                            ]
                        ]) ?>">
                            <?= Html::encode($arBlocks['FOOTER']['BUTTON']['TEXT']) ?>
                        </a>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
        </div>

        
    </div>
<style>
.intec-grid>.intec-grid-item-5 {
    -ms-flex: 0 0 20%;
    flex: 0 0 20%;
    max-width: 14%;
}

.intec-grid.intec-grid-i-15>.intec-grid-item-5 {
    padding-top: 0px;
    padding-bottom: 0px;
    padding-right: 0px;
    padding-left: 0px;
}
</style>
    <?php include (__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>