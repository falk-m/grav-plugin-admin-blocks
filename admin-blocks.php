<?php

namespace Grav\Plugin;

use Grav\Common\Page\Interfaces\PageInterface;
use \Grav\Common\Plugin;

use RocketTheme\Toolbox\Event\Event;

class AdminBlocksPlugin extends Plugin
{

    const PLUGIN_NAME = 'admin-blocks';

    public static function getSubscribedEvents(): array
    {
        return [
            'onAdminTwigTemplatePaths' => ['onAdminTwigTemplatePaths', 0],
            'onAssetsInitialized' => ['onAssetsInitialized', 0],
            'onAdminCompilePresetSCSS' => ['onAdminCompilePresetSCSS', 0],
            'onPluginsInitialized' => [['onPluginsInitialized', 0]],
            'onTNTSearchIndex' => ['onTNTSearchIndex', 0]
        ];
    }

    public function onAdminTwigTemplatePaths($event): void
    {
        $paths = $event['paths'];

        $paths[] = __DIR__ . '/templates/admin';
        $event['paths'] = $paths;
    }

    public function onAssetsInitialized()
    {
        if ($this->isAdmin()) {
            $this->grav['assets']->addJs('plugins://' . self::PLUGIN_NAME . '/assets/admin.js', ['group' => 'bottom', 'loading' => 'defer']);
        }
    }

    /**
     * Get list of form field types specified in this plugin. Only special types needs to be listed.
     *
     * @return array
     */
    public function getFormFieldTypes()
    {
        return [
            'blocks' => [
                'array' => true
            ]
        ];
    }

    public function onAdminCompilePresetSCSS(Event $event): void
    {
        $event['scss']->add($this->grav['locator']->findResource('plugins://' . self::PLUGIN_NAME . '/scss/admin.scss'));
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function onPluginsInitialized(): void
    {
        $use_demo = $this->config->get('plugins.' . self::PLUGIN_NAME . '.use_demo');

        if ($this->isAdmin()) {
            /** @var UserInterface|null $user */
            $user = $this->grav['user'] ?? null;

            if (null === $user || !$user->authorize('login', 'admin')) {
                return;
            }

            if ($use_demo) {
                $this->enable([
                    'onGetPageTemplates' =>
                    ['registerDemoPageTemplates', 0]
                ]);
            }
        } else {
            if ($use_demo) {
                $this->enable([
                    'onTwigTemplatePaths' => [
                        ['registerDemoTemplatePaths', 0]
                    ]
                ]);
            }
        }
    }

    /**
     *
     * @return void
     */
    public function registerDemoTemplatePaths(): void
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates/page';
    }

    /**
     * @param Event $event
     * @return void
     */
    public function registerDemoPageTemplates(Event $event): void
    {
        /** @var Types $types */
        $types = $event->types;
        $types->register('blocks', 'plugins://' . self::PLUGIN_NAME . '/blueprints/pages/blocks.yaml');
    }

    public function onTNTSearchIndex(Event $e)
    {
        $fields = $e['fields'];

        /** @var PageInterface */
        $page = $e['page'];

        if (isset($page->header()->blocks)) {
            $fields->blocks = json_encode($page->header()->blocks);
        }
    }
}
