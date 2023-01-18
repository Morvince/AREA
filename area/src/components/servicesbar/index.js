import React from 'react'
import { Icon } from '@iconify/react';
import { ServicesBarContainer, ServicesBarWrapper, IconBox, ServicesName } from './servicesbarElements'

const Servicesbar = () => {
  return (
    <>
      <ServicesBarContainer>
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
    </>
  )
}

export default Servicesbar
