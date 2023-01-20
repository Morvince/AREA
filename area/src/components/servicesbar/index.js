import React, { useState } from 'react';
import { Icon } from '@iconify/react';
import { ServicesBarContainer, ServicesBarWrapper, IconBox, ServicesName, SwitchSlider, RightBox } from './servicesbarElements';

const Servicesbar = () => {
  const [isOpen, setIsOpen] = useState(true);
  const [isRightBoxOpen, setIsRightBoxOpen] = useState(false);
  const [selectedService, setSelectedService] = useState(null);

  function handleClick(service) {
    if(!isRightBoxOpen || selectedService !== service) {
      setIsRightBoxOpen(true);
      setSelectedService(service);
    } else {
      setIsRightBoxOpen(false);
    }
  }

  function getColor() {
    switch(selectedService) {
      case "discord":
        return "#7289da";
      case "spotify":
        return "#1db954";
      case "instagram":
        return "#e1306c";
      case "google":
        return "#4285f4";
      case "twitter":
        return "#1da1f2";
      case "openai":
        return "#f8b429";
      default:
        return "#686f84";
    }
  }

  return (
    <>
      <ServicesBarContainer className={isOpen ? 'open' : 'closed'}>
        <ServicesName>Services</ServicesName>
        <ServicesBarWrapper>
          <IconBox onClick={() => handleClick("discord")}>
            <Icon icon="skill-icons:discord" width="75" height="75"/>
          </IconBox>
          <IconBox onClick={() => handleClick("spotify")}>
            <Icon icon="logos:spotify-icon" width="75" height="75"/>
          </IconBox>
          <IconBox onClick={() => handleClick("instagram")}>
            <Icon icon="skill-icons:instagram" width="75" height="75"/>
          </IconBox>
          <IconBox onClick={() => handleClick("google")}>
            <Icon icon="logos:google-icon" width="75" height="75"/>
          </IconBox>
          <IconBox onClick={() => handleClick("twitter")}>
            <Icon icon="skill-icons:twitter" width="75" height="75"/>
          </IconBox>
          <IconBox onClick={() => handleClick("openai")}>
            <Icon icon="logos:openai-icon" width="75" height="75"/>
          </IconBox>
        </ServicesBarWrapper>
      </ServicesBarContainer>
      <RightBox className={isRightBoxOpen ? 'open' : 'closed'} style={{ backgroundColor: getColor() }} />
      <SwitchSlider className={isOpen ? 'open' : 'closed'} onClick={() => setIsOpen(!isOpen)} />
    </>
  );
}

export default Servicesbar;