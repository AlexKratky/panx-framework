# Validator

Validator validates user inputs. You can do it by using predefined rule, creating own rule or by regex. Every rule returns true if the input is valid, false otherwise.

The predefined rules:

* validateMail(string $input): bool
* validateUsername(string $input): bool
* validatePassword(string $input): bool
* validateCheckBox(string $input): bool

### Creating own rule

Just add your function to ValidatorFunctions.php file (Located in `/app/classes/`). Every method need to be static and return boolean. Example:

```php
public static function isAdult(int $age): bool {
    if ($age < 18) {
        return false;
    }
    return true;
}
```

### Using regex

Just call function validate:

```php
Validator::validate($input, Validator::RULE_CUSTOM, $min_length, $max_length, $regex);
```



