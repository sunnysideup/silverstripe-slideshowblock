<?php

declare(strict_types=1);

namespace Sunnysideup\SlideShowBlock\Slides;

/**
 * Numbered next steps and a call-to-action button.
 *
 * @property string $CTALabel
 * @property string $CTALink
 */
class SlideShowBlockSlideSteps extends SlideShowBlockSlide
{
    private static $table_name = 'SlideShowBlockSlideSteps';

    private static $singular_name = 'Steps slide';

    private static $plural_name = 'Steps slides';

    private static $db = [
        'CTALabel' => 'Varchar(100)',
        'CTALink' => 'Varchar(255)',
    ];

    private static $field_labels = [
        'CTALabel' => 'Button text',
        'CTALink' => 'Button link (URL, /page or #anchor)',
    ];

    private static $layout_key = 'steps';

    private static $items_title = 'Steps';

    private static $items_fields = [
        'Title' => 'Step',
        'Text' => 'Text',
    ];
}
