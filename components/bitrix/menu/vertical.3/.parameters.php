<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues;
 */

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('iblock'))
    return;

$arIBlocksTypes = CIBlockParameters::GetIBlockTypes();
$arIBlocksFilter = [];
$arIBlocksFilter['ACTIVE'] = 'Y';

if (!empty($arCurrentValues['IBLOCK_TYPE']))
    $arIBlocksFilter['TYPE'] = $arCurrentValues['IBLOCK_TYPE'];

$arIBlocks = Arrays::fromDBResult(CIBlock::GetList([
    'SORT' => 'ASC'
], $arIBlocksFilter))->indexBy('ID');

$arIBlock = $arIBlocks->get($arCurrentValues['IBLOCK_ID']);

$arTemplateParameters = [];
$arTemplateParameters['IBLOCK_TYPE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MENU_VERTICAL_3_PARAMETERS_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocksTypes,
    'REFRESH' => 'Y',
    'ADDITIONAL_VALUES' => 'Y'
];

$arTemplateParameters['IBLOCK_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MENU_VERTICAL_3_PARAMETERS_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) {
        return [
            'key' => $arIBlock['ID'],
            'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
        ];
    }),
    'REFRESH' => 'Y',
    'ADDITIONAL_VALUES' => 'Y'
];

$arTemplateParameters['MAIN_VIEW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MENU_VERTICAL_3_PARAMETERS_MAIN_VIEW'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'simple' => Loc::getMessage('C_MENU_VERTICAL_3_PARAMETERS_MAIN_VIEW_SIMPLE_1'),
        'pictures' => Loc::getMessage('C_MENU_VERTICAL_3_PARAMETERS_MAIN_VIEW_PICTURES_1')
    ]
];

if (!empty($arIBlock)) {
    $arFields = Arrays::fromDBResult(CUserTypeEntity::GetList([
        'SORT' => 'ASC'
    ], [
        'ENTITY_ID' => 'IBLOCK_'.$arIBlock['ID'].'_SECTION',
        'USER_TYPE_ID' => 'file'
    ]));

    $arTemplateParameters['PROPERTY_PICTURE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MENU_VERTICAL_3_PARAMETERS_PROPERTY_PICTURE'),
        'TYPE' => 'LIST',
        'VALUES' => $arFields->asArray(function ($iIndex, $arField) {
            return [
                'key' => $arField['FIELD_NAME'],
                'value' => $arField['FIELD_NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];
}

$arTemplateParameters['MAIN_LINK_SHOW'] = array(
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MENU_VERTICAL_3_MAIN_LINK_SHOW'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y',
    'DEFAULT' => 'N'
);

if ($arCurrentValues['MAIN_LINK_SHOW'] === 'Y') {
    $arTemplateParameters['MAIN_LINK'] = [
        'NAME' => Loc::getMessage('C_MENU_VERTICAL_3_MAIN_LINK'),
        'PARENT' => 'VISUAL',
        'TYPE' => 'STRING'
    ];

    $arTemplateParameters['MAIN_LINK_TEXT'] = [
        'NAME' => Loc::getMessage('C_MENU_VERTICAL_3_MAIN_LINK_TEXT'),
        'PARENT' => 'VISUAL',
        'TYPE' => 'STRING'
    ];
}