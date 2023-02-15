import React, { useState } from 'react'
import Draggable from 'react-draggable'
import { RectangleBlock, RectangleWrapper, AutomationText, LogoWrapper, AutomationRectangle, ArrowRectangle, CircleArcBot, CircleArcTop } from './blockElements'
import MyContext from '../Context'
import { Icon } from '@iconify/react';


const Block = (props) => {
  const [backgroundColor, setbackgroundColor] = useState(props.color)
  const [pos, setPos] = useState({ x: props.top, y: props.left })
  const { sharedData, setSharedData } = React.useContext(MyContext);
  const { linkedList, setLinkedList } = React.useContext(MyContext);
  const { playlist, setPlaylist } = React.useContext(MyContext);
  const [open, setOpen] = React.useState(false);

  const handleOpen = () => {
    setOpen(!open);
  };

  const [name, setName] = useState('');
  const [desc, setDesc] = useState('');


  React.useEffect(() => {
  }, [sharedData]);

  const handleChangeDesc = (event) => {
    setName(event.target.value);
    sharedData[props.id].toSend = { name: event.target.value, desc: desc, playlist_id: playlist.playlists[0].id }
  };

  const handleChangeName = (event) => {
    setDesc(event.target.value);
    sharedData[props.id].toSend = { name: event.target.value, desc: desc, playlist_id: playlist.playlists[0].id }
  };

  const handleDrag = (e, data) => {
    var rect = e.target.getBoundingClientRect();

    for (var i = 0; i < sharedData.length; i++) {
      if (props.id !== sharedData[i].index) {
        if (rect.top > sharedData[i].top + 110 && rect.top < sharedData[i].top + 130 && rect.left > sharedData[i].left - 10 && rect.left < sharedData[i].left + 10) {
          if (props.action === false) {
            setbackgroundColor('red')
            break;
          }
        } else {
          setbackgroundColor(props.color)
        }
      }
    }
  }

  const getIdAboveMe = (id) => {
    for (var i = 0; i < sharedData.length; i++) {
      if (id !== sharedData[i].index) {
        if (sharedData[id].top > sharedData[i].top + 110 && sharedData[id].top < sharedData[i].top + 130 && sharedData[id].left > sharedData[i].left - 10 && sharedData[id].left < sharedData[i].left + 10) {
          return sharedData[i].index
        }
      }
    }
    return;
  }

  const handleDragStop = (e, data) => {
    var rect = e.target.getBoundingClientRect();

    for (var i = 0; i < sharedData.length; i++) {
      if (props.id === sharedData[i].index) {
        sharedData[i].top = rect.top;
        sharedData[i].left = rect.left;
      }
    }

    for (var i = 0; i < sharedData.length; i++) {
      if (props.id !== sharedData[i].index) {
        if (rect.top > sharedData[i].top + 110 && rect.top < sharedData[i].top + 130 && rect.left > sharedData[i].left - 10 && rect.left < sharedData[i].left + 10) {
          if (props.action === false) {
            setbackgroundColor('red')
            sharedData[props.id].above = getIdAboveMe(props.id)
            break;
          }
        } else {
          setbackgroundColor(props.color)
          sharedData[props.id].above = getIdAboveMe(props.id)
          for (var x = 0; x < linkedList.length; x++) {
            if (linkedList[x] === props.id && props.action == false) {
              linkedList.splice(x, linkedList.length - x)
            }
          }
        }
      }
    }

    for (var i = 0; i < linkedList.length; i++) {
      if (linkedList[i] === sharedData[props.id].above) {
        linkedList.splice(i + 1, 0, props.id)
      }
    }
  }

  function renderInput() {
    if (props.nbrBox === 2) {
      return (
        <div>
          <input id="name" type="text" placeholder="Name" onChange={handleChangeName} />
          <input id="description" type="text" placeholder="Description" onChange={handleChangeDesc} />
        </div>
      )
    }
  }

  function getIcon(string) {
    switch (string) {
      case "discord":
        return "ic:baseline-discord";
      case "spotify":
        return "mdi:spotify";
      case "instagram":
        return "uil:instagram-alt" ;
      case "google":
        return "uil:google";
      case "twitter":
        return "mdi:twitter" ;
      case "openai":
        return "simple-icons:openai" ;
      default:
        return "simple-icons:openai" ;
    }
  }

  function getColor(selectedService) {
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

  return (
    <Draggable bounds='parent' onClick={} onDrag={handleDrag} onStop={handleDragStop}>
      <RectangleBlock color={backgroundColor} top={pos.x} left={pos.y}>
        <RectangleWrapper>
          {(props.action === false) ? <CircleArcTop background={getColor(props.service)} /> : null}
          <CircleArcBot background={backgroundColor} />
          <AutomationText>
            {props.name}
          </AutomationText>
          <LogoWrapper>
            <AutomationRectangle>
              <Icon icon={getIcon(props.service)} width="35" height="35" color="white" />
            </AutomationRectangle>
            <ArrowRectangle onClick={() => { console.log("click") }}>
              <Icon icon="material-symbols:arrow-forward-ios-rounded" color="white" width="55" height="55" />
            </ArrowRectangle>
          </LogoWrapper>
        </RectangleWrapper>
      </RectangleBlock>
    </Draggable>
  )
}

export default Block