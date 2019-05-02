# Creating documentation using panx framework

Creation of documentation using panx framework is quite easy. All you need to do is copy your markdown files of your documentation to some folder on webserver (e.g. `/docs/`). Next step is running following command:

`php panx-worker create doc ./directory/`

Where you will replace `./directory/` with your directory containing your documentation. If your directory is called `/docs/` , you do not need to specify the folder and can run just `php panx-worker create doc`.

This command will setup routes, generate templates and set up everything else that is necessary. 

The script will ask you if you want keep your current `home.php` file or generate new one. If you answer yes, script will save route: `Route::set('/', 'home.php');` and keep content of `home.php`, otherwise it will generate new content of `home.php` and set new route.

