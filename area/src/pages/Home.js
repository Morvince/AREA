import React, {useState} from 'react'
import Sidebar from '../components/sidebar'
import Navebar from '../components/navbar'
import PuzzleBlock from '../components/puzzleBlock'
import Servicesbar from '../components/servicesbar'

const Home = () => {

  const [isOpen, setIsOpen] = useState(false)

  const toggle = () => {
    setIsOpen(!isOpen)
  }

  return (
    <div>
      <Sidebar isOpen={isOpen} toggle={toggle} />
      <Navebar toggle={toggle} changeY={720} defaultState={false}/>
      <PuzzleBlock/>
      <Servicesbar />
    </div>
  )
}

export default Home
