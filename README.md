Yii2-menu module
=================

Module for create menu on you website.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist mix8872/yii2-menu
```

or add

```
"mix8872/yii2-menu": "~1.0"
```

to the `require` section of your `composer.json`.

Then you must run migration by running command:

yii migrate --migrationPath=@vendor/mix8872/yii2-menu/src/migrations

Configure
----------

To configure module please add following to the modules section of main config:

Backend:

```php
'modules' => [
    'menu' => [
        'class' => 'mix8872\menu\Module',
        'on menuAfterCreate' => static function ($e) {
            $model = $e->model;
            // ... do something
        },
        'on menuAfterDelete' => static function ($e) {
            $model = $e->model;
            // ... do something
        },
        'on menuAfterUpdate' => static function ($e) {
            $model = $e->model;
            // ... do something
        },
        'on menuAfterSort' => static function ($e) {
            $model = $e->model;
            // ... do something
        },
        'on menuAfterAddItem' => static function ($e) {
            $model = $e->model;
            // ... do something
        },
        'on menuAfterDeleteItem' => static function ($e) {
            $model = $e->model;
            // ... do something
        },
    ],
    // ... other modules definition
],
```

Usage
-----
After configuring go to the url `/menu/default/index` and press **Add** button,  
fill the name, code and description of your new menu; 

Now you can add menu items by pressing 'plus' green button in right side.  
Menu items can be two types:
- simple
- from model

#### Simple
Simple menu item is the ordinary item with url and title. That's all.

#### From model
That's 'magic' menu item which get menu items from ActiveRecord model  
and replaces itself with the received data.

This menu item has next not ordinary fields:
- **Url** - base url, unlike a 'Simple', it contains **base** and **dynamic** parts of url  
the dynamic part must be enclosed in curly brackets and must be the same as **Url attribute**  
for example: `/news/{code}`, here `/news/` - is a basic part and `{code}` - is a dynamic part  
then **Url attribute** must declared as `code` 
- **Model class** - here you should specify full model class name (of course with namespace)
- **Url attribute** - this is attribute name which will be used in the end part of url
- **Title attribute** - attribute from which the menu item title will be taken  
you can specify the name separated by a dot: `content.title`,  
then the value will be taken from the relative table, but you can set only one dot
- **Description attribute** - attribute from which the menu item description will be taken  
you can specify the name separated by a dot: `content.description`,  
then the value will be taken from the relative table, but you can set only one dot
- **Selection parameters** - there you can declare SQL-like parameters for selection of items from model  
for example: `WHERE status = 1 ORDER BY id DESC LIMIT 2`  
**!!! BE CAREFUL, IT IS EXPERIMENTAL FEATURE AND CAN BREAK YOUR APP !!!**


Next you may echo widget with its config.

The simplest case is:

```php

<?= \mix8872\menu\widgets\MenuWidget::widget([
        'code' => 'Tx1rNy'
    ])
?>
```

But widget can be configured in more detail. Next options is available:

```
/**
 * Append class to the ul element
 * @var string
 */
public $menuClassName = '';

/**
 * Append id to the 'ul' element
 * @var string
 */
public $menuId = '';

/**
 * Append class to the all 'li' elements which are not parents or descendants
 * @var string
 */
public $itemsClassName = '';

/**
 * Append class to the all 'a' elements in the parent elements
 * @var string
 */
public $linkClassName = '';

/**
 * Append class to the all 'a' elements in the descendant elements
 * @var string
 */
public $sublinkClassName = '';

/**
 * Append class to the all descendant 'ul' elements
 * @var string
 */
public $submenuClassName = '';

/**
 * Append class to the all descendant 'li' elements
 * @var string
 */
public $subitemsClassName = '';

/**
 * Show description
 * @var bool
 */
public $description = false;

/**
 * Menu code
 * @var
 */
public $code;

/**
 * Active 'li' element class name
 * @var string
 */
public $activeClassName = 'active';

/**
 * Append class to the all parent 'li' elements
 * @var string
 */
public $parentClassName = '';

/**
 * Menu type: classic (default) or buttons
 * @var string
 */
public $type = 'classic';

/**
 * Tag to wrap submenu 'ul' element
 * @var
 */
public $submenuWrapperTag;

/**
 * Append class to submenu wrapper (submenuWrapperTag must defined)
 * @var string
 */
public $submenuWrapperClassName = '';

/**
 * If false (default) then parents elements has no links and shown as 'span'
 * @var bool
 */
public $isParentActive = false;

/**
 * Template for item 'a' elements which are not parents
 * @var string
 */
public $itemTemplate = '{link}';

/**
 * Template for item 'a' elements which are parents
 * @var string
 */
public $parentTemplate = '{link}';

/**
 * Template for all item 'a' elements
 * @var string
 */
public $linkTemplate = '{link}';

/**
 * If true then current url will be excluded from menu, default false
 * @var bool
 */
public $exceptCurrent = false;

/**
 * Array of url's which must excluded from menu
 * @var array
 */
public $exceptItems = [];

```

Events
----------

On adding, updating and deletion files will generates next events:

- EVENT_AFTER_CREATE = 'menuAfterCreate' - fires after creation new menu
- EVENT_AFTER_DELETE = 'menuAfterDelete' - fires after deletion menu
- EVENT_AFTER_DELETE_ITEM = 'menuAfterDeleteItem' - fires after deletion menu item
- EVENT_AFTER_ADD_ITEM = 'menuAfterAddItem' - fires after add menu item
- EVENT_AFTER_SORT = 'menuAfterSort' - fires after sort menu items
- EVENT_AFTER_UPDATE = 'menuAfterUpdate' - fire after update menu,  
also fires when EVENT_AFTER_ADD_ITEM, EVENT_AFTER_DELETE_ITEM and EVENT_AFTER_SORT

You can intercept events in the module configuration as in the example above.
