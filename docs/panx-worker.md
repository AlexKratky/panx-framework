# panx-worker

panx-worker is PHP script that allows you to create documentation very easily, see [Creating documentation using panx framework](https://panx.eu/docs/creating-documentation)

panx-worker is primary for creating documentation, but have more function (and a lot of function will be added in the future).

List of all commands:

```bash
php panx-worker create doc [FOLDER] # [FOLDER] is folder containing your documentation, default: ./docs/
php panx-worker install [VERSION] # Downloads panx-framework, specify [VERSION] if you need different version from latest stable.
php panx-worker install {VERSION} clean # Downloads panx-framework without useless files, like example routes, templates, panx documentation etc., also can be called without version, e.g. php panx-worker install clean
php panx-worker config # Prompts you to enter data about your project and create .config from it.
php panx-worker update [VERSION] # Updates your project to new version, specify [VERSION] if you need different version from latest stable.
php panx-worker create post {PATH} # Generates template file from markdown file from provided {PATH}.
php panx-worker create version # Create version from your project.
php panx-worker info # Generate info.json file containing information about files in your project, used in updates to determine if the file was modified or not, do not use this command unless you know what you doing.
php panx-worker serve
php panx-worker route-list # Output route list table
php panx-worker extension # List all available extensions
php panx-worker extension install {EXTENSION} # Install extension. {EXTENSION} can be url or file path. If it is URL, the file will be downloaded and after installed, if it is path, the file will be just installed
php panx-worker extension uninstall {EXTENSION} # Uninstall specified extension
php panx-worker info extension # Creates info file about extension
php panx-worker create auth # Creates AUTH table. Command need connection to db specified in .config
php panx-worker create api # Creates API table. Command need connection to db specified in .config
php panx-worker create model [NAME] # Creates model with specified name
php panx-worker create controller [NAME] # Creates controller with specified name
php panx-worker version # Output the current version of panx-worker and version hash
php panx-worker clear [cache/old] # cache - Delete all cache files; old - Delete just unused cache files
php panx-worker setup # Creates necessary folders and change chmod to 0777
php panx-worker test {TEST} # run a test
php panx-worker migrate # Migrates a db
php panx-worker version # Prints the current version of panx-worker
php panx-worker create debug # Setup DB to debug (tables)
php panx-worker create middleware {NAME} # creates middleware
php panx-worker create migration {NAME} # create migration
```







