import React from 'react'
import styled from 'styled-components'

const Icon = ({className, inputPasswd}) => {
  function seePassword() {
    inputPasswd.current.type === "password" ? inputPasswd.current.type = "text" : inputPasswd.current.type = "password"
  }
  return (
    <i className={className} onClick={seePassword}>icon</i>
  )
}

const PasswordIcon = styled(Icon)`
  position: absolute;
  left: 78%;
  top: 55%;
  &:hover {
    cursor: pointer;
  }
`;

export default PasswordIcon
