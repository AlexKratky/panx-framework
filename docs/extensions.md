# Extensions

Extensions can extend the panx-framework with new features, for example the PostManager. PostManager is one of official extension. PostManager includes templates files, css, classes etc. If you are asking, why PostManager is not the part of panx-framework by default, the answer is, that not every user will use it, so if you want to use it, you need install it additionally.

## Installing extensions

Extensions can be installed using panx-framework:

```
php panx-worker extension install {EXTENSION}
```

* {EXTENSION} can be URL or file path. If it is URL, the file will be downloaded and after installed, if it is path, the file will be just installed.

The extensions are ZIP files, so it will extract the zip files into panx-framework. Be carefully, that means some of your files can be overwritten, you should backup you project before installing extension.

## List all available extensions

```
php panx-worker extension
```

This command above will display all available extensions.

## Working with extensions

You can work with extension using panx-framework in this way:

```
php panx-worker extension {NAME} [parameters]
```

Where {NAME} is the name of extension, e.g. PostManager.

## Creating own extension

Creating own extension is easy, just write the extension you want, delete all files that are contained in panx-framework by default and run command:

```
php panx-worker info extension
```

After this, just ZIP all files and voilà, you have created extension.

 ## Uninstall  extensions

To uninstall extension, execute following command

```
php panx-worker extension uninstall {NAME} 
```

Where {NAME} is the name of extension, e.g. PostManager.

You should backup your project before executing the command.