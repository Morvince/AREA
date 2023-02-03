import React, { useState } from 'react';
import Draggable from 'react-draggable';
import { Rectangle } from './buttonBlockElements';
import MyContext from '../Context';

const ButtonBox = (props) => {
  const [backgroundColor, setbackgroundColor] = useState(props.color)
  const [pos, setPos] = useState({ x: props.top, y: props.left })
  const { sharedData, setSharedData } = React.useContext(MyContext);
  const { ID, setID } = React.useContext(MyContext);

  const handleDrag = (e, data) => {
    if (data.x > 300) {
      setbackgroundColor('#f5f5f5')
    } else {
      setbackgroundColor(props.color)
    }
  }

  const handleDragStop = (e, data) => {
    if (data.x < 300) {
      setPos({ x: props.top, y: props.left });
    } else {
      if (props.action === true) {
        for (var i = 0; i < sharedData.length; i++) {
          if (sharedData[i].action === true) {
            setbackgroundColor(props.color)
            return;
          }
        }
      }
      setSharedData(sharedData => [...sharedData, { service: props.service, index: ID, color: props.color, top: props.top, left: props.left + 500, action: props.action }])
      setID(ID + 1);
      console.log('ID: ', ID)
    }
    setbackgroundColor(props.color)
  }

  return (
    <Draggable
      onDrag={handleDrag}
      onStop={handleDragStop}
      position={{ x: 0, y: 0 }}
    >
      <Rectangle color={backgroundColor} top={pos.x} left={pos.y} />
    </Draggable>
  )
}

export default ButtonBox