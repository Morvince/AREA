import React from 'react'
import { RectangleArea, MovableBox, TickButton, ValidateButton } from './playBoxElements'
import Servicesbar from '../servicesbar'
import Block from '../block'
import MyContext from '../Context'
import { useGetUserPlaylist } from '../../api/apiSpotify';
import { useEditAutomation } from '../../api/apiServicesPage';
import { Redirect, useNavigate } from 'react-router-dom';
import { Icon } from '@iconify/react';

const PlayBox = (props) => {
  const [sharedData, setSharedData] = React.useState([]);
  const [ID, setID] = React.useState(0);
  const [linkedList, setLinkedList] = React.useState([]);
  const [playlist, setPlaylist] = React.useState([]);
  const automationId = props.automationId;
  const userPlaylist = useGetUserPlaylist();
  const editAutomation = useEditAutomation();
  const navigate = useNavigate();
  const iconColor = (sharedData).length > 1 ? 'green' : 'red';

  React.useEffect(() => {
    userPlaylist.mutate()
    if (userPlaylist.isSuccess) {
      setPlaylist(userPlaylist.data.data);
    }
  }, [sharedData]);

  function sendAutomation() {
    var actions = [];
    var i = {id: 0, number: 0, informations: {}};

    for (var j = 0; j < linkedList.length; j++) {
      i.id = sharedData[j].dbId;
      i.number = j+1;
      i.informations = sharedData[j].toSend;
      actions.push(i);
      i = {id: 0, number: 0, informations: {}}
    }
    editAutomation.mutate({id: automationId, actions: actions})
    if (editAutomation.isSuccess) {
      navigate('/', { replace: true });
    }
  }

  return (
    <RectangleArea>
      <MyContext.Provider value={{ sharedData, setSharedData, ID, setID, linkedList, linkedList, setLinkedList, playlist, setPlaylist }}>
        <Servicesbar />
        <MovableBox>
          {sharedData.map((info) => {
            return (
              <Block key={info.index} id={info.index} top={info.top} left={info.left} color={info.color} service={info.service} action={info.action} name={info.name} nbrBox={info.nbrBox} />
              )
            })}
        </MovableBox>
        <ValidateButton className={iconColor === 'green' ? 'green' : 'red'} onClick={sendAutomation} disabled={iconColor === 'red'}>
        <Icon icon="material-symbols:playlist-add-check-circle" width="100" color={iconColor} />
      </ValidateButton>
      </MyContext.Provider>
    </RectangleArea>
  )
}

export default PlayBox
