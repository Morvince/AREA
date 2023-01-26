import React, {useState} from 'react'
import Sidebar from '../components/sidebar'
import Navebar from '../components/navbar'
import PlayBox from '../components/playBox'

const Home = () => {

  const [isOpen, setIsOpen] = useState(false)

  const toggle = () => {
    setIsOpen(!isOpen)
  }

  return (
    <div>
      <Sidebar isOpen={isOpen} toggle={toggle} />
      <Navebar toggle={toggle} changeY={720} defaultState={false}/>
      <PlayBox/>
    </div>
  )
}

export default Home
