import React, { useState } from 'react'
import { Icon } from '@iconify/react';
import { ServicesBarContainer, ServicesBarWrapper, IconBox, ServicesName, SwitchSlider } from './servicesbarElements'

const Servicesbar = () => {
  const [isOpen, setIsOpen] = useState(true);

  return (
    <>
      <ServicesBarContainer className={isOpen ? 'open' : 'closed'}>
        <ServicesName>Services</ServicesName>
        <ServicesBarWrapper>
          <IconBox>
            <Icon icon="skill-icons:discord" width="75" height="75"/>
          </IconBox>
          <IconBox>
            <Icon icon="logos:spotify-icon" width="75" height="75"/>
          </IconBox>
          <IconBox>
            <Icon icon="skill-icons:instagram" width="75" height="75"/>
          </IconBox>
          <IconBox>
            <Icon icon="logos:google-icon" width="75" height="75"/>
          </IconBox>
          <IconBox>
            <Icon icon="skill-icons:twitter" width="75" height="75" />
          </IconBox>
          <IconBox>
            <Icon icon="logos:openai-icon" width="75" height="75"/>
          </IconBox>
        </ServicesBarWrapper>
      </ServicesBarContainer>
      <SwitchSlider className={isOpen ? 'open' : 'closed'} onClick={() => setIsOpen(!isOpen)} />
    </>
  )
}

export default Servicesbar