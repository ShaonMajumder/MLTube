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


    public function addChild( string $menuKey, string $key, string $label, string $route, string $icon = null, string $permissions = null, array $route_parameters = [], array $middleware = [])
    {
        if (isset($this->menus[$menuKey])) {
            $defaultChildAttributes = [
                'label' => '',
                'route' => '',
                'route_parameters' => [],
                'icon' => null,
                'permissions' => null,
                'middleware' => []
            ];

            $attributes = array_merge($defaultChildAttributes, [
                'label' => __($label),
                'route' => $route,
                'route_parameters' => $route_parameters,
                'permissions' => $permissions,
                'icon' => $icon,
                'middleware' => $middleware,
            ]);
    
            $this->menus[$menuKey]['childrens'][$key] = $attributes;
        }
    
        return $this;
    }

    public function addChilds(string $menuKey, array $childrens)
    {
        if (isset($this->menus[$menuKey])) {
            $defaultChildAttributes = [
                'label' => '',
                'route' => '',
                'route_parameters' => [],
                'icon' => null,
                'permissions' => null,
                'middleware' => []
            ];

            foreach ($childrens as $child) {
                $childAttributes = array_merge($defaultChildAttributes, $child);

                $this->menus[$menuKey]['childrens'][$childAttributes['key']] = [
                    'label' => __($childAttributes['label']),
                    'route' => $childAttributes['route'],
                    'route_parameters' => $childAttributes['route_parameters'],
                    'icon' => $childAttributes['icon'],
                    'permissions' => $childAttributes['permissions'],
                    'middleware' => $childAttributes['middleware'],
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