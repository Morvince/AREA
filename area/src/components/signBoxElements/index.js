import React, { createRef, useLayoutEffect, useRef } from 'react';
import { SignContainer, SignForm, InputSignContainer, InputSignField, InputSignLabel, InputSignLine, LinkSignContainer, LinkForgotPassword, LinkSignInOrUp, LabelCheckboxField, CheckboxField, SubmitButton, AccountButton } from './signBoxElements';
import GoogleButton from '../googleButton/index';
import PasswordIcon from '../passwordIcon/index';
import {black, darkPurple, lightPurple, white} from '../../color';

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

const SignBoxComponent = ({slideForm, handleSlideForm}) => {

  const formSignIn = useRef(null)
  const formSignUp = useRef(null)

  // let inputPassword = createRef()

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

  return (
    <SignContainer>
      <SignForm id="signUpForm" ref={formSignUp} bg={black}>
        <AccountButton onClick={handleSlideForm} colorBG={black}/>
        <LinkSignInOrUp onClick={handleSlideForm}>Already have an account?</LinkSignInOrUp>
        <h1 style={{ color: lightPurple, fontWeight: "bold", textAlign: "center", marginTop: "30px" }}>Sign up</h1>
        <p style={{ color: lightPurple, fontSize: "19px", fontStyle: "italic", fontWeight: "bold", marginTop: "30px", alignSelf: "center" }}>Sign up with Google</p>
        <GoogleButton signOption="signup_with" margin="12px 0px 0px 0px" align="center" slideForm={slideForm}/>
        <OrLine/>
        <InputSignContainer paddingTop="10px">
          <InputSignField type="text" required="required" placeholder=" " caret={black}/>
          <InputSignLabel>Username</InputSignLabel>
          <InputSignLine/>
        </InputSignContainer>
        <InputSignContainer paddingTop="25px">
          <InputSignField type="email" required="required" placeholder=" " caret={black}/>
          <InputSignLabel>Email address</InputSignLabel>
          <InputSignLine/>
        </InputSignContainer>
        <InputSignContainer paddingTop="25px">
          <InputSignField type="password" required="required" placeholder=" " caret={black}/>
          <InputSignLabel>Password</InputSignLabel>
          <InputSignLine/>
        </InputSignContainer>
        <InputSignContainer paddingTop="25px">
          <InputSignField type="password" required="required" placeholder=" " caret={black}/>
          <InputSignLabel>Confirm your password</InputSignLabel>
          <InputSignLine/>
        </InputSignContainer>
        <LinkSignContainer>
          <CheckboxSignField label="RememberMe">Remember me</CheckboxSignField>
          <SubmitButton type="submit" value="Sign up" color={black} colorTr={lightPurple} colorBS={black} borderColor={black} borderTrColor={lightPurple}/>
        </LinkSignContainer>
      </SignForm>


      <SignForm id="signInForm" ref={formSignIn}>
        <AccountButton onClick={handleSlideForm} colorBG={darkPurple}/>
        <LinkSignInOrUp onClick={handleSlideForm}>Don't have an account?</LinkSignInOrUp>
        <h1 style={{ color: lightPurple, fontWeight: "bold", textAlign: "center", marginTop: "30px" }}>Sign in</h1>
        <p style={{ color: black, fontSize: "19px", fontStyle: "italic", fontWeight: "bold", marginTop: "30px", alignSelf: "center" }}>Continue with Google</p>
        <GoogleButton signOption="continue_with" margin="12px 0px 0px 0px" align="center" slideForm={slideForm}/>
        <OrLine/>
        <InputSignContainer paddingTop="10px">
          <InputSignField type="text" required="required" placeholder=" "/>
          <InputSignLabel>Email address or Username</InputSignLabel>
          <InputSignLine/>
        </InputSignContainer>
        <InputSignContainer paddingTop="25px">
          <InputSignField type="password" required="required" placeholder=" "/>
          <InputSignLabel>Password</InputSignLabel>
          <InputSignLine/>
        </InputSignContainer>
        <LinkSignContainer>
          <LinkForgotPassword href="#">Forgot your password?</LinkForgotPassword>
          <CheckboxSignField label="RememberMe">Remember me</CheckboxSignField>
        </LinkSignContainer>
        <SubmitButton type="submit" value="Log in" mTop="18px" colorBS={darkPurple} borderColor={white} borderTrColor={lightPurple}/>
      </SignForm>
    </SignContainer>
  )
    {/* <Header>
        <div style={{width: "100px", height: "80%", backgroundColor: "#686f84"}}></div>
      </Header>
      <div style={{display: "flex", flexDirection: "column", alignItems: "center", height: "87%"}}>
        <Line height="3px"/>
        <p style={{fontSize: "18px", fontWeight: "bold", fontStyle: "italic", marginTop: "40px"}}>Continue with Google</p>
        <GoogleButton signOption="signin_with" margin="15px 0px 0px 0px"/>
        <div style={{display: "flex", justifyContent: "center", alignItems: "center", width: "30%", marginTop: "30px"}}>
          <Line width="30%"></Line>
          <p style={{fontSize: "15px", paddingLeft: "15px", paddingRight: "15px"}}>OR</p>
          <Line width="30%"></Line>
        </div>
        <LoginBox>
          <InputField label="EmailAddressUsername" labelText="Email address or Username" type="text" placeholder="Email address or Username"/>
          <InputField ref={inputPassword} label="Password" labelText="Password" type="password" placeholder="Password">
            <PasswordIcon inputPasswd={inputPassword}/>
          </InputField>
          <div style={{display: "flex", justifyContent: "space-evenly", alignItems: "center", marginTop: "15px"}}>
            <CheckboxField label="RememberMe">Remember me</CheckboxField>
            <a style={{fontSize: "14px", fontWeight: "bold", textDecoration: "underline", color: black}}>Forgot your password?</a>
          </div>
          <SubmitButton>Log In</SubmitButton>
        </LoginBox>
        <Line width="25%"/>
        <p style={{fontSize: "18px", fontWeight: "bold"}}>Don't have an account</p>
      </div> */}
}

export default SignBoxComponent