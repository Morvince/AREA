import React from 'react'
import { RectangleArea, MovableBox, ValidateButton, BinLeft, BinRight, BinWhite, SaveNamePannel, CheckButton, WrittingZone } from './playBoxElements'
import Servicesbar from '../servicesbar'
import Block from '../block'
import MyContext from '../Context'
import { useGetUserPlaylist } from '../../api/apiSpotify';
import { useEditAutomation, useAddAutomation } from '../../api/apiServicesPage';
import { Icon } from '@iconify/react';
import { useState, useRef } from 'react'

const PlayBox = (props) => {
  const [sharedData, setSharedData] = React.useState([]);
  const [ID, setID] = React.useState(0);
  const [linkedList, setLinkedList] = React.useState([]);
  const [playlist, setPlaylist] = React.useState([]);
  const automationId = props.automationId;
  const userPlaylist = useGetUserPlaylist();
  const editAutomation = useEditAutomation();
  const addAutomation = useAddAutomation();
  const { onValidate } = props;
  const [isLinkedListEmpty, setIsLinkedListEmpty] = React.useState(true);
  const contentEditableRef = useRef();
  const [showSaveNamePanel, setShowSaveNamePanel] = React.useState(false);
  const [inputValue, setInputValue] = useState('');

  const handleCheckButtonClick = () => {
    const name = contentEditableRef.current.textContent.trim();
    setShowSaveNamePanel(false);
    sendAutomation(name);
  };

  const handleInput = (event) => {
    const value = event.target.textContent.trim();
    if (value.length <= 15) {
      setInputValue(value);
    } else {
      contentEditableRef.current.textContent = inputValue;
    }
  };

  React.useEffect(() => {
    if (sharedData.length > 1) {
      setIsLinkedListEmpty(false);
    } else {
      setIsLinkedListEmpty(true);
    }
    userPlaylist.mutate()
    if (userPlaylist.isSuccess) {
      setPlaylist(userPlaylist.data.data);
    }
  }, [sharedData, linkedList]);

  function sendAutomation(name) {
    var actions = [];
    var i = { id: 0, number: 0, informations: {} };

    onValidate();
    for (var j = 0; j < linkedList.length; j++) {
      i.id = sharedData[j].dbId;
      i.number = j + 1;
      i.informations = sharedData[j].toSend;
      actions.push(i);
      i = { id: 0, number: 0, informations: {} }
    }
    if (automationId === undefined) {
      addAutomation.mutate({name: name, actions: actions});
    } else {
      editAutomation.mutate({ name: name, id: automationId, actions: actions });
    }
    setSharedData([]);
    setLinkedList([]);
  }

  return (
    <RectangleArea>
      <BinLeft> </BinLeft>
      <MyContext.Provider value={{ sharedData, setSharedData, ID, setID, linkedList, linkedList, setLinkedList, playlist, setPlaylist }}>
        <Servicesbar />
        <MovableBox>
          {sharedData.map((info) => {
            return (
              <Block key={info.index} id={info.index} top={info.top} left={info.left} color={info.color} service={info.service} action={info.action} name={info.name} nbrBox={info.nbrBox} />
            )
          })}
        </MovableBox>
        <Icon icon="mdi:delete-circle-outline" color="#373b48" width="40" style={{ position: 'absolute', top: '20%', left: '80.3%' }} />
        <ValidateButton className={isLinkedListEmpty === false ? 'green' : 'red'} onClick={() => setShowSaveNamePanel(true)} disabled={isLinkedListEmpty === true}>
          <Icon icon="material-symbols:playlist-add-check-circle" width="100" color={isLinkedListEmpty === false ? 'green' : 'red'} />
        </ValidateButton>
      </MyContext.Provider>
      <BinRight></BinRight>
      <BinWhite></BinWhite>
      {showSaveNamePanel && (
      <SaveNamePannel>
        NAME :
        <WrittingZone ref={contentEditableRef} contentEditable={true} suppressContentEditableWarning={true} onInput={handleInput} />
        <CheckButton onClick={() => { setShowSaveNamePanel(false); handleCheckButtonClick(); }}>
          <Icon icon="material-symbols:check-small" width="85" color="white" style={{ position: "absolute", left: "0%", top: "-10%" }} />
        </CheckButton>
      </SaveNamePannel>
    )}
    </RectangleArea>
  )
}

export default PlayBox
