<?php

declare(strict_types=1);

namespace Sunnysideup\SlideShowBlock\Slides;

/**
 * One big number (e.g. "20+ years") next to a list of reasons.
 *
 * @property string $StatNumber
 * @property string $StatLabel
 */
class SlideShowBlockSlideProof extends SlideShowBlockSlide
{
    private static $table_name = 'SlideShowBlockSlideProof';

    private static $singular_name = 'Proof slide';

    private static $plural_name = 'Proof slides';

    private static $db = [
        'StatNumber' => 'Varchar(20)',
        'StatLabel' => 'Varchar(255)',
    ];

    private static $field_labels = [
        'StatNumber' => 'Big number (e.g. 20+)',
        'StatLabel' => 'Text under the big number',
    ];

    private static $layout_key = 'proof';

    private static $items_title = 'Reasons';

    private static $items_fields = [
        'Title' => 'Reason',
        'Text' => 'Explanation',
    ];

}
