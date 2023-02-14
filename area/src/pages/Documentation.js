import React, { useState } from 'react'
import Sidebar from '../components/sidebar'
import Navebar from '../components/navbar'
import { useLocation } from 'react-router-dom'
import Doc from '../components/doc'

const Documentation = () => {

  const [isOpen, setIsOpen] = useState(false)
  const location = useLocation()

  const toggle = () => {
    setIsOpen(!isOpen)
  }
  return (
    <div>
      <Sidebar isOpen={isOpen} toggle={toggle} />
      <Doc> </Doc>
      <Navebar toggle={toggle} changeY={720} defaultState={false} />
    </div>
  )
}

export default Documentation
