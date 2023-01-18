import React, { useState, useRef } from 'react';
import { Rectangle, Barre} from './puzzleBlockElements';

const Mouvement = (props) => {
  const [position, setPosition] = useState({ x: props.x, y: props.y });
  const ref = useRef(null);

  const handleMouseDown = (e) => {
    const initialX = e.clientX - position.x;
    const initialY = e.clientY - position.y;

    const handleMouseMove = (e) => {
      setPosition({
        x: e.clientX - initialX,
        y: e.clientY - initialY
      });
    }

    window.addEventListener('mousemove', handleMouseMove);

    const handleMouseUp = () => {
      window.removeEventListener('mousemove', handleMouseMove);
      window.removeEventListener('mouseup', handleMouseUp);
    }

    window.addEventListener('mouseup', handleMouseUp);
  }

  return (
    <div ref={ref}>
      <Rectangle x={position.x + "px"} y={position.y + "px"} color={props.color} onMouseDown={handleMouseDown}/>
    </div>
  );
}

const PuzzeBlock = () => {
  return (
    <div>
      <Barre />
      <Mouvement x="300" y="150" color='#686f84'/>
    </div>
  )
}
  export default PuzzeBlock