import React from 'react'
import { RectangleArea, MovableBox, ValidateButton, BinLeft, BinRight, BinWhite } from './playBoxElements'
import Servicesbar from '../servicesbar'
import Block from '../block'
import MyContext from '../Context'
import { useGetUserPlaylist } from '../../api/apiSpotify';
import { useEditAutomation } from '../../api/apiServicesPage';
import { Icon } from '@iconify/react';

const PlayBox = (props) => {
  const [sharedData, setSharedData] = React.useState([]);
  const [ID, setID] = React.useState(0);
  const [linkedList, setLinkedList] = React.useState([]);
  const [playlist, setPlaylist] = React.useState([]);
  const automationId = props.automationId;
  const userPlaylist = useGetUserPlaylist();
  const editAutomation = useEditAutomation();
  const { onValidate } = props;
  const [isLinkedListEmpty, setIsLinkedListEmpty] = React.useState(true);

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
      </MyContext.Provider>
      <BinRight></BinRight>
      <BinWhite></BinWhite>
      <Icon icon="mdi:delete-circle-outline" color="#373b48" width="40" style={{ position: 'absolute', top: '20%', left: '80.3%' }} />
      <ValidateButton className={isLinkedListEmpty === false ? 'green' : 'red'} onClick={sendAutomation} disabled={isLinkedListEmpty === true}>
        <Icon icon="material-symbols:playlist-add-check-circle" width="100" color={isLinkedListEmpty === false ? 'green' : 'red'} />
      </ValidateButton>
    </RectangleArea>
  )
}

export default PlayBox
