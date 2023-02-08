import React from 'react'
import { RectangleArea, MovableBox, TickButton } from './playBoxElements'
import Servicesbar from '../servicesbar'
import Block from '../block'
import MyContext from '../Context'
import { useGetUserPlaylist } from '../../api/apiSpotify';
import { useEditAutomation } from '../../api/apiServicesPage';

const PlayBox = (props) => {
  const [sharedData, setSharedData] = React.useState([]);
  const [ID, setID] = React.useState(0);
  const [linkedList, setLinkedList] = React.useState([]);
  const [playlist, setPlaylist] = React.useState([]);
  const automationId = props.automationId;
  const userPlaylist = useGetUserPlaylist();
  const editAutomation = useEditAutomation();

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
  }

  return (
    <RectangleArea>
      <MyContext.Provider value={{ sharedData, setSharedData, ID, setID, linkedList, linkedList, setLinkedList, playlist, setPlaylist }}>
        <Servicesbar />
        <button onClick={sendAutomation}>Validate</button>
        <MovableBox>
          {sharedData.map((info) => {
            return (
              <Block key={info.index} id={info.index} top={info.top} left={info.left} color={info.color} service={info.service} action={info.action} name={info.name} nbrBox={info.nbrBox} />
            )
          })}
        </MovableBox>
      </MyContext.Provider>
    </RectangleArea>
  )
}

export default PlayBox
