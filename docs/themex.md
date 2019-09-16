# ThemeX and Components

ThemeX and Components are used to rendering components, e.g. you do not need to write `<button class="xxx yyy zzz" id="x" ...>xxxx</button>`, because you can define the component and then just use it using {component x}y{/component} or {singleComponent x} macro. Components are located in `/app/themex/` and extends Component.php or SingleComponent.php. The name syntax is Component{NAME}.php, e.g. ComponentInput.php. The single component mean, that you do not need to write anything between tags, so input is SingleComponent, on the other hand, element like button, is Component, because you need to write between the tags `<button></button>`.

Example Component:

```php
<?php
class ComponentButton extends Component {
    private $args;

    public function __construct($args) {
        $this->args = $args;
    }

    public function componentStart(): string {
        return "<button class='".join(" ", $this->args["class"])."' id='".$this->args["id"]."'>";
    }

    public function componentEnd(): string {
        return "</button>";
    }
}

//then you use it like this
{component button, class => 'class1 class2', id => clickMe}Click me!{/button}
```



Example SingleComponent:

```php
<?php
class ComponentImage extends SingleComponent {
    private $args;

    public function __construct($args) {
        $this->args = $args;
    }

    public function component(): string {
        return "<img class='".join(" ", $this->args["class"])."' id='".$this->args["id"]."' src='".$this->args["src"]."' />";
    }
}

//then you use it like this
{singleComponent image, class => 'class1, class2, classxd', id => test, src => https://picsum.photos/id/413/536/354}
```



### Create string from $args

You can use function createStringFromArgs(), which will generate the string of all things you have set:

```php+HTML
<?php
class ComponentInput extends SingleComponent {
    private $args;

    public function __construct($args) {
        $this->args = $args;
    }

    public function component(): string {
        return "<input ".$this->createStringFromArgs($this->args).">";
    }
}

//usage
?>
{component input, name => info, type => text, default => 18 yo, placeholder => Type info bout you, html => 'class=\"input\"'}

//generates
<input name="info" type="text" placeholder="Type info bout you" class="input">
```

