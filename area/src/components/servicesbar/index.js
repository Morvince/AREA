import React, { useState } from 'react';
import { Icon } from '@iconify/react';
import { ServicesBarContainer, ServicesBarWrapper, IconBox, ServicesName, LeftColumn, RectangleContener } from './servicesbarElements';
import PuzzleBlock from '../puzzleBlock';

const Servicesbar = () => {
  const [isOpen] = useState(true);
  const [isLeftBoxOpen, setisLeftBoxOpen] = useState(false);
  const [selectedService, setSelectedService] = useState(null);

  const services = [
    { nom: 'discord', nombre: 5, before : 0 },
    { nom: 'spotify', nombre: 1, before : 5 },
    { nom: 'instagram', nombre: 2, before : 6 },
    { nom: 'google', nombre: 4, before : 8 },
    { nom: 'twitter', nombre: 3, before : 12 },
    { nom: 'openai', nombre: 6, before : 15 },
  ];

  function handleClick(service) {
    if (!isLeftBoxOpen || selectedService !== service) {
      setisLeftBoxOpen(true);
      setSelectedService(service);
    } else {
      setisLeftBoxOpen(false);
      setSelectedService();
    }
  }

  function getColor() {
    switch (selectedService) {
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
        return "#434857";
      default:
        return "#373B48";
    }
  }

  function getColorPuzzleBlock() {
    switch (selectedService) {
      case "discord":
        return "#5470d6";
      case "spotify":
        return "#10a143";
      case "instagram":
        return "#c2134f";
      case "google":
        return "#d92516";
      case "twitter":
        return "#1486cc";
      case "openai":
        return "#686f84";
      default:
        return "#454b5e";
    }
  }

  const puzzleBlocks = services
    .filter(service => selectedService === null || service.nom === selectedService)
    .map((service) => {
      return [...Array(service.nombre)].map((_, i, b) => (
        <PuzzleBlock key={i+b} color={getColorPuzzleBlock()} top={(i*140)+10} left={50}/>
      ));
    });

  return (
    <LeftColumn>
      <ServicesBarContainer className={isOpen ? 'open' : 'closed'} color={getColor()}>
        <ServicesName>Services</ServicesName>
        <ServicesBarWrapper>
          <IconBox onClick={() => handleClick("discord")}>
            <Icon icon="skill-icons:discord" width="75" height="75" />
          </IconBox>
          <IconBox onClick={() => handleClick("spotify")}>
            <Icon icon="logos:spotify-icon" width="75" height="75" />
          </IconBox>
          <IconBox onClick={() => handleClick("instagram")}>
            <Icon icon="skill-icons:instagram" width="75" height="75" />
          </IconBox>
          <IconBox onClick={() => handleClick("google")}>
            <Icon icon="logos:google-icon" width="75" height="75" />
          </IconBox>
          <IconBox onClick={() => handleClick("twitter")}>
            <Icon icon="skill-icons:twitter" width="75" height="75" />
          </IconBox>
          <IconBox onClick={() => handleClick("openai")}>
            <Icon icon="logos:openai-icon" width="75" height="75" />
          </IconBox>
        </ServicesBarWrapper>
        <RectangleContener className={isLeftBoxOpen ? 'open' : 'closed'} color={getColor()}>
          {puzzleBlocks}
        </RectangleContener>
      </ServicesBarContainer>
    </LeftColumn>
  );
}

export default Servicesbar;