import React from 'react'
import { RectangleArea, MovableBox } from './playBoxElements'
import Servicesbar from '../servicesbar'
import Block from '../block'

const PlayBox = () => {
  return (
    <RectangleArea>
      <Servicesbar />
      <MovableBox>
        <Block color="#FF0000" top="0" left="0" />
      </MovableBox>
    </RectangleArea>
  )
}

export default PlayBox
