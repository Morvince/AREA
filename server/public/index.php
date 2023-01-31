<?php
    use App\Kernel;

    if (!isset($_SESSION)) {
        session_start();
    }

    require_once dirname(__DIR__).'/vendor/autoload_runtime.php';
    return function (array $context) {
        return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
    };
?>