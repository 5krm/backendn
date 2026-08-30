<?php

namespace App\Support;

class PromotionTemplateRegistry
{
    public static function all(): array
    {
        return [
            'modern' => [
                'label' => __('tutor.promotions.templates.modern'),
                'view' => 'components.promotions.templates.modern',
            ],
            'simple' => [
                'label' => __('tutor.promotions.templates.simple'),
                'view' => 'components.promotions.templates.simple',
            ],
            'gradient' => [
                'label' => __('tutor.promotions.templates.gradient'),
                'view' => 'components.promotions.templates.gradient',
            ],
        ];
    }

    public static function options(): array
    {
        return collect(static::all())
            ->mapWithKeys(fn (array $template, string $key) => [$key => $template['label']])
            ->all();
    }

    public static function view(?string $template): ?string
    {
        return static::all()[$template]['view'] ?? null;
    }

    public static function defaultTemplate(): string
    {
        return 'modern';
    }
}
