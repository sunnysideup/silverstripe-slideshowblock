<?php

declare(strict_types=1);

namespace Sunnysideup\SlideShowBlock\Blocks;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\HasManyList;
use SilverStripe\View\Requirements;
use Sunnysideup\SlideShowBlock\Slides\SlideShowBlockSlide;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * A story told as a deck of slides.
 *
 * On narrow screens the slides stack; from 48rem up they become a
 * swipeable deck (see client/src/js/SlideShowBlock.js).
 *
 * @property string $Title
 * @property bool   $ShowTitle
 * @method HasManyList<SlideShowBlockSlide> Slides()
 */
class SlideShowBlock extends BaseElement
{
    private static $table_name = 'SlideShowBlock';

    private static $singular_name = 'Slide show';

    private static $plural_name = 'Slide shows';

    private static $description = 'A swipeable deck of slides that tells a story.';

    private static $icon = 'font-icon-block-carousel';

    /**
     * Slides are edited in their own GridField, which the inline
     * (React) block editor cannot show.
     */
    private static $inline_editable = false;

    private static $has_many = [
        'Slides' => SlideShowBlockSlide::class,
    ];

    private static $owns = [
        'Slides',
    ];

    private static $cascade_deletes = [
        'Slides',
    ];

    private static $cascade_duplicates = [
        'Slides',
    ];

    /**
     * Composer name of the module that holds client/dist.
     */
    private static $resource_module = 'sunnysideup/slideshowblock';

    /**
     * Optional allow-list of slide classes. Empty = all slide types.
     *
     * @var string[]
     */
    private static $allowed_slide_classes = [];

    public function getType(): string
    {
        return _t(self::class . '.BlockType', 'Slide show');
    }

    public function getSummary(): string
    {
        $count = $this->isInDB() ? $this->Slides()->count() : 0;

        return _t(
            self::class . '.Summary',
            '{count} slide(s)',
            ['count' => $count]
        );
    }

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName('Slides');

            if (!$this->isInDB()) {
                $fields->addFieldToTab(
                    'Root.Main',
                    LiteralField::create(
                        'SlidesNotice',
                        '<p class="message notice">'
                        . _t(self::class . '.SaveFirst', 'Save this block first, then you can add slides.')
                        . '</p>'
                    )
                );

                return;
            }

            $config = GridFieldConfig_RecordEditor::create(50);
            $config->removeComponentsByType(GridFieldAddNewButton::class);
            $config->addComponent(
                GridFieldAddNewMultiClass::create('buttons-before-left')
                    ->setClasses($this->getSlideClassOptions())
            );
            $config->addComponent(GridFieldOrderableRows::create('Sort'));

            $fields->addFieldToTab(
                'Root.Main',
                GridField::create(
                    'Slides',
                    _t(self::class . '.Slides', 'Slides'),
                    $this->Slides(),
                    $config
                )
            );
        });

        return parent::getCMSFields();
    }

    /**
     * Slide types editors can add, as [class => label].
     *
     * @return array<string, string>
     */
    public function getSlideClassOptions(): array
    {
        $classes = (array) static::config()->get('allowed_slide_classes');
        if ($classes === []) {
            $classes = ClassInfo::subclassesFor(SlideShowBlockSlide::class, false);
        }

        $options = [];
        foreach ($classes as $class) {
            if (!is_a($class, SlideShowBlockSlide::class, true) || $class === SlideShowBlockSlide::class) {
                continue;
            }
            $options[$class] = $class::singleton()->i18n_singular_name();
        }
        asort($options);

        return $options;
    }

    /**
     * Used by the template; also pulls in the CSS and JS.
     *
     * @return HasManyList<SlideShowBlockSlide>
     */
    public function SlidesForTemplate(): HasManyList
    {
        $this->requireAssets();

        return $this->Slides();
    }

    public function requireAssets(): void
    {
        $module = static::resourceModule();
        // Requirements::css($module . ':client/dist/main.css');
        // Requirements::javascript($module . ':client/dist/runtime.js', ['defer' => true]);
        // Requirements::javascript($module . ':client/dist/app.js', ['defer' => true]);
    }

    public static function resourceModule(): string
    {
        return (string) static::config()->get('resource_module');
    }
}
