# menu-builder-bundle
A simple, standalone Menu Builder for Symfony 2 Applications

## Upcoming / TODO

- TODO: Introduce cache.

## Minor TODO
- Put docs inside of this bundle, this is the Core and is for developers only. The Client one is meant to be your base client, if that does not satisfy you, build your own with your own user experience
- TODO: Static config with system routes - upon import, always make all of these system

## Usage

Having properly configured uglifycss and uglifyjs is a requirement for production.

Add this bundle to your AppKernel.php `new \Forci\Bundle\MenuBuilder\ForciMenuBuilderBundle()`
Add wucdbm's QuickUIBundle to your AppKernel.php `new \Wucdbm\Bundle\QuickUIBundle\WucdbmQuickUIBundle()`

Execute `app/console doctrine:schema:update --dump-sql` and after verifying what is being executed, execute again with --force.
Alternatively, use doctrine migrations via the DoctrineMigrations bundle.

Execute `app/console forci_menu_builder:import_routes` to import your current routes from your symfony application into the tables created by the bundle.

Once this has been done, you can start using the bundle. Simply register it in your routing.yml like so:

```
wucdbm_builder:
    resource: "@ForciMenuBuilderBundle/Resources/config/routing.yml"
    prefix: /admin/builder
```

Assuming that /admin is protected by a firewall, the builder should be secure and inaccessible to random people.

You can create a link to the builder using `{{ path('forci_menu_builder_dashboard') }}`, or embed it into your admin UI via an iframe like so `<iframe src="{{ path('forci_menu_builder_dashboard') }}" style="border: 0; width: 100%; height: 100%;"></iframe>`

The User Interface is pretty anemic as this bundle only implements the core functionality and the administrative (for developers) functionality. 
If you want to let users (non-developers) manipulate menus, check out the `ForciMenuBuilderClientBundle()`
Once you have created a menu, you can access it in your application by calling the `getMenu` 
twig function, which will return `Forci\Bundle\MenuBuilder\Entity\Menu` or `null`. 
A menu contains `Forci\Bundle\MenuBuilder\Entity\MenuItem`s.
Menu items can be nested, ie they have a parent and children. 
A good idea when listing the top-level menu is to only list items whose parent is null:

```
{# New: You can use the menuTopLevelItems filter to get all top-level items: #}
{% for item in getMenu(1)|menuTopLevelItems %}
```


```
{% if getMenu(1) %} {# You could also use any constant with the constant() function or any other way of referencing the menu ID #}
    {% for item in getMenu(1).items if item.parent is null %}
        {# You can recursively include your templates to list the sub-menus #}
        {% include '@Some/location/template.html.twig' with {items: item.children} %}
    {% endfor %}
{% endif %}
```

```
{% if getMenu(1) %}
    {% for item in getMenu(1).items if item.parent is null %}
        <li>
            <a href="{{ path('admin_menu_view', {menuId: item.menu.id, itemId: item.id}) }}">
                {{ item.name }}
            </a>
        </li>
    {% endfor %}
{% endif %}
```

Printing a link for a menu is done via the `menuItemPath` twig filter/function, like so:

```
<a href="{{ item|menuItemPath }}">
    {{ item.name }}
</a>
```

Or for absolute links

```
<a href="{{ item|menuItemUrl }}">
    {{ item.name }}
</a>
```

You can also use the second (optional) parameter for `menuItemUrl` and set the type of address (one of the `Symfony\Component\Routing\Generator\UrlGeneratorInterface` constants)

## Dynamic Default Parameters

If you want to have a dynamic default parameter for some of your routes, for instance, routes with a dynamic locale:

In config.yml:
```
framework:
    default_locale:  en
```

In routing.yml:
```
some_resource:
    resource: "@SomeBundle/Resources/config/routing.yml"
    prefix: /{_locale}
    schemes:  [https]
    requirements:
        _locale: "en|de|ru"
    defaults:
        _locale: %kernel.default_locale%
```
Generally, you do NOT need the `defaults: {_locale: %kernel.default_locale%}` part because you already have the default locale configured in your framework bundle config, but this will only work for `_locale`
However, with this approach the default value for the `_locale` route parameter will be available to the menu builder when importing routes.
When building a link, you may choose to leave the field blank if there is a default parameter. 
This will allow you to change the default value for that parameter at a later point, WITHOUT having to update menu items. 
When routes are updated during the deployment of your application, the default value for that parameter of your route will also be updated.
The current value of the default parameter will always be saved upon menu item edit anyway, but the menu builder will always try to use the current default value for that route parameter.
If the default value for any parameter has been removed, it will fallback to the route default parameter as has been on the last menu item save.

## Not to be confused with symfony internal parameters such as `_locale` that may have another default value in the current context
An example would be a site with default locale of "en", but the user is browsing the "fr" version. You want your links to always point to the current locale and not to a pre-selected one or the default for your site.
Which is a feature that has not yet been developed, but this would allow you to completely ignore a parameter and not provide it if it already exists in the router context? To be researched.
See TODOs for more information on this.