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