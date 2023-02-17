import React, { useState, useEffect } from 'react';
import { Icon } from '@iconify/react';
import { ServicesBarContainer, ServicesBarWrapper, IconBox, ServicesName, LeftColumn, RectangleContener } from './servicesbarElements';
import ButtonBox from '../buttonBlock';
import { useGetAction } from '../../api/apiServicesPage';
import { useSpotifyConnect, useSpotifyConnected, useDiscordConnect, useDiscordConnected, useInstagramConnect, useInstagramConnected, useGoogleConnect, useGoogleConnected, useTwitterConnect, useTwitterConnected, useGithubConnect, useGithubConnected } from '../../api/apiSettingsPage';

const Servicesbar = () => {
  const [isOpen] = useState(true);
  const [isLeftBoxOpen, setisLeftBoxOpen] = useState(false);
  const [selectedService, setSelectedService] = useState(null);
  const [puzzleBlocktemps, setpuzzleBlocktemps] = useState([]);
  const tmpServices = useGetAction();
  const handleSpotifyConnect = useSpotifyConnect();
  const isSpotifyConnected = useSpotifyConnected();
  const handleDiscordConnect = useDiscordConnect()
  const isDiscordConnected = useDiscordConnected()
  const handleInstagramConnect = useInstagramConnect();
  const isInstagramConnected = useInstagramConnected();
  const handleGoogleConnect = useGoogleConnect();
  const isGoogleConnected = useGoogleConnected();
  const handleTwitterConnect = useTwitterConnect();
  const isTwitterConnected = useTwitterConnected();
  const handleGithubConnect = useGithubConnect();
  const isGithubConnected = useGithubConnected();

    useEffect(() => {
      isSpotifyConnected.mutate();
      isDiscordConnected.mutate();
      isInstagramConnected.mutate();
      isGoogleConnected.mutate();
      isTwitterConnected.mutate();
      isGithubConnected.mutate();
    }, []);

    const handleConnectServices = (event) => {
      event.preventDefault()
      switch (event.currentTarget.getAttribute("data-value")) {
        case "spotify":
          handleSpotifyConnect.mutate(JSON.stringify({redirect_uri: "http://localhost:8081/connectServices"}))
          break;
        case "discord":
          handleDiscordConnect.mutate(JSON.stringify({redirect_uri: "http://localhost:8081/connectServices"}))
          break;
        case "instagram":
            handleInstagramConnect.mutate(JSON.stringify({redirect_uri: "http://localhost:8081/connectServices"}))
        break;
        case "google":
            handleGoogleConnect.mutate(JSON.stringify({redirect_uri: "http://localhost:8081/connectServices"}))
        break;
        case "twitter":
            handleTwitterConnect.mutate(JSON.stringify({redirect_uri: "http://localhost:8081/connectServices"}))
        break;
        case "github":
            handleGithubConnect.mutate(JSON.stringify({redirect_uri: "http://localhost:8081/connectServices"}))
        break;
        default:
          break;
      }
    }

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
            {isDiscordConnected.isSuccess && isDiscordConnected.data.data.connected ?
              <Icon icon="skill-icons:discord" width="75" height="75" > </Icon> :
              <Icon icon="skill-icons:discord" width="75" height="75" opacity="0.5" onClick={handleConnectServices} > </Icon> 
            }
          </IconBox>
          <IconBox onClick={() => handleClick("spotify")}>
            {isSpotifyConnected.isSuccess && isSpotifyConnected.data.data.connected ?
              <Icon icon="logos:spotify-icon" width="75" height="75" > </Icon> :
              <Icon icon="logos:spotify-icon" width="75" height="75" opacity="0.5" onClick={handleConnectServices} > </Icon> 
            }
          </IconBox>
          <IconBox onClick={() => handleClick("instagram")}>
            {isInstagramConnected.isSuccess && isInstagramConnected.data.data.connected ?
              <Icon icon="skill-icons:instagram" width="75" height="75" > </Icon> :
              <Icon icon="skill-icons:instagram" width="75" height="75" opacity="0.5" onClick={handleConnectServices} > </Icon> 
            }
          </IconBox>
          <IconBox onClick={() => handleClick("google")}>
            {isGoogleConnected.isSuccess && isGoogleConnected.data.data.connected ?
              <Icon icon="logos:google-icon" width="75" height="75" > </Icon> :
              <Icon icon="logos:google-icon" width="75" height="75" opacity="0.5" onClick={handleConnectServices} > </Icon> 
            }
          </IconBox>


          <IconBox onClick={() => handleClick("twitter")}>
            {isTwitterConnected.isSuccess && isTwitterConnected.data.data.connected ?
              <Icon icon="skill-icons:twitter" width="75" height="75" > </Icon> :
              <Icon icon="skill-icons:twitter" width="75" height="75" opacity="0.5" onClick={handleConnectServices} > </Icon> 
            }
          </IconBox>
          <IconBox onClick={() => handleClick("openai")}>
            {isGithubConnected.isSuccess && isGithubConnected.data.data.connected ?
              <Icon icon="mdi:github" width="75" height="75" > </Icon> :
              <Icon icon="mdi:github" width="75" height="75" opacity="0.5" onClick={handleConnectServices} > </Icon> 
            }
          </IconBox>
        </ServicesBarWrapper>
      </ServicesBarContainer>
      <RectangleContener className={isLeftBoxOpen ? 'open' : 'closed'} color={getColor()}>
        {puzzleBlocktemps.map((info, index) => {
          return (
            <ButtonBox key={info.index} id={info.index} top={info.top} left={info.left} color={info.color} service={info.service} action={info.action} name={info.name} nbrBox={info.nbrBox} dbID={info.dbID}/>
          )
        })}
      </RectangleContener>
    </LeftColumn>
  );
}

export default Servicesbar;