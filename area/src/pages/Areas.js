import React, { useState } from 'react'
import Sidebar from '../components/sidebar'
import Navebar from '../components/navbar'
import { useLocation } from 'react-router-dom'
import EditAreas from '../components/areas'

const Areas = () => {

  const [isOpen, setIsOpen] = useState(false)

  const toggle = () => {
    setIsOpen(!isOpen)
  }
  return (
    <div>
      <Sidebar isOpen={isOpen} toggle={toggle} />
      <EditAreas> </EditAreas>
      <Navebar toggle={toggle} changeY={720} defaultState={false} />
    </div>
  )
}

export default Areas
