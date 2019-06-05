# Writing changelog

If you want to work on panx framework or create own custom version, you should know the syntax of changelog.

Changelog is file located at `./changelog`.

Changelog parse will split the file by lines, and check if that lines starting with `?`, `!` or `#`.

So if you write in changelog this:

```
...
?Example usage of question mark.
...
```

Then the line will be with blue text.

If you write in changelog this:

```
...
!Example usage of exclamation mark.
...
```

Then the line will be with red text.

And if you write in changelog this:

```
...
#Example usage of hashtag (comment).
...
```

Then the line will not be printed.