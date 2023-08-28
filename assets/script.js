window.verifPassword= verifPassword;





    function verifPassword() {
        console.log('tfgy')
        let newPasswordField = document.getElementById('mot_de_passe_newPassword_first');
        let confirmPasswordField = document.getElementById('mot_de_passe_newPassword_second');
        let errorContainer = document.getElementById('error-message-container');


        newPasswordField.addEventListener('input', verifPassword);
        confirmPasswordField.addEventListener('input', () => {
            let newPassword = newPasswordField.value;
            let confirmPassword = confirmPasswordField.value;


            if (newPassword !== confirmPassword) {
                errorContainer.innerText="Saisie incorrect";
            } else {
                errorContainer.innerText = "";
            }
        });


    }
