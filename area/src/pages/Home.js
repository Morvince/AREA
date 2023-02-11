import React, { useState } from 'react'
import Sidebar from '../components/sidebar'
import Navebar from '../components/navbar'
import PlayBox from '../components/playBox'
import { useLocation } from 'react-router-dom'

const Home = () => {

  const [isOpen, setIsOpen] = useState(false)
  const location = useLocation()

  const toggle = () => {
    setIsOpen(!isOpen)
  }
  return (
    <div>
      <Sidebar isOpen={isOpen} toggle={toggle} />
      <PlayBox automationId={location.state.automationId.automation_id} />
      <Navebar toggle={toggle} changeY={720} defaultState={false} />
    </div>
  )
}

export default Home
