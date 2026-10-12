<?php

declare(strict_types=1);

namespace Sunnysideup\SlideShowBlock\Slides;

/**
 * A short process (boxes joined by arrows) plus a bullet list.
 */
class SlideShowBlockSlideFlow extends SlideShowBlockSlide
{
    private static $table_name = 'SlideShowBlockSlideFlow';

    private static $singular_name = 'Flow slide';

    private static $plural_name = 'Flow slides';

    private static $layout_key = 'flow';

    private static $items_title = 'Flow steps';

    private static $items_fields = [
        'Title' => 'Step',
        'Text' => 'Text',
        'Highlight' => 'Highlight',
    ];

    private static $extra_items_title = 'Bullet points';

    private static $extra_items_fields = [
        'Title' => 'Bold lead-in',
        'Text' => 'Text',
    ];
}
