<?php

namespace App\Helpers;

class MenuManager
{
    protected $menus = [];

    public function addMenu(string $key, string $label, string $icon, array $childrens = [], string $permissions = null)
    {
        $this->menus[$key] = [
            'label' => __($label),
            'permissions' => $permissions,
            'icon' => $icon,
            'childrens' => $childrens
        ];

        return $this;
    }

    public function addChild(string $menuKey, string $key, string $label, string $route, string $icon = null, string $permissions = null)
    {
        if (isset($this->menus[$menuKey])) {
            $this->menus[$menuKey]['childrens'][$key] = [
                'label' => __($label),
                'route' => $route,
                'icon' => $icon,
                'permissions' => $permissions,
            ];
        }

        return $this;
    }

    public function addChilds(string $menuKey, array $childrens)
    {
        if (isset($this->menus[$menuKey])) {
            foreach ($childrens as $child) {
                $this->menus[$menuKey]['childrens'][$child['key']] = [
                    'label' => __($child['label']),
                    'route' => $child['route'],
                    'icon' => $child['icon'] ?? null,
                    'permissions' => $child['permissions'] ?? null,
                ];
            }
        }

        return $this;
    }

    public function getMenus()
    {
        return $this->menus;
    }
}