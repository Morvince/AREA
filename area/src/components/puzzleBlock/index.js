import React, { useState, useRef } from 'react';
import Draggable from 'react-draggable';
import { Rectangle } from './puzzleBlockElements';

const PuzzeBlock = (props) => {

  return (
    <Draggable>
      <Rectangle color={props.color} top={props.top} left={props.left}/>
    </Draggable>
  )
}

export default PuzzeBlock
