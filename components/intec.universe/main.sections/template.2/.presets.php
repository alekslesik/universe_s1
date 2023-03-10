<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_SECTIONS_TEMPLATE_2_PRESET_LIST_1'),
        'group' => 'sections',
        'sort' => 101,
        'properties' => [
            'QUANTITY' => 'N',
            'SECTIONS_MODE' => 'id',
            'DEPTH' => 2,
            'ELEMENTS_COUNT' => '6',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_SECTIONS_TEMPLATE_2_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'LINE_COUNT' => 3,
            'SUB_SECTIONS_SHOW' => 'Y',
            'SUB_SECTIONS_MAX' => 3,
            'SECTION_URL' => '',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];