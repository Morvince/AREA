import React, { useEffect, useState, useContext } from 'react'
import { InfoBlockContainer, InfoWrapper, InfoTitle, InfoAction, InputBox, LittleBorder } from './infoBlockElements'
import Select from 'react-select'
import MyContext from '../Context'

const DropdownSection = (props) => {
  const { playlist } = useContext(MyContext);
  const { repository } = useContext(MyContext);
  const { getUserChannels } = useContext(MyContext);
  const { sharedData } = useContext(MyContext);

  const options = useState([])

  if (props.service === "spotify") {
    for (let i = 0; i < playlist.items.length; i++)
      options.push({ value: playlist.items[i].id, label: playlist.items[i].name })
  } else if (props.service === "github") {
    for (let i = 0; i < repository.items.length; i++)
      options.push({ value: repository.items[i].id, label: repository.items[i].name })
  } else if (props.service === "discord") {
    
  }

  function onChange(e) {
    //if name already exists, update it
    if (sharedData[props.IsVisible].toSend[props.name] !== undefined) {
      sharedData[props.IsVisible].toSend[props.name] = e.value
    }
    //else create it
    else {
      sharedData[props.IsVisible].toSend[props.name] = e.value
    }
  }

  return (
    <InfoWrapper>
      <InfoTitle>{props.title}</InfoTitle>
      <InfoAction>
        <Select options={options} onChange={onChange} />
      </InfoAction>
    </InfoWrapper>
  )
}

const TextSection = (props) => {
  const { sharedData } = useContext(MyContext);
  const [text, setText] = useState(props.text)
  function onChange(e) {
    if (sharedData[props.IsVisible].toSend[props.name] !== undefined) {
      sharedData[props.IsVisible].toSend[props.name] = e.target.value
    }
    else {
      sharedData[props.IsVisible].toSend[props.name] = e.target.value
    }
  }

  useEffect(() => {
    if (sharedData[props.IsVisible].toSend[props.name] !== undefined) {
      setText(sharedData[props.IsVisible].toSend[props.name])
    }
  }, [sharedData])

  return (
    <InfoWrapper>
      <InfoTitle>{props.title}</InfoTitle>
      <InfoAction>
        <InputBox type="text" placeholder={text} onChange={onChange} />
      </InfoAction>
    </InfoWrapper>
  )
}

const InfoBlock = (props) => {
  const { sharedData } = useContext(MyContext);
  const [fields, setFields] = useState([])

  function getFields() {
    let fields = []
    for (let i = 0; i < props.action.length; i++) {
      fields.push([props.action[i].id, []])
      if (props.action[i].fields !== null) {
        for (let j = 0; j < props.action[i].fields.length; j++) {
          fields[i][1].push([])
          fields[i][1][j].push(props.action[i].fields[j].type) // text, dropdown, search
          fields[i][1][j].push(props.action[i].fields[j].title) // title
          fields[i][1][j].push(props.action[i].fields[j].name) // in case of text
        }
      }
    }
    setFields(fields)
  }
  useEffect(() => {
    getFields()
  }, [props.IsVisible])

  //for each field return a TextSection or DropdownSection
  function renderFields() {
    let infoBlock = []
    if (sharedData.length === 0 || props.IsVisible === null)
      return
    for (let i = 0; i < fields.length; i++) {
      if (sharedData[props.IsVisible].dbId === fields[i][0]) {
        for (let j = 0; j < fields[i][1].length; j++) {
          if (fields[i][1][j][0] === "text") {
            infoBlock.push(<TextSection title={fields[i][1][j][1]} text={fields[i][1][j][2]} service={props.service} IsVisible={props.IsVisible} name={fields[i][1][j][2]} />)
          } else if (fields[i][1][j][0] === "dropdown") {
            infoBlock.push(<DropdownSection title={fields[i][1][j][1]} service={props.service} IsVisible={props.IsVisible} name={fields[i][1][j][2]} />)
          } else if (fields[i][1][j][0] === "search") {
            infoBlock.push(<DropdownSection title={fields[i][1][j][1]} service={props.service} IsVisible={props.IsVisible} name={fields[i][1][j][2]} />)
          }
          if (j !== fields[i][1].length - 1) {
            infoBlock.push(<LittleBorder />)
          }
        }
      }
    }
    return infoBlock
  }

  const fieldInfo = renderFields()
  return (
    props.IsVisible !== null ? (
      <InfoBlockContainer top={(Math.trunc(props.top) - 130) + "px"} left={(Math.trunc(props.left) + 50) + "px"} background={props.background}>
        {fieldInfo}
      </InfoBlockContainer>
    ) : (<></>)
  )
}

export default InfoBlock