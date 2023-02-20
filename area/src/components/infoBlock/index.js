import React from 'react'
import { InfoBlockContainer, InfoWrapper, InfoTitle, InfoAction, InputBox } from './infoBlockElements'
import Dropdown from 'react-bootstrap/Dropdown';

const InfoBlock = () => {
  return (
    <InfoBlockContainer>
      <InfoWrapper>
        <InfoTitle>Titre</InfoTitle>
        <InfoAction>
          <InputBox id="name" type="text" placeholder="Name"/>
          <Dropdown>
            <Dropdown.Toggle variant="success" id="dropdown-basic">
              Action
            </Dropdown.Toggle>
            <Dropdown.Menu>
              <Dropdown.Item href="#/action-1">Action</Dropdown.Item>
              <Dropdown.Item href="#/action-2">Another action</Dropdown.Item>
              <Dropdown.Item href="#/action-3">Something else</Dropdown.Item>
            </Dropdown.Menu>
          </Dropdown>
        </InfoAction>
      </InfoWrapper>
    </InfoBlockContainer>
  )
}

export default InfoBlock
