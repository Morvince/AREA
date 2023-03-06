import React, {useEffect} from 'react';

const GoogleButton = ({signOption, margin, align=null, slideForm=null}) => {
  function handleCredentialResponse(response) {
    console.log("Encoded JWT ID token: " + response.credential);
  }

  useEffect(() => {
    if (slideForm !== null) {

      if (((slideForm === 0 || slideForm === 2) && signOption === "continue_with") ||
      (slideForm === 1 && signOption === "signup_with")) {
        window.google.accounts.id.initialize({
          client_id: "201465445311-2nciar5le9vqh553dqb0jnkc4jt08483.apps.googleusercontent.com",
          callback: handleCredentialResponse,
        })
        window.google.accounts.id.renderButton(
          document.getElementById("signInDiv"),
          {theme: "outline", size: "large", shape: "pill", locale: "en", text: signOption}
        )
        window.google.accounts.id.prompt()
      }
    }
  }, [signOption, slideForm])

  if (slideForm !== null) {
    if (((slideForm === 0 || slideForm === 2) && signOption === "continue_with") ||
    (slideForm === 1 && signOption === "signup_with"))
      return (
        <div id="signInDiv" style={{margin: margin, alignSelf: align}}></div>
      )
    else
        return (null)
  } else
      return (
        <div id="signInDiv" style={{margin: margin, alignSelf: align}}></div>
      )
}

export default GoogleButton