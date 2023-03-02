import React from 'react'
import { useState, useEffect, useRef } from 'react'
import { Icon } from '@iconify/react';
import Servicesbar from '../servicesbar'
import Block from '../block'
import MyContext from '../Context'
import InfoBlock from '../infoBlock'
import { useGetAction } from '../../api/apiServicesPage';
import { RectangleArea, MovableBox, ValidateButton, BinLeft, BinRight, BinWhite, SaveNamePannel, CheckButton, WrittingZone } from './playBoxElements'
import { useEditAutomation, useAddAutomation } from '../../api/apiServicesPage';

const PlayBox = (props) => {
  const [sharedData, setSharedData] = useState([]);
  const [linkedList, setLinkedList] = useState([]);
  const [dataTab, setDataTab] = useState(props.automationActions);
  const { onValidate } = props;
  const automationId = props.automationId;
  const [open, setOpen] = useState(null);
  const [ID, setID] = useState(0);
  const [isLinkedListEmpty, setIsLinkedListEmpty] = useState(true);
  const isComingFromEdit = automationId === undefined ? false : true;
  const editAutomation = useEditAutomation();
  const tmpServices = useGetAction();
  const addAutomation = useAddAutomation();
  const contentEditableRef = useRef();
  const [showSaveNamePanel, setShowSaveNamePanel] = useState(false);
  const [inputValue, setInputValue] = useState('');

  function getColorPuzzleBlock(string) {
    switch (string) {
      case "discord":
        return "#5470d6";
      case "spotify":
        return "#10a143";
      case "twitch":
        return "#c2134f";
      case "gmail":
        return "#d92516";
      case "twitter":
        return "#1486cc";
      case "github":
        return "#686f84";
      default:
        return "#454b5e";
    }
  }

  useEffect(() => {
    tmpServices.mutate()

    if (isComingFromEdit === true) {
      console.log("passed in the if ");
      for (let i = 0; i !== dataTab.automation_actions.length; i++) {
        const newAction = {
          top: 150 + (170 * i),
          left: 600,
          color: getColorPuzzleBlock(dataTab.automation_actions[i].service),
          service: dataTab.automation_actions[i].service,
          index: i,
          action: dataTab.automation_actions[i].type === "action" ? true : false,
          name: dataTab.automation_actions[i].name,
          dbID: dataTab.automation_actions[i].id,
        };
        sharedData[i] = newAction;
      };
  
      console.log("sharedata : ");
      console.log(sharedData);
    }
  }, []);

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

  useEffect(() => {
    if (sharedData.length > 1) {
      setIsLinkedListEmpty(false);
    } else {
      setIsLinkedListEmpty(true);
    }
  }, [sharedData, linkedList]);

  function sendAutomation(name) {
    console.log("click on button");
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
      addAutomation.mutate({ name: name, actions: actions });
    } else {
      editAutomation.mutate({ name: name, id: automationId, actions: actions });
    }
    setSharedData([]);
    setLinkedList([]);
    setID(0);
  }

  return (
    <RectangleArea>
      <BinLeft />
      <MyContext.Provider value={{ sharedData, setSharedData, ID, setID, linkedList, linkedList, setLinkedList, open, setOpen }}>
        <Icon icon="mdi:delete-circle-outline" color="#373b48" width="40" style={{ position: 'absolute', top: '20%', left: '80.3%' }} />
        <Servicesbar tmpServices={tmpServices} />
        <MovableBox>
          {sharedData.map((info) => {
            return (
              <Block key={info.index} id={info.index} top={info.top} left={info.left} color={info.color} service={info.service} action={info.action} name={info.name} />
            )
          })}
        </MovableBox>
        <ValidateButton className={isLinkedListEmpty === false ? 'green' : 'red'} onClick={() => {
          if (isComingFromEdit === false) {
            setOpen(null);
            setShowSaveNamePanel(true);
          } else {
            sendAutomation(dataTab.name);
          }
        }} disabled={isLinkedListEmpty === true} >
          <Icon icon="material-symbols:playlist-add-check-circle" width="100" color={isLinkedListEmpty === false ? 'green' : 'red'} />
        </ValidateButton>
        {tmpServices.isSuccess &&
          <InfoBlock IsVisible={open} top={sharedData[open]?.top} left={sharedData[open]?.left} background={sharedData[open]?.color} action={tmpServices.data.data.actions} />
        }
      </MyContext.Provider>
      <BinRight />
      <BinWhite />
      {showSaveNamePanel && (
        <SaveNamePannel>
          NAME :
          <WrittingZone ref={contentEditableRef} contentEditable={true} suppressContentEditableWarning={true} onInput={handleInput} />
          <CheckButton onClick={() => { setShowSaveNamePanel(false); handleCheckButtonClick(); }}>
            <Icon icon="material-symbols:check-small" width="85" color="white" style={{ position: "absolute", left: "0%", top: "-10%" }} />
          </CheckButton>
        </SaveNamePannel>
      )}
    </RectangleArea >
  )
}

export default PlayBox