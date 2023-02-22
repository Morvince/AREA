import React from 'react'
import { useState, useEffect } from 'react'
import { RectangleArea, MovableBox, ValidateButton, BinLeft, BinRight, BinWhite } from './playBoxElements'
import Servicesbar from '../servicesbar'
import Block from '../block'
import MyContext from '../Context'
import InfoBlock from '../infoBlock'
import { useGetUserPlaylist } from '../../api/apiSpotify';
import { useEditAutomation } from '../../api/apiServicesPage';
import { Icon } from '@iconify/react';

const PlayBox = (props) => {
  const [sharedData, setSharedData] = useState([]);
  const [linkedList, setLinkedList] = useState([]);
  const [playlist, setPlaylist] = useState([]);
  const [open, setOpen] = useState(null);
  const [ID, setID] = useState(0);
  const automationId = props.automationId;
  const userPlaylist = useGetUserPlaylist();
  const editAutomation = useEditAutomation();
  const iconColor = (sharedData).length > 1 ? 'green' : 'red';
  const { onValidate } = props;

  useEffect(() => {
    userPlaylist.mutate()
    if (userPlaylist.isSuccess) {
      setPlaylist(userPlaylist.data.data);
    }
  }, [sharedData]);

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
  }

  function getTop(id) {
    for (var i = 0; i < sharedData.length; i++) {
      if (id === sharedData[i].index)
        return ((sharedData[i].top-130)+"px")
    }
  }

  function getLeft(id) {
    for (var i = 0; i < sharedData.length; i++) {
      if (id === sharedData[i].index)
        console.log(sharedData[i])
        return ((sharedData[i].left-100)+"px")
    }
  }

  return (
    <RectangleArea>
      <BinLeft />
      <MyContext.Provider value={{ sharedData, setSharedData, ID, setID, linkedList, linkedList, setLinkedList, playlist, setPlaylist, open, setOpen }}>
        <Servicesbar />
        <MovableBox>
        <InfoBlock IsVisible={open} top={getTop(open)} left={getLeft(open)} />
          {sharedData.map((info) => {
            return (
              <Block key={info.index} id={info.index} top={info.top} left={info.left} color={info.color} service={info.service} action={info.action} name={info.name} nbrBox={info.nbrBox} />
            )
          })}
        </MovableBox>
      </MyContext.Provider>
      <BinRight />
      <BinWhite />
      <Icon icon="mdi:delete-circle-outline" color="#373b48" width="40" style={{ position: 'absolute', top: '20%', left: '80.3%' }} />
      <ValidateButton className={iconColor === 'green' ? 'green' : 'red'} onClick={sendAutomation} disabled={iconColor === 'red'}>
        <Icon icon="material-symbols:playlist-add-check-circle" width="100" color={iconColor} />
      </ValidateButton>
    </RectangleArea>
  )
}

export default PlayBox
