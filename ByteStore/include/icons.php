<?php
declare(strict_types=1);

function gs_icon(string $name, string $class = 'icon', bool $decorative = true): string
{
    $icons = [
        'gamepad' => '<path d="M7 12h10a4 4 0 0 1 3.9 4.8l-.5 2.2a2 2 0 0 1-3.2 1.1L14.5 18h-5L6.8 20.1a2 2 0 0 1-3.2-1.1l-.5-2.2A4 4 0 0 1 7 12Z"/><path d="M8 15v4M6 17h4M16.5 16.5h.01M18.5 18.5h.01" stroke-linecap="round" stroke-linejoin="round"/>',
        'user-plus' => '<path d="M15 19a5 5 0 0 0-10 0"/><circle cx="10" cy="8" r="4"/><path d="M19 8v6M16 11h6" stroke-linecap="round"/>',
        'cart-shopping' => '<circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M3 4h2l2.3 10.2A2 2 0 0 0 9.2 16H18a2 2 0 0 0 2-1.6L21 8H7" stroke-linecap="round" stroke-linejoin="round"/>',
        'right-to-bracket' => '<path d="M10 17l5-5-5-5" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12H3" stroke-linecap="round"/><path d="M12 4h6a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-6" stroke-linecap="round"/>',
        'right-from-bracket' => '<path d="M14 17l5-5-5-5" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 12H7" stroke-linecap="round"/><path d="M12 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6" stroke-linecap="round"/>',
        'circle-question' => '<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 1 1 4.3 1.7c-.8.8-1.8 1.3-1.8 2.8" stroke-linecap="round"/><circle cx="12" cy="17" r=".8" fill="currentColor" stroke="none"/>',
        'house' => '<path d="M3 11.5 12 4l9 7.5"/><path d="M5 10.5V20h14v-9.5" stroke-linecap="round" stroke-linejoin="round"/>',
        'circle-user' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="10" r="3"/><path d="M7.5 18a5.5 5.5 0 0 1 9 0" stroke-linecap="round"/>',
        'facebook-f' => '<path d="M14 8h2V4h-2a4 4 0 0 0-4 4v2H7v4h3v6h4v-6h3l1-4h-4V8a1 1 0 0 1 1-1Z" stroke-linecap="round" stroke-linejoin="round"/>',
        'x-twitter' => '<path d="M4 4l7.2 8.8L4.5 20H7l5.3-6.2L17 20h3L12.6 11.2 19 4h-2.5l-4.2 4.9L8 4Z" stroke-linecap="round" stroke-linejoin="round"/>',
        'youtube' => '<path d="M21 12s0-3-1-4.5c-.6-.8-1.2-1-2-1.1C16.2 6 12 6 12 6s-4.2 0-6 .4c-.8.1-1.4.3-2 1.1C3 9 3 12 3 12s0 3 1 4.5c.6.8 1.2 1 2 1.1 1.8.4 6 .4 6 .4s4.2 0 6-.4c.8-.1 1.4-.3 2-1.1 1-1.5 1-4.5 1-4.5Z"/><path d="m10 9 5 3-5 3Z" fill="currentColor" stroke="none"/>',
        'heart' => '<path d="m12 20-1.4-1.3C5.4 14 2 10.9 2 7.1 2 4.3 4.2 2 7 2c1.6 0 3.1.8 4 2.1C11.9 2.8 13.4 2 15 2c2.8 0 5 2.3 5 5.1 0 3.8-3.4 6.9-8.6 11.6Z"/>',
        'circle-exclamation' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5" stroke-linecap="round"/><circle cx="12" cy="16.5" r=".8" fill="currentColor" stroke="none"/>',
        'id-card' => '<rect x="3" y="6" width="18" height="12" rx="2"/><circle cx="8" cy="12" r="2"/><path d="M13 10h5M13 14h5" stroke-linecap="round"/>',
        'at' => '<circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/><path d="M15 12v1a2 2 0 0 0 4 0v-1a7 7 0 1 0-2 4.9" stroke-linecap="round" stroke-linejoin="round"/>',
        'envelope' => '<rect x="3" y="6" width="18" height="12" rx="2"/><path d="m4 8 8 6 8-6" stroke-linecap="round" stroke-linejoin="round"/>',
        'lock' => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 1 1 8 0v3" stroke-linecap="round"/><circle cx="12" cy="16" r="1" fill="currentColor" stroke="none"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M5 20a7 7 0 0 1 14 0" stroke-linecap="round"/>',
        'circle-check' => '<circle cx="12" cy="12" r="9"/><path d="m8.5 12 2.3 2.3L15.8 9.3" stroke-linecap="round" stroke-linejoin="round"/>',
        'circle-info' => '<circle cx="12" cy="12" r="9"/><path d="M12 10v5" stroke-linecap="round"/><circle cx="12" cy="7.5" r=".8" fill="currentColor" stroke="none"/>',
    ];

    $paths = $icons[$name] ?? '';
    if ($paths === '') {
        return '';
    }

    $ariaHidden = $decorative ? ' aria-hidden="true"' : '';

    return '<svg class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"' . $ariaHidden . '>' . $paths . '</svg>';
}
