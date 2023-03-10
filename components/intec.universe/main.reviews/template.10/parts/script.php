<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

$arSlider = [
    'items' => 1,
    'stageClass' => Html::cssClassFromArray([
        'owl-stage',
        'intec-grid'
    ]),
    'margin' => 10,
    'loop' => $arVisual['SLIDER']['LOOP'],
    'dots' => false,
    'nav' => true,
    'navContainerClass' => Html::cssClassFromArray([
        'intec-ui',
        'intec-ui-control-navigation'
    ]),
    'navClass' => [
        Html::cssClassFromArray([
            'intec-ui-part-button-left',
            'intec-cl-background-hover',
            'intec-cl-border-hover'
        ]),
        Html::cssClassFromArray([
            'intec-ui-part-button-right',
            'intec-cl-background-hover',
            'intec-cl-border-hover'
        ])
    ],
    'navText' => [
        FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
        FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
    ],
    'autoplay' => $arVisual['SLIDER']['AUTO']['USE'],
    'autoplayTimeout' => $arVisual['SLIDER']['AUTO']['TIME'],
    'autoplayHoverPause' => $arVisual['SLIDER']['AUTO']['HOVER'],
    'responsive' => [
        '0' => [
            'stageClass' => 'owl-stage',
            'dots' => true,
            'dotsClass' => Html::cssClassFromArray([
                'owl-dots',
                'widget-items-dots',
                'intec-grid',
                'intec-grid-a-h-center'
            ]),
            'dotClass' => Html::cssClassFromArray([
                'owl-dot',
                'widget-items-dot',
                'intec-grid-item-auto',
                'intec-cl-background'
            ]),
            'nav' => false
        ]
    ]
];

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var slider = $('[data-role="container"]', data.nodes);

        slider.owlCarousel(<?= JavaScript::toObject($arSlider) ?>);
    }, {
        'name': '[Component] intec.universe:main.reviews (template.10) > Slider',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php if ($arVisual['SEND']['USE']) {

    if (empty($arResult['SEND']['TITLE']))
        $arResult['SEND']['TITLE'] = Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_10_TEMPLATE_SEND_TITLE_DEFAULT');

    $arSendParameters = JavaScript::toObject([
        'component' => $arResult['SEND']['COMPONENT'],
        'template' => $arResult['SEND']['TEMPLATE'],
        'parameters' => $arResult['SEND']['PARAMETERS'],
        'settings' => [
            'title' => $arResult['SEND']['TITLE'],
            'parameters' => [
                'width' => null
            ]
        ]
    ]);

?>
    <script type="text/javascript">
        template.load(function (data) {
            var app = this;
            var $ = app.getLibrary('$');
            var sender = $('[data-role="review.send"]', data.nodes);

            sender.on('click', function () {
                app.api.components.show(<?= $arSendParameters ?>);
            });
        }, {
            'name': '[Component] intec.universe:main.reviews (template.10) > Send popup',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>