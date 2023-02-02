import React from 'react'
import { RectangleArea, MovableBox } from './playBoxElements'
import Servicesbar from '../servicesbar'
import Block from '../block'
import MyContext from '../Context'

const PlayBox = () => {
  const [sharedData, setSharedData] = React.useState([]);
  const [ID, setID] = React.useState(0);

  React.useEffect(() => {}, [sharedData]);

  return (
    <RectangleArea>
      <MyContext.Provider value={{ sharedData, setSharedData, ID, setID}}>
        <Servicesbar />
        <MovableBox>
          {sharedData.map((info) => {
            return (
              <Block key={info.index} id={info.index} top={info.top} left={info.left} color={info.color} service={info.service}/>
            )
          })}
        </MovableBox>
      </MyContext.Provider>
    </RectangleArea>
  )
}
export default PlayBox