<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 * @var array $arVisual
 */

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

$arResult['ORDER_FAST']['PARAMETERS']['AJAX_MODE'] = 'Y';
$arResult['ORDER_FAST']['PARAMETERS']['AJAX_OPTION_ADDITIONAL'] = $sTemplateId.'-order-fast';
$arResult['ORDER_FAST']['PARAMETERS']['PROPERTY_ARTICLE'] = $arParams['PROPERTY_ARTICLE'];
$arResult['ORDER_FAST']['PARAMETERS']['OFFERS_PROPERTY_ARTICLE'] = $arParams['OFFERS_PROPERTY_ARTICLE'];

if ($bBase)
    CJSCore::Init(array('currency'));

$arEcommerce = [
    'name' => $arResult['NAME'],
    'id' => $arResult['ID'],
    'category' => $arResult['SECTION']['NAME']
];

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = this.getLibrary('$');
        var _ = this.getLibrary('_');
        var header = $('[data-global-role="header"]');
        var root = data.nodes;
        var dynamic = $('[data-role="dynamic"]', root);
        var properties = root.data('properties');
        var dataItem = root.data('data');
        var entity = dataItem;
        var offersSets = $('[data-role="offers.sets"]', root);
        var offerVariableSelect = '<?= $arParams['OFFERS_VARIABLE_SELECT'] ?>';
        var offersDetail = $('[data-role="offers.properties.detail"]', root);
        var scrollSkuList = $('[data-role="scroll.sku.list"]', root);
        var scrollToAllProperties = $('[data-role="show.all"]', root);

        scrollSkuList.on('click', function () {
            var content = $(<?= JavaScript::toObject('#'.$sTemplateId.'-'.'sku-list') ?>, root);

            $(document).scrollTo(content, 500);
        });

        scrollToAllProperties.on('click', function () {
            var id = <?= JavaScript::toObject('#'.$sTemplateId.'-properties') ?>;
            var content = $(id, root);
            var tab = $('[href=\'' + id + '\']', root);

            tab.trigger('click');

            $(document).scrollTo(content, 500);
        });

        app.ecommerce.sendData({
            'ecommerce': {
                'detail': {
                    'products': <?= JavaScript::toObject([$arEcommerce]) ?>
                }
            }
        });

        dynamic.offers = new app.classes.catalog.Offers({
            'properties': properties,
            'list': dataItem.offers
        });

        window.offers = dynamic.offers;

        root.gallery = $('[data-role="gallery"]', root);
        root.gallery.pictures = $('[data-role="gallery.pictures"]', root.gallery);
        root.gallery.pictures.items = $('[data-role="gallery.picture"]', root.gallery.pictures);
        root.gallery.previews = $('[data-role="gallery.previews"]', root.gallery);
        root.gallery.previews.items = $('[data-role="gallery.preview"]', root.gallery.previews);
        root.gallery.videos = $('[data-role="gallery.video"]', root.gallery);
        root.section = $('[data-role="section"]', root);
        dynamic.article = $('[data-role="article"]', dynamic);
        dynamic.article.value = $('[data-role="article.value"]', dynamic.article);
        dynamic.counter = $('[data-role="counter"]', dynamic);
        dynamic.price = $('[data-role="price"]', dynamic);
        dynamic.price.base = $('[data-role="price.base"]', dynamic.price);
        dynamic.price.discount = $('[data-role="price.discount"]', dynamic.price);
        dynamic.price.difference = $('[data-role="price.difference"]', dynamic.price);
        dynamic.panel = $('[data-role="panel"]', dynamic);
        dynamic.panel.picture = $('[data-role="panel.picture"]', dynamic.panel);
        dynamic.panel.counter = $('[data-role="panel.counter"]', dynamic.panel);
        dynamic.panelMobile = $('[data-role="panel.mobile"]', dynamic);
        dynamic.purchase = $('[data-role="purchase"]', dynamic);
        dynamic.orderFast = $('[data-role="orderFast"]', dynamic);
        dynamic.price.measure = $('[data-role="price.measure"]', dynamic. price);

        if (root.section.length) (function () {
            var tabScroll = $('[data-role="scroll"]', root.section);

            tabScroll.on('initialized.owl.carousel', function (e) {
                if (e.target.firstElementChild.scrollWidth > e.target.clientWidth) {
                    e.target.dataset.navigation = 'right';
                }
            });

            var showArrow = function (e) {
                var tabPrev = $('.sections-tabs-navigation-left', tabScroll)[0];
                var tabNext = $('.sections-tabs-navigation-right', tabScroll)[0];

                if (tabPrev.classList.contains('disabled')) {
                    e.target.dataset.navigation = 'right';
                } else if (tabNext.classList.contains('disabled')) {
                    e.target.dataset.navigation = 'left';
                } else {
                    e.target.dataset.navigation = 'both';
                }
            };

            tabScroll.owlCarousel({
                'items': 2,
                'autoWidth': true,
                'nav': true,
                'navClass': [
                    'sections-tabs-navigation-left intec-cl-svg-path-stroke intec-ui-picture',
                    'sections-tabs-navigation-right intec-cl-svg-path-stroke intec-ui-picture'
                ],
                'navText': [
                    <?= JavaScript::toObject($arSvg['NAVIGATION']['LEFT']) ?>,
                    <?= JavaScript::toObject($arSvg['NAVIGATION']['RIGHT']) ?>
                ],
                'onTranslate': showArrow,
                'onResize': showArrow,
                'margin': 30,
                'dots': false
            });
        })();

        <?php if ($arVisual['MEASURES']['USE']) { ?>
        dynamic.ratio = $('[data-role="ratio"]', dynamic.purchase);
        dynamic.ratio.value = $('[data-role="ratio.value"]', dynamic.ratio);
        dynamic.ratio.measure = $('[data-role="ratio.measure"]', dynamic.ratio);
        dynamic.measures = $('[data-role="measures"]', dynamic);
        dynamic.measures.create = function () {
            var price = $('[data-role="measures.price"]', dynamic.measures);
            var select = $('[data-role="measures.select"]', dynamic.measures);

            select.each(function () {
                var self = $(this);
                var parts = {};

                parts.content = $('[data-role="measures.select.content"]', self);
                parts.option = $('[data-role="measures.select.option"]', self);

                parts.content.on('click', function () {
                    if (self.attr('data-active') === 'true')
                        self.attr('data-active', 'false');
                    else
                        self.attr('data-active', 'true');
                });
                parts.option.on('click', function () {
                    var option = $(this);

                    self.attr('data-active', 'false');

                    parts.option.attr('data-selected', 'false')
                        .removeClass('intec-cl-text');

                    option.attr('data-selected', 'true')
                        .addClass('intec-cl-text');

                    $('[data-role="measures.select.content.measure"]', parts.content).html(option.html());

                    dynamic.measures.setSelected(option.attr('data-measure-id'));

                    dynamic.update();
                });

                $(document).on('click', function (event) {
                    if (
                        !event.target.closest('[data-role="measures.select"]', self) &&
                        self.attr('data-active') === 'true'
                    ) {
                        self.attr('data-active', 'false');
                    }
                });
            });
        };
        dynamic.measures.setSelected = function (id, offerId) {
            id = _.toInteger(id);

            if (_.isNil(offerId)) {
                _.each(entity.measures.items, function (item) {
                    if (item.id === id)
                        entity.measures.selected = item;
                });
            } else {
                _.each(entity.offers[offerId].measures.items, function (item) {
                    if (item.id === id)
                        entity.offers[offerId].measures.selected = item;
                });
            }
        };
        dynamic.measures.getSelected = function (offerId) {
            if (_.isNil(offerId)) {
                return entity.measures.selected;
            } else {
                return entity.offers[offerId].measures.selected;
            }
        };
        dynamic.measures.getSelectedMeasure = function () {
            return entity.measures.selected.symbol;
        };
        dynamic.measures.calculateRatio = function (ratio) {
            return ratio * entity.measures.selected.multiplier;
        };
        dynamic.measures.calculatePrice = function (price, offerId) {
            var measure = dynamic.measures.getSelected(offerId);

            return price.discount.value / measure.multiplier;
        };
        dynamic.measures.calculateQuantity = function (quantity) {
            return entity.measures.selected.multiplier * quantity;
        };
        dynamic.measures.calculateQuantityBase = function (quantity, offerId) {
            if (_.isNil(offerId)) {
                return (quantity / entity.measures.selected.multiplier * entity.measures.base.multiplier).toFixed(3);
            } else {
                return (quantity / entity.offers[offerId].measures.selected.multiplier * entity.offers[offerId].measures.base.multiplier).toFixed(3);
            }
        };
        dynamic.measures.calculateCounter = function () {
            var counter = {
                'bounds': {
                    'minimum': entity.quantity.ratio,
                    'maximum': false
                },
                'step': entity.quantity.ratio
            };

            if (entity.measures.selected.id !== entity.measures.base.id) {
                var ratio = dynamic.measures.calculateRatio(entity.quantity.ratio);

                counter.bounds.minimum = ratio;
                counter.step = ratio;
            }

            if (entity.quantity.trace && !entity.quantity.zero)
                counter.bounds.maximum = dynamic.measures.calculateQuantity(entity.quantity.value);

            return counter;
        };
        dynamic.measures.priceByQuantity = function (priceObject, offerQuantity) {
            if (_.isNil(offerQuantity)) {
                return priceObject.quantity.from === null ||
                    dynamic.quantity.get() >= (priceObject.quantity.from * entity.measures.selected.multiplier)
            } else {
                return priceObject.quantity.from === null ||
                    offerQuantity >= (priceObject.quantity.from * entity.measures.selected.multiplier)
            }
        };
        <?php } ?>

        <?php if (empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic') { ?>
        dynamic.quantity = app.ui.createControl('numeric', {
            'node': dynamic.counter,
            'bounds': {
                'minimum': entity.quantity.ratio,
                'maximum': entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false
            },
            'step': entity.quantity.ratio
        });
        dynamic.panel.quantity = app.ui.createControl('numeric', {
            'node': dynamic.panel.counter
        });
        dynamic.update = function () {
            var article = entity.article;
            var price = null;
            var quantity = {
                'bounds': {
                    'minimum': entity.quantity.ratio,
                    'maximum': entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false
                },
                'step': entity.quantity.ratio
            };

            <?php if ($arVisual['MEASURES']['USE']) { ?>
            quantity = dynamic.measures.calculateCounter();
            <?php } ?>

            root.attr('data-available', entity.available ? 'true' : 'false');
            root.attr('data-subscribe', entity.subscribe ? 'true' : 'false');


            if (article == null)
                article = dataItem.article;

            dynamic.article.attr('data-show', article == null ? 'false' : 'true');
            dynamic.article.value.text(article);

            _.each(entity.prices, function (object) {
                <?php if ($arVisual['MEASURES']['USE']) { ?>
                if (dynamic.measures.priceByQuantity(object))
                    price = object;
                <?php } else { ?>
                if (object.quantity.from === null || dynamic.quantity.get() >= object.quantity.from)
                    price = object;
                <?php } ?>
            });

            if (price !== null) {

                <?php if ($bBase) { ?>
                BX.Currency.setCurrencyFormat(price.currency.CURRENCY, price.currency);
                <?php } ?>

                <?php if ($bBase && $arVisual['PRICE']['RECALCULATION']) { ?>
                var summary = [];
                var quantitySum;

                <?php if ($arVisual['MEASURES']['USE']) { ?>
                quantitySum = dynamic.measures.calculateQuantityBase(dynamic.quantity.get());
                <?php } else { ?>
                quantitySum = dynamic.quantity.get();
                <?php } ?>

                summary.base = price.base.value * quantitySum;
                summary.base = summary.base.toFixed(price.currency.DECIMALS);
                summary.discount = price.discount.value * quantitySum;
                summary.discount = summary.discount.toFixed(price.currency.DECIMALS);
                summary.difference = summary.base - summary.discount;
                price.base.display = BX.Currency.currencyFormat(summary.base, price.currency.CURRENCY, true);
                price.discount.display = BX.Currency.currencyFormat(summary.discount, price.currency.CURRENCY, true);
                price.discount.difference = BX.Currency.currencyFormat(summary.difference, price.currency.CURRENCY, true);
                <?php } ?>

                dynamic.price.attr('data-discount', price.discount.use ? 'true' : 'false');
                dynamic.price.base.html(price.base.display);
                dynamic.price.discount.html(price.discount.display);
                dynamic.price.difference.html(price.discount.difference);
                dynamic.orderFast.show();

                <?php if ($arVisual['MEASURES']['USE']) { ?>
                var priceByMeasure = {
                    'value': dynamic.measures.calculatePrice(price),
                    'print': null
                };

                <?php if ($bBase) { ?>
                priceByMeasure.print = BX.Currency.currencyFormat(priceByMeasure.value, price.currency.CURRENCY, true);
                <?php } ?>

                if (entity.measures.use === 'true')
                    dynamic.measures.show();
                else
                    dynamic.measures.hide();

                $('[data-role="measures.price"]', dynamic.measures).html(priceByMeasure.print);

                dynamic.ratio.value.html(dynamic.measures.calculateRatio(entity.quantity.ratio));
                dynamic.ratio.measure.html(dynamic.measures.getSelectedMeasure());
                <?php } ?>

            } else {
                dynamic.price.attr('data-discount', 'false');
                dynamic.price.base.html(null);
                dynamic.price.discount.html(null);
                dynamic.price.difference.html(null);
                dynamic.orderFast.hide();
            }

                dynamic.price.attr('data-show', price !== null ? 'true' : 'false');
                dynamic.quantity.configure(quantity);
                dynamic.panel.quantity.configure(quantity);

                dynamic.find('[data-offer]').css('display', '');
                offersDetail.find('[data-offer]').css('display', '');
                offersSets.find('[data-offer]').css('display', '');

                if (entity !== dataItem) {
                    dynamic.find('[data-offer=' + entity.id + ']').css('display', 'block');
                    offersDetail.find('[data-offer=' + entity.id + ']').css('display', 'block');
                    offersSets.find('[data-offer=' + entity.id + ']').css('display', 'block');
                    dynamic.find('[data-offer="false"]').css('display', 'none');

                    if (root.gallery.filter('[data-offer=' + entity.id + ']').length === 0)
                        root.gallery.filter('[data-offer="false"]').css('display', '');

                    if (dynamic.panel.picture.filter('[data-offer=' + entity.id + ']').length === 0)
                        dynamic.panel.picture.filter('[data-offer="false"]').css('display', '');
                }

            var basketQuantity = dynamic.quantity.get();

            <?php if ($arVisual['MEASURES']['USE']) { ?>
            basketQuantity = dynamic.measures.calculateQuantityBase(basketQuantity);
            <?php } ?>

            dynamic.find('[data-basket-id]')
                .data('basketQuantity', basketQuantity)
                .attr('data-basket-quantity', basketQuantity);

                <?php if ($arVisual['CREDIT']['SHOW']) { ?>
                    <?php if ($bBase && $arVisual['PRICE']['RECALCULATION']) { ?>
                        creditPrice(summary, price);
                    <?php } else { ?>
                        creditPrice(null, price);
                    <?php } ?>
                <?php } ?>

                <?php if ($arVisual['TIMER']['SHOW']) { ?>
                    timerUpdate(entity.id);
                <?php } ?>
                if (!entity.available) {
                    dynamic.orderFast.hide();
                }
            };

            dynamic.update();



            (function () {
                var update = false;

                dynamic.quantity.on('change', function (value) {
                    if (!update) {
                        update = true;
                        dynamic.panel.quantity.set(value);
                        dynamic.update();
                        update = false;
                    }

                    <?php if ($arVisual['COUNTER']['MESSAGE']['MAX']['SHOW']) { ?>
                        var alertElem = $('[data-role="max-message"]', root);
                        var closeIcon = $('[data-role="max-message-close"]', alertElem);
                        var maxQuantity = value;

                        <?php if ($arVisual['MEASURES']['USE']) { ?>
                            maxQuantity = dynamic.measures.calculateQuantityBase(value);
                        <?php } ?>

                        if (!dynamic.offers.isEmpty()) {
                            var currentOffer = dynamic.offers.getCurrent();

                            if (currentOffer.available && !currentOffer.quantity.zero && currentOffer.quantity.trace && currentOffer.quantity.value > 0) {
                                if (maxQuantity >= entity.quantity.value) {
                                    alertElem.fadeIn();
                                }
                            }
                        } else {
                            if (dataItem.available && !dataItem.quantity.zero && dataItem.quantity.trace && dataItem.quantity.value > 0) {
                                if (maxQuantity >= entity.quantity.value) {
                                    alertElem.fadeIn();
                                }
                            }
                        }

                        closeIcon.on('click', function (event) {
                            event.stopPropagation();
                            alertElem.fadeOut();
                        });

                        $('[data-action="decrement"]', root).on('click', function () {
                            alertElem.fadeOut();
                        });
                    <?php } ?>
                });

                dynamic.panel.quantity.on('change', function (value) {
                    dynamic.quantity.set(value);
                });
            })();
        <?php } ?>

        <?php if ($arResult['SKU_VIEW'] == 'dynamic') { ?>
            if (!dynamic.offers.isEmpty()) {
                dynamic.properties = $('[data-role="property"]', root);
                dynamic.properties.values = $('[data-role="property.value"]', dynamic.properties);
                dynamic.properties.each(function () {
                    var self = $(this);
                    var property = self.data('property');
                    var values = self.find(dynamic.properties.values);

                    values.each(function () {
                        var self = $(this);
                        var value = self.data('value');

                        self.on('click', function () {
                            dynamic.offers.setCurrentByValue(property, value);
                        });
                    });
                });

                dynamic.offers.on('change', function (event, offer, values) {
                    entity = offer;

                    $('[data-role="max-message"]', root).fadeOut();

                    _.each(values, function (values, state) {
                        _.each(values, function (values, property) {
                            property = dynamic.properties.filter('[data-property="' + property + '"]');

                            _.each(values, function (value) {
                                value = property.find(dynamic.properties.values).filter('[data-value="' + value + '"]');
                                value.attr('data-state', state);
                            });
                        });
                    });

                    if (!!offerVariableSelect) {
                        var currentUrl = new URL(document.location);
                        var getParamsUrl = currentUrl.searchParams;

                        if (!!getParamsUrl.get(offerVariableSelect)) {
                            getParamsUrl.set(offerVariableSelect, dynamic.offers.getCurrent().id);
                            currentUrl.searchParams = getParamsUrl;
                        }
                    }

                    dynamic.update();
                });

                var offerCurrent;

                _.each(dynamic.offers.getList(), function (offer) {
                    if (!offerCurrent || Number(offerCurrent.sort) > Number(offer.sort))
                        offerCurrent = offer;
                });

                if (!!offerVariableSelect) {
                    var currentUrl = new URL(document.location);
                    var getParamsUrl = currentUrl.searchParams;
                    var offerIdFromUrl = null;

                    if (!!getParamsUrl.get(offerVariableSelect)) {
                        offerIdFromUrl = dynamic.offers.getList().filter(e => e.id == getParamsUrl.get(offerVariableSelect));

                        if (offerIdFromUrl.length !== 0) {
                            offerCurrent = offerIdFromUrl[0];
                        }
                    }

                    getParamsUrl.set(offerVariableSelect, offerCurrent['id']);
                    currentUrl.searchParams = getParamsUrl;
                }

                dynamic.offers.setCurrentById(offerCurrent.id);
            }
        <?php } ?>

        root.gallery.each(function () {
            var gallery = $(this);
            var pictures;
            var previews;
            var videos;

            pictures = gallery.find(root.gallery.pictures);
            pictures.items = pictures.find(root.gallery.pictures.items);
            previews = gallery.find(root.gallery.previews);
            previews.items = previews.find(root.gallery.previews.items);
            videos = gallery.find(root.gallery.videos);

            var videogalleryUse = <?= JavaScript::toObject($arVisual['GALLERY']['VIDEO']['USE']) ?>;

            if (videogalleryUse) {
                var carouselHandler = function () {
                    setTimeout(function () {
                        var height = videos.closest('.owl-item').outerWidth();
                        videos.css('height', height);
                    }, 0);

                    $(window).on('resize', function () {
                        setTimeout(function () {
                            var height = videos.closest('.owl-item').outerWidth();
                            videos.css('height', height);
                        }, 0);
                    });

                    var uploadedVideos = $('[data-role="gallery.uploaded.video"]', videos);

                    if (uploadedVideos.length > 0) {
                        uploadedVideos.each(function () {
                            var video = this;
                            var canvas = document.createElement('canvas');
                            var videoStub = $('[data-role="video.stub"][data-id=\"' + $(video).data('id') + '\"]', previews);
                            var canvasStub = $('[data-role="canvas.stub"]', videoStub);

                            video.play().then(function () {
                                canvas.width = 120;
                                canvas.height = 120;

                                var ctx = canvas.getContext('2d');
                                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                                canvasStub[0].setAttribute('src', canvas.toDataURL('image/png'));
                                video.pause();
                            });
                        });
                    }
                };
            }

            var navUse = false;

            if (pictures.items.length > 1 || videogalleryUse)
                navUse = true;

            pictures.owlCarousel({
                'items': 1,
                'nav': navUse,
                'dots': false,
                'onInitialized': videogalleryUse ? carouselHandler : false
            });

            <?php if ($arVisual['GALLERY']['POPUP']) { ?>
                pictures.lightGallery({
                    'share': false,
                    'selector': '[data-lightGallery="true"]',
                    'thumbnail': !videogalleryUse,
                    'youtubePlayerParams': {
                        'modestbranding': 1,
                        'showinfo': 0,
                        'rel': 0,
                        'controls': 1
                    }
                });
            <?php } ?>

            <?php if ($arVisual['GALLERY']['ZOOM']) { ?>
                pictures.items.each(function () {
                    var picture = $(this);
                    var source = picture.data('src');

                    if (picture.data('type') !== 'image/gif') {
                        picture.zoom({
                            'url': source,
                            'touch': false
                        });
                    }
                });
            <?php } ?>

            <?php if ($arVisual['GALLERY']['SLIDER']) { ?>
                previews.owlCarousel({
                    'items': 6,
                    'nav': false,
                    'margin': 1
                });

                previews.set = function (number) {
                    var activeSlide = previews.items.eq(number);

                    previews.items.attr('data-active', 'false');
                    previews.items.eq(number).attr('data-active', 'true');

                    if (!activeSlide.closest('.owl-item').hasClass('active')) {
                        var index = activeSlide.closest('.owl-item').index();
                        previews.trigger('to.owl.carousel', [index, 1, true]);
                    }
                };

                previews.items.on('pointerenter', function () {
                    var preview = $(this);
                    var previewIndex = preview.parent('.owl-item').index();

                    pictures.trigger('to.owl.carousel', [previewIndex]);
                    previews.set(previewIndex);
                });

                pictures.on('changed.owl.carousel', function (event) {
                    previews.set(event.item.index);
                });
            <?php } ?>
        });

        <?php if ($arResult['FORM']['ORDER']['SHOW']) { ?>
            dynamic.order = $('[data-role="order"]', dynamic);
            dynamic.order.on('click', function () {
                var options = <?= JavaScript::toObject([
                    'id' => $arResult['FORM']['ORDER']['ID'],
                    'template' => $arResult['FORM']['ORDER']['TEMPLATE'],
                    'parameters' => [
                        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
                        'CONSENT_URL' => $arResult['URL']['CONSENT']
                    ],
                    'settings' => [
                        'title' => Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_1_FORM_TITLE')
                    ]
                ]) ?>;

                options.fields = {};

                <?php if (!empty($arResult['FORM']['ORDER']['PROPERTIES']['PRODUCT'])) { ?>
                options.fields[<?= JavaScript::toObject($arResult['FORM']['ORDER']['PROPERTIES']['PRODUCT']) ?>] = dataItem.name;
                <?php } ?>

            app.api.forms.show(options);
            app.metrika.reachGoal('forms.open');
            app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['ORDER']['ID'].'.open') ?>);
        });
        <?php } ?>

        <?php if ($arResult['FORM']['REQUEST']['SHOW']) { ?>
        dynamic.request = $('[data-role="request"]', dynamic);
        dynamic.request.on('click', function () {
            var options = <?= JavaScript::toObject([
                'id' => $arResult['FORM']['REQUEST']['ID'],
                'template' => $arResult['FORM']['REQUEST']['TEMPLATE'],
                'parameters' => [
                    'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form-request',
                    'CONSENT_URL' => $arResult['URL']['CONSENT']
                ],
                'settings' => [
                    'title' => $arVisual['BUTTONS']['REQUEST']['TEXT']
                ]
            ]) ?>;

            options.fields = {};

            <?php if (!empty($arResult['FORM']['REQUEST']['PROPERTIES']['PRODUCT'])) { ?>
            options.fields[<?= JavaScript::toObject($arResult['FORM']['REQUEST']['PROPERTIES']['PRODUCT']) ?>] = dataItem.name;
            <?php } ?>

            app.api.forms.show(options);
            app.metrika.reachGoal('forms.open');
            app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['REQUEST']['ID'].'.open') ?>);
        });
        <?php } ?>

        <?php if ($arResult['FORM']['CHEAPER']['SHOW']) { ?>
            dynamic.cheaper = $('[data-role="cheaper"]', dynamic);
            dynamic.cheaper.on('click', function () {
                var options = <?= JavaScript::toObject([
                    'id' => $arResult['FORM']['CHEAPER']['ID'],
                    'template' => $arResult['FORM']['CHEAPER']['TEMPLATE'],
                    'parameters' => [
                        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
                        'CONSENT_URL' => $arResult['URL']['CONSENT']
                    ],
                    'settings' => [
                        'title' => Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_1_FORM_CHEAPER_TITLE')
                    ]
                ]) ?>;

                options.fields = {};

                <?php if (!empty($arResult['FORM']['CHEAPER']['PROPERTIES']['PRODUCT'])) { ?>
                options.fields[<?= JavaScript::toObject($arResult['FORM']['CHEAPER']['PROPERTIES']['PRODUCT']) ?>] = dataItem.name;
                <?php } ?>

            app.api.forms.show(options);
            app.metrika.reachGoal('forms.open');
            app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['CHEAPER']['ID'].'.open') ?>);
        });
        <?php } ?>

        <?php if ($arResult['FORM']['MARKDOWN']['SHOW']) { ?>
            dynamic.markdown = $('[data-role="markdown"]', dynamic);
            dynamic.markdown.on('click', function () {
                var options = <?= JavaScript::toObject([
                    'id' => $arResult['FORM']['MARKDOWN']['ID'],
                    'template' => $arResult['FORM']['MARKDOWN']['TEMPLATE'],
                    'parameters' => [
                        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
                        'CONSENT_URL' => $arResult['URL']['CONSENT']
                    ],
                    'settings' => [
                        'title' => Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_1_FORM_MARKDOWN_TITLE')
                    ]
                ]) ?>;

                options.fields = {};

                <?php if (!empty($arResult['FORM']['MARKDOWN']['PROPERTIES']['PRODUCT'])) { ?>
                options.fields[<?= JavaScript::toObject($arResult['FORM']['MARKDOWN']['PROPERTIES']['PRODUCT']) ?>] = dataItem.name;
                <?php } ?>

            app.api.forms.show(options);
            app.metrika.reachGoal('forms.open');
            app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['MARKDOWN']['ID'].'.open') ?>);
        });
        <?php } ?>

        <?php if ($arResult['ORDER_FAST']['USE'] &&
    (empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic')) { ?>
            dynamic.orderFast = $('[data-role="orderFast"]', dynamic);
            dynamic.orderFast.on('click', function () {
                var template = <?= JavaScript::toObject($arResult['ORDER_FAST']['TEMPLATE']) ?>;
                var parameters = <?= JavaScript::toObject($arResult['ORDER_FAST']['PARAMETERS']) ?>;

                if (_.isNil(parameters))
                    parameters = {};

                parameters['PRODUCT'] = entity.id;
                parameters['QUANTITY'] = dynamic.quantity.get();

                app.api.components.show({
                    'component': 'intec.universe:sale.order.fast',
                    'template': template,
                    'parameters': parameters,
                    'settings': {
                        'parameters': {
                            'width': null
                        }
                    }
                });
            });
        <?php } ?>

        <?php if ($arResult['DELIVERY_CALCULATION']['USE'] &&
    (empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic')) { ?>
            dynamic.deliveryCalculation = $('[data-role="deliveryCalculation"]', dynamic);
            dynamic.deliveryCalculation.on('click', function () {
                var template = <?= JavaScript::toObject($arResult['DELIVERY_CALCULATION']['TEMPLATE']) ?>;
                var parameters = <?= JavaScript::toObject($arResult['DELIVERY_CALCULATION']['PARAMETERS']) ?>;

                if (_.isNil(parameters))
                    parameters = {};

                parameters['PRODUCT_ID'] = entity.id;
                parameters['PRODUCT_QUANTITY_VALUE'] = dynamic.quantity.get();
                parameters['PRODUCT_QUANTITY_MIN'] = entity.quantity.ratio;
                parameters['PRODUCT_QUANTITY_MAX'] = entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false;
                parameters['PRODUCT_QUANTITY_STEP'] = entity.quantity.ratio;

                app.api.components.show({
                    'component': 'intec.universe:catalog.delivery',
                    'template': template,
                    'parameters': parameters,
                    'settings': {
                        'parameters': {
                            'width': null
                        }
                    }
                });
            });
        <?php } ?>

        if (dynamic.panel.length === 1) (function () {
            var menuFixed = $('[data-role="top-menu"]', header);

            if (menuFixed.length !== 0) {
                menuFixed.remove();
            }

            var state = false;
            var area = $(window);
            var update;
            var panel;

            update = function () {
                var bound = 0;

                if (root.is(':visible')) {
                    bound += root.offset().top;
                }

                if (area.scrollTop() > bound) {
                    panel.show();
                } else {
                    panel.hide();
                }
            };

            panel = dynamic.panel;
            panel.css({
                'top': -panel.height()
            });

            panel.show = function () {
                if (state) return;

                state = true;
                panel.css({
                    'display': 'block'
                });

                panel.trigger('show');
                panel.stop().animate({
                    'top': 0
                }, 500)
            };

            panel.hide = function () {
                if (!state) return;

                state = false;
                panel.stop().animate({
                    'top': -panel.height()
                }, 500, function () {
                    panel.trigger('hide');
                    panel.css({
                        'display': 'none'
                    })
                })
            };

            update();

            area.on('scroll', update)
                .on('resize', update);
        })();

        if (dynamic.panelMobile.length === 1 && dynamic.purchase.length === 1) (function () {
            var state = false;
            var area = $(window);
            var update;
            var panel;

            update = function () {
                var bound = dynamic.purchase.offset().top;

                if (area.scrollTop() > bound) {
                    panel.show();
                } else {
                    panel.hide();
                }
            };

            panel = dynamic.panelMobile;
            panel.css({
                'bottom': -panel.outerHeight()
            });

            panel.show = function () {
                if (state) return;

                state = true;
                panel.css({
                    'display': 'block'
                });

                panel.trigger('show');
                panel.stop().animate({
                    'bottom': 0
                }, 500)
            };

            panel.hide = function () {
                if (!state) return;

                state = false;
                panel.stop().animate({
                    'bottom': -panel.outerHeight()
                }, 500, function () {
                    panel.trigger('hide');
                    panel.css({
                        'display': 'none'
                    })
                })
            };

            update();

            area.on('scroll', update)
                .on('resize', update);
        })();

        <?php if ($arVisual['MEASURES']['USE']) { ?>
        if (entity.prices.length !== 0) {
            dynamic.measures.create();
        } else {
            dynamic.measures.hide();
        }
        <?php } ?>

        <?php if (!empty($arResult['OFFERS']) && $arResult['SKU_VIEW'] == 'list') { ?>
            dynamic.offers = $('[data-role="offers"]', dynamic);
            dynamic.offers.list = $('[data-role="offer"]', dynamic.offers);

            dynamic.offers.list.each(function () {
                var offer = $(this);
                var dataOffer = offer.data('offer-data');

                offer.counter = $('[data-role="counter"]', offer);
                offer.quantity = app.ui.createControl('numeric', {
                    'node': offer.counter,
                    'bounds': {
                        'minimum': dataOffer.quantity.ratio,
                        'maximum': dataOffer.quantity.trace && !dataOffer.quantity.zero ? dataOffer.quantity.value : false
                    },
                    'step': dataOffer.quantity.ratio
                });

                offer.price = $('[data-role="price"]', offer);
                offer.price.base = $('[data-role="price.base"]', offer.price);
                offer.price.discount = $('[data-role="price.discount"]', offer.price);
                offer.price.difference = $('[data-role="price.difference"]', offer.price);

                <?php if ($arVisual['MEASURES']['USE']) { ?>
                offer.measures = $('[data-role="offer.measures"]', offer);
                offer.measures.create = function () {
                    if (dataOffer.measures.use === 'true')
                        offer.measures.show();

                    var price = $('[data-role="measures.price"]', offer.measures);
                    var select = $('[data-role="measures.select"]', offer.measures);

                    select.each(function () {
                        var self = $(this);
                        var parts = {};

                        parts.content = $('[data-role="measures.select.content"]', self);
                        parts.option = $('[data-role="measures.select.option"]', self);

                        parts.content.on('click', function () {
                            if (self.attr('data-active') === 'true')
                                self.attr('data-active', 'false');
                            else
                                self.attr('data-active', 'true');
                        });
                        parts.option.on('click', function () {
                            var option = $(this);

                            self.attr('data-active', 'false');

                            parts.option.attr('data-selected', 'false')
                                .removeClass('intec-cl-text');

                            option.attr('data-selected', 'true')
                                .addClass('intec-cl-text');

                            $('[data-role="measures.select.content.measure"]', parts.content).html(option.html());

                            dynamic.measures.setSelected(option.attr('data-measure-id'), dataOffer.id);

                            offer.update();
                        });

                        $(document).on('click', function (event) {
                            if (
                                !event.target.closest('[data-role="measures.select"]', self) &&
                                self.attr('data-active') === 'true'
                            ) {
                                self.attr('data-active', 'false');
                            }
                        });
                    });
                };

                offer.measures.create();
                <?php } ?>

                _.each(dataOffer.prices, function (object) {
                    if (object.quantity.from === null || offer.quantity.get() >= object.quantity.from)
                        price = object;
                });

                <?php if ($bBase) { ?>
                    BX.Currency.setCurrencyFormat(price.currency.CURRENCY, price.currency);
                <?php } ?>

                offer.update = function () {
                    var price = null;
                    var quantity = {
                        'bounds': {
                            'minimum': dataOffer.quantity.ratio,
                            'maximum': dataOffer.quantity.trace && !dataOffer.quantity.zero ? dataOffer.quantity.value : false
                        },
                        'step': dataOffer.quantity.ratio
                    };

                    <?php if ($arVisual['MEASURES']['USE']) { ?>
                        if (entity.offers[dataOffer.id].measures.selected.id !== entity.offers[dataOffer.id].measures.base.id) {
                            var ratio = entity.offers[dataOffer.id].quantity.ratio * entity.offers[dataOffer.id].measures.selected.multiplier;

                            quantity.bounds.minimum = ratio;
                            quantity.step = ratio;
                        }

                        if (entity.offers[dataOffer.id].quantity.trace && !entity.offers[dataOffer.id].quantity.zero)
                            quantity.bounds.maximum =  entity.offers[dataOffer.id].measures.selected.multiplier * entity.offers[dataOffer.id].quantity.value;
                    <?php } ?>

                    _.each(dataOffer.prices, function (object) {
                        <?php if ($arVisual['MEASURES']['USE']) { ?>
                            if (dynamic.measures.priceByQuantity(object, offer.quantity.get()))
                                price = object;
                        <?php } else { ?>
                            if (object.quantity.from === null || offer.quantity.get() >= object.quantity.from)
                                price = object;
                        <?php } ?>
                    });

                    if (price !== null) {
                        <?php if ($bBase && $arVisual['PRICE']['RECALCULATION']) { ?>
                        if (price.quantity.from === null && price.quantity.to === null) {
                            var summary = {};
                            var quantitySum = offer.quantity.get();

                            <?php if ($arVisual['MEASURES']['USE']) { ?>
                                quantitySum = dynamic.measures.calculateQuantityBase(offer.quantity.get(), dataOffer.id);
                            <?php } ?>

                            summary.base = price.base.value * quantitySum;
                            summary.discount = price.discount.value * quantitySum;
                            summary.difference = summary.base - summary.discount;
                            BX.Currency.setCurrencyFormat(price.currency.CURRENCY, price.currency);
                            price.base.display = BX.Currency.currencyFormat(summary.base, price.currency.CURRENCY, true);
                            price.discount.display = BX.Currency.currencyFormat(summary.discount, price.currency.CURRENCY, true);
                            price.discount.difference = BX.Currency.currencyFormat(summary.difference, price.currency.CURRENCY, true);
                        }
                        <?php } ?>

                        offer.price.attr('data-discount', price.discount.use ? 'true' : 'false');
                        offer.price.base.html(price.base.display);
                        offer.price.discount.html(price.discount.display);
                        offer.price.difference.html(price.discount.difference);

                        <?php if ($arVisual['MEASURES']['USE']) { ?>
                            var priceByMeasure = {
                                'value': dynamic.measures.calculatePrice(price, dataOffer.id),
                                'print': null
                            };

                            priceByMeasure.print = BX.Currency.currencyFormat(
                                priceByMeasure.value,
                                price.currency.CURRENCY,
                                true
                            );

                            $('[data-role="measures.price"]', offer).html(priceByMeasure.print);
                        <?php } ?>
                    } else {
                        offer.price.attr('data-discount', 'false');
                        offer.price.base.html(null);
                        offer.price.discount.html(null);
                        offer.price.difference.html(null);
                    }

                    offer.quantity.configure(quantity);

                    var basketQuantity = offer.quantity.get();

                    <?php if ($arVisual['MEASURES']['USE']) { ?>
                        basketQuantity = dynamic.measures.calculateQuantityBase(basketQuantity, dataOffer.id);
                    <?php } ?>

                    offer.find('[data-basket-id]')
                        .data('basketQuantity', basketQuantity)
                        .attr('data-basket-quantity', basketQuantity);
                };

                offer.quantity.on('change', function (value) {
                    offer.update();

                    <?php if ($arVisual['COUNTER']['MESSAGE']['MAX']['SHOW']) { ?>
                        var alertElem = $('[data-role="max-message-offer"]', offer);
                        var closeIcon = $('[data-role="max-message-close"]', alertElem);
                        var maxQuantity = value;

                        <?php if ($arVisual['MEASURES']['USE']) { ?>
                            maxQuantity = dynamic.measures.calculateQuantityBase(value, dataOffer.id);
                        <?php } ?>

                        if (dataOffer.available && !dataOffer.quantity.zero && dataOffer.quantity.trace && dataOffer.quantity.value > 0) {
                            if (maxQuantity >= dataOffer.quantity.value) {
                                alertElem.fadeIn();
                            }
                        }

                        closeIcon.on('click', function (event) {
                            event.stopPropagation();
                            alertElem.fadeOut();
                        });

                        $('[data-action="decrement"]', offer).on('click', function () {
                            alertElem.fadeOut();
                        });
                    <?php } ?>
                });

                <?php if ($arResult['ORDER_FAST']['USE']) { ?>

                offer.orderFast = $('[data-role="orderFast"]', offer);

                offer.orderFast.on('click', function () {
                    var template = <?= JavaScript::toObject($arResult['ORDER_FAST']['TEMPLATE']) ?>;
                    var parameters = <?= JavaScript::toObject($arResult['ORDER_FAST']['PARAMETERS']) ?>;

                    if (_.isNil(parameters))
                        parameters = {};

                    parameters['PRODUCT'] = dataOffer.id;
                    parameters['QUANTITY'] = offer.quantity.get();

                    app.api.components.show({
                        'component': 'intec.universe:sale.order.fast',
                        'template': template,
                        'parameters': parameters,
                        'settings': {
                            'parameters': {
                                'width': null
                            }
                        }
                    });
                });
            <?php } ?>

            <?php if ($arResult['DELIVERY_CALCULATION']['USE']) { ?>
                offer.deliveryCalculation = $('[data-role="deliveryCalculation"]', offer);
                offer.deliveryCalculation.on('click', function () {
                    var template = <?= JavaScript::toObject($arResult['DELIVERY_CALCULATION']['TEMPLATE']) ?>;
                    var parameters = <?= JavaScript::toObject($arResult['DELIVERY_CALCULATION']['PARAMETERS']) ?>;

                    if (_.isNil(parameters))
                        parameters = {};

                    parameters['PRODUCT_ID'] = dataOffer.id;
                    parameters['PRODUCT_QUANTITY_VALUE'] = offer.quantity.get();
                    parameters['PRODUCT_QUANTITY_MIN'] = dataOffer.quantity.ratio;
                    parameters['PRODUCT_QUANTITY_MAX'] = dataOffer.quantity.trace && !dataOffer.quantity.zero ? dataOffer.quantity.value : false;
                    parameters['PRODUCT_QUANTITY_STEP'] = dataOffer.quantity.ratio;

                    app.api.components.show({
                        'component': 'intec.universe:catalog.delivery',
                        'template': template,
                        'parameters': parameters,
                        'settings': {
                            'parameters': {
                                'width': null
                            }
                        }
                    });
                });
            <?php } ?>
            });
        <?php } ?>

        function timerUpdate(id){

            var timerParameters = <?= JavaScript::toObject($arResult['TIMER']['PROPERTIES']) ?>;

            if (!!timerParameters) {
                timerParameters.parameters.ELEMENT_ID = id;
                timerParameters.parameters.RANDOMIZE_ID = 'Y';
                timerParameters.parameters.AJAX_MODE = 'N';

                app.api.components.get(timerParameters).then(function (content) {
                    $('[data-role="timer-holder"]', root).html(content);
                });
            }
        }

        <?php if (!empty($arResult['OFFERS']) && $arResult['SKU_VIEW'] == 'list') { ?>
            var duration = '<?= !empty($arVisual['CREDIT']['DURATION']) ? $arVisual['CREDIT']['DURATION'] : 10 ?>';
            var offersCredit = $('[data-role="credit"]');

            offersCredit.each(function () {
                var price = $(this).attr('data-price');
                var currency = $(this).attr('data-currency');
                var priceDisplay = 0;
                var priceValue = null;

                if (!_.isNil(price) && !_.isNil(duration)) {
                    priceValue = price / duration;
                    priceValue = priceValue.toFixed(1);
                }

                if (!_.isNil(priceValue)) {
                    <?php if ($bBase) { ?>
                        priceDisplay = BX.Currency.currencyFormat(priceValue, currency, true);
                    <?php } else { ?>
                        priceDisplay = priceValue;
                    <?php } ?>
                }

                $('[data-role="price.credit"]', this).html(priceDisplay);
            });
        <?php } ?>

        function creditPrice(summary, price) {

            if(_.isNil(price))
                return;

            var status = $('[data-role="credit"]').data('status');
            var priceValue = 0;
            var duration = '<?= !empty($arVisual['CREDIT']['DURATION']) ? $arVisual['CREDIT']['DURATION'] : 10 ?>';
            var priceDisplay;

            if (status === 'dynamic' && !!summary && summary.discount > 0) {
                priceValue = summary.discount;
            } else {
                if (!price.discount) {
                    priceValue = price.base.value;
                } else {
                    priceValue = price.discount.value;
                }
            }

            priceValue = priceValue / duration;
            priceValue = priceValue.toFixed(1);

            <?php if ($bBase) { ?>
                var currency = price.currency.CURRENCY;
                BX.Currency.setCurrencyFormat(price.currency.CURRENCY, price.currency);
                priceDisplay = BX.Currency.currencyFormat(priceValue, currency, true);
            <?php } else { ?>
                priceDisplay = priceValue;
            <?php } ?>

            $('[data-role="price.credit"]').html(priceDisplay);
        }

        <?php if ($arVisual['PRINT']['SHOW']) { ?>
            var print = $('[data-role="print"]', dynamic);

            print.on('click', function () {
                window.print();
            });
        <?php } ?>
    }, {
        'name': '[Component] bitrix:catalog.element (catalog.default.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php unset($arEcommerce) ?>