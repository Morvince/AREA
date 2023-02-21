import React from 'react'
import { InfoBlockContainer, InfoWrapper, InfoTitle, InfoAction, InputBox } from './infoBlockElements'

const InfoBlock = () => {
  return (
    <InfoBlockContainer>
      <InfoWrapper>
        <InfoTitle>Titre</InfoTitle>
        <InfoAction>
          <InputBox id="mail" type="text" placeholder="Name"/>
        </InfoAction>
      </InfoWrapper>
    </InfoBlockContainer>
  )
}

export default InfoBlock
