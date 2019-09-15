var forms = document.querySelectorAll("form");
for (let i = 0; i < forms.length; i++) {
    console.log(forms.length);
    if(!forms[i].dataset.formxInitialized) {
        forms[i].addEventListener("submit", submitForm);
        forms[i].dataset.formxInitialized = "true";
    }
    let c = forms[i].childNodes;
    for (let x = 0; x < c.length; x++) {
        if (c[x].tagName && c[x].tagName.toLowerCase() == "input") {
            let el = c[x];
            if (el.dataset.formxIgnore && el.dataset.formxIgnore == "true") continue;
            let checkTypesSkip = [
                'button',
                'color',
                'reset',
                'range',
                'hidden',
                'image',
                'submit'
            ];
            /*
            TYPE            |   IS SUPPORTED?   |   TESTED? |   REGEX   |   EMPTY 
            button          |   FALSE           |   /       |   /       |   /
            checkbox        |   TRUE            |   FALSE   |   FALSE   |   TRUE
            color           |   FALSE           |   /       |   /       |   /
            date            |   TRUE            |   FALSE   |   TRUE    |   TRUE
            datetime-local  |   TRUE            |   FALSE   |   TRUE    |   TRUE
            email           |   TRUE            |   FALSE   |   TRUE    |   TRUE
            file            |   TRUE            |   FALSE   |   FALSE   |   TRUE
            hidden          |   FALSE           |   /       |   /       |   /
            image           |   FALSE           |   /       |   /       |   /
            month           |   TRUE            |   FALSE   |   TRUE    |   TRUE
            number          |   TRUE            |   FALSE   |   TRUE    |   TRUE
            password        |   TRUE            |   FALSE   |   TRUE    |   TRUE
            radio           |   TRUE            |   FALSE   |   FALSE   |   TRUE
            range           |   FALSE           |   /       |   /       |   /
            reset           |   FALSE           |   /       |   /       |   /
            search          |   TRUE            |   FALSE   |   TRUE    |   TRUE
            submit          |   FALSE           |   /       |   /       |   /
            tel             |   TRUE            |   FALSE   |   TRUE    |   TRUE
            text            |   TRUE            |   TRUE    |   TRUE    |   TRUE
            time            |   TRUE            |   FALSE   |   TRUE    |   TRUE
            url             |   TRUE            |   FALSE   |   TRUE    |   TRUE
            week            |   TRUE            |   FALSE   |   TRUE    |   TRUE
            */
            if (checkTypesSkip.indexOf(el.type) == -1) {
                if (!el.dataset.formxInitialized) {
                    el.addEventListener("change", validate)
                    el.addEventListener("blur", validate)
                    el.addEventListener("keyup", validate)
                    el.dataset.formxInitialized = "true";
                }
            }
        }
    }
}

function validate(e, ele = null) {
    let el = (e !== null) ? this : ele;
    if(typeof el.dataset === "undefined") {
        return true;
    }
    let required = (el.dataset.formxRequired && el.dataset.formxRequired == "true") || false;
    let regex = el.dataset.formxValidator || false;
    let errMsgEmpty = el.dataset.formxEmptyMsg || false;
    let errMsgInvalid = el.dataset.formxInvalidMsg || false;
    let err = document.querySelector("form div[data-formx-element-name='" + el.name + "']");

    if (required) {
        if(el.type != "checkbox" && el.type != "radio") {
            console.log("Y", el.value.length);
            if (el.value.length < 1) {
                el.classList.remove('formx-valid-input');
                el.classList.add('formx-invalid-input');
                if (errMsgEmpty) {
                    if (err == null) {
                        el.insertAdjacentHTML('afterEnd', "<div data-formx-element-name='" + el.name + "'>" + (errMsgEmpty.replace("%s", el.name)) + "</div>")
                    } else {
                        err.style.display = "block";
                        err.innerHTML = errMsgEmpty.replace("%s", el.name);
                    }
                }
                return false;
            }
            if (el.type == "file") {
                let fSize = el.dataset.formxFileSize || -1;
                let fCount = el.dataset.formxFileCount || -1;
                let fExtensions = el.dataset.formxFileExtensions || null;
                if(fExtensions != null) {
                    fExtensions = fExtensions.split(/,\s?/);
                }
                let error = false;
                let files = el.files;
                console.log(fExtensions);
                let size = 0;
                let count = el.files.length;
                if(fCount != -1 && count > fCount) {
                    error = true;
                }
                if(!error && (fSize != -1 || fExtensions != null)) { 
                    for(let i = 0; i < files.length; i++) {
                        if(fSize != -1) {
                            size += files[i].size;
                            if(size > fSize) {
                                error = true;
                                console.log("size", size, fSize);
                                break;
                            }
                        }
                        if(fExtensions != null) {
                            let ext = files[i].name.substring(files[i].name.lastIndexOf('.') + 1);
                            if(fExtensions.indexOf(ext) == -1) {
                                error = true;
                                console.log("ext");
                                break;
                            }
                        }
                    }
                }

                if (error) {
                    el.classList.remove('formx-valid-input');
                    el.classList.add('formx-invalid-input');
                    if (errMsgInvalid) {
                        if (err == null) {
                            el.insertAdjacentHTML('afterEnd', "<div data-formx-element-name='" + el.name + "'>" + (errMsgInvalid.replace("%s", el.name)) + "</div>")
                        } else {
                            err.style.display = "block";
                            err.innerHTML = errMsgInvalid.replace("%s", el.name);
                        }
                    }
                    return false;
                }
            }
        } else {
            console.log(el)
            if(el.type == "checkbox") {
                if(!el.checked) {
                    if (errMsgEmpty) {
                        if (err == null) {
                            el.insertAdjacentHTML('afterEnd', "<div data-formx-element-name='" + el.name + "'>" + (errMsgEmpty.replace("%s", el.name)) + "</div>")
                        } else {
                            err.style.display = "block";
                            err.innerHTML = errMsgEmpty.replace("%s", el.name);
                        }
                    }
                    return false;
                }
            } else if (el.type == "radio") {
                // atleast one radio
                if(document.querySelectorAll("input[name='"+el.name+"'][type=radio]:checked").length < 1) {
                    if (errMsgEmpty) {
                        if (err == null) {
                            el.insertAdjacentHTML('afterEnd', "<div data-formx-element-name='" + el.name + "'>" + (errMsgEmpty.replace("%s", el.name)) + "</div>")
                        } else {
                            err.style.display = "block";
                            err.innerHTML = errMsgEmpty.replace("%s", el.name);
                        }
                    }
                    return false;
                }
            }
        }

    }
    if(!regex) {
        if (!el.dataset.formxDefaultRegex || el.dataset.formxRequired != "false") {
            //use default regex by type
            switch (el.type) {
                case 'password':
                    regex = ".{6,}"
                    if (!errMsgInvalid) {
                        errMsgInvalid = formx_translations.__invalid_password
                    }
                    break;
                case 'email':
                    regex = "\\w+([\.-]?\\w+)*@\\w+([\\.-]?\\w+)*(\\.\\w{2,})+";
                    if (!errMsgInvalid) {
                        errMsgInvalid = formx_translations.__invalid_email
                    }
                    break;
                case 'url':
                    regex = "https?:\\/\\/(www\.)?[-a-zA-Z0-9@:%._\\+~#=]{1,256}\\.[a-zA-Z0-9()]{1,6}\\b([-a-zA-Z0-9()@:%_\\+.~#?&//=]*)";
                    if (!errMsgInvalid) {
                        errMsgInvalid = formx_translations.__invalid_url
                    }
                    break;
                case 'tel':
                    //min length 9
                    regex = "([+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\\s\\./0-9]*){9,}";
                    if (!errMsgInvalid) {
                        errMsgInvalid = formx_translations.__invalid_telephone
                    }
                    break;
            }
        }
    }
    if (regex) {
        re = new RegExp("^" + regex + "$");
        if (!re.test(el.value)) {
            el.classList.remove('formx-valid-input');
            el.classList.add('formx-invalid-input');
            if (errMsgInvalid) {
                if (err == null) {
                    el.insertAdjacentHTML('afterEnd', "<div data-formx-element-name='" + el.name + "'>" + (errMsgInvalid.replace("%s", el.name)) + "</div>")
                } else {
                    err.style.display = "block";
                    err.innerHTML = errMsgInvalid.replace("%s", el.name);
                }
            }
            return false;
        }
    }
    err = document.querySelector("form div[data-formx-element-name='" + el.name + "']");
    if (err) {
        if (err.dataset.formxHideOnSuccess && err.dataset.formxHideOnSuccess == "false") {
            //
            
        } else {
            err.style.display = "none";
        }
        err.innerHTML = "<br>";
    }
    el.classList.remove('formx-invalid-input');
    console.log(typeof el.type != "undefined" && el.type != "checkbox" && el.type != "radio");
    //alert(el.type != "checkbox" && el.type != "radio");
    if (el.type != "radio") 
        el.classList.add('formx-valid-input');
    return true;
}

function submitForm(e) {
    if(!validWholeForm(this)) {
        e.preventDefault();
        alert(formx_translations.__invalid_data);
    }
}

function validWholeForm(x) {
    let els = x.childNodes;
    for (let i = 0; i < els.length; i++) {
        if (!validate(null, els[i])) {
            return false;
        }
    }
    return true
}