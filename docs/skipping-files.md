# Skipping files

While you run command `php panx-worker update [VERSION]` or `php panx-worker install [VERSION] clean`, the panx-worker will skip files that are included in `./update.skip`, that means, if you will update your project, this files will not be overwritten or if you are installing clean version, this files will not be included in your project.

The syntax is easy. To skip single file, you write to `update.skip` on new line following:

```
...
README.md
example/test.txt
...
```

Example above will skip `./README.md` and `/example/test.txt`.

To skip whole directory, you write to `update.skip` on new line following:

```
...
routes/
public/download/
...
```

Example above will skip `/routes/` and `public/download/` folders and all files and subdirectories in that folder. Keep in mind that the folder need to end with `/`.

To add sub-directory of directory that you skip, you write `!` before its name:

```
...
template/
!template/default/
...
```

This will skip `/template/` folder and all sub-directories and files, but it will include `/template/default/` and all sub-directories and files inside.

To add a single file in directory that you skip, you write `!` before its name:

```
public/
!public/index.php
```

This will skip `/public/` folder and all sub-directories and files, but it will include `/public/index.php` file.