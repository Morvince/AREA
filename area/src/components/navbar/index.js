import React from 'react'
import { useNavigate } from 'react-router-dom';
import { useAddAutomation } from '../../api/apiServicesPage';
import { NavRectBg, NavTextHapilink, ButtonAreas, ButtonCreate, ButtonDocumentation, ButtonHapilink } from './navbarElements'

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
      <ButtonHapilink to="/" > Hapilink </ButtonHapilink>
      <ButtonCreate onClick={redirect} > Create </ButtonCreate>
      <ButtonAreas to="/areas" > My Areas </ButtonAreas>
      <ButtonDocumentation to="/doc" > Documentation </ButtonDocumentation>
    </>
  )
}

export default Navbar