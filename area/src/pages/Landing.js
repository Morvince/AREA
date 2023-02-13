import React, {useState} from 'react'
import Sidebar from '../components/sidebar'
import Landing from '../components/landing'

const LandingPage = () => {
    const [isOpen, setIsOpen] = useState(false)

    const toggle = () => {
        setIsOpen(!isOpen)
    }
    return (
        <div>
            <Sidebar isOpen={isOpen} toggle={toggle} />
            < Landing />
        </div>
    )
}

export default LandingPage