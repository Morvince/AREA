import React, { useEffect, useState, useContext } from 'react'
import { InfoBlockContainer, InfoWrapper, InfoTitle, InfoAction, InputBox, LittleBorder } from './infoBlockElements'
import Select from 'react-select'
import Draggable from 'react-draggable'

const DropdownSection = (props) => {
  const options = [
    { value: 'générazdadazdazdadl', label: 'géazdazdazdazdanéral' },
  ]

  return (
    <InfoWrapper>
      <InfoTitle>{props.title}</InfoTitle>
      <InfoAction>
        <Select options={options} />
      </InfoAction>
    </InfoWrapper>
  )
}

const TextSection = (props) => {
  return (
    <InfoWrapper>
      <InfoTitle>{props.title}</InfoTitle>
      <InfoAction>
        <InputBox id="mail" type="text" placeholder={props.text} />
      </InfoAction>
    </InfoWrapper>
  )
}

const InfoBlock = (props) => {
  function getFields() {
    let fields = []
    console.log('----------------------')
    for (let i = 0; i < props.action.length; i++) {
      console.log(props.action[i].fields)
    }
    console.log('----------------------')
  }

  return (
    useEffect(() => {
      console.log(props.action[props.IsVisible]?.fields)
      // getFields()
      // console.log(props.IsVisible)
    }, [props.IsVisible]),
    props.IsVisible !== null ? (
      <Draggable>
        <InfoBlockContainer top={(Math.trunc(props.top) - 130) + "px"} left={(Math.trunc(props.left) + 50) + "px"} background={props.background}>
          <TextSection title="Quel message veut-tu ecrire ?" text="Message" />
          <LittleBorder />
          <DropdownSection title="Quel service veux-tu utiliser ?" />
        </InfoBlockContainer>
      </Draggable>
    ) : (<></>)
  )
}

export default InfoBlock
