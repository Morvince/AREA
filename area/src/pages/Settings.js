import React, {useState} from 'react'
import Sidebar from '../components/sidebar'
import Navebar from '../components/navbar'
import Settings from '../components/settings'

const SettingsPage = () => {
  const [isOpen, setIsOpen] = useState(false)

  const toggle = () => {
    setIsOpen(!isOpen)
  }

  return (
    <div>
      <Sidebar isOpen={isOpen} toggle={toggle} />
      <Navebar toggle={toggle} changeY={720} defaultState={false}/>
      < Settings />
    </div>
  )
}

export default SettingsPage