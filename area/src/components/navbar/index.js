import React from 'react'
import { NavRectBg, ButtonAreas, ButtonCreate, ButtonDocumentation, ButtonHapilink, NewAreas } from './navbarElements'
import { getAreasCounter, resetAreasCounter } from '../../utils/AreasCounter'

// function that create and display the navbar of the site with his different buttons to navigate on the site
const Navbar = ({ toggle, changeY, defaultState }) => {

  return (
    <>
      <NavRectBg></NavRectBg>
      {/*  draw a popup if we have a new area */}
      {getAreasCounter() > 0 ? <NewAreas> {getAreasCounter()} </NewAreas> : null}
      {/* button to redirect on the principal page */}
      <ButtonHapilink to="/" > Hapilink </ButtonHapilink>
      {/*  button to redirect to the main page where you can create your areas */}
      <ButtonCreate to="/home" > Create </ButtonCreate>
      {/* button to redirect the user in a page where he will modify his previous areas or delete it */}
      <ButtonAreas to="/areas" onClick={resetAreasCounter}> My Areas </ButtonAreas>
      {/* button to redirect to the documentation page where there is a list of the different action and reactions for each service */}
      <ButtonDocumentation to="/doc" > Documentation </ButtonDocumentation>
    </>
  )
}

export default Navbar