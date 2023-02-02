import React, { useState } from 'react'
import Draggable from 'react-draggable'
import { RectangleBlock } from './blockElements'
import MyContext from '../Context'

const Block = (props) => {
  const [backgroundColor, setbackgroundColor] = useState(props.color)
  const [pos, setPos] = useState({ x: props.top, y: props.left })
  const { sharedData, setSharedData } = React.useContext(MyContext);

  React.useEffect(() => {}, [sharedData]);

  const handleDrag = (e, data) => {
    var rect = e.target.getBoundingClientRect();

    for (var i = 0; i < sharedData.length; i++) {
      // console.log('triggered')
      if (props.id !== sharedData[i].index) {
        console.log('reactangle '+props.id+': ', rect.top, 'rectangle 2: ', sharedData[i].top);
        if (rect.top > sharedData[i].top + 110 && rect.top < sharedData[i].top + 130) {
          setbackgroundColor('red')
        } else {
          setbackgroundColor(props.color)
        }
      }
    }
  }

  const handleDragStop = (e, data) => {
    var rect = e.target.getBoundingClientRect();

    for (var i = 0; i < sharedData.length; i++) {
      if (props.id === sharedData[i].index) {
        sharedData[i].top = rect.top;
        sharedData[i].left = rect.left;
      }
    }
  }

  return (
    <Draggable bounds='parent' onDrag={handleDrag} onStop={handleDragStop}>
      <RectangleBlock color={backgroundColor} top={pos.x} left={pos.y} />
    </Draggable>
  )
}

export default Block