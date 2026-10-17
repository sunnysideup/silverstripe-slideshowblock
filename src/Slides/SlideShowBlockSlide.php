<?php

declare(strict_types=1);

namespace Sunnysideup\SlideShowBlock\Slides;

use SilverStripe\Core\Convert;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\HasManyList;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;
use Sunnysideup\SlideShowBlock\Blocks\SlideShowBlock;
use Sunnysideup\SlideShowBlock\Model\SlideShowBlockSlideItem;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;

/**
 * Base class for every slide type.
 *
 * Each subclass (SlideShowBlockSlideXxx) has its own .ss, .scss and .js
 * named after the class. Subclasses mostly differ in configuration:
 *
 *  - layout_key          CSS modifier, e.g. "cards" -> .slideshow-slide--cards
 *  - items_title         label of the first list (empty = no list)
 *  - items_fields        [ItemField => column label] edited inline
 *  - extra_items_title / extra_items_fields   an optional second list
 *  - hidden_fields       shared fields this slide type does not use
 *
 * @property string $Kicker
 * @property string $Heading
 * @property string $Lead
 * @property string $Callout
 * @property string $CalloutNote
 * @property string $Footnote
 * @property int    $Sort
 * @property int    $SlideShowBlockID
 * @method SlideShowBlock SlideShowBlock()
 * @method HasManyList<SlideShowBlockSlideItem> Items()
 * @method HasManyList<SlideShowBlockSlideItem> ExtraItems()
 * @mixin Versioned
 */
class SlideShowBlockSlide extends DataObject
{
    private static $table_name = 'SlideShowBlockSlide';

    private static $singular_name = 'Slide';

    private static $plural_name = 'Slides';

    private static $db = [
        'Kicker' => 'Varchar(255)',
        'Heading' => 'Varchar(255)',
        'Lead' => 'Text',
        'Callout' => 'Text',
        'CalloutNote' => 'Text',
        'Footnote' => 'HTMLText',
        'Sort' => 'Int',
    ];

    private static $has_one = [
        'SlideShowBlock' => SlideShowBlock::class,
    ];

    private static $has_many = [
        'Items' => SlideShowBlockSlideItem::class . '.Slide',
        'ExtraItems' => SlideShowBlockSlideItem::class . '.ExtraSlide',
    ];

    private static $owns = [
        'Items',
        'ExtraItems',
    ];

    private static $cascade_deletes = [
        'Items',
        'ExtraItems',
    ];

    private static $cascade_duplicates = [
        'Items',
        'ExtraItems',
    ];

    private static $extensions = [
        Versioned::class,
    ];

    private static $default_sort = '"Sort" ASC, "ID" ASC';

    private static $summary_fields = [
        'SlideTypeName' => 'Type',
        'HeadingPlain' => 'Heading',
        'Items.Count' => 'Items',
    ];

    private static $field_labels = [
        'Kicker' => 'Kicker (small label above the heading)',
        'Lead' => 'Intro text',
        'CalloutNote' => 'Callout note (small text under the callout)',
    ];

    private static $layout_key = 'default';

    private static $items_title = '';

    /**
     * @var array<string, string> item field => column label
     */
    private static $items_fields = [];

    private static $extra_items_title = '';

    /**
     * @var array<string, string> item field => column label
     */
    private static $extra_items_fields = [];

    /**
     * @var string[]
     */
    private static $hidden_fields = [];



    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName(['SlideShowBlockID', 'Sort', 'Items', 'ExtraItems']);

            $heading = $fields->dataFieldByName('Heading');
            if ($heading) {
                $heading->setDescription(
                    _t(self::class . '.HeadingHelp', 'Wrap words in *asterisks* to highlight them.')
                );
            }

            foreach (['Lead', 'Callout', 'CalloutNote'] as $name) {
                $field = $fields->dataFieldByName($name);
                if ($field instanceof TextareaField) {
                    $field->setRows(2);
                }
            }

            $footnote = $fields->dataFieldByName('Footnote');
            if ($footnote instanceof HTMLEditorField) {
                $footnote->setRows(3);
            }

            $hiddenFields = (array) static::config()->get('hidden_fields');
            if ($hiddenFields) {
                $fields->removeByName((array) $hiddenFields);
            }

            $this->addItemGrid($fields, 'Items', 'items');
            $this->addItemGrid($fields, 'ExtraItems', 'extra_items');
        });

        return parent::getCMSFields();
    }

    /**
     * An inline-editable, sortable list of items.
     */
    protected function addItemGrid(FieldList $fields, string $relation, string $configPrefix): void
    {
        /** @var array<string, string> $columns */
        $columns = (array) static::config()->get($configPrefix . '_fields');
        if ($columns === []) {
            return;
        }
        $title = (string) static::config()->get($configPrefix . '_title') ?: $relation;

        if (!$this->isInDB()) {
            $fields->addFieldToTab(
                'Root.Main',
                LiteralField::create(
                    $relation . 'Notice',
                    '<p class="message notice">'
                    . Convert::raw2xml($title) . ': '
                    . _t(self::class . '.SaveFirst', 'save the slide first, then you can add items.')
                    . '</p>'
                )
            );

            return;
        }

        $display = [];
        foreach ($columns as $name => $label) {
            $display[$name] = [
                'title' => $label,
                'callback' => static fn ($record, $column) => SlideShowBlockSlideItem::inlineFieldFor($column),
            ];
        }

        $config = GridFieldConfig::create()->addComponents(
            GridFieldButtonRow::create('before'),
            GridFieldToolbarHeader::create(),
            GridFieldTitleHeader::create(),
            GridFieldEditableColumns::create()->setDisplayFields($display),
            GridFieldDeleteAction::create(),
            GridFieldAddNewInlineButton::create('buttons-before-left')
                ->setTitle(_t(self::class . '.AddItem', 'Add row')),
            GridFieldOrderableRows::create('Sort')
        );

        $fields->addFieldToTab(
            'Root.Main',
            GridField::create($relation, $title, $this->$relation(), $config)
        );
    }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (!$this->Sort && $this->SlideShowBlockID) {
            $this->Sort = (int) static::get()
                ->filter('SlideShowBlockID', $this->SlideShowBlockID)
                ->max('Sort') + 1;
        }
    }

    public function getTitle(): string
    {
        return $this->getHeadingPlain() ?: $this->getSlideTypeName();
    }

    public function getSlideTypeName(): string
    {
        return $this->i18n_singular_name();
    }

    public function getHeadingPlain(): string
    {
        return trim(str_replace('*', '', (string) $this->Heading));
    }

    /**
     * Heading with *highlighted* words wrapped in <mark>.
     */
    public function getHeadingHTML(): DBHTMLText
    {
        $safe = Convert::raw2xml((string) $this->Heading);
        $html = (string) preg_replace('/\*(.+?)\*/u', '<mark>$1</mark>', $safe);

        return DBField::create_field('HTMLFragment', $html);
    }

    public function getLayoutKey(): string
    {
        return (string) static::config()->get('layout_key');
    }

    /**
     * Rendered slide (inner markup). The block template wraps it in <li>.
     */
    public function getSlideHTML(): DBHTMLText
    {
        return $this->renderWith($this->getViewerTemplates());
    }

    public function canView($member = null)
    {
        return true;
    }

    public function canEdit($member = null)
    {
        $block = $this->SlideShowBlock();
        if ($block && $block->exists()) {
            return $block->canEdit($member);
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
