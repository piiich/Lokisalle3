// VERIFICATION FORMULAIRE //

$('#contact-form').bootstrapValidator({
//        live: 'disabled',
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            Name: {
                validators: {
                    notEmpty: {
                        message: 'Veuillez remplir le champs nom'
                    }
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: 'Veuillez remplir votre email'
                    },
                    emailAddress: {
                        message: 'Votre adresse email n\'est pas valide'
                    }
                }
            },
            Message: {
                validators: {
                    notEmpty: {
                        message: 'Veuillez saisir un message'
                    }
                }
            }
        }
    });