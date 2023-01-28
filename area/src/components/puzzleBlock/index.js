import React, { useState, useRef } from 'react';
import Draggable from 'react-draggable';
import { Rectangle } from './puzzleBlockElements';

const PuzzeBlock = (props) => {

  return (
    <Draggable>
      <Rectangle color={props.color}/>
    </Draggable>
  )
}

export default PuzzeBlock
