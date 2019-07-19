# Template systems

panx-framework can support custom template systems, but by default, supports only [Latte](https://latte.nette.org/). The template files are specified in routes, but loader.php will not include them as classic php files (If you will use for example blade, you will need to edit loader.php, because if file ends with .php, that files will be just required using require()). The custom extensions (meaning other then .php) needs handler.



View [Handlers](https://panx.eu/docs/handlers) to see how to create custom handler.

