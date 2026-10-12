<?php

declare(strict_types=1);

namespace Sunnysideup\SlideShowBlock\Slides;

/**
 * Opening slide: big heading, subtitle, a light line and a row of pills.
 *
 * @property string $SubTitle
 */
class SlideShowBlockSlideTitle extends SlideShowBlockSlide
{
    private static $table_name = 'SlideShowBlockSlideTitle';

    private static $singular_name = 'Title slide';

    private static $plural_name = 'Title slides';

    private static $db = [
        'SubTitle' => 'Varchar(255)',
    ];

    private static $field_labels = [
        'Lead' => 'Light subtitle',
    ];

    private static $layout_key = 'title';

    private static $items_title = 'Pills';

    private static $items_fields = [
        'Title' => 'Pill text',
    ];

    private static $hidden_fields = ['Callout', 'CalloutNote', 'Footnote'];
}
