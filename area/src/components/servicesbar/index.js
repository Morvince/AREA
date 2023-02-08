import React, { useState, useEffect } from 'react';
import { Icon } from '@iconify/react';
import { ServicesBarContainer, ServicesBarWrapper, IconBox, ServicesName, LeftColumn, RectangleContener } from './servicesbarElements';
import ButtonBox from '../buttonBlock';
import { useGetAction } from '../../api/apiServicesPage';

const Servicesbar = () => {
  const [isOpen] = useState(true);
  const [isLeftBoxOpen, setisLeftBoxOpen] = useState(false);
  const [selectedService, setSelectedService] = useState(null);
  const [puzzleBlocktemps, setpuzzleBlocktemps] = useState([]);
  const tmpServices = useGetAction();

  useEffect(() => {
    tmpServices.mutate()
  }, []);

  const services = [
    { nom: 'discord',   nombre: 0, info: [], action: [], name: [], nbrBox: []},
    { nom: 'spotify',   nombre: 0, info: [], action: [], name: [], nbrBox: [2,0]},
    { nom: 'instagram', nombre: 0, info: [], action: [], name: [], nbrBox: []},
    { nom: 'google',    nombre: 0, info: [], action: [], name: [], nbrBox: []},
    { nom: 'twitter',   nombre: 0, info: [], action: [], name: [], nbrBox: []},
    { nom: 'openai',    nombre: 0, info: [], action: [], name: [], nbrBox: []},
  ];

  function fillservices(id, i) {
    services[id].nombre += 1;
    services[id].name.push(tmpServices.data.data.actions[i].name);
    if (tmpServices.data.data.actions[i].type === "reaction") {
      services[id].action.push(false);
    } else {
      services[id].action.push(true);
    }
  }

  if (tmpServices.isSuccess) {
    for (let i = 0; i < tmpServices.data.data.actions.length; i++) {
      switch (tmpServices.data.data.actions[i].service) {
        case "discord":
          fillservices(0, i);
          break;
        case "spotify":
          fillservices(1, i);
          break;
        case "instagram":
          fillservices(2, i);
          break;
        case "google":
          fillservices(3, i);
          break;
        case "twitter":
          fillservices(4, i);
          break;
        case "openai":
          fillservices(5, i);
          break;
        default:
          break;
      }
    }
  }

  let t = 0;
  for (let i = 0; i < services.length; i++) {
    for (let x = 0; x < services[i].nombre; x++) {
      services[i].info.push({
        top: (x * 140) + 10,
        left: 50,
        color: getColorPuzzleBlock(services[i].nom),
        service: services[i].nom,
        index: t,
        action: services[i].action[x],
        name: services[i].name[x],
        nbrBox: services[i].nbrBox[x],
        dbID: tmpServices.data.data.actions[x].id,
      });
      t++;
    }
  }

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
              <ButtonBox key={info.index} id={info.index} top={info.top} left={info.left} color={info.color} service={info.service} action={info.action} name={info.name} nbrBox={info.nbrBox} dbID={info.dbID}/>
            )
          })}
        </RectangleContener>
      </ServicesBarContainer>
    </LeftColumn>
  );
}

export default Servicesbar;