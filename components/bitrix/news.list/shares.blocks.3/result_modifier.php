<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var $arResult
 * @var $arParams
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PROPERTY_DATE_END' => 'N',
    'PROPERTY_DISCOUNT' => 'N',
    'PROPERTY_DURATION' => 'N',
    'IBLOCK_DESCRIPTION_SHOW' => 'N',
    'ELEMENT_AS_LINK' => 'N',
    'DISCOUNT_SHOW' => 'N',
    'DURATION_SHOW' => 'N',
    'TIMER_SHOW' => 'Y',
    'TIMER_TIMER_SECONDS_SHOW' => 'N'
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'IBLOCK' => [
        'DESCRIPTION' => [
            'SHOW' => $arParams['IBLOCK_DESCRIPTION_SHOW'] === 'Y'
        ]
    ],
    'ELEMENT' => [
        'AS_LINK' => $arParams['ELEMENT_AS_LINK'] === 'Y'
    ],
    'DISCOUNT' => [
        'SHOW' => $arParams['DISCOUNT_SHOW'] === 'Y' && !empty($arParams['PROPERTY_DISCOUNT'])
    ],
    'DURATION' => [
        'SHOW' => $arParams['DURATION_SHOW'] === 'Y' && !empty($arParams['PROPERTY_DURATION'])
    ],
    'TIMER' => [
        'SHOW' => $arParams['TIMER_SHOW'] === 'Y' && !empty($arParams['PROPERTY_DATE_END']),
        'SECONDS' => [
            'SHOW' => $arParams['TIMER_TIMER_SECONDS_SHOW'] === 'Y'
        ]
    ],
    'NAVIGATION' => [
        'TOP' => [
            'SHOW' => $arParams['DISPLAY_TOP_PAGER'] && !empty($arResult['NAV_STRING'])
        ],
        'BOTTOM' => [
            'SHOW' => $arParams['DISPLAY_BOTTOM_PAGER'] && !empty($arResult['NAV_STRING'])
        ]
    ]
];

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'TIMER' => [
            'SHOW' => false,
            'VALUES' => [
                'UNTIL_DATE'
            ]
        ],
        'DURATION' => [
            'SHOW' => false,
            'VALUE' => null
        ],
        'DISCOUNT' => [
            'SHOW' => false,
            'VALUE' => null
        ]
    ];

    if (!empty($arParams['PROPERTY_DATE_END'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_DATE_END']);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arProperty['DISPLAY_VALUE'] = CIBlockFormatProperties::DateFormat(
                    'Y-m-d H:i:s',
                    MakeTimeStamp($arProperty['DISPLAY_VALUE'], CSite::GetDateFormat())
                );

                if ($arProperty['DISPLAY_VALUE'] >= Date('Y-m-d H:i:s')) {
                    $arItem['DATA']['TIMER']['SHOW'] = $arVisual['TIMER']['SHOW'];
                    $arItem['DATA']['TIMER']['VALUES']['UNTIL_DATE'] = $arProperty['DISPLAY_VALUE'];
                }
            }
        }

        unset($arProperty);
    }

    if (!empty($arParams['PROPERTY_DISCOUNT'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_DISCOUNT']);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['DISCOUNT']['SHOW'] = $arVisual['DISCOUNT']['SHOW'];
                $arItem['DATA']['DISCOUNT']['VALUE'] = $arProperty['DISPLAY_VALUE'];
            }
        }

        unset($arProperty);
    }

    if (!empty($arParams['PROPERTY_DURATION'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_DURATION']);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['DURATION']['SHOW'] = $arVisual['DURATION']['SHOW'];
                $arItem['DATA']['DURATION']['VALUE'] = $arProperty['DISPLAY_VALUE'];
            }
        }

        unset($arProperty);
    }
}

if ($arVisual['TIMER']['SHOW']) {
    $arResult['TIMER'] = [
        'TIME_ZERO_HIDE' => 'Y',
        'MODE' => 'set',
        'SETTINGS_USE' => $arParams['SETTINGS_USE'],
        'LAZYLOAD_USE' => $arVisual['LAZYLOAD']['USE'],
        'TIMER_SECONDS_SHOW' => $arParams['TIMER_TIMER_SECONDS_SHOW'],
        'TIMER_QUANTITY_SHOW' => 'N',
        'TIMER_HEADER_SHOW' => 'N',
        'TIMER_HEADER' => null,
        'TIMER_QUANTITY_OVER' => 'N',
        'TIMER_TITLE_SHOW' => 'N',
        'SALE_SHOW' => 'N',
        'SALE_HEADER_SHOW' => 'N',
        'SALE_HEADER_VALUE' => null,
        'TIMER_TITLE_ENTER' => 'N',
        'TIMER_PRODUCT_UNITS_USE' => 'N',
        'TIMER_QUANTITY_HEADER_SHOW' => 'N',
        'TIMER_QUANTITY_HEADER' => null,
        'RANDOMIZE_ID' => 'Y'
    ];
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);