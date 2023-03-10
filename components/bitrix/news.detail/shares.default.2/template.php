<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 */

$this->setFrameMode(true);

if (!Loader::includeModule('intec.core'))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];

Loc::loadMessages(__FILE__);

?>

<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-news-detail',
        'c-news-detail-shares-default-2'
    ]
]) ?>
    <?php
        foreach ($arResult['BLOCKS'] as $arKey => $arValue) {
            if ($arValue['ACTIVE'])
                include($arValue['PATH']);
        }
    ?>
<?= Html::endTag('div') ?>
<?php include(__DIR__.'/parts/script.php'); ?>
