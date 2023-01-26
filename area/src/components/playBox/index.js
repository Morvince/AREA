import React from 'react'
import { RectangleArea, MovableBox } from './playBoxElements'
import Servicesbar from '../servicesbar'


const PlayBox = () => {
  return (
    <RectangleArea>
      <Servicesbar />
      <MovableBox />
    </RectangleArea>
  )
}

export default PlayBox
