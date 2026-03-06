<?php

// Developed with the assistance of Claude Code (claude.ai)

namespace Grav\Plugin;

use Grav\Common\Plugin;

class ConfigOverridePlugin extends Plugin
{
    public static function getSubscribedEvents(): array
    {
        return [
            'onPageInitialized' => ['onPageInitialized', 0],
        ];
    }

    public function onPageInitialized(): void
    {
        $params = $_GET;

        // Multi-page mode: ?config_pages[route][key]=value
        if (isset($params['config_pages']) && is_array($params['config_pages'])) {
            foreach ($params['config_pages'] as $route => $keys) {
                if (!is_array($keys)) {
                    continue;
                }
                $route = '/' . ltrim((string) $route, '/');
                $page  = $this->grav['pages']->find($route);
                if ($page) {
                    $this->applyOverrides($page, $keys);
                }
            }
        }

        // Single-page mode: ?config_page[route][key]=value
        if (isset($params['config_page']) && is_array($params['config_page'])) {
            foreach ($params['config_page'] as $route => $keys) {
                if (!is_array($keys)) {
                    continue;
                }
                $route = '/' . ltrim((string) $route, '/');
                $page  = $this->grav['pages']->find($route);
                if ($page) {
                    $this->applyOverrides($page, $keys);
                }
            }
        }

        // Theme config mode: ?config_theme[key]=value
        if (isset($params['config_theme']) && is_array($params['config_theme'])) {
            $themeName = $this->config->get('theme_name', 'helios');
            foreach ($params['config_theme'] as $key => $value) {
                $key   = strip_tags((string) $key);
                $value = strip_tags((string) $value);
                $this->grav['config']->set('themes.' . $themeName . '.' . $key, $this->castValue($value));
            }
        }
    }

    private const BLOCKED_KEYS = ['redirect', 'access', 'template', 'process'];

    private function applyOverrides($page, array $keys): void
    {
        $header = (array) $page->header();

        foreach ($keys as $key => $value) {
            $key   = strip_tags((string) $key);
            $value = strip_tags((string) $value);

            $topLevelKey = explode('.', $key, 2)[0];
            if (in_array($topLevelKey, self::BLOCKED_KEYS, true)) {
                continue;
            }

            $this->setNestedValue($header, $key, $value);
        }

        $page->header((object) $header);
    }

    private function castValue(string $value)
    {
        if ($value === 'true') return true;
        if ($value === 'false') return false;
        if (is_numeric($value)) return $value + 0;
        return $value;
    }

    private function setNestedValue(array &$array, string $key, string $value): void
    {
        if (strpos($key, '.') === false) {
            $array[$key] = $value;
            return;
        }

        $parts = explode('.', $key, 2);

        if (!isset($array[$parts[0]]) || !is_array($array[$parts[0]])) {
            $array[$parts[0]] = [];
        }

        $this->setNestedValue($array[$parts[0]], $parts[1], $value);
    }
}
