<?php

declare(strict_types=1);

namespace Sunnysideup\SlideShowBlock\Slides;

/**
 * Pricing factors shown as sliders, followed by price bands.
 */
class SlideShowBlockSlidePricing extends SlideShowBlockSlide
{
    private static $table_name = 'SlideShowBlockSlidePricing';

    private static $singular_name = 'Pricing slide';

    private static $plural_name = 'Pricing slides';

    private static $layout_key = 'pricing';

    private static $items_title = 'Pricing factors (sliders)';

    private static $items_fields = [
        'Title' => 'Factor',
        'Text' => 'Explanation',
        'Value' => 'Slider position (0–100)',
        'Label' => 'Left label',
        'LabelAlt' => 'Right label',
    ];

    private static $extra_items_title = 'Price bands';

    private static $extra_items_fields = [
        'Title' => 'Band name',
        'Label' => 'Price',
        'Text' => 'Small print',
        'Highlight' => 'Highlight',
    ];
}
