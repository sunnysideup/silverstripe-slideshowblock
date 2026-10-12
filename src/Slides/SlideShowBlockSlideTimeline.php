<?php

declare(strict_types=1);

namespace Sunnysideup\SlideShowBlock\Slides;

use SilverStripe\ORM\ArrayList;

/**
 * Horizontal bars, scaled to the largest "Length" value.
 */
class SlideShowBlockSlideTimeline extends SlideShowBlockSlide
{
    private static $table_name = 'SlideShowBlockSlideTimeline';

    private static $singular_name = 'Timeline slide';

    private static $plural_name = 'Timeline slides';

    private static $layout_key = 'timeline';

    private static $items_title = 'Bars';

    private static $items_fields = [
        'Title' => 'Label',
        'Text' => 'Sub label',
        'Value' => 'Length (number)',
        'Label' => 'Text in bar',
    ];

    /**
     * Items with a BarWidth (0–100) relative to the longest bar.
     */
    public function BarsForTemplate(): ArrayList
    {
        $items = $this->Items();
        $max = max(1, (int) $items->max('Value'));

        $list = ArrayList::create();
        foreach ($items as $item) {
            $list->push($item->customise([
                'BarWidth' => round(max(0, (int) $item->Value) / $max * 100, 1),
            ]));
        }

        return $list;
    }
}
