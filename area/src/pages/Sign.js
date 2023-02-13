import React, { useCallback, useState } from 'react';
import styled from 'styled-components'
import SignBoxComponent from '../components/signBoxElements/index';
import SignMessage from '../components/signMessage/index';
import { black, white } from '../color';
import { useLogin, useRegister } from '../api/apiSignPage';
import { Navigate } from 'react-router-dom';

const SignPage = styled.div`
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background: ${props => props.bgColor};
  transition: background-color 0.3s;
`;

const ErrorMessage = styled.p`
  margin-left: 200px;
  font-size: 20px;
  font-style: italic;
  color: ${props => props.color};
`;

const Sign = () => {
  const [slideForm, setSlideForm] = useState(0)
  const bgColor = slideForm === 0 || slideForm === 2 ? white : black
  const handleLogin = useLogin()
  const handleRegister = useRegister()

  const handleSlideForm = useCallback(function(event) {
    event.preventDefault()
    if (slideForm === 0)
      setSlideForm(s => s + 1)
    else if (slideForm === 1)
      setSlideForm(s => s + 1)
    else if (slideForm === 2)
      setSlideForm(s => s - 1)
  }, [slideForm])

  return (
    <SignPage bgColor={bgColor}>
      <SignMessage slideForm={slideForm}/>
      <SignBoxComponent slideForm={slideForm} handleSlideForm={handleSlideForm} handleLogin={handleLogin} handleRegister={handleRegister}/>
      <div style={{position: "absolute", width: "100%", alignSelf: "flex-end", textAlign: "center", marginBottom: "110px"}}>
        {(slideForm === 0 || slideForm === 2) && handleLogin.isError ? <ErrorMessage color={black}>{handleLogin.error.response.data.message}</ErrorMessage> :
          slideForm === 1 && handleRegister.isError ? <ErrorMessage color={white}>{handleRegister.error.response.data.message}</ErrorMessage> : null}
      </div>
      {handleLogin.isSuccess && <Navigate to="/" replace={true}/>}
      {handleRegister.isSuccess && <Navigate to="/" replace={true}/>}
    </SignPage>
  )
}

export default Sign
