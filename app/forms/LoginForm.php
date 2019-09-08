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
            ->validator("Validator", "validateUsername");
        $this->form->add('input', 'password')
            ->placeholder("Type your password")
            ->type("password")
            ->html("class='input'")
            ->required(true);
        $this->form->add('input', 'info')
            ->placeholder("Type info bout you")
            ->type("text")
            ->html("class='input'");
        $this->form->add('submit', 'submit')
            ->text('Login')
            ->id('submit')
            ->html('class="button"');
    }
    
}