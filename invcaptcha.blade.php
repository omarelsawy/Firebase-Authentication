<!DOCTYPE html>
<html lang="en">
<head>
 <title>Bootstrap Example</title>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://code.getmdl.io/1.1.3/material.orange-indigo.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<script src="https://code.getmdl.io/1.1.3/material.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>

<div class="container">
                      
               <div id="divVerify" style="display: none;">
                 <form id="verification-code-form" action="#">          
                   <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                     <input class="mdl-textfield__input" type="text" id="verification-code">
                     <label class="mdl-textfield__label" for="verification-code">
                      Enter the verification code...</label>
                   </div>
                   <input type="submit" class="btn btn-success" 

                    id="verify-code-button" value="Verify Code"/>       
                 </form>
               </div> 

         <button id="sign-in-button" style="display: none;"></button>
         <form method="post" id="signup" action="/signupWithFirebase">  
           @csrf
           <div class="form-group">
              <label>Mobile:</label>
              <input class="form-control" type="text" pattern="\+[0-9\s\-\(\)]+" name="phone" placeholder="Type Mobile" id="phone-number" required>
          </div>

          <div class="form-group">
              <label>Email:</label>
              <input class="form-control" type="email" name="email" placeholder="Type Email" required>
          </div>
                  
           <input type="submit" class="btn btn-success" value="Save"/>       
         </form>
</div>

<script src="https://www.gstatic.com/firebasejs/5.7.3/firebase.js"></script>
<script>
  // Initialize Firebase
  var config = {
    apiKey: "AIzaSyBsDBE-ZQNPMCBYEZjfTEMF_vLMWGhffYE",
    authDomain: "laravelauthsms.firebaseapp.com",
    databaseURL: "https://laravelauthsms.firebaseio.com",
    projectId: "laravelauthsms",
    storageBucket: "laravelauthsms.appspot.com",
    messagingSenderId: "1038340136048"
  };
  firebase.initializeApp(config);

   window.onload = function() {
   // Listening for auth state changes.
   firebase.auth().onAuthStateChanged(function(user) {
     if (user) {
       // User is signed in.
       var uid = user.uid;
       var email = user.email;
       var photoURL = user.photoURL;
       var phoneNumber = user.phoneNumber;
       var isAnonymous = user.isAnonymous;
       var displayName = user.displayName;
       var providerData = user.providerData;
       var emailVerified = user.emailVerified;
     }
   });

  document.getElementById('verification-code-form').addEventListener('submit', onVerifyCodeSubmit);
  document.getElementById('signup').addEventListener('submit', signup);

  window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('sign-in-button', {
    'size': 'invisible',
    'callback': function(response) {
      onSignInSubmit();
    }
  });

  recaptchaVerifier.render().then(function(widgetId) {
    window.recaptchaWidgetId = widgetId;
  });
};

  function signup(e){
    e.preventDefault();
    document.getElementById('sign-in-button').click();   
    document.getElementById('signup').style.display = 'none';   
    document.getElementById('divVerify').style.display = 'block';    
  }

  function onSignInSubmit() {
    window.signingIn = true;
    var phoneNumber = getPhoneNumberFromUserInput();
    var appVerifier = window.recaptchaVerifier;
    firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier)
        .then(function (confirmationResult) {

          window.confirmationResult = confirmationResult;
          window.signingIn = false;
        }).catch(function (error) {
          console.error('Error during signInWithPhoneNumber', error);
          window.alert('Error during signInWithPhoneNumber:\n\n'
              + error.code + '\n\n' + error.message);
          window.signingIn = false;
        });
}

function getPhoneNumberFromUserInput(){
  return document.getElementById('phone-number').value;
}

function onVerifyCodeSubmit(e) {
  e.preventDefault();
  if (getCodeFromUserInput()) {
    window.verifyingCode = true;
    //updateVerifyCodeButtonUI();
    var code = getCodeFromUserInput();
    confirmationResult.confirm(code).then(function (result) {
      // User signed in successfully.
      var user = result.user;
      document.getElementById('signup').submit();
      window.verifyingCode = false;
      window.confirmationResult = null;
      //updateVerificationCodeFormUI();
    }).catch(function (error) {
      // User couldn't sign in (bad verification code?)
      console.error('Error while checking the verification code', error);
      window.alert('Error while checking the verification code:\n\n'
          + error.code + '\n\n' + error.message);
      window.verifyingCode = false;
      //updateSignInButtonUI();
      //updateVerifyCodeButtonUI();
    });
  }
}

function getCodeFromUserInput(){
  return document.getElementById('verification-code').value; 
}

</script>











