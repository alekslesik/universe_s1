<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('intec.core'))
    return;

$arTemplateParameters = [];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_PROJECTS_TEMPLATE_5_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];
$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_PROJECTS_TEMPLATE_5_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_PROJECTS_TEMPLATE_5_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        3 => '3',
        4 => '4'
    ],
    'DEFAULT' => 4
];
$arTemplateParameters['TABS_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_PROJECTS_TEMPLATE_5_TABS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['TABS_USE'] === 'Y') {
    $arTemplateParameters['TABS_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_PROJECTS_TEMPLATE_5_TABS_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_PROJECTS_TEMPLATE_5_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_PROJECTS_TEMPLATE_5_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_PROJECTS_TEMPLATE_5_POSITION_RIGHT')
        ],
        'DEFAULT' => 'center'
    ];
}

$arTemplateParameters['LINK_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_PROJECTS_TEMPLATE_5_LINK_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['BUTTON_ALL_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_PROJECTS_TEMPLATE_5_BUTTON_ALL_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['BUTTON_ALL_SHOW'] == 'Y') {
    $arTemplateParameters['BUTTON_ALL_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_PROJECTS_TEMPLATE_5_BUTTON_ALL_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_PROJECTS_TEMPLATE_5_BUTTON_ALL_TEXT_DEFAULT')
    ];
}