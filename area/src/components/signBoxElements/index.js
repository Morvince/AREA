import React, { useLayoutEffect, useRef, useState } from 'react';
import { Icon } from '@iconify/react';
import { SignContainer, SignForm, InputSignContainer, InputSignField, InputSignLabel, InputSignLine, LinkSignContainer, LinkForgotPassword, LinkSignInOrUp, LabelCheckboxField, CheckboxField, SubmitButton, AccountButton, IconArrowBox, IconPasswdBox } from './signBoxElements';
import GoogleButton from '../googleButton/index';
import {black, darkGray, darkPurple, lightPurple, white} from '../../color';

const OrLine = () => {
  return (
    <div style={{ display: "flex", justifyContent: "center", alignItems: "center", marginTop: "20px" }}>
      <i style={{ height: "1px", width: "30%", background: darkPurple }}></i>
      <p style={{ color: darkPurple, padding: "0 10px", fontSize: "1.2em", fontStyle: "italic" }}>or</p>
      <i style={{ height: "1px", width: "30%", background: darkPurple }}></i>
    </div>
  )
}

const CheckboxSignField = ({label, children, onClick=null}) => {
  return (
    <div style={{display: "flex"}}>
      <CheckboxField type="checkbox" onClick={onClick}></CheckboxField>
      <LabelCheckboxField htmlFor={label}>{children}</LabelCheckboxField>
    </div>
  )
}

export const SignBoxComponent = ({slideForm, handleSlideForm, handleLogin, handleRegister, tempAutomation}) => {

  const formSignIn = useRef(null)
  const formSignUp = useRef(null)

  const inputPasswdSignIn = useRef(null)
  const inputPasswdSignUp = useRef(null)
  const inputConfirmPasswdSignUp = useRef(null)
  const [iconPasswd, setIconPasswd] = useState({ iPSi: "mdi:eye-off", iPSu: "mdi:eye-off", iCPSu: "mdi:eye-off", last: "iPSi" })

  useLayoutEffect(() => {
    if (slideForm === 0) {
      formSignIn.current.style.opacity = 0
      formSignIn.current.style.transform = "translateY(-675px)"
    } else if (slideForm === 1) {
      formSignIn.current.style.opacity = 1
      formSignUp.current.style.transform = "translateY(675px)"
      formSignIn.current.style.transform = "translateY(0px)"
      setTimeout(() => {
        formSignUp.current.style.transitionDuration = "0s"
        formSignUp.current.style.opacity = 0
        formSignUp.current.style.transform = "translateY(-675px)"
        setTimeout(() => {
          formSignUp.current.style.transitionDuration = "0.6s"
        }, 10)
      }, 600)
    } else if (slideForm === 2) {
      formSignUp.current.style.opacity = 1
      formSignIn.current.style.transform = "translateY(675px)"
      formSignUp.current.style.transform = "translateY(0px)"
      setTimeout(() => {
        formSignIn.current.style.transitionDuration = "0s"
        formSignIn.current.style.opacity = 0
        formSignIn.current.style.transform = "translateY(-675px)"
        setTimeout(() => {
          formSignIn.current.style.transitionDuration = "0.6s"
        }, 10)
      }, 600)
    }
  }, [slideForm])

  useLayoutEffect(() => {
    if (iconPasswd["last"] === "iPSi")
      inputPasswdSignIn.current.type === "password" ? inputPasswdSignIn.current.type = "text" : inputPasswdSignIn.current.type = "password"
    if (iconPasswd["last"] === "iPSu")
      inputPasswdSignUp.current.type === "password" ? inputPasswdSignUp.current.type = "text" : inputPasswdSignUp.current.type = "password"
    if (iconPasswd["last"] === "iCPSu")
      inputConfirmPasswdSignUp.current.type === "password" ? inputConfirmPasswdSignUp.current.type = "text" : inputConfirmPasswdSignUp.current.type = "password"
  }, [iconPasswd])

  function seePassword(whichIconPasswd) {
    setIconPasswd(i => ({
      ...i,
      [whichIconPasswd]: i[whichIconPasswd] === "mdi:eye-off" ? i[whichIconPasswd] = "mdi:eye" : i[whichIconPasswd] = "mdi:eye-off",
      last: whichIconPasswd
    }))
  }

  const handleSignIn = (event) => {
    event.preventDefault()
    handleLogin.mutate(JSON.stringify(Object.fromEntries(new FormData(event.currentTarget).entries())))
    tempAutomation.mutate();

  }

  const handleSignUp = (event) => {
    event.preventDefault()
    if (inputPasswdSignUp.current.value !== inputConfirmPasswdSignUp.current.value) {
      alert("The 2 passwords differed")
      return
    }
    handleRegister.mutate(JSON.stringify(Object.fromEntries(new FormData(event.currentTarget).entries())))
    tempAutomation.mutate();
  }

  return (
    <SignContainer>
      <SignForm id="signInForm" ref={formSignIn} onSubmit={handleSignIn}>
        <AccountButton onClick={handleSlideForm} colorBG={darkPurple}/>
        <IconArrowBox color={white}>
          <Icon icon="material-symbols:keyboard-arrow-down-rounded" width="60" height="60"/>
        </IconArrowBox>
        <LinkSignInOrUp onClick={handleSlideForm}>Don't have an account?</LinkSignInOrUp>
        <h1 style={{ color: lightPurple, fontWeight: "bold", textAlign: "center", marginTop: "30px" }}>Sign in</h1>
        <p style={{ color: black, fontSize: "19px", fontStyle: "italic", fontWeight: "bold", marginTop: "30px", alignSelf: "center" }}>Continue with Google</p>
        <GoogleButton signOption="continue_with" margin="12px 0px 0px 0px" align="center" slideForm={slideForm}/>
        <OrLine/>
        <InputSignContainer paddingTop="10px">
          <InputSignField name="login" type="text" required="required" placeholder=" "/>
          <InputSignLabel>Email address or Username</InputSignLabel>
          <InputSignLine/>
        </InputSignContainer>
        <InputSignContainer paddingTop="25px">
          <InputSignField name="password" ref={inputPasswdSignIn} type="password" required="required" placeholder=" "/>
          <InputSignLabel>Password</InputSignLabel>
          <InputSignLine/>
          <IconPasswdBox className="iconPasswdTr" color={darkGray}>
            <Icon icon={iconPasswd["iPSi"]} width="30" height="30" onClick={() => seePassword("iPSi")}/>
          </IconPasswdBox>
        </InputSignContainer>
        <LinkSignContainer>
          <LinkForgotPassword href="#">Forgot your password?</LinkForgotPassword>
          <CheckboxSignField label="RememberMe">Remember me</CheckboxSignField>
        </LinkSignContainer>
        <SubmitButton type="submit" value="Log in" mTop="18px" colorBS={darkPurple} borderColor={white} borderTrColor={lightPurple}/>
      </SignForm>

      <SignForm id="signUpForm" ref={formSignUp} onSubmit={handleSignUp} bg={black}>
        <AccountButton onClick={handleSlideForm} colorBG={black}/>
        <IconArrowBox className="slideArrowColorTr" color={black}>
          <Icon icon="material-symbols:keyboard-arrow-down-rounded" width="60" height="60"/>
        </IconArrowBox>
        <LinkSignInOrUp onClick={handleSlideForm}>Already have an account?</LinkSignInOrUp>
        <h1 style={{ color: lightPurple, fontWeight: "bold", textAlign: "center", marginTop: "30px" }}>Sign up</h1>
        <p style={{ color: lightPurple, fontSize: "19px", fontStyle: "italic", fontWeight: "bold", marginTop: "30px", alignSelf: "center" }}>Sign up with Google</p>
        <GoogleButton signOption="signup_with" margin="12px 0px 0px 0px" align="center" slideForm={slideForm}/>
        <OrLine/>
        <InputSignContainer paddingTop="9px">
          <InputSignField name="username" type="text" required="required" placeholder=" " caret={black}/>
          <InputSignLabel>Username</InputSignLabel>
          <InputSignLine/>
        </InputSignContainer>
        <InputSignContainer paddingTop="20px">
          <InputSignField name="email" type="email" required="required" placeholder=" " caret={black}/>
          <InputSignLabel>Email address</InputSignLabel>
          <InputSignLine/>
        </InputSignContainer>
        <InputSignContainer paddingTop="20px">
          <InputSignField name="password" ref={inputPasswdSignUp} type="password" required="required" placeholder=" " caret={black}/>
          <InputSignLabel>Password</InputSignLabel>
          <InputSignLine/>
          <IconPasswdBox className="iconPasswdTr" color={darkGray}>
            <Icon icon={iconPasswd["iPSu"]} width="30" height="30" onClick={() => seePassword("iPSu")}/>
          </IconPasswdBox>
        </InputSignContainer>
        <InputSignContainer paddingTop="20px">
          <InputSignField ref={inputConfirmPasswdSignUp} type="password" required="required" placeholder=" " caret={black}/>
          <InputSignLabel>Confirm your password</InputSignLabel>
          <InputSignLine/>
          <IconPasswdBox className="iconPasswdTr" color={darkGray}>
            <Icon icon={iconPasswd["iCPSu"]} width="30" height="30" onClick={() => seePassword("iCPSu")}/>
          </IconPasswdBox>
        </InputSignContainer>
        <LinkSignContainer>
          <CheckboxSignField label="RememberMe">Remember me</CheckboxSignField>
          <SubmitButton type="submit" value="Sign up" color={black} colorTr={lightPurple} colorBS={black} borderColor={black} borderTrColor={lightPurple}/>
        </LinkSignContainer>
      </SignForm>
  
    </SignContainer>
  )
};

export const LoginBoxComponent = ({slideForm, handleSlideForm, handleLogin, handleRegister, tmpAutomation}) => {

  const formSignIn = useRef(null)
  const formSignUp = useRef(null)

  const inputPasswdSignIn = useRef(null)
  const inputPasswdSignUp = useRef(null)
  const inputConfirmPasswdSignUp = useRef(null)
  const [iconPasswd, setIconPasswd] = useState({ iPSi: "mdi:eye-off", iPSu: "mdi:eye-off", iCPSu: "mdi:eye-off", last: "iPSi" })

  useLayoutEffect(() => {
    if (slideForm === 0) {
      formSignUp.current.style.opacity = 0
      formSignUp.current.style.transform = "translateY(-675px)"
    } else if (slideForm === 1) {
      formSignUp.current.style.opacity = 1
      formSignIn.current.style.transform = "translateY(675px)"
      formSignUp.current.style.transform = "translateY(0px)"
      setTimeout(() => {
        formSignIn.current.style.transitionDuration = "0s"
        formSignIn.current.style.opacity = 0
        formSignIn.current.style.transform = "translateY(-675px)"
        setTimeout(() => {
          formSignIn.current.style.transitionDuration = "0.6s"
        }, 10)
      }, 600)
    } else if (slideForm === 2) {
      formSignIn.current.style.opacity = 1
      formSignUp.current.style.transform = "translateY(675px)"
      formSignIn.current.style.transform = "translateY(0px)"
      setTimeout(() => {
        formSignUp.current.style.transitionDuration = "0s"
        formSignUp.current.style.opacity = 0
        formSignUp.current.style.transform = "translateY(-675px)"
        setTimeout(() => {
          formSignUp.current.style.transitionDuration = "0.6s"
        }, 10)
      }, 600)
    }
  }, [slideForm])

  useLayoutEffect(() => {
    if (iconPasswd["last"] === "iPSi")
      inputPasswdSignIn.current.type === "password" ? inputPasswdSignIn.current.type = "text" : inputPasswdSignIn.current.type = "password"
    if (iconPasswd["last"] === "iPSu")
      inputPasswdSignUp.current.type === "password" ? inputPasswdSignUp.current.type = "text" : inputPasswdSignUp.current.type = "password"
    if (iconPasswd["last"] === "iCPSu")
      inputConfirmPasswdSignUp.current.type === "password" ? inputConfirmPasswdSignUp.current.type = "text" : inputConfirmPasswdSignUp.current.type = "password"
  }, [iconPasswd])

  function seePassword(whichIconPasswd) {
    setIconPasswd(i => ({
      ...i,
      [whichIconPasswd]: i[whichIconPasswd] === "mdi:eye-off" ? i[whichIconPasswd] = "mdi:eye" : i[whichIconPasswd] = "mdi:eye-off",
      last: whichIconPasswd
    }))
  }

  const handleSignIn = (event) => {
    event.preventDefault()
    handleLogin.mutate(JSON.stringify(Object.fromEntries(new FormData(event.currentTarget).entries())))
    tmpAutomation.mutate();
  }

  const handleSignUp = (event) => {
    event.preventDefault()
    if (inputPasswdSignUp.current.value !== inputConfirmPasswdSignUp.current.value) {
      alert("The 2 passwords differed")
      return
    }
    handleRegister.mutate(JSON.stringify(Object.fromEntries(new FormData(event.currentTarget).entries())))
    tmpAutomation.mutate();
  }

  return (
    <SignContainer>
      <SignForm id="signUpForm" ref={formSignUp} onSubmit={handleSignUp} bg={black}>
        <AccountButton onClick={handleSlideForm} colorBG={black}/>
        <IconArrowBox className="slideArrowColorTr" color={black}>
          <Icon icon="material-symbols:keyboard-arrow-down-rounded" width="60" height="60"/>
        </IconArrowBox>
        <LinkSignInOrUp onClick={handleSlideForm}>Already have an account?</LinkSignInOrUp>
        <h1 style={{ color: lightPurple, fontWeight: "bold", textAlign: "center", marginTop: "30px" }}>Sign up</h1>
        <p style={{ color: lightPurple, fontSize: "19px", fontStyle: "italic", fontWeight: "bold", marginTop: "30px", alignSelf: "center" }}>Sign up with Google</p>
        <GoogleButton signOption="signup_with" margin="12px 0px 0px 0px" align="center" slideForm={slideForm}/>
        <OrLine/>
        <InputSignContainer paddingTop="9px">
          <InputSignField name="username" type="text" required="required" placeholder=" " caret={black}/>
          <InputSignLabel>Username</InputSignLabel>
          <InputSignLine/>
        </InputSignContainer>
        <InputSignContainer paddingTop="20px">
          <InputSignField name="email" type="email" required="required" placeholder=" " caret={black}/>
          <InputSignLabel>Email address</InputSignLabel>
          <InputSignLine/>
        </InputSignContainer>
        <InputSignContainer paddingTop="20px">
          <InputSignField name="password" ref={inputPasswdSignUp} type="password" required="required" placeholder=" " caret={black}/>
          <InputSignLabel>Password</InputSignLabel>
          <InputSignLine/>
          <IconPasswdBox className="iconPasswdTr" color={darkGray}>
            <Icon icon={iconPasswd["iPSu"]} width="30" height="30" onClick={() => seePassword("iPSu")}/>
          </IconPasswdBox>
        </InputSignContainer>
        <InputSignContainer paddingTop="20px">
          <InputSignField ref={inputConfirmPasswdSignUp} type="password" required="required" placeholder=" " caret={black}/>
          <InputSignLabel>Confirm your password</InputSignLabel>
          <InputSignLine/>
          <IconPasswdBox className="iconPasswdTr" color={darkGray}>
            <Icon icon={iconPasswd["iCPSu"]} width="30" height="30" onClick={() => seePassword("iCPSu")}/>
          </IconPasswdBox>
        </InputSignContainer>
        <LinkSignContainer>
          <CheckboxSignField label="RememberMe">Remember me</CheckboxSignField>
          <SubmitButton type="submit" value="Sign up" color={black} colorTr={lightPurple} colorBS={black} borderColor={black} borderTrColor={lightPurple}/>
        </LinkSignContainer>
      </SignForm>


      <SignForm id="signInForm" ref={formSignIn} onSubmit={handleSignIn}>
        <AccountButton onClick={handleSlideForm} colorBG={darkPurple}/>
        <IconArrowBox color={white}>
          <Icon icon="material-symbols:keyboard-arrow-down-rounded" width="60" height="60"/>
        </IconArrowBox>
        <LinkSignInOrUp onClick={handleSlideForm}>Don't have an account?</LinkSignInOrUp>
        <h1 style={{ color: lightPurple, fontWeight: "bold", textAlign: "center", marginTop: "30px" }}>Sign in</h1>
        <p style={{ color: black, fontSize: "19px", fontStyle: "italic", fontWeight: "bold", marginTop: "30px", alignSelf: "center" }}>Continue with Google</p>
        <GoogleButton signOption="continue_with" margin="12px 0px 0px 0px" align="center" slideForm={slideForm}/>
        <OrLine/>
        <InputSignContainer paddingTop="10px">
          <InputSignField name="login" type="text" required="required" placeholder=" "/>
          <InputSignLabel>Email address or Username</InputSignLabel>
          <InputSignLine/>
        </InputSignContainer>
        <InputSignContainer paddingTop="25px">
          <InputSignField name="password" ref={inputPasswdSignIn} type="password" required="required" placeholder=" "/>
          <InputSignLabel>Password</InputSignLabel>
          <InputSignLine/>
          <IconPasswdBox className="iconPasswdTr" color={darkGray}>
            <Icon icon={iconPasswd["iPSi"]} width="30" height="30" onClick={() => seePassword("iPSi")}/>
          </IconPasswdBox>
        </InputSignContainer>
        <LinkSignContainer>
          <LinkForgotPassword href="#">Forgot your password?</LinkForgotPassword>
          <CheckboxSignField label="RememberMe">Remember me</CheckboxSignField>
        </LinkSignContainer>
        <SubmitButton type="submit" value="Log in" mTop="18px" colorBS={darkPurple} borderColor={white} borderTrColor={lightPurple}/>
      </SignForm>
    </SignContainer>
  )
};