<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_SERVICES_TEMPLATE_5_PRESET_TILES_7'),
        'group' => 'services',
        'sort' => 107,
        'properties' => [
            'ELEMENTS_COUNT' => '4',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'HEADER_BLOCK_SHOW' => 'Y',
            'HEADER_BLOCK_POSITION' => 'center',
            'HEADER_BLOCK_TEXT' => Loc::getMessage('PRESETS_SERVICES_TEMPLATE_5_HEADER_BLOCK_TEXT'),
            'DESCRIPTION_BLOCK_SHOW' => 'N',
            'COLUMNS' => 4,
            'LINK_USE' => 'Y',
            'FOOTER_SHOW' => 'N',
            'SECTION_URL' => '',
            'DETAIL_URL' => '',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];