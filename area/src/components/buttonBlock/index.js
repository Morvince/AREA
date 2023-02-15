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

  const handleDrag = (e, data) => {
    if (data.x > 300) {
      setbackgroundColor('#f5f5f5')
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
      setSharedData(sharedData => [...sharedData, { service: props.service, index: ID, color: props.color, top: rect.top - 80, left: rect.left - 150, action: props.action, above: null, name: props.name, nbrBox: props.nbrBox, dbId: props.dbID, toSend: [] }])
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
          <CircleArcTop background={props.color} />
          <CircleArcTop background={props.color} />
          <CircleArcBot background={props.color} />
          <AutomationText>
            {props.name}
          </AutomationText>
          <LogoWrapper>
            <AutomationRectangle>
              <Icon icon="mdi:spotify" width="35" height="35" color="white" />
            </AutomationRectangle>
            <ArrowRectangle onClick={() => { console.log("click") }}>
              <Icon icon="material-symbols:arrow-forward-ios-rounded" color="white" width="55" height="55" />
            </ArrowRectangle>
          </LogoWrapper>
        </RectangleWrapper>
      </Rectangle>
    </Draggable>
  )
}

export default ButtonBox