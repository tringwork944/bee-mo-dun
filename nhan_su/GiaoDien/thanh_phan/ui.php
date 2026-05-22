<?php
if (!function_exists('ns_ui_page_header')) {
    function ns_ui_page_header(string $title, string $subtitle = '', string $actionHtml = ''): void
    {
        echo '<div class="page-header d-print-none mb-3"><div class="row align-items-center">';
        echo '<div class="col"><h2 class="page-title">' . bao_mat_chuoi($title) . '</h2>';
        if ($subtitle !== '') echo '<div class="text-secondary">' . bao_mat_chuoi($subtitle) . '</div>';
        echo '</div>';
        if ($actionHtml !== '') echo '<div class="col-auto">' . $actionHtml . '</div>';
        echo '</div></div>';
    }
}

if (!function_exists('ns_ui_stat_card')) {
    function ns_ui_stat_card(string $label, string $value, string $tone = 'secondary'): string
    {
        return '<div class="card card-sm"><div class="card-body py-2"><div class="text-secondary">' . bao_mat_chuoi($label) . '</div><div class="h2 m-0 text-' . bao_mat_chuoi($tone) . '">' . bao_mat_chuoi($value) . '</div></div></div>';
    }
}

if (!function_exists('ns_ui_filter_bar_start')) {
    function ns_ui_filter_bar_start(): void
    {
        echo '<div class="card-body py-3 border-bottom"><form method="get" class="row g-2 align-items-end">';
    }
}

if (!function_exists('ns_ui_filter_bar_end')) {
    function ns_ui_filter_bar_end(): void
    {
        echo '</form></div>';
    }
}

if (!function_exists('ns_ui_status_badge')) {
    function ns_ui_status_badge(string $label, string $tone = 'secondary'): string
    {
        return '<span class="badge bg-' . bao_mat_chuoi($tone) . '-lt text-' . bao_mat_chuoi($tone) . '">' . bao_mat_chuoi($label) . '</span>';
    }
}

if (!function_exists('ns_ui_empty_state')) {
    function ns_ui_empty_state(string $title, string $subtitle = '', int $colspan = 1): void
    {
        echo '<tr><td colspan="' . $colspan . '" class="text-center py-3"><div class="empty m-0">';
        echo '<p class="empty-title mb-1">' . bao_mat_chuoi($title) . '</p>';
        if ($subtitle !== '') echo '<p class="empty-subtitle text-secondary mb-0">' . bao_mat_chuoi($subtitle) . '</p>';
        echo '</div></td></tr>';
    }
}

if (!function_exists('ns_ui_action_dropdown')) {
    function ns_ui_action_dropdown(array $items, string $label = 'Thao tac'): string
    {
        if ($items === []) return '<span class="text-secondary">-</span>';
        $html = '<div class="dropdown"><button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">' . bao_mat_chuoi($label) . '</button><div class="dropdown-menu dropdown-menu-end">';
        foreach ($items as $item) {
            if (!empty($item['html'])) {
                $html .= (string)$item['html'];
                continue;
            }
            $class = 'dropdown-item' . (!empty($item['danger']) ? ' text-danger' : '');
            $html .= '<a class="' . $class . '" href="' . bao_mat_chuoi((string)($item['href'] ?? '#')) . '">' . bao_mat_chuoi((string)($item['label'] ?? '')) . '</a>';
        }
        $html .= '</div></div>';
        return $html;
    }
}
