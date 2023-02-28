import React from 'react'
import { NavRectBg, ButtonAreas, ButtonCreate, ButtonDocumentation, ButtonHapilink, NewAreas } from './navbarElements'
import { getAreasCounter, resetAreasCounter } from '../../utils/AreasCounter'

const Navbar = ({ toggle, changeY, defaultState }) => {

  return (
    <>
      <NavRectBg></NavRectBg>
      {getAreasCounter() > 0 ? <NewAreas> {getAreasCounter()} </NewAreas> : null}
      <ButtonHapilink to="/" > Hapilink </ButtonHapilink>
      <ButtonCreate to="/home" > Create </ButtonCreate>
      <ButtonAreas to="/areas" onClick={resetAreasCounter}> My Areas </ButtonAreas>
      <ButtonDocumentation to="/doc" > Documentation </ButtonDocumentation>
    </>
  )
}

export default Navbar