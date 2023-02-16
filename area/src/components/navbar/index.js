import React from 'react'
import { useNavigate } from 'react-router-dom';
import { useAddAutomation } from '../../api/apiServicesPage';
import { NavRectBg, ButtonAreas, ButtonCreate, ButtonDocumentation, ButtonHapilink, NewAreas } from './navbarElements'
import { getAreasCounter, resetAreasCounter } from '../../utils/AreasCounter'

const Navbar = ({ toggle, changeY, defaultState }) => {
  const navigate = useNavigate();

  const tmpAutomation = useAddAutomation();

  function redirect() {
      tmpAutomation.mutate();
  }

  if (tmpAutomation.isSuccess) {
      navigate("/home", { replace: true, state: { automationId: tmpAutomation.data.data } })
  }

  return (
    <>
      <NavRectBg> </NavRectBg>
      {getAreasCounter() > 0 ? <NewAreas> {getAreasCounter()} </NewAreas> : null}
      <ButtonHapilink to="/" > Hapilink </ButtonHapilink>
      <ButtonCreate onClick={redirect} > Create </ButtonCreate>
      <ButtonAreas to="/areas" onClick={resetAreasCounter}> My Areas </ButtonAreas>
      <ButtonDocumentation to="/doc" > Documentation </ButtonDocumentation>
    </>
  )
}

export default Navbar