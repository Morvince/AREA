import React, { useState } from 'react'
import Sidebar from '../components/sidebar'
import Navebar from '../components/navbar'
import PlayBox from '../components/playBox'
import { useLocation } from 'react-router-dom'
import { incrementAreasCounter } from '../utils/AreasCounter'

const Home = () => {

  const [isOpen, setIsOpen] = useState(false)
  const location = useLocation()

  const automationId = location.state ? location.state.automationId.automation_id : undefined;
  const [navbarKey, setNavbarKey] = useState(0)

  const toggle = () => {
    setIsOpen(!isOpen)
  }

  const onValidate = () => {
    incrementAreasCounter()
    setNavbarKey(prevKey => prevKey + 1)
  }

  return (
    <div>
      <Sidebar isOpen={isOpen} toggle={toggle} />
      <PlayBox automationId={automationId} onValidate={onValidate} />
      <Navebar toggle={toggle} changeY={720} defaultState={false} key={navbarKey} />
    </div>
  )
}

export default Home
