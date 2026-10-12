<?php

declare(strict_types=1);

namespace Sunnysideup\SlideShowBlock\Model;

use SilverStripe\Core\Config\Configurable;

/**
 * A small set of line icons (24×24, stroke = currentColor).
 *
 * Add your own in YAML:
 *
 *   Sunnysideup\SlideShowBlock\Model\SlideShowBlockIcons:
 *     icons:
 *       rocket:
 *         label: 'Rocket'
 *         paths: '<path d="…"/>'
 */
class SlideShowBlockIcons
{
    use Configurable;

    /**
     * @var array<string, array{label: string, paths: string}>
     */
    private static $icons = [
        'arrow-up' => ['label' => 'Arrow up', 'paths' => '<path d="M12 19V5M5 12l7-7 7 7"/>'],
        'bar-chart' => ['label' => 'Bar chart', 'paths' => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>'],
        'check-circle' => ['label' => 'Check in circle', 'paths' => '<circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/>'],
        'devices' => ['label' => 'Devices', 'paths' => '<rect x="3" y="4" width="13" height="16" rx="2"/><rect x="17" y="9" width="4" height="11" rx="1"/>'],
        'dollar' => ['label' => 'Dollar', 'paths' => '<path d="M12 3v18M17 7H9.5a3 3 0 0 0 0 6h5a3 3 0 0 1 0 6H6"/>'],
        'invoice-alert' => ['label' => 'Invoice with alert', 'paths' => '<path d="M6 3h9l4 4v14H6z"/><path d="M12 10v4M12 17h.01"/>'],
        'pause' => ['label' => 'Pause', 'paths' => '<circle cx="12" cy="12" r="9"/><path d="M10 9v6M14 9v6"/>'],
        'question' => ['label' => 'Question', 'paths' => '<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 1 1 3.5 2.3c-.6.3-1 .9-1 1.6V14M12 17h.01"/>'],
        'search' => ['label' => 'Search', 'paths' => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/>'],
        'server' => ['label' => 'Server', 'paths' => '<rect x="3" y="4" width="18" height="6" rx="1.5"/><rect x="3" y="14" width="18" height="6" rx="1.5"/><path d="M7 7h.01M7 17h.01"/>'],
        'shield' => ['label' => 'Shield', 'paths' => '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/>'],
        'shield-check' => ['label' => 'Shield with tick', 'paths' => '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="M9 12l2 2 4-4"/>'],
        'sparkle' => ['label' => 'Sparkle', 'paths' => '<path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z"/><path d="M19 17l.7 1.8L21.5 19.5l-1.8.7L19 22l-.7-1.8-1.8-.7 1.8-.7z"/>'],
        'split-box' => ['label' => 'Split box', 'paths' => '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M4 12h16"/>'],
        'trend-up' => ['label' => 'Trend up', 'paths' => '<path d="M3 17l6-6 4 4 8-8"/><path d="M14 7h7v7"/>'],
        'wallet' => ['label' => 'Wallet / card', 'paths' => '<rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M7 15h3"/>'],
    ];

    /**
     * @return array<string, string> key => label
     */
    public static function options(): array
    {
        $options = [];
        foreach ((array) static::config()->get('icons') as $key => $icon) {
            $options[$key] = $icon['label'] ?? $key;
        }
        asort($options);

        return $options;
    }

    public static function svg(string $key): string
    {
        $icons = (array) static::config()->get('icons');
        if ($key === '' || empty($icons[$key]['paths'])) {
            return '';
        }

        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"'
            . ' stroke-linecap="round" stroke-linejoin="round" focusable="false">'
            . $icons[$key]['paths']
            . '</svg>';
    }
}
