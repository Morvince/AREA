import React from 'react'
import { useState, useEffect } from 'react'
import { Icon } from '@iconify/react';
import { RectangleArea, MovableBox, ValidateButton, BinLeft, BinRight, BinWhite } from './playBoxElements'
import Servicesbar from '../servicesbar'
import Block from '../block'
import MyContext from '../Context'
import InfoBlock from '../infoBlock'
import { useEditAutomation } from '../../api/apiServicesPage';
import { useGetAction } from '../../api/apiServicesPage';

const PlayBox = (props) => {
  const [sharedData, setSharedData] = useState([]);
  const [linkedList, setLinkedList] = useState([]);
  const [action, setaction] = useState([]);
  const [open, setOpen] = useState(null);
  const [ID, setID] = useState(0);
  const [isLinkedListEmpty, setIsLinkedListEmpty] = React.useState(true);
  const { onValidate } = props;
  const automationId = props.automationId;
  const editAutomation = useEditAutomation();
  const tmpServices = useGetAction();

  //request to get all services
  useEffect(() => {
    tmpServices.mutate()
    if (tmpServices.isSuccess) {
      setaction(tmpServices.data.data);
    }
  }, []);

  useEffect(() => {
    if (sharedData.length > 1) {
      setIsLinkedListEmpty(false);
    } else {
      setIsLinkedListEmpty(true);
    }
  }, [sharedData, linkedList]);

  function sendAutomation() {
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
    editAutomation.mutate({ id: automationId, actions: actions })
    setSharedData([]);
    setLinkedList([]);
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
              <Block key={info.index} id={info.index} top={info.top} left={info.left} color={info.color} service={info.service} action={info.action} name={info.name} nbrBox={info.nbrBox} />
            )
          })}
        </MovableBox>
        <ValidateButton className={isLinkedListEmpty === false ? 'green' : 'red'} onClick={sendAutomation} disabled={isLinkedListEmpty === true}>
          <Icon icon="material-symbols:playlist-add-check-circle" width="100" color={isLinkedListEmpty === false ? 'green' : 'red'} />
        </ValidateButton>
        {tmpServices.isSuccess &&
          <InfoBlock IsVisible={open} top={sharedData[open]?.top} left={sharedData[open]?.left} background={sharedData[open]?.color} action={tmpServices.data.data.actions} />
        }
      </MyContext.Provider>
      <BinRight />
      <BinWhite />
    </RectangleArea >
  )
}

export default PlayBox