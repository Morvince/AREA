import React from 'react'
import { InfoBlockContainer, InfoWrapper, InfoTitle, InfoAction, InputBox } from './infoBlockElements'
import Select from 'react-select'
import Draggable from 'react-draggable'

const DropdownSection = () => {
  const options = [
    { value: 'général', label: 'général' },
  ]

  return (
    <InfoAction>
      <Select options={options} />
    </InfoAction>
  )
}

const TextSection = (props) => {
  return (
    <InfoAction>
      <InputBox id="mail" type="text" placeholder={props.text} />
    </InfoAction>
  )
}

const InfoBlock = () => {
  return (
    <Draggable>
      <InfoBlockContainer>
        <InfoWrapper>
          <InfoTitle>Quel message veut-tu ecrire</InfoTitle>
          <TextSection text="Message" />
        </InfoWrapper>
        <InfoWrapper>
          <InfoTitle>Dans quel channel veut tu écrire se message ?</InfoTitle>
          <DropdownSection />
        </InfoWrapper>
      </InfoBlockContainer>
    </Draggable>
  )
}

export default InfoBlock
