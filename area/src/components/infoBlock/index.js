import React, { useEffect, useState, useContext } from 'react'
import { InfoBlockContainer, InfoWrapper, InfoTitle, InfoAction, InputBox, LittleBorder } from './infoBlockElements'
import Select from 'react-select'
import MyContext from '../Context'

//  function to handle the dropdown section
const DropdownSection = (props) => {
  const { playlist } = useContext(MyContext);
  const { repository } = useContext(MyContext);
  const { getUserChannels } = useContext(MyContext);
  const { sharedData } = useContext(MyContext);

  const options = useState([])

  // initialisation of the different service's dropdown lists
  if (props.service === "spotify") {
    for (let i = 0; i < playlist.items.length; i++)
      options.push({ value: playlist.items[i].id, label: playlist.items[i].name })
  } else if (props.service === "github") {
    for (let i = 0; i < repository.items.length; i++)
      options.push({ value: repository.items[i].id, label: repository.items[i].name })
  } else if (props.service === "discord") {
    for (let i = 0; i < getUserChannels.items.length; i++)
      options.push({ value: getUserChannels.items[i].id, label: getUserChannels.items[i].name })
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

  //  display the dropdown list
  return (
    <InfoWrapper>
      <InfoTitle>{props.title}</InfoTitle>
      <InfoAction>
        <Select options={options} onChange={onChange} />
      </InfoAction>
    </InfoWrapper>
  )
}

// function that manage the text section
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

  // change infos in sharedData if the current zone text is not empty
  useEffect(() => {
    if (sharedData[props.IsVisible].toSend[props.name] !== undefined) {
      setText(sharedData[props.IsVisible].toSend[props.name])
    }
  }, [sharedData])

  // display the text and tittle infos about the current block
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

  //  function that get the infos from the differents filds in order to display them
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
    //  return if the text is empty 
    if (sharedData.length === 0 || props.IsVisible === null)
      return
    //  fill the infos with the current infos to display
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
    //  display infos if the tab is not empty
    props.IsVisible !== null ? (
      <InfoBlockContainer top={(Math.trunc(props.top) - 130) + "px"} left={(Math.trunc(props.left) + 50) + "px"} background={props.background}>
        {fieldInfo}
      </InfoBlockContainer>
    ) : (<></>)
  )
}

export default InfoBlock