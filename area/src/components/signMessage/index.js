import React, { useLayoutEffect, useRef } from 'react';
import { SignMessageWrapper, SignMessageContainer } from './signMessage';

const SignMessage = ({slideForm}) => {
  const msgSignIn = useRef(null)
  const msgSignUp = useRef(null)

  useLayoutEffect(() => {
    if (slideForm === 0)
      msgSignUp.current.style.opacity = 0
    else if (slideForm === 1) {
      msgSignUp.current.style.opacity = 1
      msgSignIn.current.style.opacity = 0
      msgSignIn.current.style.transform = "translateY(50%)"
      msgSignUp.current.style.transform = "translateY(50%)"
      setTimeout(() => {
        msgSignIn.current.style.transitionDuration = "0s"
        msgSignIn.current.style.transform = "translateY(-150%)"
        setTimeout(() => {
          msgSignIn.current.style.transitionDuration = "0.6s"
        }, 10)
      }, 600)
    } else if (slideForm === 2) {
      msgSignIn.current.style.opacity = 1
      msgSignUp.current.style.opacity = 0
      msgSignUp.current.style.transform = "translateY(150%)"
      msgSignIn.current.style.transform = "translateY(-50%)"
      setTimeout(() => {
        msgSignUp.current.style.transitionDuration = "0s"
        msgSignUp.current.style.transform = "translateY(-50%)"
        setTimeout(() => {
          msgSignUp.current.style.transitionDuration = "0.6s"
        }, 10)
      }, 600)
    }
  }, [slideForm])

  return (
      <SignMessageWrapper>
        <SignMessageContainer ref={msgSignUp}>
          <h1>Welcome To Area!</h1>
        </SignMessageContainer>
        <SignMessageContainer ref={msgSignIn}>
          <h1>Welcome Back!</h1>
        </SignMessageContainer>
      </SignMessageWrapper>
  )
}

export default SignMessage