# Forms

Forms created with panx-framework using FormX class are easily validatable. All forms should be located in `/app/forms/` and should extend the Form located. Example form:

```php
<?php
class LoginForm extends Form {
    protected $form;

    public function __construct($formName = "LoginForm", $dir = null) {
        $this->formName = $formName;
        $this->dir = $dir;
        $this->form = new FormX("POST", "/login/verify/");
        $this->form->add('input', 'username')
            ->placeholder("Type your username")
            ->type("text")
            ->html("class='input' autofocus")
            ->required(true)
            ->errorMsgEmpty("Prosím, zadejte uživatelské jméno")
            ->errorMsgNotValid("Zadané uživatelské jméno je neplatné - %s musí obsahovat minimálně 4 znaky.")
            ->validator("Validator", "validateUsername")
            ->validatorRegex("[A-Za-z]{4,}");
        $this->form->add('input', 'password')
            ->placeholder("Type your password")
            ->type("password")
            ->html("class='input'")
            ->required(true)
            ->errorMsgEmpty("Prosím, zadejte heslo")
            ->errorMsgNotValid("Zadané heslo je neplatné - %s musí obsahovat minimálně 6 znaků.")
            ->validator("Validator", "validatePassword")
            ->validatorRegex(".{6,}");
        $this->form->add('input', 'info')
            ->placeholder("Type info bout you")
            ->type("text")
            ->html("class='input'")
            ->default("18 yo");
        $this->form->add('input', 'files[]', 'files')
            ->placeholder("Select files")
            ->type("file")
            ->html("class='input' multiple")
            ->required(true)
            ->errorMsgEmpty("Prosím, zvolte soubor")
            ->errorMsgNotValid("Zvolené soubory nejsou validní - %s.")
            ->fileSize("100000000")
            ->fileCount("5")
            ->fileExtensions("png, jpg");
        $this->form->add('submit', 'submit')
            ->text('Login')
            ->id('submit')
            ->html('class="button"');
    }
    
}
```

Then the Latte template file (You should name that same as you form, in our case LoginForm.latte):

```html
<form method={$form->method}>
    {csrf}
    {input username}
    {input password}
    {input info}
    {input files[]}
    
    <button {$form->submit->html|noescape} id="{$form->submit->id}" name="submit">
        {$form->submit->text}
    </button>
</form>
{if $form->errorMsg}
    {$form->errorMsg}
{/if}
```

And that is! You have just created form, now you can use the form in other latte file using form macro (`{form LoginForm}`).

### Validating the form

The form validation is easy, just call the validate() function on the form, e.g.:

```php
$f = new LoginForm();
dump($f->validate()); // returns boolean
```

### Obtaining the values from the form

You can obtain the values by using method getValues(), just keep in mind, that this function will not validate the form, so you need check if its valid using the validate() and then you should obtain the values

```php
$f = new LoginForm();
dump($f->getValues()); // returns array
```

### Getting the error

If the user have submitted form with invalid data, then you can access the invalid input name using error() method, which returns array with [0] => error type (ERROR_REQUIRED / ERROR_NOT_VALID), [1] => element name

### Rendering without macro

You can render the form using render() method, e.g.:

```php
<?php
$f = new LoginForm();
$f->render();
```

### Completely manual rendering

If you do not have Latte, then you can of course render without it, for example manual render of LoginForm:

```php+HTML
<form method="<?=$form->method?>" action="<?=$form->action?>">
    <input name="<?=$form->csrf_token->name?>" type="<?=$form->csrf_token->type?>" required value="<?=$form->csrf_token->value?>">

<input name="<?=$form->username->name?>" type="<?=$form->username->type?>" placeholder="<?=$form->username->placeholder?>" <?=$form->username->html?> <?=($form->username->required ? "required" : "")?>>
<input name="<?=$form->password->name?>" type="<?=$form->password->type?>" placeholder="<?=$form->password->placeholder?>" <?=$form->password->html?>  <?=($form->password->required ? "required" : "")?>>
<input name="<?=$form->info->name?>" type="<?=$form->info->type?>" placeholder="<?=$form->info->placeholder?>" <?=$form->info->html?>>

    
    
    <button <?=$form->submit->html?> id="<?=$form->submit->id?>" name="submit">
        <?=$form->submit->text?>
    </button>
</form>
```

But manual rendering is quite hard and takes a lot of time so you should try to avoid it.

### FormXElement - Creating and adding elements in form

To add element to the form, you need to use method `add(string $type, string $name, string $files=null): FormXElement`,  where `$type` is the html type (e.g. input, button - Used in rendering), `$name` is the name="" attribute and `$files` is for multi file upload, where the name contains "[]". The add() method returns FormXElement object, which have many methods, so you can additionally set more information about the element.

List of available methods:

 * required(bool $r): FormXElement - If sets to true, then the element is required to fill, otherwise it can be empty.
 * id(string $id): FormXElement - The element's ID.
 * type(string $t): FormXElement - The element's type, e.g. 'text', 'password', ... 
 * default(string $d): FormXElement - The element's default value. Used if the element in submited form do not have value.
 * placeholder(string $ph): FormXElement - The element's placeholder.
 * validator(string $class, string $fn_name): FormXElement - The validator class name, e.g. 'Validator'. The validator function name, e.g. 'validateUsername'.
 * validatorRegex(string $regex): FormXElement - The validator regex code.
 * html(string $h): FormXElement - The element's additional HTML code.
 * text(string $t): FormXElement - The element's text.
 * value(string $v): FormXElement - The element's value.
 * fileSize(int $s): FormXElement - The maximum upload size in bytes.
 * fileExtensions(string $e): FormXElement - The allowed file extensions as string: 'jpg, png' ...
 * fileCount(int $c): FormXElement - The maximum count of files on one upload.
 * errorMsgEmpty(string $m): FormXElement - The element's error message, when the required element is empty.
 * errorMsgNotValid(string $m): FormXElement - The element's error message, when the element is not valid (If the element have Validator).
 * component(string $n): FormXElement - The component name, e.g. 'button', 'input', ...



### Client-side validation

Forms are also validated on client side (if you render them using latte, otherwise you need to include `/res/js/FormX.js` file in your code). It use data-formx- attributes.