import React, { useState, useEffect } from 'react';
import { Icon } from '@iconify/react';
import { ServicesBarContainer, ServicesBarWrapper, IconBox, ButtonConnect, ServicesName, LeftColumn, RectangleContener } from './servicesbarElements';
import ButtonBox from '../buttonBlock';
import { useSpotifyConnect, useSpotifyConnected, useDiscordConnect, useDiscordConnected, useTwitchConnect, useTwitchConnected, useGmailConnect, useGmailConnected, useTwitterConnect, useTwitterConnected, useGithubConnect, useGithubConnected } from '../../api/apiSettingsPage';

const Servicesbar = (props) => {
  const [isOpen] = useState(true);
  const [isLeftBoxOpen, setisLeftBoxOpen] = useState(false);
  const [selectedService, setSelectedService] = useState(null);
  const [puzzleBlocktemps, setpuzzleBlocktemps] = useState([]);
  const handleSpotifyConnect = useSpotifyConnect();
  const isSpotifyConnected = useSpotifyConnected();
  const handleDiscordConnect = useDiscordConnect()
  const isDiscordConnected = useDiscordConnected()
  const handleTwitchConnect = useTwitchConnect();
  const isTwitchConnected = useTwitchConnected();
  const handleGmailConnect = useGmailConnect();
  const isGmailConnected = useGmailConnected();
  const handleTwitterConnect = useTwitterConnect();
  const isTwitterConnected = useTwitterConnected();
  const handleGithubConnect = useGithubConnect();
  const isGithubConnected = useGithubConnected();

    useEffect(() => {
      isSpotifyConnected.mutate();
      isDiscordConnected.mutate();
      isTwitchConnected.mutate();
      isGmailConnected.mutate();
      isTwitterConnected.mutate();
      isGithubConnected.mutate();
    }, []);

    const handleConnectServices = (event) => {
      event.preventDefault()
      switch (selectedService) {
        case "spotify":
          sessionStorage.setItem("serviceToConnect", "spotify")
          handleSpotifyConnect.mutate(JSON.stringify({redirect_uri: "http://localhost:8081/connectServices"}))
          break;
        case "discord":
          sessionStorage.setItem("serviceToConnect", "discord")
          handleDiscordConnect.mutate(JSON.stringify({redirect_uri: "http://localhost:8081/connectServices"}))
          break;
        case "twitch":
          sessionStorage.setItem("serviceToConnect", "twitch")
          handleTwitchConnect.mutate(JSON.stringify({redirect_uri: "http://localhost:8081/connectServices"}))
        break;
        case "gmail":
          sessionStorage.setItem("serviceToConnect", "gmail")
          handleGmailConnect.mutate(JSON.stringify({redirect_uri: "http://localhost:8081/connectServices"}))
        break;
        case "twitter":
          sessionStorage.setItem("serviceToConnect", "twitter")
          handleTwitterConnect.mutate(JSON.stringify({redirect_uri: "http://localhost:8081/connectServices"}))
        break;
        case "github":
          sessionStorage.setItem("serviceToConnect", "github")
          handleGithubConnect.mutate(JSON.stringify({redirect_uri: "http://localhost:8081/connectServices"}))
        break;
        default:
          break;
      }
    }

  function checkIfConnected() {
    switch (selectedService) {
      case "discord":
        if (isDiscordConnected.isSuccess && isDiscordConnected.data.data.connected)
          return true;
        else
          return false;
      case "spotify":
        if (isSpotifyConnected.isSuccess && isSpotifyConnected.data.data.connected)
          return true;
        else
          return false;
      case "twitch":
        if (isTwitchConnected.isSuccess && isTwitchConnected.data.data.connected)
          return true;
        else
          return false;
      case "gmail":
        if (isGmailConnected.isSuccess && isGmailConnected.data.data.connected)
          return true;
        else
          return false;
      case "twitter":
        if (isTwitterConnected.isSuccess && isTwitterConnected.data.data.connected)
          return true;
        else
          return false;
      case "github":
        if (isGithubConnected.isSuccess && isGithubConnected.data.data.connected)
          return true;
        else
          return false;
    }
  }

  const services = [
    { nom: 'discord',   nombre: 0, info: [], action: [], name: [], dbID: [] },
    { nom: 'spotify',   nombre: 0, info: [], action: [], name: [], dbID: [] },
    { nom: 'twitch', nombre: 0, info: [], action: [], name: [], dbID: [] },
    { nom: 'gmail',    nombre: 0, info: [], action: [], name: [], dbID: [] },
    { nom: 'twitter',   nombre: 0, info: [], action: [], name: [], dbID: [] },
    { nom: 'github',    nombre: 0, info: [], action: [], name: [], dbID: [] },
  ];

  function fillservices(id, i) {
    services[id].nombre += 1;
    services[id].name.push(props.tmpServices.data.data.actions[i].name);
    if (props.tmpServices.data.data.actions[i].type === "reaction") {
      services[id].action.push(false);
    } else {
      services[id].action.push(true);
    }
    services[id].dbID.push(props.tmpServices.data.data.actions[i].id);
  }

  if (props.tmpServices.isSuccess) {
    for (let i = 0; i < props.tmpServices.data.data.actions.length; i++) {
      switch (props.tmpServices.data.data.actions[i].service) {
        case "discord":
          fillservices(0, i);
          break;
        case "spotify":
          fillservices(1, i);
          break;
        case "twitch":
          fillservices(2, i);
          break;
        case "gmail":
          fillservices(3, i);
          break;
        case "twitter":
          fillservices(4, i);
          break;
        case "github":
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
        top: (x * 165) + 10,
        left: 50,
        color: getColorPuzzleBlock(services[i].nom),
        service: services[i].nom,
        index: t,
        action: services[i].action[x],
        name: services[i].name[x],
        dbID: services[i].dbID[x],
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
      case "twitch":
        return "#6713e2";
      case "gmail":
        return "#EA4335";
      case "twitter":
        return "#1da1f2";
      case "github":
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
      case "twitch":
        return "#9146ff";
      case "gmail":
        return "#d92516";
      case "twitter":
        return "#1486cc";
      case "github":
        return "#686f84";
      default:
        return "#454b5e";
    }
  }

  function getIcon(string) {
    switch (string) {
      case "discord":
        return "skill-icons:discord";
      case "spotify":
        return "logos:spotify-icon";
      case "twitch":
        return "logos:twitch";
      case "gmail":
        return "logos:google-gmail";
      case "twitter":
        return "skill-icons:twitter";
      case "github":
        return "mdi:github";
      default:
        return "mdi:github";
    }
  }

  return (
    <LeftColumn>
      <ServicesBarContainer className={isOpen ? 'open' : 'closed'} color={getColor()}>
        <ServicesName>Services</ServicesName>
        <ServicesBarWrapper>
          <IconBox onClick={() => handleClick("discord")}>
            {isDiscordConnected.isSuccess && isDiscordConnected.data.data.connected ?
              <Icon icon={getIcon("discord")} width="75" height="75" /> :
              <Icon icon={getIcon("discord")} width="75" height="75" opacity="0.5" />
            }
          </IconBox>
          <IconBox onClick={() => handleClick("spotify")}>
            {isSpotifyConnected.isSuccess && isSpotifyConnected.data.data.connected ?
              <Icon icon={getIcon("spotify")} width="75" height="75" > </Icon> :
              <Icon icon={getIcon("spotify")} width="75" height="75" opacity="0.5"/> 
            }
          </IconBox>
          <IconBox onClick={() => handleClick("twitch")}>
            {isTwitchConnected.isSuccess && isTwitchConnected.data.data.connected ?
              <Icon icon={getIcon("twitch")} color="purple" width="75" height="75" > </Icon> :
              <Icon icon={getIcon("twitch")} width="75" color="purple" height="75" opacity="0.5" > </Icon> 
            }
          </IconBox>
          <IconBox onClick={() => handleClick("gmail")}>
            {isGmailConnected.isSuccess && isGmailConnected.data.data.connected ?
              <Icon icon={getIcon("gmail")} width="75" height="75" > </Icon> :
              <Icon icon={getIcon("gmail")} width="75" height="75" opacity="0.5" > </Icon> 
            }
          </IconBox>
          <IconBox onClick={() => handleClick("twitter")}>
            {isTwitterConnected.isSuccess && isTwitterConnected.data.data.connected ?
              <Icon icon={getIcon("twitter")} width="75" height="75" />:
              <Icon icon={getIcon("twitter")} width="75" height="75" opacity="0.5" > </Icon>
            }
          </IconBox>
          <IconBox onClick={() => handleClick("github")}>
            {isGithubConnected.isSuccess && isGithubConnected.data.data.connected ?
              <Icon icon={getIcon("github")} width="75" height="75" /> :
              <Icon icon={getIcon("github")} width="75" height="75" opacity="0.5"/>
            }
          </IconBox>
        </ServicesBarWrapper>
      </ServicesBarContainer>
      <RectangleContener className={isLeftBoxOpen ? 'open' : 'closed'} color={getColor()}>
        {checkIfConnected() == false ?
          <ButtonConnect onClick={handleConnectServices}> Connect </ButtonConnect> :
          puzzleBlocktemps.map((info, index) => {
            return (
              <ButtonBox key={info.index} id={info.index} top={info.top} left={info.left} color={info.color} service={info.service} action={info.action} name={info.name} dbID={info.dbID}  icon={getIcon(info.service)}/>
            )
          })
        }
      </RectangleContener>
    </LeftColumn >
  );
}

export default Servicesbar;