import React, { useState, useEffect } from 'react';
import { Icon } from '@iconify/react';
import { ServicesBarContainer, ServicesBarWrapper, IconBox, ServicesName, LeftColumn, RectangleContener } from './servicesbarElements';
import PuzzleBlock from '../puzzleBlock';

const Servicesbar = () => {
  const [isOpen] = useState(true);
  const [isLeftBoxOpen, setisLeftBoxOpen] = useState(false);
  const [selectedService, setSelectedService] = useState(null);
  const [puzzleBlocktemps, setpuzzleBlocktemps] = useState([])

  const services = [
    { nom: 'discord', nombre: 5, info: [], block: [] },
    { nom: 'spotify', nombre: 1, info: [], block: [] },
    { nom: 'instagram', nombre: 2, info: [], block: [] },
    { nom: 'google', nombre: 4, info: [], block: [] },
    { nom: 'twitter', nombre: 3, info: [], block: [] },
    { nom: 'openai', nombre: 6, info: [], block: [] },
  ];
  let t = 0;
  for (let i = 0; i < services.length; i++) {
    for (let x = 0; x < services[i].nombre; x++) {
      services[i].info.push({
        top: (x * 140) + 10,
        left: 50,
        color: getColorPuzzleBlock(services[i].nom),
        service: services[i].nom,
        index: t
      });
      t++;
    }
    services[i].block = services[i].info.map(info => (
      <PuzzleBlock
        top={info.top}
        left={info.left}
        color={info.color}
        service={info.service}
        // key={i*services[i].length}
      />
    ));
  }

  //fonction pour débugg------------
  const logBlocks = () => {
    for (let i = 0; i < services.length; i++) {
      console.log(`Service ${services[i].nom}:`)
      for (let x = 0; x < services[i].block.length; x++) {
        console.log(services[i].block[x])
      }
    }
  }

  function displayPuzzleBlockTemps() {
    console.log("--------------------")
    puzzleBlocktemps.forEach(element => {
      console.log(element);
    });
  }
  //--------------------------------

  function handleClick(service) {
    if (!isLeftBoxOpen || selectedService !== service) {
      setisLeftBoxOpen(true);
      setSelectedService(service);
      const selectedServiceData = services.find(s => s.nom === service);
      setpuzzleBlocktemps(selectedServiceData.info.slice());
    } else {
      setisLeftBoxOpen(false);
      setSelectedService(null);
      setpuzzleBlocktemps([]);
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

  function getColorPuzzleBlock(string) {
    switch (string) {
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
  displayPuzzleBlockTemps();

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
          {puzzleBlocktemps.map((info, index) => {
            return (
              <PuzzleBlock key={info.index} top={info.top} left={info.left} color={info.color} service={info.service} />
            )
          })}
        </RectangleContener>
      </ServicesBarContainer>
    </LeftColumn>
  );
}

export default Servicesbar;