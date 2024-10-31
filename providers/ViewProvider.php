<?php

namespace Providers;

class ViewProvider
{
    public static function show($template, $params): void
    {
        $template = './views/' . $template . '.php';

        require_once($template);
    }

    public static function showErrors($params, $field)
    {
        $template = './views/components/show_errors.php';

        require($template);
    }
}