<?php

declare(strict_types=1);

use Symfony\AI\Agent\Toolbox\Tool\Wikipedia;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\AiConfig;

return static function (ContainerConfigurator $container, AiConfig $ai): void {
    $services = $container->services()->defaults()->autowire()->autoconfigure();
    $services->set(Wikipedia::class);

    // --- Platforms ---
    $ai->platform()->gemini()->apiKey(value: "%env(GEMINI_API_KEY)%");

    // --- Agents ---
    $agent = $ai->agent(name: "wikipedia");
    $model = $agent->model([
        "name" => "gemini-2.5-flash",
        "options" => [
            "temperature" => 0.5,
        ],
    ]);
    $agent->tools([
        "enabled" => true,
        "services" => [
            Wikipedia::class
        ],
    ]);
    $model->prompt([
        "text" => "Please answer the users question based on Wikipedia and provide a link to the article.",
        "include_tools" => true,
    ]);
};
