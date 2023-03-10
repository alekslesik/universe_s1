<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
    <?php

use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arCodes
 */

$arFiles = [];

/**
 * @param $arItem
 * @param $sProperty
 */
$hCollect = function (&$arItem, $sProperty = null) use (&$arFiles) {
    if (!empty($arItem['PREVIEW_PICTURE']) && !Type::isArray($arItem['PREVIEW_PICTURE'])) {
        $arFiles[] = $arItem['PREVIEW_PICTURE'];
    } else {
        $arFiles[] = $arItem['PREVIEW_PICTURE']['ID'];
    }

    if (!empty($arItem['DETAIL_PICTURE']) && !Type::isArray($arItem['DETAIL_PICTURE']))
        $arFiles[] = $arItem['DETAIL_PICTURE'];

    if (!empty($sProperty)) {
        $arPictures = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $sProperty,
            'VALUE'
        ]);

        if (!Type::isArray($arPictures))
            $arPictures = [];

        foreach ($arPictures as $iPicture)
            $arFiles[] = $iPicture;
    }
};

$hCollect($arResult['ITEM'], $arParams['PROPERTY_PICTURES']);

if (!empty($arFiles)) {
    $arFiles = Arrays::fromDBResult(CFile::GetList([], [
        '@ID' => implode(',', $arFiles)
    ]))->each(function ($iIndex, &$arFile) {
        $arFile['SRC'] = CFile::GetFileSRC($arFile);
    })->indexBy('ID');
} else {
    $arFiles = new Arrays();
}

/**
 * @param $arItem
 * @param $sProperty
 */
$hSet = function (&$arItem, $sProperty = null) use (&$arResult, &$arFiles) {
    $arItem['PICTURES'] = [
        'PROPERTIES' => [
            'ALT' => $arResult['ITEM']['NAME'],
            'TITLE' => $arResult['ITEM']['NAME'],
        ],
        'VALUES' => []
    ];

    if (!empty($arItem['PREVIEW_PICTURE']) && !Type::isArray($arItem['PREVIEW_PICTURE'])) {
        $arItem['PREVIEW_PICTURE'] = $arFiles->get($arItem['PREVIEW_PICTURE']);
    } else {
        $arItem['PREVIEW_PICTURE'] = $arFiles->get($arItem['PREVIEW_PICTURE']['ID']);
    }

    if (!empty($arItem['DETAIL_PICTURE']) && !Type::isArray($arItem['DETAIL_PICTURE']))
        $arItem['DETAIL_PICTURE'] = $arFiles->get($arItem['DETAIL_PICTURE']);

    if (!empty($arItem['DETAIL_PICTURE'])) {
        $arItem['PICTURES']['VALUES'][] = $arItem['DETAIL_PICTURE'];

        if (!empty($arItem['DETAIL_PICTURE']['ALT']))
            $arItem['PICTURES']['PROPERTIES']['ALT'] = $arItem['DETAIL_PICTURE']['ALT'];

        if (!empty($arItem['DETAIL_PICTURE']['TITLE']))
            $arItem['PICTURES']['PROPERTIES']['TITLE'] = $arItem['DETAIL_PICTURE']['TITLE'];

    } else if (!empty($arItem['PREVIEW_PICTURE'])) {
        $arItem['PICTURES']['VALUES'][] = $arItem['PREVIEW_PICTURE'];

        if (!empty($arItem['PREVIEW_PICTURE']['ALT']))
            $arItem['PICTURES']['PROPERTIES']['ALT'] = $arItem['PREVIEW_PICTURE']['ALT'];

        if (!empty($arItem['PREVIEW_PICTURE']['TITLE']))
            $arItem['PICTURES']['PROPERTIES']['TITLE'] = $arItem['PREVIEW_PICTURE']['TITLE'];
    }

    if (!empty($sProperty)) {
        $arPictures = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $sProperty,
            'VALUE'
        ]);

        if (!Type::isArray($arPictures))
            $arPictures = [];

        foreach ($arPictures as $iPicture) {
            $arPicture = $arFiles->get($iPicture);

            if (empty($arPicture))
                continue;

            $arItem['PICTURES']['VALUES'][] = $arPicture;
        }
    }
};

$hSet($arResult['ITEM'], $arParams['PROPERTY_PICTURES']);

unset($hCollect);
unset($hSet);
unset($arFiles);