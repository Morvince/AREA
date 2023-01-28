import React, { useState, useEffect } from 'react';
import Draggable from 'react-draggable';
import { Rectangle } from './puzzleBlockElements';

const PuzzeBlock = (props) => {

  const [position, setPosition] = useState({ x: props.left, y: props.top });
  const [isMagnet, setIsMagnet] = useState(false);

  useEffect(() => {
    checkForMagnet();
  }, [position]);

  const checkForMagnet = () => {
    // Check if the distance between this rectangle and other rectangles is less than a certain distance
    // If it is, set isMagnet to true and update the position of this rectangle
  }

  const handleStop = (e, data) => {
    setPosition({ x: data.x, y: data.y });
  }

  const magnetStyles = isMagnet ? { transform: `translate(${position.x}px, ${position.y}px)` } : {};

  return (
    <Draggable /* onStop={handleStop} */>
      <Rectangle color={props.color} top={props.top} left={props.left}/>
    </Draggable>
  )
}

export default PuzzeBlock
