import React, { useCallback, useState } from 'react';
import styled from 'styled-components'
import SignBoxComponent from '../components/signBoxElements/index';
import SignMessage from '../components/signMessage/index';
import { black, white } from '../color';

const SignPage = styled.div`
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background: ${props => props.bgColor};
  transition: background-color 0.3s;
`;

const Sign = () => {
  const [slideForm, setSlideForm] = useState(0)
  const bgColor = slideForm === 0 || slideForm === 2 ? white : black

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
      <SignBoxComponent slideForm={slideForm} handleSlideForm={handleSlideForm}/>
    </SignPage>
  )
}

export default Sign
