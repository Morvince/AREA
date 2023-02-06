import React, { useState } from 'react'
import Draggable from 'react-draggable'
import { RectangleBlock } from './blockElements'


const Block = (props) => {

  const [backgroundColor, setbackgroundColor] = useState(props.color)
  const [pos, setPos] = useState({ x: props.top, y: props.left })

  return (
    <Draggable bounds='parent'>
      <RectangleBlock color={backgroundColor} top={pos.x} left={pos.y}/>
    </Draggable>
  )
}

export default Block
