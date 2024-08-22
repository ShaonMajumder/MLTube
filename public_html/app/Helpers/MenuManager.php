<?php

namespace App\Helpers;

class MenuManager
{
    protected $menus = [];

    // public function addMenu(string $key, string $route, string $permissions = null, string $label, string $icon, array $childrens = [])
    // {
    //     $this->menus[$key] = [
    //         'label' => __($label),
    //         'route' => $route,
    //         'permissions' => $permissions,
    //         'icon' => $icon,
    //         'childrens' => $childrens
    //     ];

    //     return $this;
    // }
    public function addMenu(string $menuKey, array $attributes)
    {
        $defaults = [
            'route' => '',
            'route_parameters' => [],
            'permissions' => null,
            'label' => '',
            'icon' => '',
            'childrens' => [],
            'middleware' => []
        ];

        $attributes = array_merge($defaults, $attributes);

        $this->menus[$menuKey] = [
            'label' => __($attributes['label']),
            'route' => $attributes['route'],
            'route_parameters' => $attributes['route_parameters'],
            'permissions' => $attributes['permissions'],
            'middleware' => $attributes['middleware'],
            'icon' => $attributes['icon'],
            'childrens' => $attributes['childrens'],
        ];

        return $this;
    }


    public function addChild(string $menuKey, string $key, string $label, string $route, string $icon = null, string $permissions = null)
    {
        if (isset($this->menus[$menuKey])) {
            $this->menus[$menuKey]['childrens'][$key] = [
                'label' => __($label),
                'route' => $route,
                'permissions' => $permissions,
                'icon' => $icon,
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