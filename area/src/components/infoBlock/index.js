import React, { useEffect, useState, useContext } from 'react'
import { InfoBlockContainer, InfoWrapper, InfoTitle, InfoAction, InputBox, LittleBorder } from './infoBlockElements'
import Select from 'react-select'
import Draggable from 'react-draggable'
import MyContext from '../Context'

//import request
import { useGetUserPlaylist } from '../../api/apiSpotify';

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
  const [name, setName] = useState('');
  const [desc, setDesc] = useState('');
  const [infoPrint, setInfoPrint] = useState([]);
  const { sharedData } = useContext(MyContext);

  const handleChangeDesc = (event) => {
    setName(event.target.value);
    // sharedData[props.id].toSend = { name: event.target.value, desc: desc, playlist_id: playlist.playlists[0].id }
  };

  const handleChangeName = (event) => {
    setDesc(event.target.value);
    // sharedData[props.id].toSend = { name: event.target.value, desc: desc, playlist_id: playlist.playlists[0].id }
  };

  //request
  // const [playlist, setPlaylist] = useState([]);
  // const userPlaylist = useGetUserPlaylist();

  const [fields, setFields] = useState([])

  function getFields() {
    let fields = []
    for (let i = 0; i < props.action.length; i++) {
      fields.push([props.action[i].id, []])
      if (props.action[i].fields !== null) {
        for (let j = 0; j < props.action[i].fields.length; j++) {
          fields[i][1].push([])
          fields[i][1][j].push(props.action[i].fields[j].type)
          fields[i][1][j].push("")
        }
      }
    }
    setFields(fields)
  }

  useEffect(() => {
    // userPlaylist.mutate()
    // if (userPlaylist.isSuccess) {
      // setPlaylist(userPlaylist.data.data);
    // }
    getFields()
  }, [props.IsVisible])

  //for each field return a TextSection or DropdownSection
  function renderFields() {
    let infoBlock = []
    if (sharedData.length === 0 || props.IsVisible === null) {
      return
    }
    for (let i = 0; i < fields.length; i++) {
      if (sharedData[props.IsVisible].dbId === fields[i][0]) {
        for (let j = 0; j < fields[i][1].length; j++) {
          if (fields[i][1][j][0] === "text") {
            infoBlock.push(<TextSection title={fields[i][1][j][1]} text={fields[i][1][j][2]} />)
          } else if (fields[i][1][j][0] === "dropdown") {
            infoBlock.push(<DropdownSection title={fields[i][1][j][1]} />)
          } else if (fields[i][1][j][0] === "search") {
            infoBlock.push(<DropdownSection title={fields[i][1][j][1]} />)
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
      <Draggable>
        <InfoBlockContainer top={(Math.trunc(props.top) - 130) + "px"} left={(Math.trunc(props.left) + 50) + "px"} background={props.background}>
          {fieldInfo}
        </InfoBlockContainer>
      </Draggable>
    ) : (<></>)
  )
}

export default InfoBlock
