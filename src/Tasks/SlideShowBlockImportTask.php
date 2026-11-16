<?php

declare(strict_types=1);

namespace Sunnysideup\SlideShowBlock\Tasks;

use Symfony\Component\Console\Input\InputInterface;
use SilverStripe\PolyExecution\PolyOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use DNADesign\Elemental\Extensions\ElementalPageExtension;
use DNADesign\Elemental\Models\BaseElement;
use DNADesign\Elemental\Models\ElementalArea;
use Page;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\Core\Path;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DataObject;
use Sunnysideup\SlideShowBlock\Blocks\SlideShowBlock;
use Sunnysideup\SlideShowBlock\Model\SlideShowBlockSlideItem;
use Sunnysideup\SlideShowBlock\Slides\SlideShowBlockSlide;
use Symfony\Component\Yaml\Yaml;

/**
 * Creates a Slide show block (with its slides and items) from a YAML file.
 *
 * Options (CLI options, shortcuts in brackets):
 *
 *   --file=… (-f)    YAML file. Either "vendor/module:path/file.yml", a path relative
 *               to the project root, or an absolute path.
 *               Default: the Ship Shape deck that ships with this module.
 *   --page=… (-p)    Page ID or URLSegment to add the block to. Without it, a new
 *               draft page is created, titled from the YAML "PageTitle".
 *   --replace (-r)  Archive any slide show block with the same title on that page first.
 *   --publish (-P)  Publish the block (and the page, if this task created it).
 *
 * Example:
 *   vendor/bin/sake tasks:slideshowblock-import --page=hosting --replace --publish
 *
 * YAML format: see _content/ship-shape-hosting.yml. Keys are the field names
 * of the block, slide and item classes; "Type" picks the slide class
 * (e.g. "Cards" means SlideShowBlockSlideCards, or give the full class name).
 */
class SlideShowBlockImportTask extends BuildTask
{
    protected static string $commandName = 'slideshowblock-import';

    protected string $title = 'Slide show block: import from YAML';

    protected static string $description = 'Creates a slide show block with all its slides and items from a YAML file. '
        . 'Options: file, page, replace, publish.';

    private static $default_file = 'app/_content/mycontent.yml';

    /** @var string[] */
    private array $warnings = [];

    private ?PolyOutput $output = null;

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        $this->output = $output;
        $this->warnings = [];

        $fileLocation = (string) ($input->getOption('file') ?: static::config()->get('default_file'));
        // @TODO (SS6 upgrade) this line overrides the "file" option (kept from the original code).
        $fileLocation = 'app/_content/ship-shape-hosting.yml';

        $file = $this->resolveFile($fileLocation);
        if ($file === null) {
            // @TODO (SS6 upgrade) this returns file contents, not a path (kept from the original code).
            $file = file_get_contents(Path::join(BASE_PATH, $fileLocation));
            if (! $file) {
                $this->say('Could not find the YAML file. Check the "file" option.', 'error');

                return Command::FAILURE;
            }
        }

        $this->say('Reading ' . $file);
        $data = Yaml::parseFile($file);
        if (!is_array($data) || empty($data['Slides']) || !is_array($data['Slides'])) {
            $this->say('The YAML file needs a "Slides" list.', 'error');

            return Command::FAILURE;
        }

        $publish = (bool) $input->getOption('publish');
        [$page, $pageIsNew] = $this->findOrCreatePage((string) $input->getOption('page'), $data);
        if (!$page) {
            return Command::FAILURE;
        }

        $area = $this->elementalAreaFor($page);
        if (!$area instanceof ElementalArea) {
            return Command::FAILURE;
        }

        $blockTitle = (string) ($data['Title'] ?? 'Slide show');
        if ($input->getOption('replace')) {
            $this->archiveExisting($area, $blockTitle);
        }

        $block = SlideShowBlock::create();
        $this->assign($block, $data, ['Slides', 'PageTitle']);
        $block->Title = $blockTitle;
        $block->ParentID = $area->ID;
        $block->Sort = (int) $area->Elements()->max('Sort') + 1;
        $block->write();
        $this->say(sprintf('Created block "%s" (#%d) on page "%s"', $blockTitle, $block->ID, $page->Title), 'created');
        foreach (array_values($data['Slides']) as $index => $slideData) {
            $this->createSlide($block, (array) $slideData, $index + 1);
        }

        if ($publish) {
            $block->publishRecursive();
            $this->say('Published the block and all of its slides', 'created');
            if ($pageIsNew) {
                $page->publishRecursive();
                $this->say('Published the new page', 'created');
            }
        } else {
            $this->say('Saved as draft. Add --publish to publish.');
        }

        foreach ($this->warnings as $warning) {
            $this->say($warning, 'error');
        }

        return Command::SUCCESS;
    }

    protected function createSlide(SlideShowBlock $block, array $data, int $sort): void
    {
        $class = $this->slideClassFor((string) ($data['Type'] ?? ''));
        if ($class === null) {
            $this->warnings[] = sprintf('Slide %d: unknown Type "%s", skipped.', $sort, $data['Type'] ?? '');

            return;
        }

        /** @var SlideShowBlockSlide $slide */
        $slide = $class::create();
        $this->assign($slide, $data, ['Type', 'Items', 'ExtraItems'], 'Slide ' . $sort);
        $slide->SlideShowBlockID = $block->ID;
        $slide->Sort = $sort;
        $slide->write();

        $counts = [];
        foreach (['Items' => 'SlideID', 'ExtraItems' => 'ExtraSlideID'] as $list => $foreignKey) {
            $rows = $data[$list] ?? [];
            foreach (array_values((array) $rows) as $i => $row) {
                $item = SlideShowBlockSlideItem::create();
                $this->assign($item, is_array($row) ? $row : ['Title' => (string) $row], [], sprintf('Slide %d %s', $sort, $list));
                $item->$foreignKey = $slide->ID;
                $item->Sort = $i + 1;
                $item->write();
            }

            $counts[] = count((array) $rows) . ($list === 'Items' ? ' items' : ' extra items');
        }

        $this->say(sprintf(
            '  %d. %s: %s (%s)',
            $sort,
            $slide->i18n_singular_name(),
            $slide->getHeadingPlain(),
            implode(', ', $counts)
        ), 'created');
    }

    /**
     * Copy known database fields onto the record; warn about the rest.
     */
    protected function assign(DataObject $record, array $data, array $skip, string $context = ''): void
    {
        foreach ($data as $field => $value) {
            if (in_array($field, $skip, true)) {
                continue;
            }

            if (!$record->hasDatabaseField($field)) {
                $this->warnings[] = trim(sprintf(
                    '%s: "%s" is not a field on %s, ignored.',
                    $context,
                    $field,
                    $record->i18n_singular_name()
                ), ': ');
                continue;
            }

            $record->$field = is_string($value) ? rtrim($value, "\n") : $value;
        }
    }

    protected function slideClassFor(string $type): ?string
    {
        if ($type === '') {
            return null;
        }

        $candidates = [
            $type,
            SlideShowBlockSlide::class . $type,
        ];
        foreach ($candidates as $class) {
            if (class_exists($class) && is_subclass_of($class, SlideShowBlockSlide::class)) {
                return $class;
            }
        }

        return null;
    }

    /**
     * @return array{0: ?SiteTree, 1: bool} page, created by this task
     */
    protected function findOrCreatePage(string $pageRef, array $data): array
    {
        if ($pageRef !== '') {
            $page = ctype_digit($pageRef)
                ? SiteTree::get()->byID((int) $pageRef)
                : SiteTree::get()->filter(['URLSegment' => $pageRef])->first();
            if (!$page) {
                $this->say(sprintf('No page found for "%s".', $pageRef), 'error');

                return [null, false];
            }

            return [$page, false];
        }

        $page = Page::create();
        $page->Title = (string) ($data['PageTitle'] ?? $data['Title'] ?? 'Slide show');
        $page->ParentID = 0;
        $page->write();
        $this->say(sprintf('Created draft page "%s" (#%d)', $page->Title, $page->ID), 'created');

        return [$page, true];
    }

    protected function elementalAreaFor(SiteTree $page): ?ElementalArea
    {
        if (!$page->hasExtension(ElementalPageExtension::class)) {
            $this->say(
                sprintf('"%s" (%s) does not use Elemental. Add ElementalPageExtension to that page type.', $page->Title, $page->ClassName),
                'error'
            );

            return null;
        }

        $area = $page->ElementalArea();
        if (!$area || !$area->exists()) {
            // Elemental creates missing areas when the page is written.
            $page->write();
            $area = $page->ElementalArea();
        }

        return ($area && $area->exists()) ? $area : null;
    }

    protected function archiveExisting(ElementalArea $area, string $title): void
    {
        $existing = SlideShowBlock::get()->filter(['ParentID' => $area->ID, 'Title' => $title]);
        foreach ($existing as $old) {
            /** @var BaseElement $old */
            $old->doArchive();
            $this->say(sprintf('Archived existing block #%d', $old->ID), 'deleted');
        }
    }

    protected function resolveFile(string $spec): ?string
    {
        $spec = trim($spec);
        if ($spec === '') {
            return null;
        }

        if (is_file($spec)) {
            return $spec;
        }

        $local = Path::join(BASE_PATH, $spec);
        if (is_file($local)) {
            return $local;
        }

        if (str_contains($spec, ':')) {
            [$moduleName, $relative] = array_map(trim(...), explode(':', $spec, 2));
            $module = ModuleLoader::getModule($moduleName);
            if ($module) {
                $path = $module->getResource($relative)->getPath();
                if (is_file($path)) {
                    return $path;
                }
            }
        }

        return null;
    }

    protected function say(string $message, string $type = ''): void
    {
        $this->output?->writeln($message);
    }

    public function getOptions(): array
    {
        return [
            new InputOption('file', 'f', InputOption::VALUE_REQUIRED, 'YAML file: "vendor/module:path/file.yml", a path relative to the project root, or an absolute path'),
            new InputOption('page', 'p', InputOption::VALUE_REQUIRED, 'Page ID or URLSegment to add the block to (a new draft page is created without it)'),
            new InputOption('replace', 'r', InputOption::VALUE_NONE, 'Archive any slide show block with the same title on that page first'),
            new InputOption('publish', 'P', InputOption::VALUE_NONE, 'Publish the block (and the page, if this task created it)'),
        ];
    }
}
