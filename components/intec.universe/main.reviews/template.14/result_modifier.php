<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'VIDEO_IBLOCK_TYPE' => null,
    'VIDEO_IBLOCK_ID' => null,
    'PROPERTY_RATING' => null,
    'PROPERTY_VIDEO' => null,
    'VIDEO_IBLOCK_PROPERTY_LINK' => null,
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'PREVIEW_TRUNCATE_USE' => 'N',
    'PREVIEW_TRUNCATE_WORDS' => 0,
    'RATING_SHOW' => 'Y',
    'VIDEO_SHOW' => 'Y',
    'VIDEO_IMAGE_QUALITY' => 'hqdefault',
    'BUTTON_ALL_SHOW' => 'N',
    'BUTTON_ALL_TEXT' => null,
    'SEND_USE' => 'N',
    'SEND_TEMPLATE' => null,
    'SEND_TITLE' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'PREVIEW' => [
        'TRUNCATE' => [
            'USE' => $arParams['PREVIEW_TRUNCATE_USE'] === 'Y',
            'WORDS' => Type::toInteger($arParams['PREVIEW_TRUNCATE_WORDS'])
        ]
    ],
    'VIDEO' => [
        'SHOW' => $arParams['VIDEO_SHOW'] === 'Y',
        'QUALITY' => ArrayHelper::fromRange([
            'mqdefault',
            'hqdefault',
            'sddefault',
            'maxresdefault'
        ], $arParams['VIDEO_IMAGE_QUALITY'])
    ],
    'RATING' => [
        'SHOW' => $arParams['RATING_SHOW'] === 'Y' && !empty($arParams['PROPERTY_RATING'])
    ],
    'SLIDER' => [
        'USE' => true
    ],
    'SEND' => [
        'USE' => $arParams['SEND_USE'] === 'Y' && !empty($arParams['SEND_TEMPLATE'])
    ]
];

if ($arVisual['PREVIEW']['TRUNCATE']['WORDS'] < 1)
    $arVisual['PREVIEW']['TRUNCATE']['USE'] = false;

$arVideos = [];

$hGetProperty = function (&$arItem, $property) {
    $property = ArrayHelper::getValue($arItem, [
        'PROPERTIES',
        $property,
        'VALUE'
    ]);

    if (!empty($property)) {
        if (Type::isArray($property))
            $property = ArrayHelper::getFirstValue($property);

        return $property;
    }

    return null;
};

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'PREVIEW' => [
            'SHOW' => false,
            'VALUE' => null,
        ],
        'RATING' => [
            'SHOW' => false,
            'VALUE' => null
        ],
        'VIDEO' => [
            'SHOW' => false,
            'VALUE' => null
        ]
    ];

    if (!empty($arItem['PREVIEW_TEXT']))
        $arItem['DATA']['PREVIEW']['VALUE'] = $arItem['PREVIEW_TEXT'];
    else if (!empty($arItem['DETAIL_TEXT']))
        $arItem['DATA']['PREVIEW']['VALUE'] = $arItem['DETAIL_TEXT'];

    if (!empty($arItem['DATA']['PREVIEW']['VALUE']))
        $arItem['DATA']['PREVIEW']['VALUE'] = trim($arItem['DATA']['PREVIEW']['VALUE']);

    if ($arVisual['PREVIEW']['TRUNCATE']['USE'] && !empty($arItem['DATA']['PREVIEW']['VALUE'])) {
        $words = array_filter(
            explode(' ', Html::stripTags($arItem['DATA']['PREVIEW']['VALUE']))
        );

        if (count($words) > $arVisual['PREVIEW']['TRUNCATE']['WORDS']) {
            $words = ArrayHelper::slice($words, 0, $arVisual['PREVIEW']['TRUNCATE']['WORDS']);

            $lastKey = $words[$arVisual['PREVIEW']['TRUNCATE']['WORDS'] - 1];

            if (ArrayHelper::isIn(StringHelper::cut($lastKey, -1), ['.', ',', ':', ';']))
                $words[$arVisual['PREVIEW']['TRUNCATE']['WORDS'] - 1] = StringHelper::cut(
                    $lastKey,
                    0,
                    StringHelper::length($lastKey) - 1
                );

            $arItem['DATA']['PREVIEW']['VALUE'] = implode(' ', $words).'...';

            unset($lastKey);
        }

        unset($words);
    }

    if (!empty($arItem['DATA']['PREVIEW']['VALUE']))
        $arItem['DATA']['PREVIEW']['SHOW'] = true;

    if (!empty($arParams['PROPERTY_RATING'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['PROPERTY_RATING'],
            'VALUE_XML_ID'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            $arItem['DATA']['RATING']['SHOW'] = $arVisual['RATING']['SHOW'];
            $arItem['DATA']['RATING']['VALUE'] = $arProperty;
        }

        unset($arProperty);
    }

    if (
        $arVisual['VIDEO']['SHOW'] &&
        !empty($arParams['VIDEO_IBLOCK_ID']) &&
        !empty($arParams['PROPERTY_VIDEO']) &&
        !empty($arParams['VIDEO_IBLOCK_PROPERTY_LINK'])
    ) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['PROPERTY_VIDEO'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            $arItem['DATA']['VIDEO']['VALUE'] = $arProperty;
            $arVideos[] = $arProperty;
        }
    }
}

unset($arItem);

if (!empty($arVideos)) {
    $youtube = function ($url) {
        $arrUrl = parse_url($url);

        if (isset($arrUrl['query'])) {
            $arrUrlGet = explode('&', $arrUrl['query']);
            foreach ($arrUrlGet as $value) {
                $arrGetParam = explode('=', $value);
                if (!strcmp(array_shift($arrGetParam), 'v')) {
                    $videoID = array_pop($arrGetParam);
                    break;
                }
            }
            if (empty($videoID)) {
                $videoID = array_pop(explode('/', $arrUrl['path']));
            }
        } else {
            $videoID = array_pop(explode('/', $url));
        }

        return [
            'iframe' => 'https://www.youtube.com/embed/'.$videoID,
            'src' => 'https://www.youtube.com/watch?v='.$videoID,
            'mqdefault' => 'https://img.youtube.com/vi/'.$videoID.'/mqdefault.jpg',
            'hqdefault' => 'https://img.youtube.com/vi/'.$videoID.'/hqdefault.jpg',
            'sddefault' => 'https://img.youtube.com/vi/'.$videoID.'/sddefault.jpg',
            'maxresdefault' => 'https://img.youtube.com/vi/'.$videoID.'/maxresdefault.jpg',
            'id' => $videoID
        ];
    };

    $arVideosData = [];
    $rsVideos = CIBlockElement::GetList([], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arParams['VIDEO_IBLOCK_ID'],
        'ID' => $arVideos
    ]);

    while ($arVideo = $rsVideos->GetNextElement()) {
        $arData = $arVideo->GetFields();
        $arData['PROPERTIES'] = $arVideo->GetProperties();

        $arVideosData[$arData['ID']] = $arData['PROPERTIES'][$arParams['VIDEO_IBLOCK_PROPERTY_LINK']]['VALUE'];

        if (Type::isArray($arVideosData[$arData['ID']]))
            $arVideosData[$arData['ID']] = ArrayHelper::getFirstValue($arVideosData[$arData['ID']]);
    }

    unset($rsVideos, $arVideo, $arData);

    if (!empty($arVideosData)) {
        foreach ($arVideosData as &$arItem) {
            $arItem = $youtube($arItem);
        }

        unset($arItem);

        $arVideosData = Arrays::from($arVideosData);

        foreach ($arResult['ITEMS'] as &$arItem) {
            if (empty($arItem['DATA']['VIDEO']['VALUE']))
                continue;

            if ($arVideosData->exists($arItem['DATA']['VIDEO']['VALUE'])) {
                $arItem['DATA']['VIDEO']['SHOW'] = $arVisual['VIDEO']['SHOW'];
                $arItem['DATA']['VIDEO']['VALUE'] = $arVideosData->get($arItem['DATA']['VIDEO']['VALUE']);
            }
        }

        unset($arItem);
    }

    unset($youtube, $arVideosData);
}

unset($arProperty);

if ($arVisual['RATING']['SHOW']) {
    $arResult['RATING'] = Arrays::fromDBResult(CIBlockPropertyEnum::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'CODE' => $arParams['PROPERTY_RATING']
    ]))->indexBy('XML_ID')->asArray(function ($key, $value) {
        return [
            'key' => $key,
            'value' => $value['VALUE']
        ];
    });
}

$arResult['BLOCKS']['FOOTER'] = [
    'SHOW' => $arParams['BUTTON_ALL_SHOW'] === 'Y',
    'TEXT' => !empty($arParams['BUTTON_ALL_TEXT']) ? trim($arParams['BUTTON_ALL_TEXT']) : null,
    'LINK' => null
];

if (!empty($arParams['LIST_PAGE_URL']))
    $arResult['BLOCKS']['FOOTER']['LINK'] = StringHelper::replaceMacros($arParams['LIST_PAGE_URL'], [
        'SITE_DIR' => SITE_DIR
    ]);

if (empty($arResult['BLOCKS']['FOOTER']['LINK']))
    $arResult['BLOCKS']['FOOTER']['SHOW'] = false;

if ($arVisual['SEND']['USE'])
    include(__DIR__.'/modifiers/send.php');

$arResult['VISUAL'] = ArrayHelper::merge($arResult['VISUAL'], $arVisual);

unset($arVisual);