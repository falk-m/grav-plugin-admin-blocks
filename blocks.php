<?php

namespace Grav\Plugin;

use \Grav\Common\Plugin;

use RocketTheme\Toolbox\Event\Event;

class BlocksPlugin extends Plugin
{
    public static function getSubscribedEvents(): array
    {
        return [
            'onAdminTwigTemplatePaths' => ['onAdminTwigTemplatePaths', 0],
            'onAssetsInitialized' => ['onAssetsInitialized', 0],
            'onAdminCompilePresetSCSS' => ['onAdminCompilePresetSCSS', 0]

        ];
    }

    public function onAdminTwigTemplatePaths($event): void
    {
        $paths = $event['paths'];

        $paths[] = __DIR__ . '/admin-templates';
        $event['paths'] = $paths;
    }

    // Add assets to the Admin
    public function onAssetsInitialized()
    {
        if ($this->isAdmin()) {

            // add JS
            $this->grav['assets']->addJs('plugins://blocks/assets/admin.js', ['group' => 'bottom', 'loading' => 'defer']);
            $this->grav['assets']->addJs('plugins://blocks/assets/twind.js', ['group' => 'head']);
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
        $event['scss']->add($this->grav['locator']->findResource('plugins://blocks/scss/admin.scss'));
    }
}
