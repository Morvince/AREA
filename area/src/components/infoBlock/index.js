import React from 'react'
import { InfoBlockContainer, InfoWrapper, InfoTitle, InfoAction, InputBox, LittleBorder } from './infoBlockElements'
import Select from 'react-select'
import Draggable from 'react-draggable'

const DropdownSection = () => {
  const options = [
    { value: 'générazdadazdazdadl', label: 'géazdazdazdazdanéral' },
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

const InfoBlock = (props) => {
  console.log(props.top, props.left)
  return (
    props.IsVisible !== null ? (
    <Draggable>
       <InfoBlockContainer top={props.top} left={props.left}>
         <InfoWrapper>
           <InfoTitle>Quel message veut-tu ecrire</InfoTitle>
           <TextSection text="Message" />
         </InfoWrapper>
         <LittleBorder />
         <InfoWrapper>
           <InfoTitle>Dans quel channel veut tu écrire se message ?</InfoTitle>
           <DropdownSection />
         </InfoWrapper>
       </InfoBlockContainer>
     </Draggable>
    ) : ( <></> )
  )
}

export default InfoBlock
