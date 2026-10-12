<?php

declare(strict_types=1);

namespace Sunnysideup\SlideShowBlock\Slides;

/**
 * A grid of cards, each with an optional icon.
 *
 *  - Default: yellow icons on tinted cards (promises, features)
 *  - Warning: orange icons on white cards (problems)
 *  - Plain:   no icon tiles, compact cards (lists of facts)
 *
 * Tick "Highlight" on a card to make it stand out.
 *
 * @property string $CardStyle
 * @property string $Columns
 */
class SlideShowBlockSlideCards extends SlideShowBlockSlide
{
    private static $table_name = 'SlideShowBlockSlideCards';

    private static $singular_name = 'Cards slide';

    private static $plural_name = 'Cards slides';

    private static $db = [
        'CardStyle' => "Enum('Default,Warning,Plain', 'Default')",
        'Columns' => "Enum('Auto,2,3,4', 'Auto')",
    ];

    private static $defaults = [
        'CardStyle' => 'Default',
        'Columns' => 'Auto',
    ];

    private static $field_labels = [
        'CardStyle' => 'Card style',
        'Columns' => 'Columns on wide screens',
    ];

    private static $layout_key = 'cards';

    private static $items_title = 'Cards';

    private static $items_fields = [
        'Icon' => 'Icon',
        'Title' => 'Title',
        'Text' => 'Text',
        'Highlight' => 'Highlight',
    ];

    public function getCardStyleClass(): string
    {
        return strtolower((string) $this->CardStyle ?: 'default');
    }

    public function getColumnsClass(): string
    {
        return strtolower((string) $this->Columns ?: 'auto');
    }
}
