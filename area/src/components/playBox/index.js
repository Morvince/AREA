import React from 'react'
import { RectangleArea, MovableBox } from './playBoxElements'
import Servicesbar from '../servicesbar'
import Block from '../block'
import MyContext from '../Context'

const PlayBox = () => {
  const [sharedData, setSharedData] = React.useState([]);

  React.useEffect(() => {
    console.log('Shared data updated: ', sharedData);
  }, [sharedData]);

  return (
    <RectangleArea>
      <MyContext.Provider value={{ sharedData, setSharedData }}>
        <Servicesbar />
        <MovableBox>
          {sharedData.map((info) => {
            return (
              <Block key={info.index} id={info.index} top={info.top} left={info.left} color={info.color} service={info.service} />
            )
          })}
        </MovableBox>
      </MyContext.Provider>
    </RectangleArea>
  )
}
export default PlayBox