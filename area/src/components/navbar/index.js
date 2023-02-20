import React from 'react'
import { Navigate, useLocation } from 'react-router-dom';
import { useAddAutomation } from '../../api/apiServicesPage';
import { NavRectBg, ButtonAreas, ButtonCreate, ButtonDocumentation, ButtonHapilink, NewAreas } from './navbarElements'
import { getAreasCounter, resetAreasCounter } from '../../utils/AreasCounter'

const Navbar = ({ toggle, changeY, defaultState }) => {

  const location = useLocation()
  const tmpAutomation = useAddAutomation();
  function redirect(event) {
      event.preventDefault()
      if (location.pathname === "/home")
        return
      tmpAutomation.mutate();
  }

  if (tmpAutomation.isSuccess) {
    return (
      <Navigate to="/home" state={{automationId: tmpAutomation.data.data}}/>
    )
  } else if (tmpAutomation.isError) {
    return (
      <Navigate to="/login"/>
    )
  }

  return (
    <>
      <NavRectBg></NavRectBg>
      {getAreasCounter() > 0 ? <NewAreas> {getAreasCounter()} </NewAreas> : null}
      <ButtonHapilink to="/" > Hapilink </ButtonHapilink>
      <ButtonCreate onClick={redirect} > Create </ButtonCreate>
      <ButtonAreas to="/areas" onClick={resetAreasCounter}> My Areas </ButtonAreas>
      <ButtonDocumentation to="/doc" > Documentation </ButtonDocumentation>
    </>
  )
}

export default Navbar