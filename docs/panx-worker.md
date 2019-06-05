# panx-worker

panx-worker is PHP script that allows you to create documentation very easily, see [Creating documentation using panx framework](https://panx.eu/docs/creating-documentation)

panx-worker is primary for creating documentation, but have more function (and a lot of function will be added in the future).

List of all commands:

```bash
php panx-worker create doc [FOLDER] # [FOLDER] is folder containing your documentation, default: ./docs/
php panx-worker install [VERSION] # Downloads panx-framework, specify [VERSION] if you need different version from latest stable.
php panx-worker install {VERSION} clean # Downloads panx-framework without useless files, like example routes, templates, panx documentation etc.
php panx-worker config # Prompts you to enter data about your project and create .config from it.
php panx-worker update [VERSION] # Updates your project to new version, specify [VERSION] if you need different version from latest stable.
php panx-worker create post {PATH} # Generates template file from markdown file from provided {PATH}.
php panx-worker create version # Create version from your project.
php panx-worker info # Generate info.json file containing information about files in your project, used in updates to determine if the file was modified or not, do not use this command unless you know what you doing.
```

