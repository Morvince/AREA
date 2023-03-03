import React, { useState } from 'react'
import Sidebar from '../components/sidebar'
import Navebar from '../components/navbar'
import Doc from '../components/doc'

const Documentation = () => {

  const [isOpen, setIsOpen] = useState(false)

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
