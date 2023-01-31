import React, { useState } from 'react';
import Draggable from 'react-draggable';
import { Rectangle } from './buttonBlockElements';

const ButtonBox = (props) => {
  const [backgroundColor, setbackgroundColor] = useState(props.color)
  const [pos, setPos] = useState({ x: props.top, y: props.left })

  const handleDrag = (e, data) => {
    console.log(props.top, props.left)
  }

  const handleDragStop = (e, data) => {
    if (data.x < 300) {
      setPos({ x: props.top, y: props.left });
      setbackgroundColor(props.color)
      setbackgroundColor(props.color)
    } else {
      setbackgroundColor("red")
    }
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