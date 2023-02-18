import React, {useState} from 'react'
import Sidebar from '../components/sidebar'
import Navebar from '../components/navbar'
import Landing from '../components/landing'

const LandingPage = () => {
    const [isOpen, setIsOpen] = useState(false)

    const toggle = () => {
        setIsOpen(!isOpen)
    }
    return (
      <div>
        <Sidebar isOpen={isOpen} toggle={toggle} />
        <Navebar toggle={toggle} changeY={720} defaultState={false} />
        < Landing />
      </div>
    )
}

export default LandingPage