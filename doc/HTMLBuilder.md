# `axy/html-build`

Some simple helpers for build HTML elements.
All of them assembled as static methods of the class `axy\html\build\HTMLBuilder`.  
It don't validate tags and attributes.
Just builds code by input data.

#### `escape(string $text): string`

Escape HTML-code in a string.
As `htmlspecialchars()`.

#### `style(array $styles [, bool $escape = true]): string`

Builds value for `style` attribute from an array with css-properties.

```php
$styles = [
    'color' => 'red',
    'border' => '1px solid black',
    'font-weight' => null,
];
echo HTMLBuilder::style($styles);
```

Result:

```
color:red;border:1px solid black
```

`NULL`-values don't output, all other convert to strings.

The second argument is `TRUE` by default - the result string will be escaped and it can just insert to HTML. 

#### `attributes(array $attrs[, string $prefix = ' ']): string`

Takes an array with describe of attributes of some tag.
Returns these as single string:


```php
$attrs = [
    'id' => 'my-id',
    'class' => ['one', 'two'],
    'style' => [
        'color' => 'green',
    ],
];

echo '<div'.HTMLBuilder::attributes($attrs).'>';
```

Result:

```html
<div id="my-id" class="one two" style="color:green">
```

Is the array `$attrs` keys are name of attributes.
Values can be follow types:

* String - output as is (with escape)
* Number - output as string
* Regular array (contains 0-index) - a list of string these out joined with space. 
For example for `class` attribute - list of classes. 
* Assoc array - as `style` (see above)
* boolean - flag-attribute
    * `TRUE` - enabled, the name used as value - `selected="selected"`
    * `FALSE` - disabled - attribute don't show
* NULL - attribute don't show (unlike empty string and 0).

If the result string is not empty the attribute `$prefix` will be added to the start.
Space by default - attribute string can be inserted into a tag without excess spaces.

#### `tag(string $name [, array|string $attrs = null, bool $single = false]): string`

Returns an open tag.

```php
echo HTMLBuilder::tag('br', null, true); // <br />
```

* `name` - the name of a tag
* `attrs` - tag attributes
    * array - see `attributes()` above
    * string - as is
    * NULL - no attributes
* `single` - it is single tag that must closed with `/>`

#### `element(string $name[, array|string $attrs = null, array|string $content = null]): string`

```php
echo HTMLBuilder::element('p', ['class' => 'x'], 'text'); // <p class="x">text</p>
```

Output HTML element: open and close tags and content between these.

* `$name` - the name of outer tag
* `$attrs` - attributes (see `tag()` above for the format)
* `$content` - content of the element
    * `NULL` - this is single tag
    * String - output with escape (empty string don't make tag as single, unlike `NULL`)
    * Number or other scalar - convert to string
    * Array
        * If key `html` is exist - output it without escape
        * If key `name` is exist - it is nested element - processed by `element()`.
        Keys `attrs` and `single` is optional. 
    * In other case - recursive travers of each element. All results join without separator.

#### `select(string|array|null $attrs, array $options, $current = null [, string $none = null]): string`

Builds `<select>` element.

```php
$name = 'month';
$current = (int)date('m');
$options = [
    1 => 'January',
    2 => 'February',
    // ...
];
echo HTMLBuilder::select(['name' => $name], $options, $current, 'Select, please');
```

* `$attrs` - the open tag attributes (for example, `name` for a form)
* `$options` - variants array (see format below)
* `$current` - key of the default selected element (`NULL` - no selected)
* `$none` - When `$current=NULL` and `$none` is set then create new option element
with the value="" and the caption="$none". It element push to the start of variants list and select. 

Options formats:

* `key => value`: `[1 => 'January', 2 => 'February']
* List of items: `[[1, 'January'], [2, 'February']]`
* List of dictionaries: `[['key' => 1, 'label' => 'January'], ['key' => 2, 'label' => 'February']]`
