<?php

declare(strict_types=1);

namespace Sunnysideup\SlideShowBlock\Model;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;
use Sunnysideup\SlideShowBlock\Slides\SlideShowBlockSlide;

/**
 * One row in a slide's list: a pill, a card, a step, a price band…
 *
 * Every slide type uses the same item class and picks the columns it needs
 * (see SlideShowBlockSlide::$items_fields). An item belongs to one list:
 * either Slide (the main list) or ExtraSlide (the second list).
 *
 * @property string $Title
 * @property string $Text
 * @property string $Label
 * @property string $LabelAlt
 * @property int    $Value
 * @property string $Icon
 * @property bool   $Highlight
 * @property int    $Sort
 * @property int    $SlideID
 * @property int    $ExtraSlideID
 * @method SlideShowBlockSlide Slide()
 * @method SlideShowBlockSlide ExtraSlide()
 * @mixin Versioned
 */
class SlideShowBlockSlideItem extends DataObject
{
    private static $table_name = 'SlideShowBlockSlideItem';

    private static $singular_name = 'Slide item';

    private static $plural_name = 'Slide items';

    private static $db = [
        'Title' => 'Varchar(255)',
        'Text' => 'Text',
        'Label' => 'Varchar(100)',
        'LabelAlt' => 'Varchar(100)',
        'Value' => 'Int',
        'Icon' => 'Varchar(50)',
        'Highlight' => 'Boolean',
        'Sort' => 'Int',
    ];

    private static $has_one = [
        'Slide' => SlideShowBlockSlide::class,
        'ExtraSlide' => SlideShowBlockSlide::class,
    ];

    private static $extensions = [
        Versioned::class,
    ];

    private static $default_sort = '"Sort" ASC, "ID" ASC';

    private static $summary_fields = [
        'Title' => 'Title',
        'Text' => 'Text',
    ];

    /**
     * The form field used for each column when editing inline.
     */
    public static function inlineFieldFor(string $name): FormField
    {
        return match ($name) {
            'Text' => TextareaField::create($name)->setRows(2),
            'Value' => NumericField::create($name),
            'Icon' => DropdownField::create($name, '', SlideShowBlockIcons::options())
                ->setEmptyString('(none)'),
            'Highlight' => CheckboxField::create($name),
            default => TextField::create($name),
        };
    }

    public function getIconSVG(): ?DBHTMLText
    {
        $svg = SlideShowBlockIcons::svg((string) $this->Icon);
        if ($svg === '') {
            return null;
        }

        return DBField::create_field('HTMLFragment', $svg);
    }

    /**
     * Value clamped to 0–100, for bars and sliders.
     */
    public function getValuePercent(): int
    {
        return max(0, min(100, (int) $this->Value));
    }

    public function getOwnerSlide(): ?SlideShowBlockSlide
    {
        foreach ([$this->Slide(), $this->ExtraSlide()] as $slide) {
            if ($slide && $slide->exists()) {
                return $slide;
            }
        }

        return null;
    }

    public function canView($member = null)
    {
        return true;
    }

    public function canEdit($member = null)
    {
        $slide = $this->getOwnerSlide();
        if ($slide) {
            return $slide->canEdit($member);
        }

        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canDelete($member = null)
    {
        return $this->canEdit($member);
    }

    public function canCreate($member = null, $context = [])
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }
}
