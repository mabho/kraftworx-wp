<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class Section extends Block
{
    /**
     * The block name.
     *
     * @var string
     */
    public $name = 'Section';

    /**
     * The block description.
     *
     * @var string
     */
    public $description = 'A reusable layout section with container, column, and background options.';

    /**
     * The block category.
     *
     * @var string
     */
    public $category = 'kraftworx';

    /**
     * The block icon.
     *
     * @var string|array
     */
    public $icon = 'layout';

    /**
     * The block keywords.
     *
     * @var array
     */
    public $keywords = ['layout', 'section', 'container'];

    /**
     * The block API version.
     *
     * @var int
     */
    public $apiVersion = 3;

    /**
     * The ACF block version.
     *
     * @var int
     */
    public $blockVersion = 3;

    /**
     * The supported block features.
     *
     * @var array
     */
    public $supports = [
        'multiple' => true,
    ];

    /**
     * The block template.
     *
     * @var array
     */
    public $template = [
        'core/paragraph' => ['placeholder' => 'Add content...'],
    ];

    /**
     * Data to be passed to the block before rendering.
     */
    public function with(): array
    {
        return [
            'container_type' => get_field('container_type') ?: 'boxed',
            'columns' => get_field('columns') ?: '100',
            'background_color' => get_field('background_color') ?: 'light',
        ];
    }

    /**
     * The block field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('section');

        $fields
            ->addSelect('container_type', [
                'label' => 'Container',
                'choices' => [
                    'boxed' => 'Boxed',
                    'full' => 'Full Width',
                    'edge' => 'Edge to Edge',
                ],
                'default_value' => 'boxed',
            ])
            ->addSelect('columns', [
                'label' => 'Grid layout',
                'choices' => [
                    '100' => '100',
                    '50-50' => '50 / 50',
                    '33-33-33' => '33 / 33 / 33',
                ],
                'default_value' => '100',
            ])
            ->addSelect('background_color', [
                'label' => 'Background color',
                'choices' => [
                    'light' => 'Light',
                    'dark' => 'Dark',
                    'primary' => 'Primary',
                    'secondary' => 'Secondary',
                    'accent' => 'Accent',
                ],
                'default_value' => 'light',
            ]);

        return $fields->build();
    }
}
