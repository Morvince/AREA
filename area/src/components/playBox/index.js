import React from 'react'
import { RectangleArea, MovableBox, TickButton } from './playBoxElements'
import Servicesbar from '../servicesbar'
import Block from '../block'
import MyContext from '../Context'
import { useGetUserPlaylist } from '../../api/apiSpotify';

const PlayBox = (props) => {
  const [sharedData, setSharedData] = React.useState([]);
  const [ID, setID] = React.useState(0);
  const [linkedList, setLinkedList] = React.useState([]);
  const automationId = props.automationId;
  const userPlaylist = useGetUserPlaylist();
  const [playlist, setPlaylist] = React.useState([]);

  React.useEffect(() => {
    userPlaylist.mutate()
    if (userPlaylist.isSuccess) {
      setPlaylist(userPlaylist.data.data);
      console.log(playlist)
    }
  }, [sharedData]);

  function sendAutomation() {
    // POST -> automation_id : INT
    //              actions : array(
    //                     [i] => array(
    //                                 id => INT,
    //                                 number => INT, (ordre dans la liste)
    //                                 informations => array() : exemple = "name" : String, "playlist_id" : String, ...
    //                             );
    //                 );
    //create this array
    var actions = [];
    var i = [];
    //iterate over the linked list
    for (var j = 0; j < linkedList.length; j++) {
      i.push(sharedData[j].dbId);
      i.push(j);
      i.push(sharedData[j].toSend);
      actions.push(i);
      i = [];
    }
    console.log({
      automation_id: automationId,
      actions: actions
    });
  }

  return (
    <RectangleArea>
      <MyContext.Provider value={{ sharedData, setSharedData, ID, setID, linkedList, linkedList, setLinkedList, playlist, setPlaylist }}>
        <Servicesbar />
        <button onClick={sendAutomation}>Validate</button>
        <MovableBox>
          {sharedData.map((info) => {
            return (
              <Block key={info.index} id={info.index} top={info.top} left={info.left} color={info.color} service={info.service} action={info.action} name={info.name} nbrBox={info.nbrBox}/>
            )
          })}
        </MovableBox>
      </MyContext.Provider>
    </RectangleArea>
  )
}

export default PlayBox
