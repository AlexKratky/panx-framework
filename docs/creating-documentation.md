# Creating documentation using panx framework

Creation of documentation using panx framework is quite easy. All you need to do is copy your markdown files of your documentation to some folder on webserver (e.g. `/docs/`). Next step is running following command:

`php panx-worker create doc /directory/`

Where you will replace `/directory/` with your directory containing your documentation. If your directory is called `/docs/` , you do not need to specify the folder and can run just `php panx-worker create doc`.

This command will setup routes, generate templates and set up everything else that is necessary. 

The script will prompt you to order the pages of documentation or you can write default to use alphabetical. Also, you can use sorting file (See 'sorting file' section).

The script will ask you if you want keep your current `home.php` file or generate new one. If you answer yes, script will save route: `Route::set('/', 'home.php');` and keep content of `home.php`, otherwise it will generate new content of `home.php` and set new route.

The script will prompt you, where should be the `/docs/` request redirected, you can leave it empty or enter page. For example, if you have page with ID `intro`, you will write `intro`. This will redirect `/docs/` to `/docs/intro/`.

The last thing is if you want use dark theme or light theme in documentation. You should always use the dark one.

### Generating documentation to custom folder

`php panx-worker create doc {SOURCE} {FOLDER}`

For example: `php panx-worker create doc /docs/v1/ /v1/` will generate documentation from /docs/v1/ folder  to /v1/ folder. Always enter '/' on start and end of path

### sorting file

The sorting file is located in documentation folder and contains the sorting of files. So everytime when you generate the documentation, you do not need to enter sorting. The syntax is easy -  just the file name without extension and enter, e.g.:

```
intro
getting-started
routes
panx-worker
creating-documentation
custom-functions
cache
logs
posts
skipping-files
changelog
```

