# Upgrading `sunnysideup/slideshowblock` to Silverstripe CMS 6

## 1. Dependency requirements

⚠️ BREAKING CHANGE: the module no longer installs on Silverstripe 5. Update your project's constraints to match:

| Package                                | Old     | New      |
|----------------------------------------|---------|----------|
| `silverstripe/framework`               | `^5.0`  | `^6.0`   |
| `silverstripe/admin`                   | `^2.0`  | `^3.0`   |
| `dnadesign/silverstripe-elemental`     | `^5.0`  | `^6.0`   |
| `symbiote/silverstripe-gridfieldextensions` | `^4.0` | `^5.2` |

Other `composer.json` notes:

- `require-dev` was changed from `{}` to `[]`. Composer tolerates this, but `composer validate` may complain. Use `{}` if it does.
- `minimum-stability: dev` is unchanged. Leave it unless you want stable-only resolution.

## 2. Block configuration

### `SlideShowBlock`: `$description` renamed to `$class_description`

⚠️ BREAKING CHANGE: Elemental 6 reads the block's CMS description from `$class_description`.

- Rename `private static $description` to `private static $class_description` in any subclass of `SlideShowBlock`.
- Rename it in any YAML or extension config that sets `SlideShowBlock.description`.

## 3. Import task: now a CLI command

`SlideShowBlockImportTask` was rewritten for the Silverstripe 6 `BuildTask` API, which is built on Symfony Console. It no longer runs through `dev/tasks/...` in the browser.

### Invocation

⚠️ BREAKING CHANGE: the HTTP/query-string interface is gone. Update any scripts, deploy hooks or docs that call the task.

```diff
-vendor/bin/sake dev/tasks/slideshowblock-import "page=hosting&replace=1&publish=1"
+vendor/bin/sake tasks:slideshowblock-import --page=hosting --replace --publish
```

| Old (query string) | New (CLI option) | Shortcut |
|--------------------|------------------|----------|
| `file=…`           | `--file=…`       | `-f`     |
| `page=…`           | `--page=…`       | `-p`     |
| `replace=1`        | `--replace`      | `-r`     |
| `publish=1`        | `--publish`      | `-P`     |

- `replace` and `publish` are now flags and take no value.
- Shortcuts are case-sensitive: `-p` is page, `-P` is publish.
- The old `private static $segment` is replaced by `protected static string $commandName = 'slideshowblock-import'`.

### API changes for subclasses

⚠️ BREAKING CHANGE: if you extend the task, update it as follows.

- Replace `public function run($request)` with `protected function execute(InputInterface $input, PolyOutput $output): int`.
- Read options with `$input->getOption('name')` instead of `$request->getVar('name')`.
- Return `Command::SUCCESS` or `Command::FAILURE`. Every early `return;` now returns `Command::FAILURE`.
- Declare options in `getOptions()`, which returns `InputOption[]`. Undeclared options are rejected by the CLI.
- Property signatures are now typed:
  - `protected string $title`
  - `protected static string $description`
- `Director::baseFolder()` is replaced by the `BASE_PATH` constant.

### Output behaviour

- `say()` now calls `PolyOutput::writeln()`. The old `DB::alteration_message()` styling is gone.
- The `$type` argument (`error`, `created`) is accepted but ignored. Errors and successes look the same in the output.
- Messages are no longer HTML-escaped, because the task is CLI-only.
- The trailing `Done.` message was removed.
- Warnings are still printed at the end of the run.

### 🚨 Unresolved issues carried over from the original code

**🚨 CRITICAL REVIEW REQUIRED: the `--file` option is ignored.** `execute()` reads the option and the `default_file` config, then immediately overwrites the result with the hardcoded path `app/_content/ship-shape-hosting.yml`. Every run imports that one file, whatever you pass. This was in the original code and is marked `@TODO (SS6 upgrade)` in the source. Remove the hardcoded assignment before relying on `--file` or `default_file`.

**🚨 CRITICAL REVIEW REQUIRED: broken fallback when the YAML file cannot be resolved.** If `resolveFile()` returns `null`, the task calls `file_get_contents()` and puts the file *contents* in `$file`. It then passes that to `Yaml::parseFile()`, which expects a *path*. This branch will fail or misbehave. Also marked `@TODO (SS6 upgrade)`. Fix it by failing cleanly or by using `Yaml::parse()` on the contents.

**🚨 CRITICAL REVIEW REQUIRED: the hardcoded path points at the project, not the module.** `app/_content/ship-shape-hosting.yml` is looked up under the project root. The task's docblock says the default is the Ship Shape deck shipped with the module. Check that the file exists in your project, or the task will fail.

## 4. Verification checklist

1. Run `composer update` and confirm the new constraints resolve.
2. Run `vendor/bin/sake dev/build flush=1`.
3. Run `vendor/bin/sake tasks:slideshowblock-import --help` and confirm the four options are listed.
4. Open an existing Slide show block in the CMS and confirm its description appears in the block picker.
5. Fix the 🚨 items in section 3 before using the import task in any environment.
