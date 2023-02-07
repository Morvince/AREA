import React from 'react'
import { RectangleArea, MovableBox, TickButton } from './playBoxElements'
import Servicesbar from '../servicesbar'
import Block from '../block'
import MyContext from '../Context'

const PlayBox = (props) => {
  const [sharedData, setSharedData] = React.useState([]);
  const [ID, setID] = React.useState(0);
  const [linkedList, setLinkedList] = React.useState([]);
  const automationId = props.automationId;

  React.useEffect(() => {
  }, [sharedData]);

  console.log("ID = ", automationId)

  return (
    <RectangleArea>
      <MyContext.Provider value={{ sharedData, setSharedData, ID, setID, linkedList, linkedList, setLinkedList }}>
        <Servicesbar />
        <MovableBox>
          {sharedData.map((info) => {
            return (
              <Block key={info.index} id={info.index} top={info.top} left={info.left} color={info.color} service={info.service} action={info.action} name={info.name} nbrBox={info.nbrBox} />
            )
          })}
        </MovableBox>
        <TickButton />
      </MyContext.Provider>
    </RectangleArea>
  )
}

export default PlayBox
