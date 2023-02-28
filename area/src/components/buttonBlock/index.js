import React, { useState } from 'react';
import Draggable from 'react-draggable';
import { Rectangle, AutomationText, LogoWrapper, AutomationRectangle, ArrowRectangle, RectangleWrapper, CircleArcBot, CircleArcTop } from './buttonBlockElements';
import { Icon } from '@iconify/react';
import MyContext from '../Context';

const ButtonBox = (props) => {
  const [backgroundColor, setbackgroundColor] = useState(props.color)
  const [pos, setPos] = useState({ x: props.top, y: props.left })
  const { sharedData, setSharedData } = React.useContext(MyContext)
  const { ID, setID } = React.useContext(MyContext)
  const { linkedList } = React.useContext(MyContext)

  function getIcon(string) {
    switch (string) {
      case "discord":
        return "ic:baseline-discord";
      case "spotify":
        return "mdi:spotify";
      case "instagram":
        return "uil:instagram-alt" ;
      case "google":
        return "logos:google-gmail";
      case "twitter":
        return "mdi:twitter" ;
      case "github":
        return "mdi:github" ;
      default:
        return "mdi:github" ;
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
      case "github":
        return "#434857";
      default:
        return "#f9F9F9";
    }
  }

  const handleDrag = (e, data) => {
    if (data.x > 300) {
      setbackgroundColor(getColor(props.service))
    } else {
      setbackgroundColor(props.color)
    }
  }

  const handleDragStop = (e, data) => {
    var rect = e.target.getBoundingClientRect();
    if (data.x < 300) {
      setPos({ x: props.top, y: props.left });
    } else {
      if (props.action === true) {
        for (var i = 0; i < sharedData.length; i++) {
          if (sharedData[i].action === true) {
            setbackgroundColor(props.color)
            return;
          }
        }
        linkedList.push(ID)
      }
      setSharedData(sharedData => [...sharedData, { service: props.service, index: ID, color: props.color, top: rect.top - 80, left: rect.left - 150, action: props.action, above: null, name: props.name, dbId: props.dbID, toSend: [] }])
      setID(ID + 1);
    }
    setbackgroundColor(props.color)
  }

  return (
    <Draggable
      onDrag={handleDrag}
      onStop={handleDragStop}
      position={{ x: 0, y: 0 }}
    >
      <Rectangle color={backgroundColor} top={pos.x} left={pos.y}>
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
            <ArrowRectangle onClick={() => {}}>
              <Icon icon="material-symbols:arrow-forward-ios-rounded" color="white" width="55" height="55" />
            </ArrowRectangle>
          </LogoWrapper>
        </RectangleWrapper>
      </Rectangle>
    </Draggable>
  )
}

export default ButtonBox