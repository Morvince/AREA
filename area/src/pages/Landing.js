import React, {useState} from 'react'
import Navebar from '../components/navbar'

const Landing = () => {
    const [isOpen, setIsOpen] = useState(false)

    const toggle = () => {
        setIsOpen(!isOpen)
    }

    return (
        <div>
            <Navebar toggle={toggle} changeY={720} defaultState={false}/>
        </div>
    )
}

export default Landing 