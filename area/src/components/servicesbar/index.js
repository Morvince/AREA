import React, { useState } from 'react';
import { Icon } from '@iconify/react';
import { ServicesBarContainer, ServicesBarWrapper, IconBox, ServicesName, SwitchSlider, RectangleContener } from './servicesbarElements';
import PuzzleBlock from '../puzzleBlock';
import { Bin, Bin2, Bin3 } from '../bin/binElements'

const Servicesbar = () => {
  const [isOpen, setIsOpen] = useState(true);
  const [isLeftBoxOpen, setisLeftBoxOpen] = useState(false);
  const [selectedService, setSelectedService] = useState(null);

  function handleClick(service) {
    if(!isLeftBoxOpen || selectedService !== service) {
      setisLeftBoxOpen(true);
      setSelectedService(service);
    } else {
      setisLeftBoxOpen(false);
      setSelectedService();
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
        return "#EA4335";
      case "twitter":
        return "#1da1f2";
      case "openai":
        return "#686f84";
      default:
        return "#373B48";
    }
  }

  return (
    <>
      < Bin color={getColor()} />
      <ServicesBarContainer className={isOpen ? 'open' : 'closed'} color={getColor()}>
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
      <RectangleContener className={isLeftBoxOpen ? 'open' : 'closed'} color={getColor()}>
        <PuzzleBlock x={40} y={20} color={getColor()}/>
      </RectangleContener>
      < Bin2 color={getColor()} />
      < Bin3 />

    </>
  );
}

export default Servicesbar;